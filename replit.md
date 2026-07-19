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
Deploy dari Windows dengan PowerShell:
```powershell
.\scripts\deploy\deploy.ps1   # V6 — recommended
.\scripts\deploy.ps1          # V5 — alternatif
```

## ⚠️ PROTEKSI FITUR STABIL — BACA INI SEBELUM EDIT

### Masalah yang sudah diperbaiki (jangan dibalik)
Sebelumnya setiap deploy me-reset hero background, judul halaman depan, data donasi, dan laporan LPJ. Penyebabnya adalah:

1. **Deploy scripts memanggil `db:seed`** — sudah dihapus dari semua script deploy
2. **Seeder memakai `updateOrCreate`** — sudah diganti ke `firstOrCreate`

### File yang TIDAK BOLEH diubah balik
| File | Yang harus ada |
|------|----------------|
| `database/seeders/SettingSeeder.php` | `Setting::firstOrCreate(...)` — BUKAN `updateOrCreate` |
| `database/seeders/DemoFundSeeder.php` | `DonationCampaign::firstOrCreate(...)` — BUKAN `updateOrCreate` |
| `scripts/deploy.ps1` | TIDAK ada `db:seed` |
| `scripts/deploy/deploy.ps1` | TIDAK ada `db:seed` |
| `scripts/server_management/deploy_updates.py` | TIDAK ada `db:seed` |

### Aturan seeder
- `firstOrCreate` = hanya buat jika belum ada → **AMAN untuk deploy**
- `updateOrCreate` = buat atau timpa → **BERBAHAYA, menghapus perubahan admin**

### Push ke GitHub
Fix sudah ada di Replit dan di server. Agar permanen di repo GitHub, lakukan dari Windows:
```bash
git add database/seeders/SettingSeeder.php database/seeders/DemoFundSeeder.php scripts/deploy.ps1 scripts/deploy/deploy.ps1 scripts/server_management/deploy_updates.py
git commit -m "fix: firstOrCreate di seeder + hapus db:seed dari deploy scripts"
git push origin main
```

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
- Jangan tambahkan `db:seed` ke script deploy manapun
