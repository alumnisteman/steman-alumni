<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DonationCampaign;

class DemoFundSeeder extends Seeder
{
    public function run(): void
    {
        // Palet warna untuk donut chart
        $reuni2026Colors = [
            '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6',
            '#ef4444', '#06b6d4', '#ec4899', '#84cc16',
            '#f97316', '#14b8a6', '#6366f1',
        ];

        $reuni2026Dist = [
            ['label' => 'Penyediaan Konsumsi',                  'percent' => 24, 'amount' =>  54740000],
            ['label' => 'Sewa Panggung & Audio Visual (2 hari)','percent' => 23, 'amount' =>  53000000],
            ['label' => 'Cindera Mata Almamater / SMK N 2',     'percent' => 11, 'amount' =>  25400000],
            ['label' => 'Cetak & Media Publikasi',              'percent' => 11, 'amount' =>  24446400],
            ['label' => 'Material Dekorasi Venue',              'percent' =>  9, 'amount' =>  20541000],
            ['label' => 'Sewa & Belanja Lain-lain',             'percent' =>  7, 'amount' =>  15100000],
            ['label' => 'Bantuan Sembako 100 Paket',            'percent' =>  6, 'amount' =>  12760000],
            ['label' => 'Sewa Perlengkapan Pendukung Venue',    'percent' =>  5, 'amount' =>  12305000],
            ['label' => 'ATK & Operasional Kesekretariatan',    'percent' =>  3, 'amount' =>   6105280],
            ['label' => 'Software & Kelengkapan IT',            'percent' =>  1, 'amount' =>   2614050],
            ['label' => 'Rapat LPJ & Pembubaran Panitia',       'percent' =>  1, 'amount' =>   3330000],
        ];

        // Tambahkan warna ke setiap item distribusi
        foreach ($reuni2026Dist as $i => &$item) {
            $item['color'] = $reuni2026Colors[$i] ?? '#6366f1';
        }
        unset($item);

        // ── 1. Dana Reuni Akbar 2026 — data REAL dari LPJ PDF ──────────────────
        // firstOrCreate: hanya buat jika BELUM ada. Data LPJ real tidak akan
        // tertimpa oleh proses deploy atau seeder yang dijalankan ulang.
        DonationCampaign::firstOrCreate(
            ['slug' => 'dana-reuni-akbar-2026'],
            [
                'title'         => 'Dana Reuni Akbar 2026',
                'description'   => 'Laporan Pertanggungjawaban Panitia Pelaksana Reuni Akbar 2026 Forum Silaturahmi Alumni STEMAN Ternate. Tema: "Menjalin Silaturahmi, Merajut Kisah, dan Membangun Sinergi". Dilaksanakan 20–26 Juni 2026 di SMK Negeri 2 Ternate, Lapangan Ngaralamo, dan Landmark Kota Ternate. Total peserta: 840 orang.',
                'bank_info'     => 'Laporan ini merupakan dokumen pertanggungjawaban resmi panitia kepada seluruh alumni.',
                'goal_amount'   => 230000000,
                'current_amount'=> 230345250,   // Total Pemasukan real dari LPJ hal.10
                'type'          => 'event',
                'status'        => 'completed',
                'is_featured'   => true,
                'total_expense' => 230341930,   // Total Pengeluaran real dari LPJ hal.10
                'expense_distribution' => $reuni2026Dist,
                'sponsor_count'     => 11,      // 11 donatur potensial dari LPJ hal.11
                'show_donor_list'   => true,
                'report_status'     => 'verified',
                'report_verified_at'=> '2026-07-18',
            ]
        );

        // ── 2. Update campaign "INFORMASI KEUANGAN" — sama, pakai data LPJ ────
        $inf = DonationCampaign::where('slug', 'like', '%informasi-keuangan%')->first();
        if ($inf) {
            $inf->update([
                'title'         => 'Reuni Akbar 2026 — Laporan Keuangan',
                'description'   => 'Laporan Pertanggungjawaban resmi Panitia Pelaksana Reuni Akbar 2026. Total 840 peserta, 11 donatur utama, 25 dari 37 angkatan berkontribusi. Pengelolaan keuangan sangat efisien: sisa kas Rp 3.320.',
                'current_amount'=> 230345250,
                'goal_amount'   => 230000000,
                'type'          => 'event',
                'status'        => 'completed',
                'total_expense' => 230341930,
                'expense_distribution' => $reuni2026Dist,
                'sponsor_count'     => 11,
                'show_donor_list'   => true,
                'report_status'     => 'verified',
                'report_verified_at'=> '2026-07-18',
            ]);
        }

        // ── 3. Dana Beasiswa Abadi STEMAN ───────────────────────────────────────
        $beasiswaDist = [
            ['label' => 'Beasiswa S1',              'percent' => 55, 'amount' => 10175000, 'color' => '#3b82f6'],
            ['label' => 'Beasiswa SMA/SMK',         'percent' => 25, 'amount' =>  4625000, 'color' => '#10b981'],
            ['label' => 'Administrasi Yayasan',     'percent' => 12, 'amount' =>  2220000, 'color' => '#f59e0b'],
            ['label' => 'Biaya Seleksi & Survey',   'percent' =>  8, 'amount' =>  1480000, 'color' => '#8b5cf6'],
        ];

        DonationCampaign::firstOrCreate(
            ['slug' => 'dana-beasiswa-abadi-steman'],
            [
                'title'         => 'Dana Beasiswa Abadi STEMAN',
                'description'   => 'Program beasiswa berkelanjutan untuk putra-putri alumni STEMAN berprestasi yang membutuhkan dukungan finansial dalam melanjutkan pendidikan tinggi. Dana dikelola sebagai investasi jangka panjang oleh yayasan.',
                'bank_info'     => "Bank: Mandiri\nNo. Rek: 1234-567-8901\nAtas Nama: Yayasan Alumni Steman Ternate\n\nBank: BCA\nNo. Rek: 0234567890\nAtas Nama: Yayasan Alumni Steman Ternate",
                'goal_amount'   => 100000000,
                'current_amount'=> 43750000,
                'type'          => 'foundation',
                'status'        => 'active',
                'is_featured'   => true,
                'total_expense' => 18500000,
                'expense_distribution' => $beasiswaDist,
                'sponsor_count'     => 28,
                'show_donor_list'   => true,
                'report_status'     => 'verified',
                'report_verified_at'=> '2026-07-01',
            ]
        );

        $this->command->info('DemoFundSeeder: data real LPJ Reuni Akbar 2026 berhasil dimuat.');
    }
}
