<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\DbDumper\Databases\MySql as SpatieMySql;

class BackupController extends Controller
{
    public function download()
    {
    // Allow long-running backup
    @set_time_limit(300);
        // Define the filename and path
        $filename = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/' . $filename);

        // Get database configuration
        $dbHost = env('DB_HOST');
        $dbPort = env('DB_PORT');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        // Ensure destination directory exists
        if (!is_dir(dirname($path))) {
            @mkdir(dirname($path), 0755, true);
        }

        try {
            $dumper = SpatieMySql::create()
                ->setDbName($dbName)
                ->setUserName($dbUser)
                ->setPassword($dbPass ?? '')
                ->setHost($dbHost)
                ->setPort((int)$dbPort)
                ->addExtraOption('--single-transaction')
                ->addExtraOption('--quick')
                ->addExtraOption('--skip-lock-tables')
                ->addExtraOption('--set-gtid-purged=OFF')
                ->addExtraOption('--skip-column-statistics'); // avoid version mismatch issues

            // Respect socket if provided
            $dbSocket = env('DB_SOCKET');
            if (!empty($dbSocket)) {
                // If the version doesn't support setSocket, pass as extra option
                if (method_exists($dumper, 'setSocket')) {
                    $dumper->setSocket($dbSocket);
                } else {
                    $dumper->addExtraOption('--socket=' . escapeshellarg($dbSocket));
                }
            }

            // Allow custom mysqldump binary path
            $binPathFromEnv = env('MYSQLDUMP_PATH_DIR');
            if ($binPathFromEnv && is_dir($binPathFromEnv)) {
                $dumper->setDumpBinaryPath(rtrim($binPathFromEnv, '/'));
            } else {
                $mysqldumpFull = $this->resolveMysqldumpPath();
                if ($mysqldumpFull) {
                    $dumper->setDumpBinaryPath(dirname($mysqldumpFull));
                }
            }

            // Try native mysqldump first
            try {
                $dumper->dumpToFile($path);
                Log::info('mysqldump completed', ['path' => $path]);
            } catch (\Throwable $dumpError) {
                Log::warning('mysqldump failed, attempting PHP fallback', [
                    'error' => $dumpError->getMessage(),
                ]);
                $this->phpFallbackDump($path, $dbName, $dbHost, $dbUser, $dbPass);
            }

            if (!file_exists($path) || filesize($path) === 0) {
                Log::error('Backup file missing or empty after dump.');
                Session::flash('error', 'Backup failed: file missing or empty after dump.');
                return redirect()->route('dashboard');
            }

            return response()->download($path)->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            Log::error('Database backup failed: ' . $e->getMessage());
            Session::flash('error', 'Failed to create database backup: ' . $e->getMessage());
            return redirect()->route('dashboard');
        }
    }

    private function resolveMysqldumpPath(): ?string
    {
        // Allow override from .env
        $fromEnv = env('MYSQLDUMP_PATH');
        if ($fromEnv && is_executable($fromEnv)) {
            return $fromEnv;
        }

        // Common locations on macOS and Linux
        $candidates = [
            '/opt/homebrew/bin/mysqldump',      // Homebrew (Apple Silicon)
            '/usr/local/bin/mysqldump',        // Homebrew (Intel)
            '/usr/local/mysql/bin/mysqldump',  // MySQL pkg
            '/usr/bin/mysqldump',              // System
        ];
        foreach ($candidates as $path) {
            if (is_executable($path)) return $path;
        }

        // Last resort: which
        $whichOutput = [];
        $code = 1;
        @exec('which mysqldump', $whichOutput, $code);
        if ($code === 0 && !empty($whichOutput[0]) && is_executable($whichOutput[0])) {
            return $whichOutput[0];
        }

        return null;
    }

