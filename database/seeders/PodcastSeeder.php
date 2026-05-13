<?php

namespace Database\Seeders;

use App\Models\Podcast;
use Illuminate\Database\Seeder;

class PodcastSeeder extends Seeder
{
    public function run(): void
    {
        Podcast::create([
            'title' => 'Menembus Tech Giant dari SMK',
            'slug' => 'menembus-tech-giant-smk',
            'guest_name' => 'Kurnianto (Alumni 2015)',
            'category' => 'career',
            'description' => 'Bagaimana alumni kita bisa bersaing di perusahaan teknologi global. Simak strateginya!',
            'audio_url' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
            'thumbnail_url' => 'https://img.youtube.com/vi/aqz-KE-bpKQ/0.jpg',
            'duration' => '15:20',
            'is_published' => true
        ]);

        Podcast::create([
            'title' => 'Life in Tokyo: Kisah Alumni di Jepang',
            'slug' => 'life-in-tokyo-alumni',
            'guest_name' => 'Siti Aminah (Alumni 2018)',
            'category' => 'overseas',
            'description' => 'Pengalaman kerja di industri manufaktur Jepang dan bagaimana beradaptasi dengan budaya di sana.',
            'audio_url' => 'https://www.youtube.com/watch?v=S0Q4giuz0eM',
            'thumbnail_url' => 'https://img.youtube.com/vi/S0Q4giuz0eM/0.jpg',
            'duration' => '22:10',
            'is_published' => true
        ]);

        Podcast::create([
            'title' => 'Membangun Bisnis dari Nol',
            'slug' => 'membangun-bisnis-nol',
            'guest_name' => 'Budi Santoso (Alumni 2012)',
            'category' => 'startup',
            'description' => 'Dari hobi menjadi bisnis yang menghasilkan. Tips startup untuk alumni muda.',
            'audio_url' => 'https://www.youtube.com/watch?v=L_LUpnjuyP0',
            'thumbnail_url' => 'https://img.youtube.com/vi/L_LUpnjuyP0/0.jpg',
            'duration' => '18:45',
            'is_published' => true
        ]);
    }
}
