<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        // Cek kolom yang tersedia di tabel programs (aman untuk DB lama maupun baru)
        $hasRegistrationLink = Schema::hasColumn('programs', 'registration_link');

        $programs = [
            [
                'title'             => 'Beasiswa Alumni',
                'slug'              => 'beasiswa-alumni',
                'description'       => 'Program bantuan biaya pendidikan bagi adik-adik siswa SMKN 2 yang berprestasi namun kurang mampu.',
                'content'           => "Program Beasiswa Alumni adalah inisiatif dari para alumni untuk memberikan kontribusi nyata bagi almamater.\n\nKriteria Penerima:\n1. Siswa aktif SMKN 2.\n2. Memiliki prestasi akademik atau non-akademik.\n3. Berasal dari keluarga kurang mampu.\n\nCara Pendaftaran:\nSilakan hubungi pengurus alumni melalui email atau nomor kontak yang tersedia.",
                'icon'              => 'bi-mortarboard-fill',
                'status'            => 'active',
                'registration_link' => 'https://forms.gle/ex-beasiswa-alumni',
            ],
            [
                'title'             => 'Mentoring Karir',
                'slug'              => 'mentoring-karir',
                'description'       => 'Bimbingan karir dan persiapan dunia kerja langsung dari alumni profesional kepada lulusan baru.',
                'content'           => "Mentoring Karir menghubungkan alumni yang sudah berpengalaman (Mentor) dengan alumni baru atau siswa (Mentee).\n\nTopik Mentoring:\n1. Persiapan CV & Interview.\n2. Tips meniti karir di industri tertentu.\n3. Akses ke jaringan profesional.\n\nJadwal Sesi:\nDilakukan secara berkala baik online maupun offline.",
                'icon'              => 'bi-briefcase-fill',
                'status'            => 'active',
                'registration_link' => 'https://forms.gle/ex-mentoring-karir',
            ],
            [
                'title'             => 'Social Impact',
                'slug'              => 'social-impact',
                'description'       => 'Kegiatan sosial, pengabdian masyarakat, dan bantuan kemanusiaan atas nama keluarga besar alumni.',
                'content'           => "Program Social Impact adalah wadah bagi alumni untuk melakukan kegiatan sosial yang bermanfaat bagi masyarakat luas.\n\nKegiatan Rutin:\n1. Donor darah.\n2. Bakti sosial ke panti asuhan.\n3. Bantuan bencana alam.\n\nMari bergabung untuk memberikan dampak positif bagi lingkungan sekitar kita.",
                'icon'              => 'bi-heart-fill',
                'status'            => 'active',
                'registration_link' => 'https://forms.gle/ex-social-impact',
            ],
        ];

        foreach ($programs as $program) {
            // Hapus registration_link jika kolom belum ada (database lama)
            if (!$hasRegistrationLink) {
                unset($program['registration_link']);
            }

            // Gunakan DB::table untuk menghindari mass assignment issue
            $existing = DB::table('programs')->where('slug', $program['slug'])->first();

            if ($existing) {
                DB::table('programs')->where('slug', $program['slug'])->update(
                    array_merge($program, ['updated_at' => now()])
                );
            } else {
                DB::table('programs')->insert(
                    array_merge($program, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        }
    }
}
