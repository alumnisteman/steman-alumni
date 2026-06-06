# Panduan Pemeliharaan (Maintenance) — Portal Alumni STEMAN
**Terakhir diperbarui: Juni 2026 | Status: Production Active**

---

## Daftar Isi
1. [Jadwal Pemeliharaan Rutin](#1-jadwal-pemeliharaan-rutin)
2. [Cek Status Sistem](#2-cek-status-sistem)
3. [System Guard — Pengawas Otomatis](#3-system-guard--pengawas-otomatis)
4. [Backup dan Restore Database](#4-backup-dan-restore-database)
5. [Deploy Update Kode Terbaru](#5-deploy-update-kode-terbaru)
6. [Optimasi dan Bersihkan Cache](#6-optimasi-dan-bersihkan-cache)
7. [Reset Password Admin](#7-reset-password-admin)
8. [Troubleshooting Error Umum](#8-troubleshooting-error-umum)
9. [Penghapusan Data Permanen](#9-penghapusan-data-permanen)

---

## 1. Jadwal Pemeliharaan Rutin

| Frekuensi | Tugas | Cara |
|---|---|---|
| Setiap menit | Heartbeat scheduler | Otomatis |
| Setiap menit | Jalankan antrian queue | Otomatis (container `steman_queue`) |
| Setiap 5 menit | Cek kesehatan health_guard | Otomatis (cron) |
| Setiap jam | Autoheal sistem | Otomatis (cron) |
| Setiap hari pukul 02:00 | Backup database | Otomatis (cron) |
| Setiap hari pukul 03:00 | Bersihkan cache & optimasi | Otomatis (cron) |
| Seminggu sekali | Hapus file sampah storage | Otomatis (Minggu pukul 04:00) |
| Bulanan | Update dependency, cek keamanan | Manual oleh admin teknis |

---

## 2. Cek Status Sistem

### Cek Semua Container

```bash
docker ps --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}'
```

Semua container berikut harus berstatus `Up (healthy)`:

| Container | Fungsi |
|---|---|
| `steman_app` | Aplikasi Laravel |
| `steman_nginx` | Web Server |
| `steman_db` | Database |
| `steman_queue` | Worker queue |
| `steman_redis` | Cache & Session |
| `steman_meilisearch` | Pencarian |

### Cek Log Error Laravel secara Live

```bash
docker exec steman_app tail -f /var/www/storage/logs/laravel.log
```

### Cek Log Nginx

```bash
docker compose -f docker-compose.prod.yml logs -f nginx
```

### Cek Log Scheduler

```bash
tail -30 /var/log/steman-scheduler.log
```

### Cek Log Autoheal

```bash
tail -30 /var/www/steman-alumni/storage/logs/autoheal.log
```

---

## 3. System Guard — Pengawas Otomatis

System Guard adalah fitur pengawas otomatis yang memeriksa **21 titik kesehatan** setiap menit dan memperbaiki masalah secara otomatis jika bisa.

### Jalankan Pengecekan Manual

```bash
docker exec steman_app php artisan system:guard
```

Output normal (semua aman):
```
✅ ALL SYSTEMS OPERATIONAL — No issues found.
```

### Kirim Laporan Status ke Telegram

```bash
docker exec steman_app php artisan system:guard --report
```

### Titik Pemeriksaan System Guard

| Kode | Pemeriksaan | Keterangan |
|---|---|---|
| `db_down` | Koneksi database | Ping ke MySQL |
| `redis_down` | Koneksi Redis | Ping ke Redis |
| `queue_overload` | Antrian queue | Jumlah job > 1.000 |
| `meili_down` | Meilisearch | Cek endpoint `/health` |
| `disk_low` | Ruang disk | Alert jika < 1 GB |
| `storage_broken` | Folder storage | Cek bisa ditulis |
| `log_bloated` | Ukuran log | Alert jika > 50 MB |
| `scheduler_dead` | Scheduler hidup | Cek heartbeat tiap menit |
| `news_api_down` | News API | Cek jika API key dikonfigurasi |
| `smoke_test` | Halaman publik | Coba akses halaman utama |
| `symlink_broken` | Symlink storage | Cek `/public/storage` |
| `ai_offline` | AI Service | Cek Gemini/OpenRouter |

### Cara Membaca Status Circuit Breaker

Jika sebuah masalah gagal diperbaiki berkali-kali, System Guard akan masuk mode **OPEN** (10 menit tidak mencoba lagi) untuk menghindari spam. Reset manual:

```bash
docker exec steman_app php artisan tinker --execute "
  cache()->forget('system_guard_circuit_NAMA_ISSUE_state');
  echo 'Circuit breaker direset';
"
```

Ganti `NAMA_ISSUE` dengan kode masalah (misalnya: `scheduler_dead`, `news_api_down`).

---

## 4. Backup dan Restore Database

### Backup Manual Segera

```bash
# Backup database ke file terkompresi
docker exec steman_db mysqldump -u app_user -pPASSWORD_DB steman_alumni 2>/dev/null \
  | gzip > /root/backup_steman_$(date +%Y%m%d_%H%M%S).sql.gz

echo "Backup selesai: /root/backup_steman_*.sql.gz"
ls -lh /root/backup_steman_*.sql.gz | tail -3
```

### Cek Jadwal Backup Otomatis

```bash
crontab -l | grep backup
```

Seharusnya ada baris:
```
0 2 * * * cd /var/www/steman-alumni && ./scripts/backup_database.sh
0 2 * * * /usr/local/bin/steman-backup.sh
```

### Jalankan Script Backup Otomatis secara Manual

```bash
bash /var/www/steman-alumni/scripts/backup_database.sh
```

### Lihat Daftar File Backup

```bash
ls -lh /var/www/steman-alumni/backups/database/ 2>/dev/null || \
ls -lh /root/backup_steman_*.sql.gz 2>/dev/null
```

### Restore Database dari Backup

> ⚠️ **PERINGATAN:** Restore akan **menghapus semua data saat ini** di database. Pastikan Anda yakin!

```bash
# Dekompresi dan restore
gunzip < /root/NAMA_FILE_BACKUP.sql.gz | \
  docker exec -i steman_db mysql -u app_user -pPASSWORD_DB steman_alumni

echo "Restore selesai"
```

Setelah restore, bersihkan cache Laravel:

```bash
docker exec steman_app php artisan cache:clear
docker exec steman_app php artisan config:cache
```

---

## 5. Deploy Update Kode Terbaru

### Deploy via Script Otomatis (Direkomendasikan)

```bash
cd /var/www/steman-alumni
bash scripts/deploy.sh
```

Script ini secara otomatis:
1. Pull kode terbaru dari GitHub (`git pull`)
2. Rebuild image Docker
3. Jalankan migrasi database
4. Bersihkan dan rebuild cache Laravel
5. Set ulang permission folder storage
6. Restart container

### Deploy Manual (Langkah Demi Langkah)

```bash
cd /var/www/steman-alumni

# 1. Pull kode terbaru
git pull origin main

# 2. Rebuild dan restart container
docker compose -f docker-compose.prod.yml up -d --build

# 3. Jalankan migrasi (jika ada perubahan database)
docker exec steman_app php artisan migrate --force

# 4. Bersihkan cache lama
docker exec steman_app php artisan optimize:clear

# 5. Rebuild cache production
docker exec steman_app php artisan config:cache
docker exec steman_app php artisan route:cache
docker exec steman_app php artisan view:cache

# 6. Perbaiki permission
docker exec steman_app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker exec steman_app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "Deploy selesai!"
```

### Upload File Spesifik dari Lokal ke Server

```bash
# Dari komputer lokal, upload file tunggal
scp -o StrictHostKeyChecking=no \
  app/Services/NamaService.php \
  root@103.175.219.57:/var/www/steman-alumni/app/Services/NamaService.php

# Terapkan di server
ssh root@103.175.219.57 "docker exec steman_app php artisan config:cache"
```

---

## 6. Optimasi dan Bersihkan Cache

### Bersihkan Semua Cache (Jalankan Saat Ada Masalah Tampilan)

```bash
docker exec steman_app php artisan optimize:clear
```

Ini akan membersihkan: config cache, view cache, route cache, event cache, compiled files.

### Rebuild Cache Production (Jalankan Setelah Bersihkan)

```bash
docker exec steman_app php artisan config:cache
docker exec steman_app php artisan route:cache
docker exec steman_app php artisan view:cache
```

### Bersihkan Log yang Membengkak

```bash
# Cek ukuran log saat ini
docker exec steman_app du -sh /var/www/storage/logs/laravel.log

# Potong log (file tidak dihapus, hanya dikosongkan)
docker exec steman_app truncate -s 0 /var/www/storage/logs/laravel.log

echo "Log dikosongkan"
```

### Optimasi Database (Bulanan)

```bash
docker exec steman_db mysql -u app_user -pPASSWORD_DB steman_alumni \
  -e "OPTIMIZE TABLE users, news, jobs, alumni_profiles, audit_logs;"
```

### Bersihkan Image Docker yang Tidak Terpakai

```bash
docker image prune -f
docker volume prune -f
echo "Pembersihan Docker selesai"
```

---

## 7. Reset Password Admin

### Via Artisan Tinker

```bash
docker exec -it steman_app php artisan tinker
```

Di dalam Tinker, jalankan:

```php
$user = \App\Models\User::where('email', 'email_admin@gmail.com')->first();
$user->password = Hash::make('PasswordBaru@2026');
$user->save();
echo "Password berhasil diubah untuk: " . $user->email;
exit
```

### Via Perintah Langsung (Non-Interaktif)

```bash
docker exec steman_app php artisan tinker --execute \
  "\$u = \App\Models\User::where('email','email_admin@gmail.com')->first(); \$u->password = Hash::make('PasswordBaru@2026'); \$u->save(); echo 'Password diubah';"
```

---

## 8. Troubleshooting Error Umum

### ❌ Error 502 Bad Gateway

**Penyebab:** Container `steman_app` crash atau tidak merespons.

**Solusi:**
```bash
# Restart container PHP
docker compose -f docker-compose.prod.yml restart app

# Cek log untuk detail
docker exec steman_app tail -50 /var/www/storage/logs/laravel.log
```

### ❌ Error 500 Server Error

**Penyebab:** Kesalahan di kode PHP, view cache rusak, atau folder framework hilang.

**Solusi:**
```bash
# Pastikan folder framework ada
docker exec steman_app mkdir -p /var/www/storage/framework/{views,sessions,cache/data}
docker exec steman_app chmod -R 775 /var/www/storage/framework
docker exec steman_app chown -R www-data:www-data /var/www/storage/framework

# Bersihkan view cache
docker exec steman_app php artisan optimize:clear
docker exec steman_app php artisan optimize
```

### ❌ Foto / Gambar Tidak Muncul (404)

**Penyebab:** Storage symlink rusak atau permission salah.

**Solusi:**
```bash
docker exec steman_app php artisan storage:link --force
docker exec steman_app chown -R www-data:www-data /var/www/storage/app/public
docker exec steman_app chmod -R 775 /var/www/storage/app/public
```

### ❌ 419 Page Expired (CSRF Error)

**Penyebab:** Session kadaluarsa atau konfigurasi domain session salah.

**Solusi:**
```bash
docker exec steman_app php artisan cache:clear
# Pastikan SESSION_DOMAIN di .env sudah benar: .alumni-steman.my.id
```

### ❌ Scheduler Terdeteksi Mati

**Penyebab:** Heartbeat belum ditulis setelah container restart.

**Solusi:**
```bash
# Tulis ulang heartbeat secara manual
docker exec steman_app php artisan scheduler:heartbeat

# Verifikasi
docker exec steman_app php artisan system:guard
```

### ❌ Antrian (Queue) Macet

**Penyebab:** Container `steman_queue` crash atau tidak berjalan.

**Solusi:**
```bash
# Cek status container queue
docker ps | grep steman_queue

# Restart queue worker
docker compose -f docker-compose.prod.yml restart queue

# Restart semua worker yang menggantung
docker exec steman_app php artisan queue:restart
```

### ❌ SSL Expired / Tidak Bisa Akses HTTPS

**Penyebab:** Certbot gagal memperbarui sertifikat.

**Solusi:**
```bash
# Perbarui SSL secara manual
docker exec steman_certbot certbot renew --force-renewal

# Reload Nginx
docker exec steman_nginx nginx -s reload
```

---

## 9. Penghapusan Data Permanen

Aplikasi menggunakan fitur *Soft Deletes* — data yang "dihapus" dari panel admin sebenarnya hanya disembunyikan (bisa dipulihkan). Untuk menghapus permanen:

```bash
docker exec -it steman_app php artisan tinker
```

Di dalam Tinker:

```php
# Hapus permanen berita yang sudah di-trash
\App\Models\News::onlyTrashed()->forceDelete();

# Hapus permanen alumni yang sudah di-trash
\App\Models\User::onlyTrashed()->where('role', 'alumni')->forceDelete();

echo "Penghapusan permanen selesai";
exit
```

> ⚠️ **Data yang sudah di-forceDelete tidak bisa dipulihkan. Lakukan backup terlebih dahulu!**

---

*© 2026 Portal Alumni SMKN 2 Ternate (STEMAN)*
