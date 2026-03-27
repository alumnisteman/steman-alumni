<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Alumni Pelopor',
                'description' => 'Diberikan kepada alumni yang bergabung di awal sistem baru.',
                'image' => 'badges/pioneer.png',
                'points_required' => 0,
            ],
            [
                'name' => 'Kontributor Aktif',
                'description' => 'Aktif berdiskusi di forum alumni.',
                'image' => 'badges/contributor.png',
                'points_required' => 100,
            ],
            [
                'name' => 'Inspirator Steman',
                'description' => 'Mencapai poin tinggi dan memberikan dampak positif.',
                'image' => 'badges/inspirator.png',
                'points_required' => 500,
            ],
            [
                'name' => 'Legenda SMKN 2',
                'description' => 'Level tertinggi untuk dedikasi luar biasa.',
                'image' => 'badges/legend.png',
                'points_required' => 1500,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['name' => $badge['name']], $badge);
        }
    }
}
