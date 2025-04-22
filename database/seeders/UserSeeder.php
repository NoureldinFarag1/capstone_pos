<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin']);
        }

        if (!Role::where('name', 'manager')->exists()) {
            Role::create(['name' => 'manager']);
        }

        if (!Role::where('name', 'cashier')->exists()) {
            Role::create(['name' => 'cashier']);
        }

        // Create admin user if doesn't exist
        if (!User::where('email', 'admin@example.com')->exists()) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $admin->assignRole('admin');
        } else {
            $admin = User::where('email', 'admin@example.com')->first();
            $admin->assignRole('admin');
        }

        // Create manager user if doesn't exist
        if (!User::where('email', 'manager@example.com')->exists()) {
            $manager = User::create([
                'name' => 'Store Manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $manager->assignRole('manager');
        } else {
            $manager = User::where('email', 'manager@example.com')->first();
            $manager->assignRole('manager');
        }

        // Create 3 cashier users if they don't exist
        for ($i = 1; $i <= 3; $i++) {
            $email = "cashier$i@example.com";

            if (!User::where('email', $email)->exists()) {
                $cashier = User::create([
                    'name' => "Cashier $i",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]);
                $cashier->assignRole('cashier');
            } else {
                $cashier = User::where('email', $email)->first();
                $cashier->assignRole('cashier');
            }
        }
    }
}
