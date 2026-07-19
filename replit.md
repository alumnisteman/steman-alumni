# Alumni STEMAN - Portal Resmi Forum Silaturahmi Alumni STEMAN Ternate

## Gambaran Proyek
Aplikasi web Laravel 12 (PHP) untuk manajemen alumni SMKN 2 Ternate. Fitur utama: direktori alumni, donasi, beasiswa, mentoring, forum, merchandise, peta persebaran alumni, dan sistem chat real-time (WebSocket via Laravel Reverb).

## Stack Teknologi
- **Backend**: Laravel 12 (PHP 8.2)
- **Frontend**: Blade templates + Vite + Bootstrap 5
- **Database**: MySQL
- **Cache/Queue**: Redis
- **Real-time**: Laravel Reverb (WebSocket)
- **Search**: Meilisearch
- **Infrastructure**: Docker (docker-compose.prod.yml)
- **Server Produksi**: `103.175.219.57` → https://alumni-steman.my.id

## Cara Deploy ke Server Produksi
Deploy dilakukan dari lokal menggunakan PowerShell:
```powershell
.\scripts\deploy\deploy.ps1  # Deploy V6 (ZIP + push ke server)
```
**Atau** dari Replit via SSH:
```bash
sshpass -p 'PASSWORD' ssh root@103.175.219.57 "cd /var/www/steman-alumni && bash scripts/deploy.sh"
```

## Proteksi Fitur Stabil (PENTING)
`SettingSeeder` menggunakan `firstOrCreate` (bukan `updateOrCreate`) agar nilai yang diubah admin **tidak pernah di-reset** saat deploy. Ini melindungi:
- Hero background & judul halaman depan
- Data ketua umum, sambutan, foto
- Semua pengaturan yang bisa diubah via admin panel

**Jangan ubah kembali ke `updateOrCreate` di `database/seeders/SettingSeeder.php`.**

## Variabel Lingkungan yang Diperlukan
Lihat `.env.example` untuk daftar lengkap. Kunci utama:
- `APP_KEY` — Laravel app key
- `DB_*` — koneksi MySQL
- `REDIS_*` — koneksi Redis
- `REVERB_*` — WebSocket config
- `GOOGLE_CLIENT_ID/SECRET` — Google OAuth
- `LINKEDIN_CLIENT_ID/SECRET` — LinkedIn OAuth
- `GEMINI_API_KEY` — Google Gemini AI
- `MAIL_*` — SMTP Gmail

## User Preferences
- Jangan ubah fitur yang sudah stabil (donasi, hero background) saat deploy
- Gunakan `firstOrCreate` di semua seeder untuk setting yang bisa diubah admin
