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
                'key' => 'running_text',
                'value' => 'Selamat Datang di Portal Resmi IKATAN ALUMNI SMKN 2 Ternate - Jalin Silaturahmi, Bangun Kontribusi!',
                'label' => 'Teks Berjalan (Running Text)',
                'group' => 'general'
            ],
            [
                'key' => 'contact_address',
                'value' => 'Jl. Ki Hajar Dewantoro, Ternate',
                'label' => 'address Kontak',
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

            // Chairman Section
            [
                'key' => 'chairman_name',
                'value' => 'H. Ahmad Yusuf, S.T.',
                'label' => 'Nama Ketua Umum',
                'group' => 'chairman'
            ],
            [
                'key' => 'chairman_period',
                'value' => 'Periode 2024 - 2028',
                'label' => 'Jabatan / Periode Ketua Umum',
                'group' => 'chairman'
            ],
            [
                'key' => 'chairman_message',
                'value' => 'Selamat datang di portal resmi Ikatan Alumni SMKN 2 Ternate. Mari kita jalin silaturahmi.',
                'label' => 'Sambutan Singkat Ketua Umum',
                'group' => 'chairman'
            ],
            [
                'key' => 'alumni_message',
                'value' => 'Wadah silaturahmi, kolaborasi, dan kontribusi nyata lulusan untuk almamater dan bangsa.',
                'label' => 'message Khusus Untuk Alumni',
                'group' => 'chairman'
            ],
            [
                'key' => 'chairman_photo',
                'value' => 'https://ui-avatars.com/api/?name=Ketua+Umum&background=ffcc00&color=000&size=400',
                'label' => 'Foto Ketua Umum',
                'group' => 'chairman'
            ],

            // Event Chairman Section
            [
                'key' => 'event_chairman_name',
                'value' => 'M. Rizky Ramadhan',
                'label' => 'Nama Ketua Panitia',
                'group' => 'event_chairman'
            ],
            [
                'key' => 'event_chairman_period',
                'value' => 'Reuni Akbar 2026',
                'label' => 'Tema / Keterangan Event',
                'group' => 'event_chairman'
            ],
            [
                'key' => 'event_chairman_message',
                'value' => 'Mari sukseskan acara temu kangen alumni tahun ini.',
                'label' => 'Sambutan Ketua Panitia',
                'group' => 'event_chairman'
            ],
            [
                'key' => 'event_chairman_photo',
                'value' => 'https://ui-avatars.com/api/?name=Ketua+Panitia&background=007bff&color=fff&size=400',
                'label' => 'Foto Ketua Panitia',
                'group' => 'event_chairman'
            ],

            // Hero Section (New)
            [
                'key' => 'hero_title',
                'value' => 'Koneksi Abadi, Kontribusi Tanpa Henti',
                'label' => 'Judul Banner Utama (Hero)',
                'group' => 'hero'
            ],
            [
                'key' => 'hero_description',
                'value' => 'Bergabunglah dengan ribuan alumni lainnya untuk membangun masa depan yang lebih cerah.',
                'label' => 'Deskripsi Banner Utama',
                'group' => 'hero'
            ],
            // Profile / Vision & Mission
            [
                'key' => 'vision',
                'value' => 'Menjadi wadah alumni yang solid, inovatif, dan berkontribusi nyata bagi almamater serta masyarakat luas.',
                'label' => 'Visi Organisasi',
                'group' => 'profile'
            ],
            [
                'key' => 'mission',
                'value' => "1. Menjalin komunikasi antar alumni di seluruh penjuru dunia.\n2. Memberikan beasiswa dan dukungan karir bagi lulusan baru.\n3. Berkontribusi dalam pengembangan sarana dan prasarana sekolah.",
                'label' => 'Misi Utama',
                'group' => 'profile'
            ],
            [
                'key' => 'hero_background',
                'value' => '/assets/images/hero_iluni.png',
                'label' => 'Gambar Latar Banner Utama (Hero Background)',
                'group' => 'hero'
            ],

            // Secretary Section
            [
                'key' => 'secretary_name',
                'value' => 'Hj. Siti Aminah',
                'label' => 'Nama Sekretaris Panitia',
                'group' => 'secretary'
            ],
            [
                'key' => 'secretary_period',
                'value' => 'Reuni Akbar 2026',
                'label' => 'Periode Sekretaris',
                'group' => 'secretary'
            ],
            [
                'key' => 'secretary_message',
                'value' => 'Mari kita sukseskan bersama agenda silaturahmi besar ini.',
                'label' => 'Sambutan Sekretaris',
                'group' => 'secretary'
            ],
            [
                'key' => 'secretary_photo',
                'value' => 'https://ui-avatars.com/api/?name=Sekretaris+Panitia&background=28a745&color=fff&size=400',
                'label' => 'Foto Sekretaris',
                'group' => 'secretary'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
