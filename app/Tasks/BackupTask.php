<?php

namespace App\Tasks;

use Illuminate\Support\Facades\Artisan;

class BackupTask
{
    public function __invoke()
    {
        // Run the backup command
        Artisan::call('backup:run --only-db');
    }
}
