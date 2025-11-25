<?php

namespace App\Tasks;

use Illuminate\Support\Facades\Artisan;

class BackupTask
{
    public function __invoke()
    {
        try {
            // Run the backup command (database only)
            Artisan::call('backup:run', ['--only-db' => true]);
            \Log::info('Scheduled backup finished', [
                'output' => Artisan::output(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Scheduled backup failed', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
