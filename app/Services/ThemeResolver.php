<?php
namespace App\Services;

use App\Models\HolidayTheme;
use Carbon\Carbon;

/**
 * ThemeResolver
 *
 * Mengembalikan nama file CSS tema yang aktif berdasarkan tanggal
 * dan prioritas. Jika tidak ada event yang sedang berlangsung,
 * mengembalikan 'default'.
 */
class ThemeResolver
{
    /**
     * Nama file CSS (tanpa .css) tema aktif.
     */
    public static function getActiveTheme(): string
    {
        $theme = HolidayTheme::where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->orderByDesc('priority')
            ->first();

        return $theme ? $theme->css_file : 'default';
    }

    /**
     * Mengembalikan seluruh data tema aktif (untuk banner, warna, dll.)
     */
    public static function getActiveThemeData()
    {
        return HolidayTheme::where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->orderByDesc('priority')
            ->first();
    }
}
?>
