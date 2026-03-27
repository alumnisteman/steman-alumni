<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            BadgeSeeder::class,
            ProgramSeeder::class,
            SettingSeeder::class,
        ]); // Default Admin User
        User::updateOrCreate(
            ['email' => 'admin@steman.ac.id'],
            [
                'name' => 'Administrator',
                'password' => \Illuminate\Support\Facades\Hash::make('Admin@1234'),
                'role' => 'admin',
                'status' => 'approved',
                'email_verified_at' => now(),
            ]
        );

        // Test User (non-admin)
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
