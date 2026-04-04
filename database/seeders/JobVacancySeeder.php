<?php
namespace Database\Seeders;

use App\Models\JobVacancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobVacancySeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            [
                'title' => 'Web Developer',
                'slug' => 'web-developer-' . Str::random(5),
                'company' => 'Tech Solutions Ltd.',
                'location' => 'Jakarta (Remote)',
                'description' => 'Mencari pengembang web Laravel junior.',
                'content' => 'Persyaratan: PHP, Laravel, Tailwind CSS.',
                'status' => 'active',
                'type' => 'Full-time'
            ],
            [
                'title' => 'Graphic Designer',
                'slug' => 'graphic-designer-' . Str::random(5),
                'company' => 'Creative Agency',
                'location' => 'Surabaya',
                'description' => 'Mencari desainer grafis kreatif.',
                'content' => 'Persyaratan: Adobe Photoshop, Illustrator.',
                'status' => 'active',
                'type' => 'Contract'
            ],
            [
                'title' => 'Admin Gudang',
                'slug' => 'admin-gudang-' . Str::random(5),
                'company' => 'Logistics Express',
                'location' => 'Malang',
                'description' => 'Mencari admin gudang teliti.',
                'content' => 'Persyaratan: Microsoft Excel, Pengalaman gudang.',
                'status' => 'active',
                'type' => 'Full-time'
            ],
        ];

        foreach ($jobs as $job) {
            JobVacancy::updateOrCreate(['title' => $job['title']], $job);
        }
    }
}
