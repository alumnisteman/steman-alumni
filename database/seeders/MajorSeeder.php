<?php
namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    public function run(): void
    {
        $majors = [
            ['name' => 'Teknik Komputer dan Jaringan', 'group' => 'TIK', 'status' => 'active'],
            ['name' => 'Rekayasa Perangkat Lunak', 'group' => 'TIK', 'status' => 'active'],
            ['name' => 'Multimedia', 'group' => 'TIK', 'status' => 'active'],
            ['name' => 'Akuntansi', 'group' => 'Bisnis', 'status' => 'active'],
            ['name' => 'Perkantoran', 'group' => 'Bisnis', 'status' => 'active'],
            ['name' => 'Pemasaran', 'group' => 'Bisnis', 'status' => 'active'],
        ];

        foreach ($majors as $major) {
            Major::updateOrCreate(['name' => $major['name']], $major);
        }
    }
}
