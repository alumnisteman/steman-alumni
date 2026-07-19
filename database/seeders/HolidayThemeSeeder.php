<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HolidayTheme;
use Carbon\Carbon;

class HolidayThemeSeeder extends Seeder
{
    public function run()
    {
        // Reset existing data (useful for development, safe in production as we only seed non‑critical lookup table)
        HolidayTheme::truncate();

        // Default fallback theme
        HolidayTheme::create([
            'name'            => 'default',
            'start_date'      => null,
            'end_date'        => null,
            'banner'          => 'default.jpg',
            'primary_color'   => '#0d6efd',
            'secondary_color' => '#6c757d',
            'priority'        => 0,
            'is_active'       => true,
        ]);

        // Kemerdekaan RI – 1‑31 Agustus
        HolidayTheme::create([
            'name'            => 'kemerdekaan',
            'start_date'      => Carbon::create(null, 8, 1),
            'end_date'        => Carbon::create(null, 8, 31),
            'banner'          => 'kemerdekaan.jpg',
            'primary_color'   => '#c8102e',
            'secondary_color' => '#ffffff',
            'priority'        => 70,
            'is_active'       => true,
        ]);

        // HUT STEMAN – 1‑30 November
        HolidayTheme::create([
            'name'            => 'hut_steman',
            'start_date'      => Carbon::create(null, 11, 1),
            'end_date'        => Carbon::create(null, 11, 30),
            'banner'          => 'hut_steman.jpg',
            'primary_color'   => '#ff9800',
            'secondary_color' => '#212529',
            'priority'        => 80,
            'is_active'       => true,
        ]);

        // Reuni Akbar – contoh 30 hari sebelum reuni (1‑Jun‑2026 – 30‑Jul‑2026)
        HolidayTheme::create([
            'name'            => 'reuni_akbar',
            'start_date'      => Carbon::create(2026, 6, 1),
            'end_date'        => Carbon::create(2026, 7, 30),
            'banner'          => 'reuni_akbar.jpg',
            'primary_color'   => '#4caf50',
            'secondary_color' => '#ffeb3b',
            'priority'        => 100,
            'is_active'       => true,
        ]);

        // Lebaran – contoh 2026‑04‑09 – 2026‑04‑15 (menyesuaikan kalender Hijriah)
        HolidayTheme::create([
            'name'            => 'lebaran',
            'start_date'      => Carbon::create(2026, 4, 9),
            'end_date'        => Carbon::create(2026, 4, 15),
            'banner'          => 'lebaran.jpg',
            'primary_color'   => '#28a745',
            'secondary_color' => '#ffc107',
            'priority'        => 90,
            'is_active'       => true,
        ]);

        // Natal – 24‑Des‑2026 – 1‑Jan‑2027
        HolidayTheme::create([
            'name'            => 'natal',
            'start_date'      => Carbon::create(2026, 12, 24),
            'end_date'        => Carbon::create(2027, 1, 1),
            'banner'          => 'natal.jpg',
            'primary_color'   => '#d32f2f',
            'secondary_color' => '#ffeb3b',
            'priority'        => 95,
            'is_active'       => true,
        ]);

        // Admin Birthday – 11 Oktober tiap tahun
        HolidayTheme::create([
            'name'            => 'admin_birthday',
            'start_date'      => Carbon::create(null, 10, 11),
            'end_date'        => Carbon::create(null, 10, 11),
            'banner'          => 'admin_birthday.jpg',
            'primary_color'   => '#ff69b4',
            'secondary_color' => '#ffffff',
            'priority'        => 85,
            'is_active'       => true,
        ]);

        
    }
}
?>
