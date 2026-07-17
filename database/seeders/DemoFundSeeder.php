<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DonationCampaign;

class DemoFundSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dana Reuni Akbar 2026
        DonationCampaign::updateOrCreate(
            ['slug' => 'dana-reuni-akbar-2026'],
            [
                'title'         => 'Dana Reuni Akbar 2026',
                'description'   => 'Program penggalangan dana untuk mendukung penyelenggaraan Reuni Akbar Alumni STEMAN Ternate 2026. Dana digunakan untuk venue, konsumsi, dokumentasi, dan kenang-kenangan seluruh peserta.',
                'bank_info'     => "Bank: BRI\nNo. Rek: 0123-0456-7890\nAtas Nama: Forum Silaturahmi Alumni Steman Ternate\n\nBank: BNI\nNo. Rek: 0987654321\nAtas Nama: Forum Silaturahmi Alumni Steman Ternate",
                'goal_amount'   => 50000000,
                'current_amount'=> 27500000,
                'type'          => 'event',
                'status'        => 'active',
                'is_featured'   => true,
                'total_expense' => 22000000,
                'expense_distribution' => [
                    ['label' => 'Venue & Dekorasi',          'percent' => 35, 'amount' => 7700000],
                    ['label' => 'Konsumsi & Katering',       'percent' => 30, 'amount' => 6600000],
                    ['label' => 'Dokumentasi & Publikasi',   'percent' => 15, 'amount' => 3300000],
                    ['label' => 'Kenang-kenangan',           'percent' => 12, 'amount' => 2640000],
                    ['label' => 'Operasional Panitia',       'percent' =>  8, 'amount' => 1760000],
                ],
                'sponsor_count'    => 12,
                'show_donor_list'  => true,
                'report_status'    => 'draft',
            ]
        );

        // 2. Dana Beasiswa Abadi
        DonationCampaign::updateOrCreate(
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
                'expense_distribution' => [
                    ['label' => 'Beasiswa S1',              'percent' => 55, 'amount' => 10175000],
                    ['label' => 'Beasiswa SMA/SMK',         'percent' => 25, 'amount' =>  4625000],
                    ['label' => 'Administrasi Yayasan',     'percent' => 12, 'amount' =>  2220000],
                    ['label' => 'Biaya Seleksi & Survey',   'percent' =>  8, 'amount' =>  1480000],
                ],
                'sponsor_count'     => 28,
                'show_donor_list'   => true,
                'report_status'     => 'verified',
                'report_verified_at'=> '2026-07-01',
            ]
        );

        // 3. Update campaign "INFORMASI KEUANGAN" dengan data laporan
        $inf = DonationCampaign::where('slug', 'like', '%informasi-keuangan%')->first();
        if ($inf) {
            $inf->update([
                'current_amount'    => 17800000,
                'goal_amount'       => 30000000,
                'total_expense'     => 15000000,
                'expense_distribution' => [
                    ['label' => 'Konsumsi',     'percent' => 40, 'amount' => 6000000],
                    ['label' => 'Operasional',  'percent' => 30, 'amount' => 4500000],
                    ['label' => 'Dokumentasi',  'percent' => 20, 'amount' => 3000000],
                    ['label' => 'Lain-lain',    'percent' => 10, 'amount' => 1500000],
                ],
                'sponsor_count'  => 5,
                'show_donor_list'=> true,
                'report_status'  => 'draft',
            ]);
        }

        $this->command->info('DemoFundSeeder: 2 fund baru + 1 updated.');
    }
}
