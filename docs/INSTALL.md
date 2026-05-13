# 📘 PANDUAN INSTALASI LENGKAP — STEMAN ALUMNI PORTAL
**Versi:** 4.2 | **Framework:** Laravel 11

---

## 📋 Daftar Isi
1. [Prasyarat Sistem](#1-prasyarat-sistem)
2. [Clone & Instalasi Lokal](#2-clone--instalasi-lokal)
3. [Konfigurasi Environment (.env)](#3-konfigurasi-environment-env)
4. [Setup Database](#4-setup-database)
5. [Deploy ke Production (Docker + VPS)](#5-deploy-ke-production-docker--vps)
6. [Akun Default & Role](#6-akun-default--role)
7. [Pemeliharaan & Update](#7-pemeliharaan--update)
8. [Troubleshooting](#8-troubleshooting)

---

## 1. Prasyarat Sistem

### Server Production
| Komponen | Minimum | Rekomendasi |
|---|---|---|
| OS | Ubuntu 20.04 | Ubuntu 22.04 LTS |
| CPU | 1 vCore | 2 vCore |
| RAM | 1 GB | 2 GB |
| Storage | 20 GB | 40 GB SSD |
| Docker | 24.x | 26.x |
| Docker Compose | 2.x | 2.24+ |

### Lokal (Development)
- PHP 8.2+
- Composer 2.x
- Node.js 18+ & NPM
- MySQL 8.0 / MariaDB 10.6+

---

## 2. Clone & Instalasi Lokal

```bash
# 1. Clone repository
git clone https://github.com/your-org/steman-alumni.git
cd steman-alumni

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies & build assets
npm install && npm run build

# 4. Salin file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate
```

---

## 3. Konfigurasi Environment (.env)

Edit file `.env` dan isi nilai-nilai berikut:

```dotenv
APP_NAME="Alumni SMKN 2 Ternate"
APP_ENV=production
APP_KEY=                    # Sudah diisi oleh key:generate
APP_DEBUG=false
APP_URL=https://domain-anda.com

# ─── DATABASE ────────────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=steman_alumni
DB_USERNAME=root
DB_PASSWORD=rahasia_anda

# ─── STORAGE ─────────────────────────────────────────
FILESYSTEM_DISK=public

# ─── EMAIL (Opsional) ────────────────────────────────
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email@gmail.com
MAIL_PASSWORD=app_password
MAIL_FROM_ADDRESS=email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 4. Setup Database

```bash
# Jalankan semua migrasi
php artisan migrate --force

# Isi data awal (akun admin, editor, logo, badge, program)
php artisan db:seed --force

# Buat symbolic link storage
php artisan storage:link
```

Setelah seeder selesai, akun berikut akan tersedia:

| Email | Password | Role |
|---|---|---|
| `admin@steman.ac.id` | `Admin@1234` | admin |
| `editor@steman.ac.id` | `Editor@1234` | editor |

---

## 5. Deploy ke Production (Docker + VPS)

### 5a. Persiapan Server

```bash
# SSH ke server
ssh root@IP_SERVER_ANDA

# Install Docker
curl -fsSL https://get.docker.com | sh

# Install Docker Compose
apt install docker-compose-plugin -y
```

### 5b. Upload Project ke Server

Dari komputer lokal (Windows — gunakan pscp):
```powershell
# Upload seluruh project
pscp -pw PASSWORD -r . root@IP_SERVER:/var/www/steman-alumni/
```

### 5c. Konfigurasi Docker

```bash
cd /var/www/steman-alumni
# Edit .env production
nano .env
# Jalankan container
docker compose up -d --build
```

### 5d. Inisialisasi Aplikasi di Container

```bash
# Masuk ke container app
docker exec -it steman-alumni-app-1 bash

# Inisialisasi
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan steman:check-integrity  # Verifikasi instalasi 100%
php artisan optimize
exit
```

> [!IMPORTANT]
> **PENTING:** Pastikan `APP_URL` di file `.env` sudah benar (termasuk https://). Jika salah, sistem audit **Smoke Test** akan gagal karena tidak bisa menghubungi server sendiri.

---

## 6. Akun Default & Role

| Role | Akses | Dibuat Otomatis |
|---|---|---|
| **admin** | Penuh (Akses Sistem & Konten) | ✅ via Seeder |
| **editor** | Penuh (Akses Sistem & Konten) | ✅ via Seeder |
| **alumni** | Portal publik & profil | Daftar mandiri |

Untuk menambah role baru, edit konstanta `ROLES` di `app/Models/User.php`.

---

## 7. Pemeliharaan & Update

```bash
# Update Kode
cd /var/www/steman-alumni
git pull origin main
docker exec steman-alumni-app-1 php artisan migrate --force
docker exec steman-alumni-app-1 php artisan optimize:clear
docker restart steman-alumni-app-1 steman_nginx
```

---

## 8. Troubleshooting

### ❌ Gambar tidak muncul
```bash
php artisan storage:link
```

### ❌ Perubahan CSS/JS tidak muncul
```bash
php artisan optimize:clear
```

### ❌ Container tidak mau start
```bash
docker compose down
docker compose up -d --build
docker compose logs app
```

---

## 📞 Dukungan

- **Portal Utama:** https://alumni-steman.my.id
- **Admin:** alumnisteman@gmail.com

---
*Dokumen ini diperbarui untuk sistem tanpa lisensi (Full Version).*
*Terakhir diperbarui: April 2026*
