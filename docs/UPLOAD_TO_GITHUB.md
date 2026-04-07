# 🚀 Panduan Upload & CI/CD – v4.1 [Hardened Edition]

Dokumen ini berisi langkah-langkah untuk Bapak mengupload kode ke GitHub dan mengaktifkan fitur **Auto-Deploy**.

---

## 🛠️ Bagian 1: Upload Kode ke GitHub (Pertama Kali)

Bapak silakan buka **Git Bash** atau **Terminal** di folder ini, lalu jalankan perintah berikut secara berurutan:

1. **Inisialisasi Git**:
   ```bash
   git init
   git add .
   git commit -m "feat: Initial commit v4.1 Project Hardened & Refined"
   ```

2. **Hubungkan ke Repository Baru**:
   Silakan buat repository baru di GitHub (kosong, tanpa README/License), lalu hubungkan:
   ```bash
   git remote add origin https://github.com/USERNAME/REPO_NAME.git
   git branch -M main
   ```

3. **Push Kode**:
   ```bash
   git push -u origin main
   ```

---

## 🔄 Bagian 2: Cara Update Kode (Sync Rutin)
Setiap kali Bapak selesai melakukan perubahan di komputer lokal:
```bash
git add .
git commit -m "update: deskripsi perubahan Bapak"
git push
```
Server produksi akan mendeteksi perubahan ini secara otomatis jika **GitHub Runner** sudah aktif.

---

## 🤖 Bagian 3: Aktifkan Auto-Deploy (GitHub Runner)

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
> **Scaling**: Jika Bapak ingin menambah lebih dari 3 replika (misal 5 atau 10), Bapak cukup ganti angka `replicas: 3` di file `docker-compose.prod.yml` lalu push lagi! Sistem akan otomatis melakukan *rolling update* tanpa *downtime*.

---

### 🛡️ Catatan Keamanan Penting
1. **GitHub Secrets**: Pastikan Bapak sudah mengatur IP Server, SSH User, dan SSH Key di menu *Settings > Secrets* repositori GitHub.
2. **Nginx Hardening**: Dokumentasi `.env`, `.git`, dan folder `vendor` kini secara otomatis dilindungi oleh Nginx di server produksi. Percobaan akses langsung akan menghasilkan error 403.


### Sertifikat & Keamanan
Pastikan Bapak sudah mengisi file `.env` di server. File `.env` **TIDAK AKAN** ikut terupload ke GitHub demi keamanan database Bapak.
