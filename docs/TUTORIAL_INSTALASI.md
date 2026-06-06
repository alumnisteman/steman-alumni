# Panduan Instalasi — Portal Alumni STEMAN
**Terakhir diperbarui: Juni 2026 | Arsitektur: Laravel 12 + Docker Production**

---

## Daftar Isi
1. [Persyaratan Sistem](#1-persyaratan-sistem)
2. [Instalasi Lokal untuk Development](#2-instalasi-lokal-untuk-development)
3. [Instalasi Production di VPS](#3-instalasi-production-di-vps)
4. [Konfigurasi Pasca Instalasi](#4-konfigurasi-pasca-instalasi)
5. [Konfigurasi Notifikasi Telegram](#5-konfigurasi-notifikasi-telegram)
6. [Troubleshooting Instalasi](#6-troubleshooting-instalasi)

---

## 1. Persyaratan Sistem

| Komponen | Versi Minimum |
|---|---|
| Sistem Operasi | Ubuntu 22.04 LTS (direkomendasikan) |
| Docker Engine | 24.x ke atas |
| Docker Compose | v2.x ke atas |
| RAM Server | Minimal 4 GB (direkomendasikan 8 GB) |
| Penyimpanan | Minimal 20 GB kosong |
| Domain | Domain aktif dengan SSL untuk production |

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

DB_HOST=db
DB_DATABASE=steman_alumni
DB_USERNAME=app_user
DB_PASSWORD=password_lokal_anda
```

### Langkah 3 — Jalankan dengan Docker

```bash
docker compose up -d --build
```

Tunggu hingga semua container berstatus `Up`. Cek dengan:

```bash
docker ps
```

### Langkah 4 — Inisialisasi Database (Pertama Kali)

```bash
# Jalankan migrasi database
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
| `http://localhost:8000` | Halaman utama |
| `http://localhost:8000/admin/dashboard` | Panel admin |
| `http://localhost:8000/login` | Halaman login |

**Akun Admin Default:**
- Email: `admin@steman.ac.id`
- Password: `Admin@1234`

> ⚠️ **Ganti password default segera setelah login pertama!**

---

## 3. Instalasi Production di VPS

### Prasyarat Server

Pastikan VPS sudah terinstall Docker Engine dan Docker Compose, serta domain sudah diarahkan ke IP VPS.

### Langkah 1 — Login ke Server via SSH

```bash
ssh root@103.175.219.57
```

### Langkah 2 — Clone Repositori ke Server

```bash
cd /var/www
git clone https://github.com/alumnisteman/steman-alumni.git
cd steman-alumni
```

### Langkah 3 — Buat File .env Production

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

DB_HOST=db
DB_DATABASE=steman_alumni
DB_USERNAME=app_user
DB_PASSWORD=password_kuat_anda

REDIS_HOST=redis

SESSION_DOMAIN=.alumni-steman.my.id
SESSION_SECURE_COOKIE=true

# Notifikasi Telegram (opsional tapi disarankan)
TELEGRAM_BOT_TOKEN=
TELEGRAM_CHAT_ID=
```

### Langkah 4 — Jalankan Stack Production

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

### Langkah 5 — Inisialisasi Database

> ⚠️ Gunakan `migrate` (bukan `migrate:fresh`) jika data sudah ada — `migrate:fresh` menghapus semua data!

```bash
# Jalankan migrasi
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
```

### Langkah 6 — Set Akun Admin

```bash
docker exec steman_app php artisan tinker --execute \
  "\App\Models\User::where('email','email_anda@gmail.com')->update(['role'=>'admin']);"
```

### Langkah 7 — Verifikasi Container Berjalan

```bash
docker ps --format 'table {{.Names}}\t{{.Status}}'
```

Container yang harus berstatus `Up (healthy)`:

| Container | Fungsi |
|---|---|
| `steman_app` | Aplikasi Laravel (PHP-FPM) |
| `steman_nginx` | Web Server / Reverse Proxy |
| `steman_db` | Database MySQL/MariaDB |
| `steman_queue` | Worker antrian background job |
| `steman_redis` | Cache, session, queue |
| `steman_meilisearch` | Mesin pencarian |
| `steman_certbot` | Pembaruan SSL otomatis |

### Langkah 8 — Verifikasi System Guard

```bash
docker exec steman_app php artisan system:guard
```

Output yang diharapkan:
```
✅ ALL SYSTEMS OPERATIONAL — No issues found.
```

---

## 4. Konfigurasi Pasca Instalasi

### A. Pengaturan Tampilan Portal

Login sebagai Admin → **Admin Panel → Settings** untuk mengatur:
- Nama dan deskripsi organisasi
- Banner utama (hero section)
- Foto dan sambutan Ketua Umum
- Informasi kontak resmi

### B. Tambah Data Jurusan

Admin Panel → **Jurusan → Tambah Jurusan**

Contoh: TKJ, RPL, Multimedia, Teknik Mesin, dll.

### C. Tambah Berita / Pengumuman Pertama

Admin Panel → **Berita → Tulis Berita**

### D. Mengaktifkan Fitur Pencarian (Meilisearch)

Jika container `steman_meilisearch` sudah berjalan, sinkronisasi data:

```bash
docker exec steman_app php artisan scout:import "App\Models\User"
docker exec steman_app php artisan scout:import "App\Models\News"
```

---

## 5. Konfigurasi Notifikasi Telegram

System Guard mengirim notifikasi otomatis ke Telegram saat terjadi masalah di server (database mati, disk penuh, queue macet, dll.).

### Langkah 1 — Buat Bot Telegram

1. Buka aplikasi Telegram, cari kontak **@BotFather**
2. Kirim perintah: `/newbot`
3. Masukkan nama bot: contoh `Steman Alumni Monitor`
4. Masukkan username bot: contoh `steman_monitor_bot`
5. Salin **token** yang diberikan BotFather
   - Format: `1234567890:AAFxxxxxxxxxxxxxxxxxxxxxxx`

### Langkah 2 — Dapatkan Chat ID

**Cara 1 — Via API:**
1. Kirim pesan apa saja ke bot yang baru dibuat
2. Buka browser, akses URL berikut (ganti `TOKEN` dengan token bot):
   ```
   https://api.telegram.org/botTOKEN/getUpdates
   ```
3. Cari nilai `"id"` di dalam objek `"chat"` — itulah Chat ID Anda

**Cara 2 — Via @userinfobot:**
1. Cari `@userinfobot` di Telegram
2. Kirim pesan apa saja
3. Bot akan membalas dengan ID Anda

### Langkah 3 — Isi Token ke .env Server

```bash
# Edit .env di server
nano /var/www/steman-alumni/.env
```

Isi bagian Telegram:

```env
TELEGRAM_BOT_TOKEN=1234567890:AAFxxxxxxxxxxxxxxxxxxxxxxx
TELEGRAM_CHAT_ID=123456789
```

### Langkah 4 — Terapkan dan Test

```bash
# Terapkan konfigurasi baru
docker exec steman_app php artisan config:cache

# Test kirim notifikasi
docker exec steman_app php artisan system:guard --report
```

Jika berhasil, Anda akan menerima pesan di Telegram seperti:
```
✅ Semua Sistem Berjalan Normal
21/21 checks passed.
Tidak ada tindakan yang diperlukan.
```

---

## 6. Troubleshooting Instalasi

| Error | Penyebab | Solusi |
|---|---|---|
| `502 Bad Gateway` | Container PHP-FPM tidak berjalan | `docker compose -f docker-compose.prod.yml restart app` |
| `419 Page Expired` | Session atau CSRF token kadaluarsa | Refresh halaman, hapus cookie |
| `500 Server Error` | Kesalahan kode atau konfigurasi | Cek log: `docker exec steman_app tail -50 /var/www/storage/logs/laravel.log` |
| Foto tidak muncul | Storage symlink belum ada | `docker exec steman_app php artisan storage:link --force` |
| Login gagal berulang | Rate limit aktif | Tunggu 2 menit, atau `docker exec steman_app php artisan cache:clear` |
| Scheduler tidak jalan | Cron tidak aktif di server | Cek: `crontab -l` — harus ada baris `artisan schedule:run` |
| Search tidak berfungsi | Meilisearch belum sinkron | `docker exec steman_app php artisan scout:import "App\Models\User"` |

---

*© 2026 Portal Alumni SMKN 2 Ternate (STEMAN)*
