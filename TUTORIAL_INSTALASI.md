# 🚀 Panduan Instalasi STEMAN Alumni Portal v5

Selamat datang di era baru konektivitas alumni! STEMAN Alumni Portal v5 dirancang dengan arsitektur modern berbasis **Laravel 12**, **Docker**, dan **AI-Driven Matchmaking**.

---

## 💎 Fitur Unggulan Versi 5.0
- **Geospatial Heatmap**: Visualisasi persebaran alumni di seluruh dunia.
- **3D Digital ID Card**: Kartu alumni interaktif dengan teknologi holografik.
- **AI Career Insight**: Rekomendasi karir cerdas berdasarkan data profil.
- **Real-time Notifications**: Didukung oleh Laravel Reverb untuk interaksi instan.
- **Premium Dark Mode**: Antarmuka modern yang nyaman di mata.

---

## 🏗️ Opsi 1: Instalasi via Docker (Sangat Direkomendasikan)
Metode ini adalah yang paling stabil, cepat, dan terisolasi. Cocok untuk server lokal maupun VPS Production.

### Langkah-langkah:
1. **Masuk ke folder proyek:**
   ```bash
   cd steman-alumni
   ```
2. **Jalankan script installer otomatis:**
   ```bash
   bash install_docker.sh
   ```
3. **Tunggu hingga selesai.** Script akan otomatis:
   - Mencari port yang tersedia (HTTP/HTTPS).
   - Membangun image Docker (Multi-stage Build).
   - Menjalankan database, queue worker, dan websocket server.
   - Menjalankan migrasi dan seeding data awal.

### Akses Aplikasi:
- **URL**: `http://localhost:[PORT_ANDA]`
- **Login Admin**: `admin@steman.ac.id` / `Admin@1234`

---

## 🌐 Opsi 2: Instalasi via VPS/Shared Hosting (Manual)
Gunakan jika Anda tidak ingin menggunakan Docker. Pastikan server memenuhi syarat: PHP 8.2+, MySQL 8.0+, Node.js 20+.

### Langkah Cepat:
- **Ubuntu/Debian**: `bash install_ubuntu.sh`
- **CentOS/AlmaLinux**: `bash install_centos.sh`
- **cPanel**: `bash install_cpanel.sh`

---

## ⚙️ Konfigurasi Pasca-Instalasi (PENTING)

### 1. Konfigurasi Social Login
Buka file `.env` dan masukkan kredensial agar fitur Login Google/GitHub berfungsi:
```env
GOOGLE_CLIENT_ID=your-id
GOOGLE_CLIENT_SECRET=your-secret
GITHUB_CLIENT_ID=your-id
GITHUB_CLIENT_SECRET=your-secret
```

### 2. Aktifkan Notifikasi Real-time
Pastikan container `steman_reverb` berjalan jika menggunakan Docker. Jika manual, jalankan:
```bash
php artisan reverb:start
```

---

## 🔒 Keamanan & Produksi
- **Backup berkala**: Jalankan `bash scripts/backup.sh` setiap sore.
- **Optimasi**: Jalankan `bash scripts/maintenance.sh` untuk menjaga performa tetap 100%.

---

> _"Menghubungkan masa lalu, membangun masa depan."_
> **Ikatan Alumni SMKN 2 Ternate — Arsitektur v5.0**
