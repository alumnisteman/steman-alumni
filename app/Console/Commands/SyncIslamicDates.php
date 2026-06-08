<?php

namespace App\Console\Commands;

use App\Models\EventTheme;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Perintah: php artisan event-themes:sync-islamic {tahun_masehi?}
 *
 * Menghitung tanggal Masehi dari referensi Hijriah yang tersimpan di DB
 * lalu memperbarui start_month/start_day/end_month/end_day secara otomatis.
 *
 * Algoritma Hijri→Gregorian menggunakan rumus Tabular Islamic Calendar
 * dengan titik acuan: 1 Muharram 1445 H = 19 Juli 2023 (Saudi Arabia / terverifikasi).
 */
class SyncIslamicDates extends Command
{
    protected $signature   = 'event-themes:sync-islamic {year? : Tahun Masehi target (default: tahun ini)}';
    protected $description = 'Perbarui tanggal tema Hari Besar Islam sesuai kalender Hijriah tahun yang ditentukan';

    // Leap years dalam siklus 30 tahun Hijriah (tahun ke-2,5,7,10,13,16,18,21,24,26,29)
    private const HIJRI_LEAP = [2, 5, 7, 10, 13, 16, 18, 21, 24, 26, 29];

    // Akumulasi hari dari 1 Muharram ke awal setiap bulan (tahun biasa)
    private const MONTH_OFFSET = [0, 30, 59, 89, 118, 148, 177, 207, 236, 266, 295, 325];

    // Titik acuan terverifikasi: 1 Muharram 1445 H = 19 Juli 2023
    private const ANCHOR_HIJRI_YEAR = 1445;
    private const ANCHOR_DATE       = '2023-07-19';

    public function handle(): int
    {
        $gregorianYear = (int) ($this->argument('year') ?? now()->year);

        $this->info("🌙 Sinkronisasi tanggal Hari Besar Islam untuk tahun Masehi {$gregorianYear}...");
        $this->newLine();

        $themes = EventTheme::where('is_islamic', true)
                            ->whereNotNull('hijri_month')
                            ->whereNotNull('hijri_day')
                            ->get();

        if ($themes->isEmpty()) {
            $this->warn('Tidak ada tema Islam dengan data Hijriah di database.');
            return self::SUCCESS;
        }

        $updated = 0;
        $anchor  = Carbon::parse(self::ANCHOR_DATE);

        foreach ($themes as $theme) {
            // Hitung tahun Hijriah yang kemungkinan jatuh di $gregorianYear
            $hijriYearApprox = self::ANCHOR_HIJRI_YEAR + intval(($gregorianYear - 2023) / 0.970224);

            // Coba H-1, H, H+1 — ambil yang tanggalnya paling tepat jatuh di $gregorianYear
            $best = null;
            for ($hy = $hijriYearApprox - 1; $hy <= $hijriYearApprox + 1; $hy++) {
                $start = $this->hijriToGregorian($hy, $theme->hijri_month, $theme->hijri_day, $anchor);
                if ($start->year === $gregorianYear) {
                    $best = $start;
                    break;
                }
            }

            if (! $best) {
                $this->warn("  ⏭  {$theme->name}: tidak ada tanggal di tahun {$gregorianYear}, dilewati.");
                continue;
            }

            $duration = max(1, (int) ($theme->hijri_duration ?? 1));
            $end      = $best->copy()->addDays($duration - 1);

            $theme->update([
                'start_month' => $best->month,
                'start_day'   => $best->day,
                'end_month'   => $end->month,
                'end_day'     => $end->day,
            ]);
            EventTheme::flushCache();

            $updated++;
            $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            $this->line(sprintf(
                '  ✅  %-35s %s %d – %s %d (%d hari)',
                $theme->name,
                $bulan[$best->month], $best->day,
                $bulan[$end->month],  $end->day,
                $duration
            ));
        }

        $this->newLine();
        $this->info("✔  {$updated} dari {$themes->count()} tema berhasil diperbarui.");
        return self::SUCCESS;
    }

    /**
     * Konversi tanggal Hijriah → Carbon (Gregorian) menggunakan offset dari anchor.
     *
     * Metode: hitung selisih hari dari anchor (1 Muharram 1445 = 2023-07-19)
     * ke target, lalu tambahkan ke tanggal anchor.
     */
    private function hijriToGregorian(int $hYear, int $hMonth, int $hDay, Carbon $anchor): Carbon
    {
        // Total hari dari epoch ke 1 Muharram anchor (1445 H)
        $daysToAnchor = $this->totalDaysFromEpoch(self::ANCHOR_HIJRI_YEAR, 1, 1);
        $daysToTarget = $this->totalDaysFromEpoch($hYear, $hMonth, $hDay);
        $offset       = $daysToTarget - $daysToAnchor;

        return $anchor->copy()->addDays($offset);
    }

    /**
     * Hitung total hari dari epoch Hijriah ke (year, month, day).
     * Rumus: sum hari semua tahun sebelumnya + hari dalam tahun ini.
     */
    private function totalDaysFromEpoch(int $year, int $month, int $day): int
    {
        $days = 0;

        // Hari dari tahun 1 s/d (year-1)
        for ($y = 1; $y < $year; $y++) {
            $days += $this->isHijriLeap($y) ? 355 : 354;
        }

        // Hari dari awal tahun ke awal bulan ini
        $days += self::MONTH_OFFSET[$month - 1];

        // Koreksi: bulan 12 di tahun kabisat dapat 30 hari (default 29)
        // — tidak perlu; MONTH_OFFSET sudah menghitung 29 untuk Dzulhijjah.

        // Hari dalam bulan ini
        $days += $day;

        return $days;
    }

    private function isHijriLeap(int $year): bool
    {
        return in_array($year % 30, self::HIJRI_LEAP, true);
    }
}
