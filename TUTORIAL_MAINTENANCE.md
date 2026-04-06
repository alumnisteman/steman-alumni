# 🛠️ Panduan Pemeliharaan (Maintenance) – v6.0 Hardened

> Versi terakhir diperbarui: April 2026
> Status: Production Stable (API Ready)

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

### ❌ 500 Server Error (View / Path Not Found)
**Penyebab:** View cache rusak atau direktori framework di `storage/` terhapus (karena masuk .gitignore).

**Solusi:**
```bash
# 1. Pastikan folder framework ada
docker exec steman_app mkdir -p storage/framework/{views,sessions,cache}
docker exec steman_app chmod -R 775 storage/framework
docker exec steman_app chown -R www-data:www-data storage/framework

# 2. Bersihkan view cache
docker exec steman_app php artisan view:clear
```

### ❌ Entrypoint Crash (Read-only System)
**Penyebab:** Docker entrypoint mencoba melakukan `chmod` pada volume yang di-mount sebagai `:ro` (Read-only).

**Solusi:** Update `docker-entrypoint.sh` untuk menggunakan `2>/dev/null || true` pada perintah `chmod`. Ini sudah diperbaiki di v6.1.

### ❌ 404 Not Found
**Penyebab:** Route tidak ditemukan, view hilang, atau file tidak ada.

**Solusi:**
```bash
docker exec steman_app php artisan config:cache
docker exec steman_app php artisan route:cache
docker exec steman_app php artisan view:cache
docker exec steman_app php artisan event:cache
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

## 6️⃣ Emergency: Reset Database Permissions

Jika `app_user` kehilangan akses atau container DB restart dengan IP baru yang tidak dikenali:

1. Gunakan script recovery:
```bash
bash scripts/db/reset_db.sh
```
Script ini akan otomatis menghentikan aplikasi, menjalankan MariaDB dalam mode pemulihan (`--skip-grant-tables`), memperbaiki izin user `app_user` ke host `%` (any host), dan merestart sistem secara aman.

---

## 7️⃣ Pembaruan IP Server (Jaringan Berubah)
Setiap kali IP server berubah (DHCP/restart router), jalankan:
```powershell
# Windows
.\scripts\deploy\update-ip.ps1
```

---

## 8️⃣ Reset Password Admin
Jika lupa password admin, gunakan Artisan Tinker:
```bash
docker exec -it steman_app php artisan tinker
```
Lalu di Tinker:
```php
$user = \App\Models\User::where('role', 'admin')->first();
$user->password = Hash::make('PasswordBaru@123');
$user->save();
```

---

## 9️⃣ Pembersihan Data Sampah (Soft Deletes)
Aplikasi menggunakan *SoftDeletes*. Untuk menghapus permanen data yang sudah masuk "Trash":
```bash
docker exec -it steman_app php artisan tinker
```
```php
\App\Models\News::onlyTrashed()->forceDelete();
\App\Models\User::onlyTrashed()->forceDelete();
```

---

## 🔟 Cek Kesehatan Sistem (Full Audit)
Jalankan audit rutin:
```bash
# Cek semua container
docker compose ps

# Uji koneksi DB dari dalam App
docker exec steman_app php artisan db:show

# Cek log 50 baris terakhir
docker exec steman_app tail -n 50 storage/logs/laravel.log
```

---

> _"Satu tetes pemeliharaan mencegah seember perbaikan."_
> **Ikatan Alumni STEMAN — Arsitektur v6.1 (Hardened)**
