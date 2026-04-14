<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MapDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            ['Jakarta', -6.2088, 106.8456, 'Teknik Ketenagalistrikan'],
            ['Tokyoo', 35.6762, 139.6503, 'Teknologi Informasi'],
            ['London', 51.5074, -0.1278, 'Teknik Mesin'],
            ['New York', 40.7128, -74.0060, 'Teknik Sipil'],
            ['Sydney', -33.8688, 151.2093, 'Teknik Elektronika'],
            ['Makassar', -5.1476, 119.4327, 'Teknik Ketenagalistrikan'],
            ['Sorong', -0.8765, 131.2558, 'Teknik Mesin'],
            ['Manado', 1.4748, 124.8421, 'Teknologi Informasi'],
        ];

        foreach ($locations as $loc) {
            User::updateOrCreate(
                ['email' => strtolower(str_replace(' ', '', $loc[0])) . '@steman.ac.id'],
                [
                    'name' => 'Alumni ' . $loc[0],
                    'password' => Hash::make('Password123!'),
                    'role' => 'alumni',
                    'major' => $loc[3],
                    'graduation_year' => rand(2010, 2024),
                    'latitude' => $loc[1],
                    'longitude' => $loc[2],
                    'city_name' => $loc[0],
                    'country_name' => ($loc[0] == 'Jakarta' || $loc[0] == 'Makassar' || $loc[0] == 'Sorong' || $loc[0] == 'Manado') ? 'Indonesia' : 'International',
                    'status' => 'active'
                ]
            );
        }
    }
}