    private function phpFallbackDump(string $path, string $dbName, string $host, string $user, ?string $pass): void
    {
        $tables = [];
        foreach (DB::select('SHOW TABLES') as $row) {
            $values = array_values((array)$row);
            if (isset($values[0])) {
                $tables[] = $values[0];
            }
        }

        $fh = fopen($path, 'w');
        if (!$fh) {
            throw new \RuntimeException('Cannot open fallback dump file for writing');
        }
        fwrite($fh, "-- Fallback PHP dump\n-- Database: {$dbName}\n-- Generated: " . date('c') . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($tables as $table) {
            $create = DB::select("SHOW CREATE TABLE `{$table}`");
            if (!empty($create)) {
                $createSql = $create[0]->{'Create Table'} ?? $create[0]->{'Create Table'} ?? null;
                if ($createSql) {
                    fwrite($fh, "-- Structure for table `{$table}`\nDROP TABLE IF EXISTS `{$table}`;\n{$createSql};\n\n");
                }
            }
            fwrite($fh, "-- Data for table `{$table}`\n");
            DB::table($table)->orderBy(DB::raw('1'))->chunk(500, function ($rows) use ($fh, $table) {
                $inserts = [];
                foreach ($rows as $row) {
                    $vals = [];
                    foreach ((array)$row as $val) {
                        if (is_null($val)) {
                            $vals[] = 'NULL';
                        } else {
                            $escaped = addslashes((string)$val);
                            $escaped = str_replace(["\n", "\r"], ['\\n', '\\r'], $escaped);
                            $vals[] = "'{$escaped}'";
                        }
                    }
                    $inserts[] = '(' . implode(',', $vals) . ')';
                }
                if ($inserts) {
                    fwrite($fh, "INSERT INTO `{$table}` VALUES \n    " . implode("\n   ,", $inserts) . ";\n");
                }
            });
            fwrite($fh, "\n");
        }
        fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($fh);
        Log::info('Fallback PHP dump completed', ['path' => $path]);
    }
    public function showSyncForm()
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }
        return view('backup.sync');
    }

    public function sync(Request $request)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }
        $validated = $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt|max:51200', // 50MB
        ]);

        $file = $validated['sql_file'];
        $originalName = $file->getClientOriginalName();
        $tempDir = storage_path('app/restore-temp');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }
        $tempPath = $tempDir . '/' . time() . '-' . preg_replace('/[^A-Za-z0-9_.-]/','_', $originalName);
        $file->move($tempDir, basename($tempPath));

        $connection = DB::connection();
        $pdo = $connection->getPdo();
        $pdoTransaction = false;

        if (!$pdo->inTransaction()) {
            try {
                $pdo->beginTransaction();
                $pdoTransaction = true;
            } catch (\Throwable $txError) {
                Log::warning('Unable to open wrapping transaction for restore; continuing without rollback safety', [
                    'error' => $txError->getMessage(),
                ]);
            }
        }

        try {
            Log::info('Starting database sync from file', ['file' => $tempPath]);
            $connection->statement('SET FOREIGN_KEY_CHECKS=0');

            $handle = fopen($tempPath, 'r');
            if (!$handle) {
                throw new \RuntimeException('Cannot open uploaded file for reading');
            }
            $statement = '';
            while (($line = fgets($handle)) !== false) {
                $trim = trim($line);
                // Skip comments and empty lines
                if ($trim === '' || str_starts_with($trim, '--') || str_starts_with($trim, '#') || preg_match('/^\/\*/', $trim)) {
                    continue;
                }
                $statement .= $line;
                if (preg_match('/;\s*$/', $trim)) {
                    $normalized = trim($statement);
                    if ($this->shouldSkipControlStatement($normalized)) {
                        $statement = '';
                        continue;
                    }
                    // Execute accumulated statement
                    try {
                        $connection->unprepared($statement);
                    } catch (\Throwable $stmtErr) {
                        fclose($handle);
                        throw $stmtErr;
                    }
                    $statement = '';
                }
            }
            fclose($handle);

            if ($pdoTransaction && $pdo->inTransaction()) {
                $pdo->commit();
            }
            $connection->statement('SET FOREIGN_KEY_CHECKS=1');
            Log::info('Database sync completed successfully');
            return redirect()->route('dashboard')->with('status','Database synchronized from file successfully.');
        } catch (\Throwable $e) {
            if ($pdoTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $connection->statement('SET FOREIGN_KEY_CHECKS=1');
            Log::error('Database sync failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['sql_file' => 'Restore failed: ' . $e->getMessage()]);
        } finally {
            // Optionally remove temp file
            if (isset($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    private function shouldSkipControlStatement(string $statement): bool
    {
        $normalized = strtoupper(trim($statement));
        $normalized = rtrim($normalized, ';');

        $controlKeywords = [
            'COMMIT',
            'ROLLBACK',
            'START TRANSACTION',
            'BEGIN',
            'BEGIN TRANSACTION',
            'LOCK TABLES',
            'UNLOCK TABLES',
            'SET AUTOCOMMIT=0',
            'SET AUTOCOMMIT=1',
            'SET FOREIGN_KEY_CHECKS=0',
            'SET FOREIGN_KEY_CHECKS=1',
        ];

        foreach ($controlKeywords as $keyword) {
            if ($normalized === $keyword || str_starts_with($normalized, $keyword . ' ')) {
                return true;
            }
        }

        return false;
    }
}
