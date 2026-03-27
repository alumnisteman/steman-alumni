<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            [
                'key' => 'site_name',
                'value' => 'IKATAN ALUMNI SMKN 2',
                'label' => 'Nama Situs',
                'group' => 'general'
            ],
            [
                'key' => 'school_name',
                'value' => 'SMKN 2 Ternate',
                'label' => 'Nama Sekolah',
                'group' => 'general'
            ],
            [
                'key' => 'contact_address',
                'value' => 'Jl. Ki Hajar Dewantoro, Ternate',
                'label' => 'Alamat Kontak',
                'group' => 'contact'
            ],

            // Contact
            [
                'key' => 'contact_email',
                'value' => 'sekretariat@alumni_smkn2.id',
                'label' => 'Email Kontak',
                'group' => 'contact'
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62-123-4567-890',
                'label' => 'Nomor Telepon',
                'group' => 'contact'
            ],

            // Program Labels
            [
                'key' => 'program_scholarship',
                'value' => 'Beasiswa Alumni',
                'label' => 'Label Program Beasiswa',
                'group' => 'program'
            ],
            [
                'key' => 'program_mentoring',
                'value' => 'Mentoring Karir',
                'label' => 'Label Program Mentoring',
                'group' => 'program'
            ],
            [
                'key' => 'program_social_impact',
                'value' => 'Social Impact',
                'label' => 'Label Program Sosial',
                'group' => 'program'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
