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
                'icon' => 'bi-award',
                'points_required' => 0,
            ],
            [
                'name' => 'Kontributor Aktif',
                'description' => 'Aktif berdiskusi di forum alumni.',
                'icon' => 'bi-chat-dots',
                'points_required' => 100,
            ],
            [
                'name' => 'Inspirator Steman',
                'description' => 'Mencapai poin tinggi dan memberikan dampak positif.',
                'icon' => 'bi-lightning-charge',
                'points_required' => 500,
            ],
            [
                'name' => 'Legenda SMKN 2',
                'description' => 'Level tertinggi untuk dedikasi luar biasa.',
                'icon' => 'bi-trophy',
                'points_required' => 1500,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['name' => $badge['name']], $badge);
        }
    }
}
