# Panduan Instalasi — Portal Alumni STEMAN
**Terakhir diperbarui: 1 Juli 2026 | Arsitektur: Laravel 12 + Docker Production**
**Server: Ubuntu 24.04 LTS | PHP 8.4 | MariaDB 10.6 | Redis Alpine**

---

## Daftar Isi
1. [Persyaratan Sistem](#1-persyaratan-sistem)
2. [Instalasi Lokal untuk Development](#2-instalasi-lokal-untuk-development)
3. [Instalasi Production di VPS](#3-instalasi-production-di-vps)
4. [Konfigurasi Pasca Instalasi](#4-konfigurasi-pasca-instalasi)
5. [Konfigurasi Notifikasi Telegram](#5-konfigurasi-notifikasi-telegram)
6. [Monitoring Stack (Grafana + Prometheus)](#6-monitoring-stack-grafana--prometheus)
7. [Troubleshooting Instalasi](#7-troubleshooting-instalasi)

---

## 1. Persyaratan Sistem

| Komponen | Versi / Spesifikasi |
|---|---|
| Sistem Operasi | Ubuntu 24.04 LTS (production aktif) |
| Docker Engine | 24.x ke atas |
| Docker Compose | v2.x ke atas |
| RAM Server | Minimal 4 GB (production pakai 7.8 GB) |
| Penyimpanan | Minimal 40 GB kosong (production 96 GB, terpakai 26 GB) |
| Domain | Domain aktif dengan SSL (Let's Encrypt via Certbot) |
| PHP | 8.4.x (baked ke Docker image) |
| Database | MariaDB 10.6 |

---

## 2. Instalasi Lokal untuk Development

### Langkah 1 — Clone Repositori

```bash
git clone https://github.com/alumnisteman/steman-alumni.git
cd steman-alumni
```

### Langkah 2 — Buat File Konfigurasi

```bash
cp .env.example .env
```

Edit file `.env`, sesuaikan bagian berikut:

```env
APP_NAME="Portal Alumni STEMAN"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_DATABASE=steman_alumni
DB_USERNAME=app_user
DB_PASSWORD=password_lokal_anda

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Langkah 3 — Jalankan dengan Docker

```bash
docker compose up -d --build
```

Tunggu hingga semua container berstatus `Up`. Cek dengan:

```bash
docker ps --format 'table {{.Names}}\t{{.Status}}'
```

### Langkah 4 — Inisialisasi Database (Pertama Kali)

```bash
# Jalankan migrasi database + seed data awal
docker exec steman_app php artisan migrate:fresh --seed --force

# Buat symlink storage (wajib agar foto/gambar tampil)
docker exec steman_app php artisan storage:link

# Set permission folder storage
docker exec steman_app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker exec steman_app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Build cache Laravel
docker exec steman_app php artisan optimize
```

### Langkah 5 — Akses Aplikasi

| URL | Keterangan |
|---|---|
| `http://localhost:8000` | Halaman utama portal |
| `http://localhost:8000/admin/dashboard` | Panel admin |
| `http://localhost:8000/login` | Halaman login |
| `http://localhost:8000/steman-earth` | Peta sebaran alumni |

**Akun Admin Default:**
- Email: `admin@steman.ac.id`
- Password: `Admin@1234`

> ⚠️ **Ganti password default segera setelah login pertama!**

---

## 3. Instalasi Production di VPS

### Prasyarat Server

- VPS dengan Ubuntu 24.04 LTS
- Docker Engine & Docker Compose v2 sudah terinstall
- Domain sudah diarahkan ke IP VPS (A record)
- Port 80 dan 443 terbuka di firewall

### Langkah 1 — Login ke Server via SSH

```bash
ssh root@103.175.219.57
```

### Langkah 2 — Install Docker (jika belum ada)

```bash
curl -fsSL https://get.docker.com | bash
systemctl enable docker && systemctl start docker
```

### Langkah 3 — Clone Repositori ke Server

```bash
mkdir -p /var/www
cd /var/www
git clone https://github.com/alumnisteman/steman-alumni.git
cd steman-alumni
```

### Langkah 4 — Buat File .env Production

```bash
cp .env.example .env
nano .env
```

Wajib diisi dengan nilai production:

```env
APP_NAME="Portal Alumni STEMAN"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://alumni-steman.my.id

DB_CONNECTION=mysql
DB_HOST=db
DB_DATABASE=steman_alumni
DB_USERNAME=app_user
DB_PASSWORD=password_kuat_anda

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

SESSION_DOMAIN=.alumni-steman.my.id
SESSION_SECURE_COOKIE=true

JWT_SECRET=isi_dengan_random_string_panjang

# Notifikasi Telegram (sangat disarankan)
TELEGRAM_BOT_TOKEN=token_bot_anda
TELEGRAM_CHAT_ID=chat_id_anda

# AI Service (Gemini)
GEMINI_API_KEY=api_key_gemini_anda

# Email (SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email_anda@gmail.com
MAIL_PASSWORD=app_password_gmail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=email_anda@gmail.com
MAIL_FROM_NAME="Portal Alumni STEMAN"
```

### Langkah 5 — Jalankan Stack Production

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

Proses build pertama membutuhkan waktu 5–15 menit. Pantau dengan:

```bash
docker compose -f docker-compose.prod.yml logs -f app
```

### Langkah 6 — Inisialisasi Database

> ⚠️ Gunakan `migrate` (bukan `migrate:fresh`) jika data sudah ada — `migrate:fresh` **menghapus semua data!**

```bash
# Jalankan semua migrasi
docker exec steman_app php artisan migrate --force

# Buat symlink storage
docker exec steman_app php artisan storage:link --force

# Set permission
docker exec steman_app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker exec steman_app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Build cache production
docker exec steman_app php artisan config:cache
docker exec steman_app php artisan route:cache
docker exec steman_app php artisan view:cache
docker exec steman_app php artisan event:cache
```

### Langkah 7 — Set Akun Admin Pertama

```bash
docker exec steman_app php artisan tinker --execute \
  "\App\Models\User::where('email','email_anda@gmail.com')->update(['role'=>'admin']);"
```

### Langkah 8 — Setup Cron Jobs di Server

Buka crontab root:

```bash
crontab -e
```

Tambahkan baris-baris berikut (sesuai kondisi production saat ini):

```cron
# Laravel Scheduler — wajib, jalan setiap menit
* * * * * docker exec steman_app php artisan schedule:run >> /var/log/steman-scheduler.log 2>&1

# System Guard & Health Check — setiap 5 menit
*/5 * * * * /var/www/steman-alumni/scripts/health_check.sh >> /var/www/steman-alumni/storage/logs/health.log 2>&1

# Auto-Recovery — setiap 2 menit
*/2 * * * * /var/www/steman-alumni/scripts/auto-recovery.sh >> /var/www/steman-alumni/storage/logs/auto-recovery.log 2>&1

# Autoheal — setiap 5 menit
*/5 * * * * /var/www/steman-alumni/scripts/steman-autoheal.sh >> /var/www/steman-alumni/storage/logs/autoheal.log 2>&1

# Container Watchdog — setiap 5 menit
*/5 * * * * /bin/bash /var/www/steman-alumni/steman-scripts/container-watchdog.sh >> /var/log/steman-watchdog.log 2>&1

# Backup database — setiap hari pukul 02:00
0 2 * * * /var/www/steman-alumni/scripts/backup.sh >> /var/www/steman-alumni/storage/logs/backup.log 2>&1
0 2 * * * /usr/local/bin/steman-backup.sh

# Maintenance & optimasi — setiap hari pukul 03:00
0 3 * * * bash /var/www/steman-alumni/scripts/steman-maintenance.sh >> /var/log/steman-maintenance.log 2>&1

# System optimize — setiap hari pukul 00:00
0 0 * * * /bin/bash /var/www/steman-alumni/scripts/system_optimize.sh >> /var/www/steman-alumni/storage/logs/optimize.log 2>&1

# Monitor error log — setiap 6 jam
0 */6 * * * cd /var/www/steman-alumni && ./scripts/monitor_errors.sh >> ./logs/monitor.log 2>&1

# Cleanup storage lama — setiap Minggu pukul 04:00
0 4 * * 0 cd /var/www/steman-alumni && ./scripts/cleanup_storage.sh >> ./logs/cleanup.log 2>&1

# Perpanjangan SSL otomatis — setiap hari pukul 01:00
0 1 * * * /usr/local/bin/certbot-renew-hook.sh >> /var/log/certbot-hook.log 2>&1
```

### Langkah 9 — Verifikasi Container Berjalan

```bash
docker compose -f docker-compose.prod.yml ps --format 'table {{.Name}}\t{{.Status}}'
```

Semua container berikut harus berstatus `Up (healthy)`:

| Container | Image | Fungsi |
|---|---|---|
| `steman_app` | `steman-alumni-app:latest` | Aplikasi Laravel (PHP-FPM 8.4) |
| `steman_nginx` | `macbre/nginx-brotli` | Web Server + Brotli compression |
| `steman_db` | `mariadb:10.6` | Database MariaDB |
| `steman_queue` | `steman-alumni-app:latest` | Worker antrian background job |
| `steman_scheduler` | `steman-alumni-app:latest` | Laravel Scheduler |
| `steman_reverb` | `steman-alumni-app:latest` | WebSocket (Laravel Reverb) |
| `steman_redis` | `redis:alpine` | Cache, session, queue |
| `steman_meilisearch` | `getmeili/meilisearch:latest` | Mesin pencarian full-text |
| `steman_certbot` | `certbot/certbot:latest` | Pembaruan SSL otomatis |
| `steman_grafana` | `grafana/grafana:latest` | Dashboard monitoring |
| `steman_prometheus` | `prom/prometheus:latest` | Metrics collection |
| `steman_node_exporter` | `prom/node-exporter:latest` | Server metrics |

### Langkah 10 — Verifikasi System Guard

```bash
docker exec steman_app php artisan system:guard
```

Output yang diharapkan:
```
✅ ALL SYSTEMS OPERATIONAL — No issues found.
Total: 21 checks | 0 issues found.
```

### Langkah 11 — Sinkronisasi Data Pencarian

```bash
docker exec steman_app php artisan scout:import "App\Models\User"
docker exec steman_app php artisan scout:import "App\Models\News"
```

---

## 4. Konfigurasi Pasca Instalasi

### A. Pengaturan Tampilan Portal

Login sebagai Admin → **Admin Panel → Settings** untuk mengatur:
- Nama dan deskripsi organisasi
- Banner utama (hero section)
- Foto dan sambutan Ketua Umum
- Informasi kontak resmi
- Font website (pilihan dari Google Fonts)
- Tahun event / angkatan

### B. Fitur Auto-Approve Alumni

Admin Panel → **Settings → Alumni** untuk mengaktifkan fitur persetujuan otomatis registrasi alumni baru (opsional).

### C. Tambah Data Jurusan

Admin Panel → **Jurusan → Tambah Jurusan**

Contoh: TKJ, RPL, Multimedia, Teknik Mesin, dll.

### D. Konfigurasi Email Selamat Datang

Sistem otomatis mengirim email sambutan ke alumni yang baru mendaftar dan notifikasi ke admin. Pastikan konfigurasi SMTP di `.env` sudah benar dan test dengan:

```bash
docker exec steman_app php artisan tinker --execute \
  "Mail::to('test@example.com')->send(new App\Mail\WelcomeRegisterMail(App\Models\User::first()));"
```

### E. Geocoding Peta Alumni (Steman Earth)

Alumni dengan alamat terisi akan di-geocode otomatis saat pertama kali menyimpan profil. Untuk menjalankan geocoding massal:

```bash
docker exec steman_app php artisan app:audit-integrity --fix
```

Cek status koordinat:

```bash
docker exec steman_app php artisan tinker --execute \
  "echo App\Models\User::where('role','alumni')->whereNull('latitude')->count() . ' alumni tanpa koordinat';"
```

---

## 5. Konfigurasi Notifikasi Telegram

System Guard mengirim notifikasi otomatis ke Telegram saat terjadi masalah di server.

### Langkah 1 — Buat Bot Telegram

1. Buka Telegram, cari **@BotFather**
2. Kirim `/newbot`
3. Masukkan nama bot: contoh `Steman Alumni Monitor`
4. Masukkan username bot: contoh `steman_monitor_bot`
5. Salin **token** yang diberikan BotFather

### Langkah 2 — Dapatkan Chat ID

Buka browser, kirim pesan ke bot Anda terlebih dahulu, lalu akses:
```
https://api.telegram.org/botTOKEN_ANDA/getUpdates
```
Cari nilai `"id"` di dalam objek `"chat"`.

### Langkah 3 — Isi Token ke .env Server

```bash
nano /var/www/steman-alumni/.env
```

```env
TELEGRAM_BOT_TOKEN=1234567890:AAFxxxxxxxxxxxxxxxxxxxxxxx
TELEGRAM_CHAT_ID=123456789
```

### Langkah 4 — Terapkan dan Test

```bash
# Terapkan konfigurasi baru
docker exec steman_app php artisan config:cache

# Test kirim laporan ke Telegram
docker exec steman_app php artisan system:guard --report
```

### Perilaku Notifikasi System Guard

| Simbol | Arti |
|---|---|
| ✅ | Masalah berhasil diperbaiki otomatis |
| ⚠️ | Masalah ditemukan tapi bisa pulih sendiri |
| 🚨 | Masalah kritis, perlu perhatian manual |

> **Catatan anti-flood:** Jika sebuah masalah gagal diperbaiki terus-menerus, sistem otomatis menekan notifikasi selama 6 jam (misalnya `earth_data_mismatch` dengan alamat tidak valid) agar tidak spam ke Telegram.

---

## 6. Monitoring Stack (Grafana + Prometheus)

Production server dilengkapi monitoring dengan Grafana dan Prometheus.

### Akses Grafana

Secara default Grafana berjalan di port `3000` pada server. Akses via SSH tunnel:

```bash
ssh -L 3000:localhost:3000 root@103.175.219.57
```

Buka browser: `http://localhost:3000`

- Username default: `admin`
- Password: sesuai `GF_SECURITY_ADMIN_PASSWORD` di `docker-compose.prod.yml`

### Metrics yang Dikumpulkan

- CPU, RAM, disk, network via Node Exporter
- Jumlah request, error rate via Nginx
- Status container Docker

---

## 7. Troubleshooting Instalasi

| Error | Penyebab | Solusi |
|---|---|---|
| `502 Bad Gateway` | Container PHP-FPM tidak berjalan | `docker compose -f docker-compose.prod.yml restart app` |
| `419 Page Expired` | Session atau CSRF token kadaluarsa | Refresh halaman, hapus cookie |
| `500 Server Error` | Kesalahan kode atau konfigurasi | `docker exec steman_app tail -50 /var/www/storage/logs/laravel.log` |
| Foto tidak muncul | Storage symlink belum ada | `docker exec steman_app php artisan storage:link --force` |
| Login gagal berulang | Rate limit / brute force protection aktif | Tunggu 2 menit atau `docker exec steman_app php artisan cache:clear` |
| Scheduler tidak jalan | Cron belum dikonfigurasi | Cek `crontab -l`, pastikan ada baris `artisan schedule:run` |
| Search tidak berfungsi | Meilisearch belum sinkron | `docker exec steman_app php artisan scout:import "App\Models\User"` |
| Peta alumni kosong | Geocoding belum dijalankan | `docker exec steman_app php artisan app:audit-integrity --fix` |
| WebSocket tidak connect | Container reverb tidak jalan | `docker compose -f docker-compose.prod.yml restart reverb` |
| Email tidak terkirim | Konfigurasi SMTP salah | Cek `MAIL_*` di `.env`, test dengan tinker |

---

*© 2026 Portal Alumni SMKN 2 Ternate (STEMAN) — Diperbarui 1 Juli 2026*
