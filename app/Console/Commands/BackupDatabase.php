<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup');
        Artisan::call('backup:run --only-db');
        $this->info('Database backup completed!');
    }

    protected function cleanupOldBackups()
    {
        $backupDirectory = storage_path('app/backups/');

        // Get all backup files in the backup directory
        $files = glob($backupDirectory . '*.sql');

        // Get the current date
        $now = Carbon::now();

        foreach ($files as $file) {
            $fileCreationTime = Carbon::createFromTimestamp(filemtime($file));
            $daysOld = $now->diffInDays($fileCreationTime);

            // If the backup is older than 7 days, delete it
            if ($daysOld > 7) {
                unlink($file);
                $this->info("Deleted old backup: {$file}");
            }
        }
    }
}
