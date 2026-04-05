# 🛠️ Panduan Pemeliharaan – STEMAN Alumni Portal v5

> Versi terakhir diperbarui: April 2026

Ikuti panduan ini secara rutin agar portal tetap **stabil, cepat, dan aman**.

---

## 📋 Ringkasan Jadwal Maintenance

| Frekuensi | Tugas |
|---|---|
| Setiap hari | Backup otomatis database + file |
| Seminggu sekali | Bersihkan log, optimasi cache |
| Setiap bulan | Update dependency, cek keamanan & Hapus gambar usang |
| Setiap ada error | Baca log, identifikasi penyebab |

---

## 1️⃣ Melihat Status Aplikasi

### Cek semua container berjalan:
```bash
docker compose ps
```
Status semua service harus `Up (healthy)`. Jika ada yang `Exit` atau `Unhealthy`:
```bash
docker compose restart
```

### Cek log error Laravel secara live:
```bash
docker exec steman_app tail -f storage/logs/laravel.log
```

### Cek log Nginx:
```bash
docker compose logs -f nginx
```

### Cek log WebSocket (Reverb):
```bash
docker compose logs -f reverb
```

---

## 2️⃣ Deploy Update Terbaru (Dari GitHub)

Setiap kali ada update kode dari repositori GitHub, jalankan:

```powershell
# Windows PowerShell — One-Click Deploy
.\deploy.ps1
```

Script ini otomatis:
1. Pull kode terbaru dari GitHub
2. Build ulang image Docker
3. Jalankan migrasi database
4. Rebuild cache Laravel (config, view, route)
5. Restart semua container

---

## 3️⃣ Backup Manual

### Backup Database:
```bash
# Backup MySQL ke file .sql
docker exec steman_db mysqldump -u root -p steman_alumni > backup_$(date +%Y%m%d).sql
```

### Backup File Upload (Foto, Dokumen):
```bash
# Salin seluruh folder storage ke direktori backup
cp -r storage/app/public ./backups/storage_$(date +%Y%m%d)
```

### Restore Database dari Backup:
```bash
docker exec -i steman_db mysql -u root -p steman_alumni < backup_TANGGAL.sql
```

---

## 4️⃣ Optimasi & Bersihkan Cache

Jalankan perintah ini minimal seminggu sekali:

```bash
# Bersihkan aplikasi cache
docker exec steman_app php artisan cache:clear

# Rebuild config cache (percepat load)
docker exec steman_app php artisan config:cache

# Rebuild view cache
docker exec steman_app php artisan view:cache

# Hapus log yang membengkak (hati-hati: ini menghapus log lama)
docker exec steman_app truncate -s 0 storage/logs/laravel.log

# (Opsional) Bersihkan file cache WebP jika direktori storage/news membengkak
# Sistem otomatis memutus gambar WebP tapi rutin dicek
docker exec steman_app chmod -R 775 storage/app/public/news
```

---

## 5️⃣ Troubleshooting Error

### ❌ 502 Bad Gateway
**Penyebab:** PHP-FPM kehabisan memori atau timeout karena query besar.

**Solusi:**
```bash
# Restart PHP-FPM
docker compose restart app

# Cek log untuk detail error
docker exec steman_app tail -50 storage/logs/laravel.log
```

### ❌ 500 Server Error
**Penyebab:** Variabel PHP undefined, DB error, atau konfigurasi salah.

**Solusi:**
```bash
# Aktifkan debug sementara untuk melihat detail error
# Edit .env: APP_DEBUG=true
# Akses halaman yang error, lalu kembalikan APP_DEBUG=false

docker exec steman_app php artisan config:clear
```

### ❌ 404 Not Found
**Penyebab:** Route tidak ditemukan, view hilang, atau file tidak ada.

**Solusi:**
```bash
# Refresh routing cache
docker exec steman_app php artisan route:clear
docker exec steman_app php artisan route:cache
```
> Catatan: Jika ada route yang menggunakan closure (anonymous function), `route:cache` akan gagal. Ini normal — jalankan hanya `route:clear`.

### ❌ 419 Page Expired (CSRF)
**Penyebab:** Sesi PHP kadaluarsa atau token CSRF tidak cocok.

**Solusi:** Refresh halaman. Jika terus terjadi:
```bash
docker exec steman_app php artisan session:clear
docker exec steman_app php artisan cache:clear
```

### ❌ Gambar/Foto Tidak Muncul
**Penyebab:** Symlink storage belum dibuat atau volume Nginx tidak sinkron.

**Solusi:**
```bash
docker exec steman_app php artisan storage:link
docker exec steman_app chmod -R 775 storage
docker exec steman_app chown -R www-data:www-data storage
```

### ❌ Notifikasi Real-time Tidak Masuk
**Penyebab:** Container Reverb tidak berjalan.

**Solusi:**
```bash
docker compose up -d reverb
# Pastikan port 8080 tidak diblok firewall
```

---

## 6️⃣ Pembaruan IP Server (Jaringan Berubah)

Setiap kali IP server berubah (DHCP/restart router), jalankan:
```powershell
# Windows
.\update-ip.ps1
```

---

## 7️⃣ Reset Password Admin

Jika lupa password admin, gunakan Artisan Tinker:
```bash
docker exec -it steman_app php artisan tinker
```
Lalu di Tinker:
```php
$user = \App\Models\User::where('email', 'admin@steman.ac.id')->first();
$user->password = \Illuminate\Support\Facades\Hash::make('PasswordBaru@123');
$user->save();
exit;
```

---

## 8️⃣ Cek Kesehatan Sistem Lengkap (One-Time Check)

Jalankan perintah ini untuk memverifikasi semua sistem berjalan normal:
```bash
# 1. Status container
docker compose ps

# 2. Koneksi database
docker exec steman_app php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';"

# 3. Cek storage symlink
docker exec steman_app ls -la public/storage

# 4. Cek log error terbaru
docker exec steman_app tail -20 storage/logs/laravel.log

# 5. Cek versi PHP
docker exec steman_app php -v
```

---

> _Keamanan dan stabilitas data alumni adalah tanggung jawab bersama._
> **Ikatan Alumni SMKN 2 Ternate — Maintenance Guide v5.1**
