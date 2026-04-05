# 🚀 Panduan Upload & CI/CD - Alumni STEMAN

Dokumen ini berisi langkah-langkah untuk Bapak mengupload kode ke GitHub dan mengaktifkan fitur **Auto-Deploy**.

---

## 🛠️ Bagian 1: Upload Kode ke GitHub (Pertama Kali)

Bapak silakan buka **Git Bash** atau **Terminal** di folder ini, lalu jalankan perintah berikut secara berurutan:

1. **Konfigurasi Identitas** (Hanya sekali seumur hidup):
   ```bash
   git config --global user.name "Alumni STEMAN Admin"
   git config --global user.email "admin@alumnisteman.com"
   ```

2. **Ganti Nama Folder & Sync**:
   ```bash
   # Inisialisasi Git kembali (jika tadi belum masuk path)
   git init
   git remote add origin https://github.com/alumnisteman/steman-alumni.git
   ```

3. **Kirim Kode**:
   ```bash
   git add .
   git commit -m "feat: Integrasi GitHub, Scaling (3 Replicas), API v1, & Image Compression"
   git branch -M main
   git push -u origin main
   ```

---

## 🤖 Bagian 2: Aktifkan Auto-Deploy (GitHub Runner)

Agar server Bapak di `192.168.1.5` bisa mendeteksi perubahan dari GitHub, Bapak perlu mendaftarkan server tersebut sebagai **GitHub Runner**.

1. Buka Browser, masuk ke GitHub repository Bapak.
2. Klik **Settings** -> **Actions** -> **Runners**.
3. Klik tombol **New self-hosted runner**.
4. Pilih **OS: Linux** (Karena server Bapak pakai Ubuntu).
5. Bapak cukup **Copy-Paste** perintah yang muncul di kotak hitam ke Terminal SSH server Bapak.
6. Saat diminta nama runner, isi saja: `steman-server`.
7. Terakhir jalankan: `./run.sh`.

---

## 📈 Apa yang Terjadi Setelah Ini?

Setiap kali Bapak melakukan `git push`, maka server Bapak akan:
1. Menarik kode terbaru secara otomatis.
2. Menjalankan **3 replika aplikasi** (Scaling).
3. Mengaktifkan **OPcache** (Optimization).
4. Menjalankan migrasi API & mengkompresi aset (WebP) secara mandiri.

> [!TIP]
> **Scaling**: Jika Bapak ingin menambah lebih dari 3 replika (misal 5 atau 10), Bapak cukup ganti angka `replicas: 3` di file `docker-compose.prod.yml` lalu push lagi!

---

### Sertifikat & Keamanan
Pastikan Bapak sudah mengisi file `.env` di server. File `.env` **TIDAK AKAN** ikut terupload ke GitHub demi keamanan database Bapak.
