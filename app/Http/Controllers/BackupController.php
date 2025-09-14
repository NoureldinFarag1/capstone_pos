<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                ->addExtraOption('--skip-lock-tables');

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

            // Extra options
            $dumper->addExtraOption('--set-gtid-purged=OFF');

            $dumper->dumpToFile($path);

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
}
