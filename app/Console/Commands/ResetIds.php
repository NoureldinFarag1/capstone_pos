<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB as DB;

class ResetIds extends Command
{
    protected $signature = 'reset:ids';
    protected $description = 'Resets all sale, item, brand IDs to start from 0';

    public function handle()
    {
        $this->info('Resetting IDs for sales, items, and brands...');

        // Reset Brands Table
        DB::statement('TRUNCATE TABLE brands');
        DB::statement('ALTER TABLE brands AUTO_INCREMENT = 1');
        $this->info('Brand IDs reset successfully.');

        // You can add more tables if needed
        // DB::statement('TRUNCATE TABLE table_name RESTART IDENTITY');

        $this->info('All IDs have been reset.');
    }
}
