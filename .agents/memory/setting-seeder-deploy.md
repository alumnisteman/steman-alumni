---
name: SettingSeeder & DemoFundSeeder deploy reset fix
description: Mengapa seeder harus pakai firstOrCreate, dan di mana saja db:seed harus dihapus dari deploy scripts
---

## Aturan
`database/seeders/SettingSeeder.php` dan `database/seeders/DemoFundSeeder.php` harus menggunakan `firstOrCreate`, **bukan** `updateOrCreate`.

Selain itu, panggilan `db:seed` harus **dihapus** dari semua script deploy berikut:
- `scripts/deploy.ps1`
- `scripts/deploy/deploy.ps1`
- `scripts/server_management/deploy_updates.py`

**Why:** Script deploy memangil `php artisan db:seed --class=SettingSeeder --force` setiap deploy. `updateOrCreate` menimpa semua nilai admin (hero_background, hero_title, data kampanye donasi, foto ketua umum, dsb) ke nilai default seeder — merusak konten yang sudah diubah via admin panel. `DemoFundSeeder` juga memakai `updateOrCreate` yang menimpa `current_amount` dan data LPJ kampanye donasi.

**How to apply:**
- Setiap kali ada perubahan pada seeder, pastikan tetap pakai `firstOrCreate`.
- Jangan tambahkan kembali `db:seed` ke deploy scripts.
- Jika ada seeder baru yang perlu dipaksa berjalan ulang (sekali saja), jalankan manual via SSH, bukan lewat deploy.

## Deploy scripts yang sudah dibersihkan dari db:seed
- `scripts/deploy.ps1` (V5) — baris lama: `db:seed --class=SettingSeeder` → diganti `migrate --force`
- `scripts/deploy/deploy.ps1` (V6) — baris lama: `db:seed --class=SettingSeeder --force; db:seed --class=MapDataSeeder --force` → diganti `migrate --force`
- `scripts/server_management/deploy_updates.py` — baris lama: `db:seed --class=SettingSeeder --force` → diganti `migrate --force`

## Server produksi
- IP: `103.175.219.57`
- Path: `/var/www/steman-alumni`
- URL: https://alumni-steman.my.id
- Fix sudah dicopy ke server dan ke dalam container (`steman_app`)
- Fix belum di-push ke GitHub — perlu user push dari Windows agar permanen di repo
