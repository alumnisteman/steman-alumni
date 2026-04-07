# 🚀 Panduan Instalasi – STEMAN Alumni Portal v4.1 [Hardened Edition]

> Versi terakhir diperbarui: April 2026
> Arsitektur: Laravel Modular + Consolidated Migrations + Docker CI/CD Ready

---

## 📋 Persyaratan Sistem

| Komponen | Versi Minimum |
|---|---|
| OS | Windows 10/11, Ubuntu 22.04+, macOS 13+ |
| Docker Desktop | 4.x ke atas |
| Git | 2.x |
| PowerShell | 5.1+ (Windows) / bash (Linux/Mac) |
| RAM Server | Minimal 2 GB |
| Disk Space | Minimal 5 GB |

---

## 🏗️ OPSI 1: Instalasi Cepat via Docker (Sangat Direkomendasikan)

Metode ini paling stabil dan terisolasi — cocok untuk server lokal maupun produksi.

### Langkah 1 — Clone & Masuk ke Folder Proyek
```bash
git clone https://github.com/USERNAME/steman-alumni.git
cd steman-alumni
```

### Langkah 2 — Buat File `.env`
```bash
cp .env.example .env
```
Edit file `.env`, sesuaikan nilai berikut:
```env
APP_NAME="Ikatan Alumni SMKN 2"
APP_URL=http://192.168.1.15:8000       # Ganti dengan IP server Anda
DB_PASSWORD=rahasia_kuat_di_sini

# Opsional: Social Login (Google/GitHub)
GOOGLE_CLIENT_ID=your-id
GOOGLE_CLIENT_SECRET=your-secret
GITHUB_CLIENT_ID=your-id
GITHUB_CLIENT_SECRET=your-secret
```

### Langkah 3 — Build & Jalankan
```bash
# Windows (PowerShell)
.\scripts\deploy\deploy.ps1

# Linux / Mac (Manual Docker)
docker compose -f docker-compose.prod.yml up -d --build
```

### Langkah 4 — Inisialisasi Pertama Kali (Wajib)
```bash
# Jalankan migrasi database (Menggunakan skema konsolidasi v4.1)
docker exec steman_app php artisan migrate:fresh --seed --force

# Buat storage symlink (Wajib agar upload foto tampil)
docker exec steman_app php artisan storage:link

# Sinkronisasi Izin File (PENTING: Pastikan folder storage dapat ditulis oleh Docker)
docker exec steman_app chmod -R 775 storage bootstrap/cache
docker exec steman_app chown -R www-data:www-data storage bootstrap/cache
```

### Langkah 5 — Akses Aplikasi
| URL | Keterangan |
|---|---|
| `http://IP_SERVER:8000` | Halaman Utama |
| `http://IP_SERVER:8000/admin/dashboard` | Panel Admin |
| `http://IP_SERVER:8000/login` | Login Alumni |

**Akun Admin Default:**
- Email: `admin@steman.ac.id` (Ganti dengan role admin Anda)
- Password: `Admin@1234` (Ganti segera setelah login!)

---

## 🌐 OPSI 2: Update IP Server (Jaringan Berubah)

Jika IP lokal server Anda berubah, jalankan:
```powershell
# Windows PowerShell
.\scripts\deploy\update-ip.ps1
```
Script ini otomatis memperbarui `APP_URL` di `.env` sesuai IP aktif, lalu rebuild container.

---

## ⚙️ Konfigurasi Pasca-Instalasi

### A. Verifikasi Semua Service Berjalan
```bash
docker compose ps
```
Semua container harus `Up (healthy)`:
- `steman_app` — PHP-FPM
- `steman_nginx` — Web Server
- `steman_db` — MySQL
- `steman_reverb` — WebSocket (Notifikasi)

### B. Konfigurasi Settings Portal
Login sebagai Admin → buka **Admin → Settings** untuk mengatur:
- Nama & deskripsi organisasi
- Informasi kontak & alamat
- Sambutan Ketua Umum & Ketua Panitia (foto, nama, pesan)
- Banner utama (hero section)

### C. Tambah Data Jurusan
Admin → **Jurusan** → Tambah jurusan yang tersedia (TKJ, RPL, Multimedia, dll.)

### D. Penggunaan API v1 (Opsional)
Aplikasi ini sekarang memiliki API Layer di endpoint `/api/v1/auth/login` yang diotentikasi menggunakan **Laravel Sanctum**. Jika berencana menghubungkan Mobile App, token akan secara otomatis digenerate ketika sukses login.
Tidak memerlukan perintah khusus karena instalasi Sanctum sudah terintegrasi.

### E. Aktifkan Notifikasi Real-time
Pastikan `steman_reverb` berjalan. Jika tidak:
```bash
docker compose up -d reverb
```

---

## 🔒 Keamanan Produksi

- Pastikan `APP_DEBUG=false` di `.env` saat produksi
- Ganti `APP_KEY` jika diperlukan: `docker exec steman_app php artisan key:generate`
- Gunakan HTTPS (SSL) jika diakses via domain publik
- Backup rutin setiap hari (lihat `TUTORIAL_MAINTENANCE.md`)

### 🛡️ Proteksi Berkas Sensitif
Sistem ini menggunakan konfigurasi Nginx yang sangat ketat:
- Akses langsung ke file `.env` dan `.git` akan diblokir (**403 Forbidden**).
- Akses ke folder `/vendor` diblokir total secara struktural.
- File PHP didalam folder `/storage` tidak dapat dieksekusi oleh webserver.

---

## 🛠️ Troubleshooting Instalasi

| Error | Penyebab | Solusi |
|---|---|---|
| `502 Bad Gateway` | PHP-FPM OOM/timeout | Kurangi data yang di-fetch (sudah diperbaiki di v5.1) |
| `419 Page Expired` | Session/CSRF expired | Refresh halaman atau hapus cookie |
| `500 Server Error` | Variabel undefined di controller | Periksa `storage/logs/laravel.log` |
| Foto tidak muncul | Storage symlink belum dibuat | Jalankan `php artisan storage:link` |
| Login Social gagal | OAuth credentials salah | Periksa `.env` GOOGLE/GITHUB client ID |

---

> _"Menghubungkan masa lalu, membangun masa depan."_
> **Ikatan Alumni SMKN 2 Ternate — Arsitektur v4.1 [Hardened]**
