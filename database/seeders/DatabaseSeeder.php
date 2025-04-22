<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First run roles and permissions seeder
        $this->call(RolesAndPermissionsSeeder::class);

        // Create users including admin, manager, and cashiers
        $this->call(UserSeeder::class);

        // Add product structure - categories and brands
        $this->call(CategorySeeder::class);
        $this->call(BrandSeeder::class);

        // Add inventory items
        $this->call(ItemSeeder::class);

        // Finally, add sales data
        $this->call(SaleSeeder::class);
    }
}
