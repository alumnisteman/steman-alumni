<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class MapSeeder extends Seeder
{
    public function run()
    {
        $cities = [
            ['lat' => -6.2088, 'lng' => 106.8456, 'c' => 'Jakarta'],
            ['lat' => -7.2504, 'lng' => 112.7688, 'c' => 'Surabaya'],
            ['lat' => -6.9175, 'lng' => 107.6191, 'c' => 'Bandung'],
            ['lat' => -0.7893, 'lng' => 113.9213, 'c' => 'Kalimantan'],
            ['lat' => 0.7893,  'lng' => 127.3842, 'c' => 'Ternate'],
            ['lat' => 1.4822,  'lng' => 124.8489, 'c' => 'Manado'],
            ['lat' => -5.1477, 'lng' => 119.4327, 'c' => 'Makassar'],
            ['lat' => 3.5952,  'lng' => 98.6722,  'c' => 'Medan'],
            ['lat' => 35.6895, 'lng' => 139.6917, 'c' => 'Tokyo, Japan'], // International
            ['lat' => 1.3521,  'lng' => 103.8198, 'c' => 'Singapore'], // International
        ];

        // Only seed if there are no users with coordinates to avoid messing up real data
        if (User::whereNotNull('latitude')->count() < 10) {
            User::where('role', 'alumni')->inRandomOrder()->take(80)->get()->each(function ($u) use ($cities) {
                $r = $cities[array_rand($cities)];
                
                // Add some random jitter so they don't overlap perfectly
                $u->latitude = $r['lat'] + (rand(-100, 100) / 1000);
                $u->longitude = $r['lng'] + (rand(-100, 100) / 1000);
                $u->city_name = $r['c'];
                $u->save();
            });
            echo "Coordinates seeded successfully!\n";
        } else {
            echo "Coordinates already exist.\n";
        }
    }
}
