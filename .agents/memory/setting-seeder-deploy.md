---
name: SettingSeeder deploy reset fix
description: Mengapa SettingSeeder harus pakai firstOrCreate, dan script deploy mana yang memanggilnya
---

## Aturan
`database/seeders/SettingSeeder.php` harus menggunakan `Setting::firstOrCreate(['key' => ...], $setting)`, **bukan** `updateOrCreate`.

**Why:** Script deploy (`scripts/deploy.ps1`, `scripts/deploy/deploy.ps1`, `scripts/server_management/deploy_updates.py`) memanggil `php artisan db:seed --class=SettingSeeder --force` setiap deploy. Kalau pakai `updateOrCreate`, semua nilai admin (hero_background, hero_title, chairman_photo, dsb) akan di-reset ke nilai default setiap kali deploy — merusak konten yang sudah diubah via admin panel.

**How to apply:** Setiap kali ada perubahan pada SettingSeeder, pastikan tetap pakai `firstOrCreate`. Jika ada setting baru yang perlu ditambahkan, cukup tambahkan ke array `$settings` — `firstOrCreate` akan otomatis membuat record baru jika belum ada, tanpa menyentuh yang sudah ada.

## Script deploy yang memanggil SettingSeeder
- `scripts/deploy.ps1` (V5) — baris: `db:seed --class=SettingSeeder`
- `scripts/deploy/deploy.ps1` (V6) — baris: `db:seed --class=SettingSeeder --force`
- `scripts/server_management/deploy_updates.py` — baris: `db:seed --class=SettingSeeder --force`

## Server produksi
- IP: `103.175.219.57`
- Path: `/var/www/steman-alumni`
- URL: https://alumni-steman.my.id
- Deploy script: `scripts/deploy.sh` (git pull + docker build + migrate)
