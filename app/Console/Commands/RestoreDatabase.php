<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class RestoreDatabase extends Command
{
    protected $signature = 'backup:restore {file : Path to SQL file}';
    protected $description = 'Restore database from a SQL dump file';

    public function handle(): int
    {
        $path = $this->argument('file');
        if (!File::exists($path)) {
            $this->error('File not found: ' . $path);
            return 1;
        }
        if (File::size($path) > 50 * 1024 * 1024) { // 50MB limit
            $this->error('File too large (>50MB).');
            return 1;
        }

        $this->warn('Starting restore from: ' . $path);
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::beginTransaction();
            $handle = fopen($path, 'r');
            if (!$handle) {
                throw new \RuntimeException('Cannot open file for reading');
            }
            $statement = '';
            $count = 0;
            while (($line = fgets($handle)) !== false) {
                $trim = trim($line);
                if ($trim === '' || str_starts_with($trim, '--') || str_starts_with($trim, '#') || preg_match('/^\/\*/', $trim)) {
                    continue;
                }
                $statement .= $line;
                if (preg_match('/;\s*$/', $trim)) {
                    DB::unprepared($statement);
                    $statement = '';
                    $count++;
                    if ($count % 100 === 0) {
                        $this->line('Executed ' . $count . ' statements...');
                    }
                }
            }
            fclose($handle);
            DB::commit();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->info('Restore completed. Executed statements: ' . $count);
            Log::info('CLI restore completed', ['file' => $path, 'statements' => $count]);
            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            Log::error('CLI restore failed', ['error' => $e->getMessage()]);
            $this->error('Restore failed: ' . $e->getMessage());
            return 1;
        }
    }
}
