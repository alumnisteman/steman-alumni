# Panduan Deployment ke VPS Production
## Portal Alumni STEMAN
**Terakhir diperbarui: Juni 2026**

---

## Daftar Isi
1. [Prasyarat Server VPS](#1-prasyarat-server-vps)
2. [Instalasi Awal di VPS (Satu Kali)](#2-instalasi-awal-di-vps-satu-kali)
3. [Setup GitHub Actions CI/CD (Opsional)](#3-setup-github-actions-cicd-opsional)
4. [Deployment Manual via SSH](#4-deployment-manual-via-ssh)
5. [Upload File Spesifik dari Lokal](#5-upload-file-spesifik-dari-lokal)
6. [Setup SSL dan Domain](#6-setup-ssl-dan-domain)
7. [Konfigurasi Crontab (Scheduler)](#7-konfigurasi-crontab-scheduler)
8. [Monitoring dan Verifikasi](#8-monitoring-dan-verifikasi)

---

## 1. Prasyarat Server VPS

### Spesifikasi Server yang Digunakan

| Komponen | Nilai |
|---|---|
| IP Server | `103.175.219.57` |
| OS | Ubuntu 22.04 LTS |
| RAM | 8 GB |
| Storage | 96 GB |
| Domain | `alumni-steman.my.id` |
| Admin Domain | `admin.alumni-steman.my.id` |

### Software yang Harus Sudah Terinstall di VPS

```bash
# Install Docker Engine
curl -fsSL https://get.docker.com | sh
usermod -aG docker root

# Install Docker Compose v2
apt install docker-compose-plugin -y

# Install Git
apt install git -y

# Verifikasi
docker --version
docker compose version
git --version
```

---

## 2. Instalasi Awal di VPS (Satu Kali)

### Langkah 1 — Login ke Server

```bash
ssh root@103.175.219.57
```

### Langkah 2 — Clone Repositori

```bash
mkdir -p /var/www
cd /var/www
git clone https://github.com/alumnisteman/steman-alumni.git
cd steman-alumni
```

### Langkah 3 — Buat File .env Production

```bash
cp .env.example .env
nano .env
```

Nilai wajib untuk production:

```env
APP_NAME="Portal Alumni STEMAN"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://alumni-steman.my.id

DB_HOST=db
DB_PORT=3306
DB_DATABASE=steman_alumni
DB_USERNAME=app_user
DB_PASSWORD=password_database_kuat

REDIS_HOST=redis
REDIS_PORT=6379

SESSION_DRIVER=redis
SESSION_DOMAIN=.alumni-steman.my.id
SESSION_SECURE_COOKIE=true

FILESYSTEM_DISK=public
QUEUE_CONNECTION=redis
CACHE_STORE=redis

# Notifikasi Telegram
TELEGRAM_BOT_TOKEN=
TELEGRAM_CHAT_ID=
```

### Langkah 4 — Jalankan Stack Production

```bash
cd /var/www/steman-alumni
docker compose -f docker-compose.prod.yml up -d --build
```

Tunggu semua container selesai build (5–15 menit pertama kali).

### Langkah 5 — Inisialisasi Aplikasi

```bash
# Migrasi database
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

### Langkah 6 — Set Admin Pertama

```bash
docker exec steman_app php artisan tinker --execute \
  "\App\Models\User::where('email','email_admin@gmail.com')->update(['role'=>'admin']);"
```

### Langkah 7 — Verifikasi Deployment

```bash
# Cek semua container berjalan
docker ps --format 'table {{.Names}}\t{{.Status}}'

# Jalankan System Guard
docker exec steman_app php artisan system:guard
```

Output yang diharapkan: `✅ ALL SYSTEMS OPERATIONAL`

---

## 3. Setup GitHub Actions CI/CD (Opsional)

Jika ingin setiap push ke `main` otomatis ter-deploy ke server:

### Langkah 1 — Tambahkan GitHub Secrets

Buka repositori GitHub → **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

| Nama Secret | Nilai |
|---|---|
| `SERVER_HOST` | `103.175.219.57` |
| `SERVER_USER` | `root` |
| `SERVER_PORT` | `22` |
| `SERVER_SSH_KEY` | Isi konten private key SSH (`~/.ssh/id_rsa`) |

### Langkah 2 — Buat File Workflow

Buat file `.github/workflows/deploy.yml`:

```yaml
name: Deploy ke VPS Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    name: Deploy via SSH
    runs-on: ubuntu-latest

    steps:
      - name: Jalankan Deployment di Server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SERVER_SSH_KEY }}
          port: ${{ secrets.SERVER_PORT }}
          script: |
            cd /var/www/steman-alumni || exit 1
            git pull origin main

            # Rebuild container
            docker compose -f docker-compose.prod.yml up -d --build

            # Jalankan migrasi dan optimasi
            docker exec steman_app php artisan migrate --force
            docker exec steman_app php artisan optimize:clear
            docker exec steman_app php artisan config:cache
            docker exec steman_app php artisan route:cache
            docker exec steman_app php artisan view:cache

            # Perbaiki permission
            docker exec steman_app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
            docker exec steman_app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

            echo "✅ Deploy selesai"
```

Commit dan push file ini — setiap push ke `main` akan otomatis men-deploy ke server.

---

## 4. Deployment Manual via SSH

### Deploy Cepat (Script Sudah Tersedia)

```bash
ssh root@103.175.219.57
cd /var/www/steman-alumni
bash scripts/deploy.sh
```

### Deploy Langkah Demi Langkah

```bash
ssh root@103.175.219.57
cd /var/www/steman-alumni

# 1. Pull kode terbaru
git pull origin main

# 2. Rebuild container (hanya jika ada perubahan Dockerfile atau composer.json)
docker compose -f docker-compose.prod.yml up -d --build

# 3. Jalankan migrasi (jika ada file migration baru)
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

# 7. Verifikasi
docker exec steman_app php artisan system:guard
```

---

## 5. Upload File Spesifik dari Lokal

Jika perlu mengedit satu file dan langsung upload ke server tanpa git:

### Dari Terminal (Linux/Mac)

```bash
# Upload file tunggal
scp app/Services/SomeService.php root@103.175.219.57:/var/www/steman-alumni/app/Services/SomeService.php

# Terapkan di server
ssh root@103.175.219.57 "docker exec steman_app php artisan config:cache"
```

### Dari Windows (PowerShell dengan SCP)

```powershell
# Upload file
scp .\app\Services\SomeService.php root@103.175.219.57:/var/www/steman-alumni/app/Services/SomeService.php
```

---

## 6. Setup SSL dan Domain

SSL dikelola secara otomatis oleh container `steman_certbot` menggunakan Let's Encrypt.

### Perbarui SSL secara Manual

```bash
# Perbarui sertifikat
docker exec steman_certbot certbot renew --force-renewal

# Reload Nginx
docker exec steman_nginx nginx -s reload
```

### Cek Status SSL

```bash
# Cek tanggal kadaluarsa sertifikat
docker exec steman_certbot certbot certificates
```

### Konfigurasi Domain di .env

Pastikan `.env` di server sudah benar:
```env
APP_URL=https://alumni-steman.my.id
SESSION_DOMAIN=.alumni-steman.my.id
SESSION_SECURE_COOKIE=true
```

---

## 7. Konfigurasi Crontab (Scheduler)

Crontab server sudah dikonfigurasi. Untuk verifikasi:

```bash
crontab -l
```

Baris yang harus ada:

```cron
# Scheduler Laravel — wajib berjalan setiap menit
* * * * * docker exec steman_app php artisan schedule:run >> /var/log/steman-scheduler.log 2>&1

# Backup database harian pukul 02:00
0 2 * * * cd /var/www/steman-alumni && ./scripts/backup_database.sh >> ./storage/logs/backup.log 2>&1

# Optimasi sistem harian pukul 00:00
0 0 * * * /bin/bash /var/www/steman-alumni/scripts/system_optimize.sh >> /var/www/steman-alumni/storage/logs/optimize.log 2>&1

# Autoheal setiap jam
0 * * * * /bin/bash /var/www/steman-alumni/scripts/steman-autoheal.sh >> /var/www/steman-alumni/storage/logs/autoheal.log 2>&1

# Monitor kesehatan setiap 5 menit
*/5 * * * * /var/www/steman-alumni/scripts/health_check.sh >> /var/www/steman-alumni/storage/logs/health.log 2>&1
```

### Tambahkan Crontab (Jika Belum Ada)

```bash
crontab -e
```

Tambahkan baris yang diperlukan lalu simpan.

---

## 8. Monitoring dan Verifikasi

### Cek Status Semua Layanan

```bash
# Status container
docker ps --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}'

# System Guard — cek 21 titik kesehatan
docker exec steman_app php artisan system:guard

# Log Laravel terbaru
docker exec steman_app tail -30 /var/www/storage/logs/laravel.log

# Log scheduler
tail -20 /var/log/steman-scheduler.log
```

### Cek Penggunaan Sumber Daya

```bash
# RAM dan CPU per container
docker stats --no-stream --format 'table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}'

# Penggunaan disk
df -h /
```

### Kirim Laporan Status ke Telegram

```bash
docker exec steman_app php artisan system:guard --report
```

---

*© 2026 Portal Alumni SMKN 2 Ternate (STEMAN) — Dokumen Teknis*
