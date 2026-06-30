# Panduan Pemeliharaan (Maintenance) — Portal Alumni STEMAN
**Terakhir diperbarui: 1 Juli 2026 | Status: Production Active**
**Server: 103.175.219.57 | Ubuntu 24.04 LTS | PHP 8.4 | MariaDB 10.6**

---

## Daftar Isi
1. [Jadwal Pemeliharaan Rutin](#1-jadwal-pemeliharaan-rutin)
2. [Cek Status Sistem](#2-cek-status-sistem)
3. [System Guard — Pengawas Otomatis 21 Titik](#3-system-guard--pengawas-otomatis-21-titik)
4. [Backup dan Restore Database](#4-backup-dan-restore-database)
5. [Deploy Update Kode Terbaru](#5-deploy-update-kode-terbaru)
6. [Optimasi dan Bersihkan Cache](#6-optimasi-dan-bersihkan-cache)
7. [Reset Password Admin](#7-reset-password-admin)
8. [Geocoding Peta Alumni (Steman Earth)](#8-geocoding-peta-alumni-steman-earth)
9. [Troubleshooting Error Umum](#9-troubleshooting-error-umum)
10. [Penghapusan Data Permanen](#10-penghapusan-data-permanen)

---

## 1. Jadwal Pemeliharaan Rutin

Semua tugas rutin berikut berjalan **otomatis via cron** di server. Tidak perlu tindakan manual kecuali ada masalah.

| Frekuensi | Tugas | Script / Perintah | Log |
|---|---|---|---|
| Setiap menit | Laravel Scheduler (queue, heartbeat, dll.) | `artisan schedule:run` | `/var/log/steman-scheduler.log` |
| Setiap 2 menit | Auto-Recovery container & layanan | `scripts/auto-recovery.sh` | `storage/logs/auto-recovery.log` |
| Setiap 5 menit | Health Guard System Guard | `scripts/health_check.sh` | `storage/logs/health.log` |
| Setiap 5 menit | Autoheal (self-healing otomatis) | `scripts/steman-autoheal.sh` | `storage/logs/autoheal.log` |
| Setiap 5 menit | Container Watchdog | `steman-scripts/container-watchdog.sh` | `/var/log/steman-watchdog.log` |
| Setiap 6 jam | Monitor error log | `scripts/monitor_errors.sh` | `logs/monitor.log` |
| Pukul 00:00 | Optimasi sistem | `scripts/system_optimize.sh` | `storage/logs/optimize.log` |
| Pukul 01:00 | Perpanjangan SSL otomatis | `certbot-renew-hook.sh` | `/var/log/certbot-hook.log` |
| Pukul 02:00 | Backup database | `scripts/backup.sh` | `storage/logs/backup.log` |
| Pukul 03:00 | Maintenance harian | `scripts/steman-maintenance.sh` | `/var/log/steman-maintenance.log` |
| Minggu 04:00 | Cleanup storage lama | `scripts/cleanup_storage.sh` | `logs/cleanup.log` |
| Bulanan | Update dependency, cek keamanan | Manual oleh admin teknis | — |

### Cek Daftar Cron Aktif

```bash
crontab -l
```

---

## 2. Cek Status Sistem

### Cek Semua Container

```bash
docker compose -f /var/www/steman-alumni/docker-compose.prod.yml ps --format 'table {{.Name}}\t{{.Status}}'
```

Container yang harus berstatus `Up (healthy)`:

| Container | Fungsi |
|---|---|
| `steman_app` | Aplikasi Laravel (PHP-FPM 8.4) |
| `steman_nginx` | Web Server Nginx + Brotli |
| `steman_db` | Database MariaDB 10.6 |
| `steman_queue` | Worker antrian background job |
| `steman_scheduler` | Laravel Scheduler |
| `steman_reverb` | WebSocket (Laravel Reverb) |
| `steman_redis` | Cache & Session |
| `steman_meilisearch` | Mesin pencarian full-text |
| `steman_certbot` | Pembaruan SSL otomatis |
| `steman_grafana` | Dashboard monitoring |
| `steman_prometheus` | Metrics collection |
| `steman_node_exporter` | Server metrics (CPU/RAM/Disk) |

### Cek Log Error Laravel secara Live

```bash
docker exec steman_app tail -f /var/www/storage/logs/laravel.log
```

### Cek 50 Baris Terakhir Log

```bash
docker exec steman_app tail -50 /var/www/storage/logs/laravel.log
```

### Cek Log Nginx

```bash
docker compose -f /var/www/steman-alumni/docker-compose.prod.yml logs -f nginx
```

### Cek Log Scheduler

```bash
tail -30 /var/log/steman-scheduler.log
```

### Cek Log Autoheal

```bash
tail -30 /var/www/steman-alumni/storage/logs/autoheal.log
```

### Cek Log Auto-Recovery

```bash
tail -30 /var/www/steman-alumni/storage/logs/auto-recovery.log
```

### Cek Disk dan Memory

```bash
df -h /          # Ruang disk
free -h          # Penggunaan RAM
```

---

## 3. System Guard — Pengawas Otomatis 21 Titik

System Guard adalah fitur pengawas otomatis yang memeriksa **21 titik kesehatan** setiap siklus dan memperbaiki masalah secara otomatis jika memungkinkan.

### Jalankan Pengecekan Manual

```bash
docker exec steman_app php artisan system:guard
```

Output normal (semua aman):
```
✅ ALL SYSTEMS OPERATIONAL — No issues found.
Total: 21 checks | 0 issues found.
```

### Kirim Laporan Status ke Telegram

```bash
docker exec steman_app php artisan system:guard --report
```

### 21 Titik Pemeriksaan System Guard

| # | Kode | Pemeriksaan | Auto-Fix? |
|---|---|---|---|
| 1 | `db_down` | Koneksi MariaDB | ❌ (perlu manual) |
| 2 | `redis_down` | Koneksi Redis | ❌ (perlu manual) |
| 3 | `queue_overload` | Jumlah job > 1.000 | ✅ Restart worker |
| 4 | `meili_down` | Meilisearch health | ❌ (kirim notif) |
| 5 | `disk_low` | Ruang disk < 1 GB | ✅ Bersihkan log |
| 6 | `storage_broken` | Folder storage writable | ✅ Fix permission |
| 7 | `log_bloated` | Log > 50 MB | ✅ Truncate log |
| 8 | `session_domain` | SESSION_DOMAIN di .env | ✅ Auto-set |
| 9 | `captcha_patch` | Logika captcha di AuthController | ❌ (perlu manual) |
| 10 | `nginx_down` | Nginx health check | ❌ (perlu manual) |
| 11 | `audit_broken` | Integritas audit log | ✅ Clear cache audit |
| 12 | `route_mismatch` | Konsistensi route | ✅ Clear route cache |
| 13 | `route_shadowing` | Route tertutup wildcard | ❌ (perlu manual) |
| 14 | `smoke_test` | Halaman `/` dan `/login` | ✅ Clear cache + restart |
| 15 | `migration_mismatch` | Migrasi pending | ✅ Auto-migrate |
| 16 | `symlink_broken` | public/storage symlink | ✅ Recreate symlink |
| 17 | `ai_offline` | AI Service (Gemini) | ✅ Reset AI cache |
| 18 | `earth_data_mismatch` | Koordinat alumni hilang | ✅ Auto-geocoding |
| 19 | `news_api_down` | News API (jika dikonfigurasi) | ✅ Clear news cache |
| 20 | `scheduler_dead` | Laravel Scheduler heartbeat | ❌ (kirim notif) |
| 21 | `queue_worker_dead` | Failed jobs > 10 dalam 5 menit | ✅ Restart queue |

### Perilaku Notifikasi Telegram

| Simbol | Arti |
|---|---|
| ✅ Auto-Fixed | Masalah ditemukan dan berhasil diperbaiki otomatis |
| ⚠️ Butuh Perhatian | Masalah yang perlu tindak lanjut manual |
| 🚨 Kritis | Masalah serius, layanan terganggu |

> **Anti-flood:** Jika sebuah masalah tidak bisa diselesaikan sepenuhnya (misal: alamat alumni tidak valid untuk geocoding), sistem menekan notifikasi selama **6 jam** agar tidak spam ke Telegram.

### Reset Circuit Breaker Manual

Jika sebuah issue terjebak di kondisi `OPEN` dan ingin dicoba ulang segera:

```bash
docker exec steman_app php artisan tinker --execute "
  \$issue = 'earth_data_mismatch'; // ganti dengan nama issue
  \$prefix = 'system_guard_circuit_';
  foreach(['_state','_retries','_last','_opened_at','_total_failures'] as \$s) {
    cache()->forget(\$prefix . \$issue . \$s);
  }
  cache()->forget('system_guard:earth_suppressed');
  echo 'Circuit breaker direset untuk: ' . \$issue;
"
```

Atau untuk reset semua circuit breaker sekaligus:

```bash
docker exec steman_app php artisan cache:clear
```

---

## 4. Backup dan Restore Database

### Backup Manual Segera

```bash
# Backup database ke file terkompresi dengan timestamp
docker exec steman_db mysqldump -u app_user -pPASSWORD_DB steman_alumni 2>/dev/null \
  | gzip > /root/backup_steman_$(date +%Y%m%d_%H%M%S).sql.gz

echo "Backup selesai:"
ls -lh /root/backup_steman_*.sql.gz | tail -3
```

### Jalankan Script Backup Otomatis secara Manual

```bash
bash /var/www/steman-alumni/scripts/backup.sh
```

### Lihat Daftar File Backup

```bash
# Backup di direktori project
ls -lh /var/www/steman-alumni/backups/ 2>/dev/null

# Backup di root
ls -lh /root/backup_steman_*.sql.gz 2>/dev/null
```

### Restore Database dari Backup

> ⚠️ **PERINGATAN:** Restore akan **menghapus semua data saat ini**. Lakukan backup terbaru sebelum restore!

```bash
# Dekompresi dan restore
gunzip < /root/NAMA_FILE_BACKUP.sql.gz | \
  docker exec -i steman_db mysql -u app_user -pPASSWORD_DB steman_alumni

echo "Restore selesai"

# Bersihkan cache setelah restore
docker exec steman_app php artisan cache:clear
docker exec steman_app php artisan config:cache
```

---

## 5. Deploy Update Kode Terbaru

### Deploy via Script (Direkomendasikan)

```bash
cd /var/www/steman-alumni
bash deploy_remote.sh
```

### Deploy Manual Langkah Demi Langkah

```bash
cd /var/www/steman-alumni

# 1. Pull kode terbaru dari GitHub
git pull origin main

# 2. Jalankan migrasi jika ada perubahan database
docker exec steman_app php artisan migrate --force

# 3. Bersihkan PHP opcache di semua container
for c in steman_app steman_scheduler steman_queue steman_reverb; do
  docker exec $c php -r "opcache_reset();" 2>/dev/null && echo "$c: opcache cleared"
done

# 4. Rebuild cache production
docker exec steman_app php artisan optimize:clear
docker exec steman_app php artisan config:cache
docker exec steman_app php artisan route:cache
docker exec steman_app php artisan view:cache
docker exec steman_app php artisan event:cache

# 5. Perbaiki permission (jika diperlukan)
docker exec steman_app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker exec steman_app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "Deploy selesai!"
```

### Upload File Spesifik ke Server (Tanpa Full Deploy)

```bash
# Dari komputer lokal / Replit — upload file tunggal
scp -o StrictHostKeyChecking=no \
  app/Services/NamaService.php \
  root@103.175.219.57:/var/www/steman-alumni/app/Services/NamaService.php

# Reset opcache agar perubahan langsung aktif
ssh root@103.175.219.57 '
  for c in steman_app steman_scheduler steman_queue; do
    docker exec $c php -r "opcache_reset();" 2>/dev/null
  done
  echo "Opcache cleared"
'
```

> ⚠️ **Penting:** Setelah upload file PHP secara langsung, selalu reset PHP opcache. Tanpa ini, container masih menjalankan kode lama dari cache meskipun file sudah diperbarui.

### Backup ke GitHub (Sinkronisasi)

```bash
cd /var/www/steman-alumni

# Commit semua perubahan
git add -A
git -c user.email="deploy@steman-alumni.my.id" -c user.name="Deploy" \
  commit -m "sync: $(date '+%Y-%m-%d %H:%M') production backup"

# Push ke GitHub (gunakan token PAT jika belum ada SSH key GitHub)
git remote set-url origin "https://TOKEN_GITHUB@github.com/alumnisteman/steman-alumni.git"
git push origin main
git remote set-url origin "https://github.com/alumnisteman/steman-alumni.git"
```

---

## 6. Optimasi dan Bersihkan Cache

### Bersihkan Semua Cache (Jalankan Saat Ada Masalah Tampilan)

```bash
docker exec steman_app php artisan optimize:clear
```

Membersihkan: config cache, view cache, route cache, event cache, compiled files.

### Rebuild Cache Production (Jalankan Setelah Bersihkan)

```bash
docker exec steman_app php artisan config:cache
docker exec steman_app php artisan route:cache
docker exec steman_app php artisan view:cache
docker exec steman_app php artisan event:cache
```

### Reset PHP Opcache di Semua Container

```bash
for c in steman_app steman_scheduler steman_queue steman_reverb; do
  docker exec $c php -r "if(function_exists('opcache_reset')){opcache_reset(); echo '$c: cleared\n';}" 2>/dev/null
done
```

### Bersihkan Log yang Membengkak

```bash
# Cek ukuran log saat ini
docker exec steman_app du -sh /var/www/storage/logs/laravel.log

# Kosongkan log (file tidak dihapus, hanya dikosongkan isinya)
docker exec steman_app truncate -s 0 /var/www/storage/logs/laravel.log

echo "Log dikosongkan"
```

### Optimasi Database (Lakukan Bulanan)

```bash
docker exec steman_db mysql -u app_user -pPASSWORD_DB steman_alumni \
  -e "OPTIMIZE TABLE users, news, jobs, failed_jobs, audit_logs;"
```

### Bersihkan Image Docker yang Tidak Terpakai

```bash
docker image prune -f
docker volume prune -f
echo "Pembersihan Docker selesai"
```

### Jalankan Audit Integritas Sistem Manual

```bash
# Hanya cek (tidak perbaiki)
docker exec steman_app php artisan app:audit-integrity

# Cek + perbaiki otomatis
docker exec steman_app php artisan app:audit-integrity --fix
```

---

## 7. Reset Password Admin

### Via Perintah Langsung (Non-Interaktif)

```bash
docker exec steman_app php artisan tinker --execute \
  "\$u = \App\Models\User::where('email','email_admin@gmail.com')->first();
   \$u->password = Hash::make('PasswordBaru@2026');
   \$u->save();
   echo 'Password berhasil diubah untuk: ' . \$u->email;"
```

### Via Artisan Tinker Interaktif

```bash
docker exec -it steman_app php artisan tinker
```

Di dalam Tinker:

```php
$user = \App\Models\User::where('email', 'email_admin@gmail.com')->first();
$user->password = Hash::make('PasswordBaru@2026');
$user->save();
echo "Password diubah untuk: " . $user->email;
exit
```

### Set / Ubah Role User

```bash
# Set sebagai admin
docker exec steman_app php artisan tinker --execute \
  "\App\Models\User::where('email','email@gmail.com')->update(['role'=>'admin']);"

# Set sebagai alumni
docker exec steman_app php artisan tinker --execute \
  "\App\Models\User::where('email','email@gmail.com')->update(['role'=>'alumni']);"
```

---

## 8. Geocoding Peta Alumni (Steman Earth)

Fitur Steman Earth menampilkan peta sebaran alumni berdasarkan koordinat GPS yang diperoleh dari geocoding alamat.

### Cek Alumni Tanpa Koordinat

```bash
docker exec steman_app php artisan tinker --execute "
  \$total = App\Models\User::where('role','alumni')->count();
  \$tanpa = App\Models\User::where('role','alumni')
    ->whereNull('latitude')->count();
  echo \"Total alumni: \$total\n\";
  echo \"Tanpa koordinat: \$tanpa\n\";
  echo \"Sudah terpetakan: \" . (\$total - \$tanpa) . \"\n\";
"
```

### Jalankan Geocoding Massal

```bash
docker exec steman_app php artisan app:audit-integrity --fix
```

### Fix Manual untuk Alamat yang Gagal Geocoding

Jika geocoding otomatis gagal karena format alamat tidak dikenali:

```bash
docker exec steman_app php artisan tinker --execute "
  // Ganti id, lat, lng dengan nilai yang sesuai
  App\Models\User::where('id', ID_ALUMNI)->update([
    'latitude'  => -6.2088,  // contoh: Jakarta
    'longitude' => 106.8456,
    'city_name' => 'Jakarta Pusat',
  ]);
  echo 'Koordinat berhasil di-set';
"
```

### Reset Suppression Cache (Jika Notif Diblokir 6 Jam)

```bash
docker exec steman_app php artisan tinker --execute "
  cache()->forget('system_guard:earth_suppressed');
  echo 'Suppression dihapus — system guard akan cek ulang';
"
```

---

## 9. Troubleshooting Error Umum

### ❌ Error 502 Bad Gateway

**Penyebab:** Container `steman_app` crash atau tidak merespons.

```bash
# Restart container PHP
docker compose -f /var/www/steman-alumni/docker-compose.prod.yml restart app

# Cek log untuk detail
docker exec steman_app tail -50 /var/www/storage/logs/laravel.log
```

### ❌ Error 500 Server Error

**Penyebab:** Kode PHP error, view cache rusak, atau folder framework hilang.

```bash
# Pastikan folder framework ada dan writable
docker exec steman_app mkdir -p /var/www/storage/framework/{views,sessions,cache/data}
docker exec steman_app chmod -R 775 /var/www/storage/framework
docker exec steman_app chown -R www-data:www-data /var/www/storage/framework

# Bersihkan dan rebuild semua cache
docker exec steman_app php artisan optimize:clear
docker exec steman_app php artisan optimize
```

### ❌ Foto / Gambar Tidak Muncul (404)

**Penyebab:** Storage symlink rusak atau permission salah.

```bash
docker exec steman_app php artisan storage:link --force
docker exec steman_app chown -R www-data:www-data /var/www/storage/app/public
docker exec steman_app chmod -R 775 /var/www/storage/app/public
```

### ❌ 419 Page Expired (CSRF Error)

**Penyebab:** Session kadaluarsa atau SESSION_DOMAIN salah di `.env`.

```bash
docker exec steman_app php artisan cache:clear
# Pastikan di .env: SESSION_DOMAIN=.alumni-steman.my.id
```

### ❌ Login Diblokir (429 Too Many Requests)

**Penyebab:** Brute Force Protection aktif setelah terlalu banyak percobaan login gagal.

```bash
# Clear cache rate limiter
docker exec steman_app php artisan cache:clear
```

Atau tunggu otomatis terbuka dalam 2 menit.

### ❌ Scheduler Terdeteksi Mati

**Penyebab:** Heartbeat belum ditulis setelah container restart.

```bash
# Tulis ulang heartbeat secara manual
docker exec steman_app php artisan scheduler:heartbeat

# Verifikasi
docker exec steman_app php artisan system:guard
```

### ❌ Antrian (Queue) Macet

**Penyebab:** Container `steman_queue` crash atau terlalu banyak failed jobs.

```bash
# Cek status container queue
docker ps | grep steman_queue

# Restart queue worker
docker compose -f /var/www/steman-alumni/docker-compose.prod.yml restart queue

# Restart semua worker yang menggantung
docker exec steman_app php artisan queue:restart

# Lihat failed jobs
docker exec steman_app php artisan queue:failed
```

### ❌ WebSocket Tidak Berfungsi (Realtime)

**Penyebab:** Container `steman_reverb` tidak berjalan.

```bash
docker compose -f /var/www/steman-alumni/docker-compose.prod.yml restart reverb
docker compose -f /var/www/steman-alumni/docker-compose.prod.yml logs reverb
```

### ❌ SSL Expired / Tidak Bisa Akses HTTPS

**Penyebab:** Certbot gagal memperbarui sertifikat.

```bash
# Perbarui SSL secara manual
docker exec steman_certbot certbot renew --force-renewal

# Reload Nginx
docker exec steman_nginx nginx -s reload
```

### ❌ Search Tidak Berfungsi

**Penyebab:** Meilisearch belum sinkron atau container restart.

```bash
docker exec steman_app php artisan scout:import "App\Models\User"
docker exec steman_app php artisan scout:import "App\Models\News"
```

### ❌ Perubahan Kode Tidak Efektif Setelah Upload

**Penyebab:** PHP opcache di container masih menyimpan bytecode lama.

```bash
for c in steman_app steman_scheduler steman_queue steman_reverb; do
  docker exec $c php -r "opcache_reset();" 2>/dev/null && echo "$c: cleared"
done
```

### ❌ Notifikasi Telegram Flood (Sama Berulang-Ulang)

**Penyebab:** Circuit breaker terus direset karena masalah tidak bisa diselesaikan penuh.

```bash
# Identifikasi sisa masalah
docker exec steman_app php artisan system:guard

# Reset circuit breaker untuk issue tertentu
docker exec steman_app php artisan tinker --execute "
  \$issue = 'earth_data_mismatch';
  \$prefix = 'system_guard_circuit_';
  foreach(['_state','_retries','_last','_opened_at','_total_failures'] as \$s) {
    cache()->forget(\$prefix . \$issue . \$s);
  }
  cache()->forget('system_guard:' . \$issue . '_suppressed');
  echo 'Circuit direset';
"
```

---

## 10. Penghapusan Data Permanen

Aplikasi menggunakan fitur *Soft Deletes* — data yang "dihapus" dari panel admin sebenarnya hanya disembunyikan (bisa dipulihkan). Untuk menghapus permanen:

> ⚠️ **Data yang sudah di-forceDelete TIDAK BISA dipulihkan. Lakukan backup terlebih dahulu!**

```bash
# Backup dulu
docker exec steman_db mysqldump -u app_user -pPASSWORD_DB steman_alumni 2>/dev/null \
  | gzip > /root/backup_sebelum_delete_$(date +%Y%m%d_%H%M%S).sql.gz

# Hapus permanen
docker exec steman_app php artisan tinker --execute "
  // Hapus berita yang sudah di-trash
  \App\Models\News::onlyTrashed()->forceDelete();

  // Hapus alumni yang sudah di-trash
  \App\Models\User::onlyTrashed()->where('role','alumni')->forceDelete();

  echo 'Penghapusan permanen selesai';
"
```

---

## Referensi Cepat

| Kebutuhan | Perintah |
|---|---|
| Cek semua container | `docker compose -f docker-compose.prod.yml ps` |
| Health check System Guard | `docker exec steman_app php artisan system:guard` |
| Kirim laporan ke Telegram | `docker exec steman_app php artisan system:guard --report` |
| Bersihkan semua cache | `docker exec steman_app php artisan optimize:clear` |
| Rebuild cache production | `docker exec steman_app php artisan optimize` |
| Reset opcache semua container | `for c in steman_app steman_scheduler steman_queue; do docker exec $c php -r "opcache_reset();"; done` |
| Backup database sekarang | `bash /var/www/steman-alumni/scripts/backup.sh` |
| Audit integritas + fix | `docker exec steman_app php artisan app:audit-integrity --fix` |
| Lihat log error live | `docker exec steman_app tail -f /var/www/storage/logs/laravel.log` |

---

*© 2026 Portal Alumni SMKN 2 Ternate (STEMAN) — Diperbarui 1 Juli 2026*
