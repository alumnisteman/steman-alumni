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
        // 1. Seed Basis (Badge, Program, Setting, Major, Jobs)
        $this->call([
            BadgeSeeder::class,
            ProgramSeeder::class,
            SettingSeeder::class,
            MajorSeeder::class,
            JobVacancySeeder::class,
        ]);

        // 2. Default Admin User
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

        // 3. Real Alumni Data (Test)
        $alumniData = [
            [
                'name' => 'Alumni Nasional 1',
                'email' => 'alumni1@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'alumni',
                'status' => 'approved',
                'latitude' => -6.2088, // Jakarta
                'longitude' => 106.8456,
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'tahun_lulus' => '2020',
            ],
            [
                'name' => 'Alumni Nasional 2',
                'email' => 'alumni2@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'alumni',
                'status' => 'approved',
                'latitude' => -7.2575, // Surabaya
                'longitude' => 112.7521,
                'jurusan' => 'Rekayasa Perangkat Lunak',
                'tahun_lulus' => '2021',
            ],
            [
                'name' => 'Alumni Internasional',
                'email' => 'alumni_intl@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'alumni',
                'status' => 'approved',
                'latitude' => 35.6762, // Tokyo
                'longitude' => 139.6503,
                'jurusan' => 'Multimedia',
                'tahun_lulus' => '2019',
            ],
        ];

        foreach ($alumniData as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
