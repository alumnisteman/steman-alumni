# Panduan Otomasi Sistem — Portal Alumni STEMAN
**Terakhir diperbarui: Juni 2026**

---

## Daftar Isi
1. [Gambaran Umum Otomasi](#1-gambaran-umum-otomasi)
2. [System Guard — Pengawas Otomatis](#2-system-guard--pengawas-otomatis)
3. [Scheduler dan Heartbeat](#3-scheduler-dan-heartbeat)
4. [Script Maintenance](#4-script-maintenance)
5. [Script Backup Database](#5-script-backup-database)
6. [Script Monitor Error](#6-script-monitor-error)
7. [Script Autoheal](#7-script-autoheal)
8. [Jadwal Crontab Lengkap](#8-jadwal-crontab-lengkap)
9. [Notifikasi Telegram](#9-notifikasi-telegram)

---

## 1. Gambaran Umum Otomasi

Portal Alumni STEMAN dilengkapi sistem otomasi berlapis yang bekerja tanpa perlu intervensi manual:

| Sistem | Frekuensi | Fungsi |
|---|---|---|
| Laravel Scheduler | Setiap menit | Jalankan tugas terjadwal Laravel |
| Scheduler Heartbeat | Setiap menit | Tulis tanda hidup ke cache |
| System Guard | Setiap menit (via scheduler) | Cek 21 titik kesehatan |
| Health Check | Setiap 5 menit (cron) | Monitor website dari luar |
| Autoheal | Setiap jam (cron) | Perbaikan otomatis masalah umum |
| Backup Database | Setiap hari pukul 02:00 | Simpan salinan database |
| Optimasi Sistem | Setiap hari pukul 00:00 | Bersihkan cache, log, temp file |
| Monitor Error | Setiap 6 jam | Deteksi lonjakan error di log |
| SSL Renewal | Setiap hari pukul 03:00 | Perbarui sertifikat SSL |
| Cleanup Storage | Setiap Minggu pukul 04:00 | Hapus file sampah |

---

## 2. System Guard — Pengawas Otomatis

System Guard adalah komponen pengawas utama yang memeriksa **21 titik kesehatan** setiap menit dan mencoba memperbaiki masalah secara otomatis.

### Cara Kerja

```
Scheduler (setiap menit)
    └─> php artisan system:guard
            ├─> HealthChecker::run() — cek 21 kondisi
            ├─> Jika ada masalah → Fixer::handle() — coba perbaiki otomatis
            ├─> Circuit Breaker — cegah spam jika masalah berulang
            └─> Notifier::send() — kirim alert ke Telegram
```

### Jalankan Manual

```bash
# Cek biasa
docker exec steman_app php artisan system:guard

# Cek + kirim laporan ke Telegram (meski semua OK)
docker exec steman_app php artisan system:guard --report
```

### 21 Titik Pemeriksaan

| Kode Masalah | Yang Diperiksa | Auto-Fix? |
|---|---|---|
| `db_down` | Koneksi ke MySQL | Tidak (butuh manual) |
| `redis_down` | Koneksi ke Redis | Tidak (butuh manual) |
| `queue_overload` | Panjang antrian queue | Ya (restart worker) |
| `meili_down` | Meilisearch online | Tidak |
| `disk_low` | Ruang disk > 1 GB | Ya (bersihkan log) |
| `storage_broken` | Folder storage bisa ditulis | Ya (chmod) |
| `log_bloated` | Log < 50 MB | Ya (potong log) |
| `session_domain` | SESSION_DOMAIN di .env | Ya (auto-set) |
| `captcha_patch` | Patch captcha terpasang | Tidak |
| `nginx_down` | Nginx merespons | Tidak |
| `audit_broken` | Integritas audit log | Ya (rebuild) |
| `route_mismatch` | Route yang tidak terdefinisi | Tidak |
| `route_shadowing` | Route tertutup wildcard | Tidak |
| `smoke_test` | Halaman publik bisa diakses | Tidak |
| `migration_mismatch` | Ada migrasi pending | Tidak |
| `symlink_broken` | Storage symlink ada | Ya (buat ulang) |
| `ai_offline` | AI Service (Gemini/OpenRouter) | Tidak |
| `earth_data_mismatch` | Alumni tanpa koordinat | Tidak |
| `news_api_down` | News API (jika dikonfigurasi) | Tidak |
| `scheduler_dead` | Heartbeat < 10 menit lalu | Tidak |
| `queue_worker_dead` | Gagal job < 10 per 5 menit | Tidak |

### Circuit Breaker

Jika sebuah masalah gagal diperbaiki 5 kali berturut-turut, circuit breaker akan **OPEN** selama 10 menit (tidak mencoba lagi untuk menghindari spam). Reset manual:

```bash
docker exec steman_app php artisan tinker --execute \
  "cache()->forget('system_guard_circuit_NAMA_ISSUE_state'); echo 'Reset OK';"
```

---

## 3. Scheduler dan Heartbeat

### Cara Kerja Scheduler

Laravel Scheduler dijalankan oleh cron setiap menit:

```cron
* * * * * docker exec steman_app php artisan schedule:run >> /var/log/steman-scheduler.log 2>&1
```

### Tugas Terjadwal (Kernel.php)

| Perintah | Jadwal | Fungsi |
|---|---|---|
| `scheduler:heartbeat` | Setiap menit | Tulis timestamp ke cache |
| `steman:backup` | Setiap hari pukul 02:15 | Backup database via Artisan |
| `logs:clean` | Setiap hari pukul 02:30 | Hapus log lama |

### Cek Status Scheduler

```bash
# Lihat log scheduler
tail -30 /var/log/steman-scheduler.log

# Cek heartbeat masih segar (< 10 menit)
docker exec steman_app php artisan tinker --execute \
  "echo 'Heartbeat terakhir: ' . (time() - cache()->get('system_guard:scheduler_heartbeat', 0)) . ' detik lalu';"
```

### Tulis Heartbeat Manual (Jika Scheduler Baru Restart)

```bash
docker exec steman_app php artisan scheduler:heartbeat
```

---

## 4. Script Maintenance

**Lokasi:** `/var/www/steman-alumni/scripts/maintenance.sh`

**Fungsi:** Optimasi harian sistem dan aplikasi.

**Yang dilakukan:**
- Bersihkan cache Laravel (`optimize:clear`)
- Rebuild cache production (`config:cache`, `route:cache`, `view:cache`)
- Hapus log yang membengkak
- Optimasi Docker (hapus image tak terpakai)
- Analisis tabel database

**Jalankan Manual:**
```bash
bash /var/www/steman-alumni/scripts/maintenance.sh
```

**Cek Log:**
```bash
tail -30 /var/www/steman-alumni/storage/logs/optimize.log
```

---

## 5. Script Backup Database

**Lokasi:** `/var/www/steman-alumni/scripts/backup_database.sh`

**Fungsi:** Backup database harian dengan retensi otomatis.

**Yang dilakukan:**
- Dump database MySQL ke file `.sql.gz`
- Simpan di folder backup dengan nama bertanggal
- Hapus backup lebih dari 7 hari secara otomatis
- Kirim notifikasi Telegram (jika dikonfigurasi)

**Jalankan Manual:**
```bash
bash /var/www/steman-alumni/scripts/backup_database.sh
```

**Lokasi File Backup:**
```
/var/www/steman-alumni/backups/database/
/root/backup_steman_*.sql.gz
```

**Cek Log:**
```bash
tail -30 /var/www/steman-alumni/storage/logs/backup.log 2>/dev/null || \
tail -30 /var/www/steman-alumni/backups/backup.log 2>/dev/null
```

---

## 6. Script Monitor Error

**Lokasi:** `/var/www/steman-alumni/scripts/monitor_errors.sh`

**Fungsi:** Deteksi lonjakan error di log Laravel.

**Yang dilakukan:**
- Hitung jumlah baris ERROR dalam 6 jam terakhir
- Kirim notifikasi Telegram jika error > 10
- Tampilkan 5 error terakhir di notifikasi

**Jalankan Manual:**
```bash
bash /var/www/steman-alumni/scripts/monitor_errors.sh
```

---

## 7. Script Autoheal

**Lokasi:** `/var/www/steman-alumni/scripts/steman-autoheal.sh`

**Fungsi:** Perbaikan otomatis masalah umum setiap jam.

**Yang dilakukan:**
- Restart container yang crash atau unhealthy
- Perbaiki permission storage
- Bersihkan sesi Redis yang kedaluwarsa
- Log semua tindakan ke file autoheal

**Jalankan Manual:**
```bash
bash /var/www/steman-alumni/scripts/steman-autoheal.sh
```

**Cek Log:**
```bash
tail -30 /var/www/steman-alumni/storage/logs/autoheal.log
```

---

## 8. Jadwal Crontab Lengkap

Untuk melihat semua jadwal aktif di server:

```bash
crontab -l
```

Jadwal yang terpasang di server:

```cron
# === SISTEM STEMAN ALUMNI ===

# Scheduler Laravel — WAJIB setiap menit
* * * * * docker exec steman_app php artisan schedule:run >> /var/log/steman-scheduler.log 2>&1

# Backup database — setiap hari pukul 02:00
0 2 * * * cd /var/www/steman-alumni && ./scripts/backup_database.sh >> ./storage/logs/backup.log 2>&1
0 2 * * * /usr/local/bin/steman-backup.sh

# Bersihkan cache Redis — setiap hari pukul 03:00
0 3 * * * docker exec -i steman_app php artisan cache:clear >> /var/log/steman-cache.log 2>&1

# Autoheal — setiap jam
0 * * * * /bin/bash /var/www/steman-alumni/scripts/steman-autoheal.sh >> /var/www/steman-alumni/storage/logs/autoheal.log 2>&1

# Optimasi sistem — setiap hari tengah malam
0 0 * * * /bin/bash /var/www/steman-alumni/scripts/system_optimize.sh >> /var/www/steman-alumni/storage/logs/optimize.log 2>&1

# Monitor error — setiap 6 jam
0 */6 * * * cd /var/www/steman-alumni && ./scripts/monitor_errors.sh >> ./storage/logs/monitor.log 2>&1

# Health check — setiap 5 menit
*/5 * * * * /var/www/steman-alumni/scripts/health_check.sh >> /var/www/steman-alumni/storage/logs/health.log 2>&1

# Monitor website dari luar — setiap menit
* * * * * /var/www/monitor_site.sh

# SSL renewal — setiap hari pukul 03:00
0 3 * * * /usr/local/bin/certbot-renew-hook.sh >> /var/log/certbot-hook.log 2>&1

# Cleanup storage — setiap Minggu pukul 04:00
0 4 * * 0 cd /var/www/steman-alumni && ./scripts/cleanup_storage.sh >> ./storage/logs/cleanup.log 2>&1

# Maintenance — setiap hari pukul 03:00 (sudah mencakup bersihkan log)
0 3 * * * cd /var/www/steman-alumni && ./scripts/maintenance.sh >> ./storage/logs/maintenance.log 2>&1
```

---

## 9. Notifikasi Telegram

Sistem mengirim notifikasi ke Telegram dalam kondisi berikut:

| Kondisi | Jenis Notifikasi |
|---|---|
| Ada masalah yang berhasil diperbaiki otomatis | ⚠️ Warning |
| Ada masalah yang butuh perhatian manual | 🚨 Critical |
| `system:guard --report` dijalankan | ✅ Success (jika semua OK) |
| Error monitor melewati threshold | 🚨 Critical |
| Backup database berhasil | ℹ️ Info |
| Backup database gagal | 🚨 Critical |

### Setup Token Telegram

```bash
# Edit .env di server
nano /var/www/steman-alumni/.env
```

```env
TELEGRAM_BOT_TOKEN=1234567890:AAFxxxxxxxxxxxxxxxxxxxxxxx
TELEGRAM_CHAT_ID=123456789
```

```bash
# Terapkan
docker exec steman_app php artisan config:cache
```

Lihat [Panduan Instalasi — Bagian 5](TUTORIAL_INSTALASI.md#5-konfigurasi-notifikasi-telegram) untuk cara mendapatkan token dan Chat ID.

### Test Notifikasi

```bash
docker exec steman_app php artisan system:guard --report
```

---

*© 2026 Portal Alumni SMKN 2 Ternate (STEMAN) — Dokumen Teknis*
