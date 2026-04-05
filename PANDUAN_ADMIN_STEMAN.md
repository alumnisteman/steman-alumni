# 📘 BUKU PANDUAN ADMINISTRATOR
## Portal Ikatan Alumni SMKN 2 Ternate (STEMAN)
**Versi 6.0 (Modular API-Ready) | Dokumen Resmi**

---

## 📋 DAFTAR ISI

1. [Pendahuluan](#1-pendahuluan)
2. [Akses & Login Dashboard](#2-akses--login-dashboard)
3. [Tampilan Utama Dashboard](#3-tampilan-utama-dashboard)
4. [Manajemen Pengguna (User)](#4-manajemen-pengguna-user)
5. [Manajemen Jurusan](#5-manajemen-jurusan)
6. [Manajemen Berita](#6-manajemen-berita)
7. [Manajemen Lowongan Kerja](#7-manajemen-lowongan-kerja)
8. [Manajemen Program](#8-manajemen-program)
9. [Manajemen Galeri](#9-manajemen-galeri)
10. [Manajemen Pesan Masuk (Inbox)](#10-manajemen-pesan-masuk-inbox)
11. [Pengaturan Situs (Settings)](#11-pengaturan-situs-settings)
12. [Ekspor Data Alumni](#12-ekspor-data-alumni)
13. [Backup & Restore Database](#13-backup--restore-database)
14. [Keamanan & Pemeliharaan](#14-keamanan--pemeliharaan)
15. [FAQ & Pemecahan Masalah](#15-faq--pemecahan-masalah)

---

## 1. PENDAHULUAN

Portal Alumni STEMAN adalah sistem informasi berbasis web yang dirancang untuk mengelola data alumni SMKN 2 Ternate. Sistem ini dilengkapi dengan fitur berikut:

- ✅ Manajemen data alumni dan pengguna
- ✅ Publikasi berita dan pengumuman
- ✅ Manajemen lowongan kerja
- ✅ Manajemen program dan kegiatan
- ✅ Galeri foto
- ✅ Peta sebaran alumni global
- ✅ Pengaturan tampilan situs secara dinamis
- ✅ Kotak pesan dari alumni/publik
- ✅ Akses API via Laravel Sanctum untuk Integrasi Mobile App

### Peran Pengguna

| Peran | Akses |
|-------|-------|
| **Admin (Superadmin)** | Akses penuh ke seluruh fitur dashboard, termasuk User Management dan Settings |
| **Editor** | Dapat mengelola Berita, Galeri, Program, dan Lowongan – TIDAK bisa akses User & Settings |
| **Alumni** | Hanya akses halaman alumni (profil, direktori, dsb.) |

---

## 2. AKSES & LOGIN DASHBOARD

### 2.1 Membuka Halaman Login

1. Buka browser dan kunjungi: `https://[domain-atau-ip-server]/login`
2. Masukkan **Email** dan **Password** akun admin Anda.
3. Jawab pertanyaan keamanan Captcha (misalnya: `5 + 3 = ?`).
4. Klik tombol **"Login Sekarang"**.

> [!IMPORTANT]
> Gunakan akun dengan role `admin` untuk mendapatkan akses penuh ke Dashboard.

### 2.2 Akun Admin Default

```
Email    : admin@steman.ac.id
Password : Admin@1234
```

> [!CAUTION]
> **Ganti password default segera setelah login pertama kali!** Buka menu **Dashboard → Profil → Ubah Password**.

### 2.3 Menuju Dashboard Admin

Setelah login berhasil, klik **"Dashboard"** di menu navigasi bagian atas, atau akses langsung melalui:
`https://[domain]/admin/dashboard`

---

## 3. TAMPILAN UTAMA DASHBOARD

Halaman dashboard menampilkan ringkasan data sistem secara real-time.

### 3.1 Kartu Statistik

Di bagian atas dashboard terdapat 4 kartu statistik:

| Kartu | Keterangan |
|-------|------------|
| 🎓 **Total Alumni** | Jumlah alumni terdaftar dan sudah disetujui |
| 🌍 **Internasional** | Alumni yang bekerja/tinggal di luar Indonesia |
| 📚 **Jurusan** | Total jurusan yang aktif terdaftar |
| 💼 **Lowongan** | Total lowongan kerja yang sedang aktif |

### 3.2 Menu Manajemen

Di bawah statistik, tersedia tombol cepat menuju semua halaman admin:

- 👥 **User** – Kelola akun alumni dan admin
- 🎓 **Jurusan** – Tambah/ubah jurusan
- 💼 **Lowongan** – Kelola loker
- ⚙️ **Email & Situs** – Pengaturan konten dan branding
- 📰 **Berita** – Kelola artikel dan pengumuman
- 📅 **Program** – Kelola kegiatan alumni
- 🖼️ **Galeri** – Kelola foto dan video
- ✉️ **Inbox** – Pesan masuk dari publik

### 3.3 Peta Sebaran Alumni

Di bagian bawah dashboard terdapat **peta interaktif** yang menampilkan lokasi alumni di seluruh dunia.

---

## 4. MANAJEMEN PENGGUNA (USER)

> [!IMPORTANT]
> Fitur ini hanya bisa diakses oleh Admin (Superadmin), bukan Editor.

**Akses**: Dashboard → Menu **User** atau URL `/admin/users`

### 4.1 Melihat Daftar Pengguna

Halaman ini menampilkan seluruh pengguna yang terdaftar, lengkap dengan:
- Nama & Email
- Status (Pending / Approved / Rejected)
- Peran (Admin / Editor / Alumni)
- Tanggal Daftar

### 4.2 Menyetujui Pendaftar Baru

1. Temukan alumni yang berstatus **"Pending"**.
2. Klik tombol **"Setujui"** (ikon ✅) atau **"Tolak"** (ikon ❌).
3. Status akan langsung berubah.

### 4.3 Mengubah Peran Pengguna

1. Klik dropdown **"Role"** pada baris pengguna.
2. Pilih peran: `admin`, `editor`, atau `alumni`.
3. Perubahan tersimpan otomatis.

### 4.4 Menambah Admin/Editor Baru

1. Klik tombol **"+ Tambah Pengguna"**.
2. Isi formulir: Nama, Email, Password, dan Peran.
3. Klik **"Simpan"**.

### 4.5 Menghapus Pengguna

1. Klik ikon 🗑️ pada baris pengguna yang ingin dihapus.
2. Konfirmasi penghapusan dengan klik **"Ya, Hapus"**.

> [!WARNING]
> Penghapusan pengguna bersifat permanen dan tidak dapat dibatalkan.

---

## 5. MANAJEMEN JURUSAN

**Akses**: Dashboard → Menu **Jurusan** atau URL `/admin/majors`

### 5.1 Menambah Jurusan Baru

1. Isi kolom **"Nama Jurusan"** pada formulir di bagian atas.
2. Isi **"Kelompok/Bidang"** (misal: Teknologi Informasi, Teknik, dsb.).
3. Klik tombol **"Tambah"**.

### 5.2 Mengubah Jurusan

1. Klik ikon ✏️ (Edit) pada jurusan yang ingin diubah.
2. Ubah nama atau kelompok.
3. Klik **"Simpan Perubahan"**.

### 5.3 Menonaktifkan Jurusan

Ubah status jurusan menjadi `inactive` agar tidak muncul di formulir pendaftaran alumni.

---

## 6. MANAJEMEN BERITA

**Akses**: Dashboard → Menu **Berita** atau URL `/admin/news`

### 6.1 Membuat Artikel Baru

1. Klik tombol **"+ Tulis Berita"**.
2. Isi formulir:
   - **Judul**: Judul artikel
   - **Kategori**: Pilih kategori berita
   - **Konten**: Isi artikel (mendukung format teks panjang)
   - **Thumbnail**: Unggah gambar sampul (sistem otomatis melakukan kompresi 30% dan konversi ke WebP untuk performa maksimal)
   - **Status**: Pilih `Draft` (belum tayang) atau `Published` (langsung tayang)
3. Klik **"Simpan & Terbitkan"**.

### 6.2 Mengedit Artikel

1. Klik ikon ✏️ pada artikel yang ingin diedit.
2. Ubah konten yang diperlukan.
3. Klik **"Update"**.

### 6.3 Menghapus Artikel

1. Klik ikon 🗑️ pada artikel.
2. Konfirmasi penghapusan.

### 6.4 Mengubah Status Artikel

- **Draft**: Artikel tidak terlihat oleh publik.
- **Published**: Artikel langsung terlihat di halaman depan dan halaman berita.

---

## 7. MANAJEMEN LOWONGAN KERJA

**Akses**: Dashboard → Menu **Lowongan** atau URL `/admin/jobs`

### 7.1 Menambah Lowongan Baru

1. Klik **"+ Tambah Lowongan"**.
2. Isi formulir:
   - **Judul Posisi**: Nama pekerjaan yang ditawarkan
   - **Perusahaan**: Nama perusahaan
   - **Lokasi**: Kota/Provinsi/Negara
   - **Tipe**: `Full-time`, `Part-time`, `Magang`, atau `Remote`
   - **Deskripsi Pekerjaan**: Detail persyaratan dan job desk
   - **Deadline**: Batas waktu pendaftaran
   - **Status**: `active` (aktif) atau `closed` (ditutup)
3. Klik **"Simpan"**.

### 7.2 Menutup Lowongan

1. Klik ✏️ Edit pada lowongan.
2. Ubah Status menjadi `closed`.
3. Klik **"Update"**.

---

## 8. MANAJEMEN PROGRAM

**Akses**: Dashboard → Menu **Program** atau URL `/admin/programs`

Program adalah kegiatan resmi organisasi alumni (seperti reuni, webinar, beasiswa, dll.).

### 8.1 Menambah Program Baru

1. Klik **"+ Buat Program"**.
2. Isi:
   - **Judul Program**
   - **Deskripsi**
   - **Tanggal Mulai & Selesai**
   - **Status**: `active` atau `inactive`
3. Klik **"Simpan"**.

### 8.2 Menonaktifkan Program

Ubah status program menjadi `inactive` agar tidak tampil di halaman publik.

---

## 9. MANAJEMEN GALERI

**Akses**: Dashboard → Menu **Galeri** atau URL `/admin/gallery`

### 9.1 Mengunggah Foto Baru

1. Klik **"+ Unggah Media"**.
2. Isi:
   - **Judul Foto/Video**
   - **Tipe**: `photo` atau `video`
   - **File**: Pilih file dari komputer (maks. 50MB)
3. Klik **"Unggah"**.

### 9.2 Menghapus Foto

1. Klik ikon 🗑️ pada foto.
2. Konfirmasi penghapusan.

---

## 10. MANAJEMEN PESAN MASUK (INBOX)

**Akses**: Dashboard → Menu **Inbox** atau URL `/admin/messages`

Pesan masuk berasal dari formulir kontak di halaman publik (`/kontak`).

### 10.1 Membaca Pesan

1. Klik pada baris pesan untuk membuka isinya.
2. Pesan yang sudah dibaca akan ditandai secara otomatis.

### 10.2 Membalas Pesan

1. Buka pesan yang ingin dibalas.
2. Isi kolom **"Balasan"** di bagian bawah.
3. Klik **"Kirim Balasan"**.

> [!NOTE]
> Balasan akan dikirim ke email pengirim secara otomatis. Pastikan konfigurasi email (SMTP) sudah benar di halaman Settings.

### 10.3 Menghapus Pesan

1. Klik ikon 🗑️ pada pesan.
2. Konfirmasi penghapusan.

---

## 11. PENGATURAN SITUS (SETTINGS)

> [!IMPORTANT]
> Fitur ini hanya bisa diakses oleh Admin (Superadmin).

**Akses**: Dashboard → Menu **Email & Situs** atau URL `/admin/settings`

Halaman ini adalah **pusat kendali konten** situs. Semua teks dan gambar di halaman depan dapat diubah dari sini.

### 11.1 Identitas & Branding

| Kolom | Keterangan |
|-------|------------|
| Nama Situs | Nama organisasi (tampil di Navbar dan Footer) |
| Nama Sekolah | Nama sekolah (tampil di halaman pendaftaran) |

### 11.2 Profil & Visi Misi Organisasi

| Kolom | Keterangan |
|-------|------------|
| **Visi Organisasi** | Teks visi yang tampil di halaman depan (Kotak Teks Besar) |
| **Misi Utama** | Teks misi yang tampil di halaman depan (Kotak Teks Besar) |

> [!TIP]
> Untuk Misi Utama, gunakan baris baru untuk setiap poin misi. Contoh:
> ```
> 1. Menjalin komunikasi antar alumni.
> 2. Memberikan beasiswa.
> 3. Berkontribusi untuk almamater.
> ```

### 11.3 Banner Utama (Hero)

| Kolom | Keterangan |
|-------|------------|
| Judul Banner | Teks besar di bagian atas halaman utama |
| Deskripsi Banner | Teks kecil di bawah judul |
| **Gambar Latar (Hero Background)** | Upload foto untuk latar belakang banner utama |

### 11.4 Informasi Kontak

| Kolom | Keterangan |
|-------|------------|
| Alamat | Alamat sekretariat |
| Email Kontak | Email resmi organisasi |
| Nomor Telepon | Nomor telepon yang bisa dihubungi |

### 11.5 Sambutan Ketua Umum

| Kolom | Keterangan |
|-------|------------|
| Nama Ketua Umum | Nama lengkap |
| Jabatan/Periode | Contoh: "Periode 2024-2028" |
| Sambutan Singkat | Teks sambutan ketua |
| **Foto Ketua Umum** | Upload foto (direkomendasikan 400×400px) |

### 11.6 Sambutan Ketua Panitia

Sama seperti Ketua Umum, diisi untuk acara/event khusus (misalnya Reuni Akbar).

### 11.7 Cara Menyimpan Perubahan

Setelah selesai mengisi semua kolom yang ingin diubah:
1. Scroll ke bawah halaman.
2. Klik tombol biru **"SIMPAN & UNGGAH PERUBAHAN"**.
3. Tunggu beberapa detik hingga muncul notifikasi sukses ✅.

---

## 12. EKSPOR DATA ALUMNI

**Akses**: `/admin/export`

Fitur ini memungkinkan Admin mengunduh seluruh data alumni dalam format spreadsheet.

1. Buka URL `/admin/export` di browser Anda.
2. File akan otomatis terunduh dalam format `.xlsx` (Excel).

Data yang diekspor meliputi: Nama, Email, NISN, Jurusan, Tahun Lulus, Status, Lokasi.

---

## 13. BACKUP & RESTORE DATABASE

> [!IMPORTANT]
> Backup database adalah langkah paling penting untuk melindungi data alumni dari kehilangan akibat kesalahan sistem, serangan, atau kerusakan server. Lakukan backup secara rutin!

### 13.1 Sistem Backup Otomatis (Crontab)

Server sudah dikonfigurasi untuk melakukan backup otomatis **setiap hari pukul 02:00 dini hari**. Backup tersimpan di folder:
```
/opt/steman-alumni/backups/database/
```

Setiap file backup diberi nama dengan format:
```
steman_alumni_YYYYMMDD_HHMMSS.sql.gz
```
Contoh: `steman_alumni_20260405_020000.sql.gz`

> [!NOTE]
> Backup lama yang sudah lebih dari **30 hari** akan otomatis dihapus oleh sistem untuk menghemat ruang penyimpanan.

### 13.2 Menjalankan Backup Manual

Jika Anda ingin membuat backup kapan saja (di luar jadwal otomatis), jalankan perintah ini di terminal VPS:

```bash
cd /opt/steman-alumni

# Jalankan backup manual
bash docker/scripts/backup-db.sh
```

Output yang muncul jika backup berhasil:
```
[2026-04-05 14:30:00] ====================================================
[2026-04-05 14:30:00]   MEMULAI PROSES BACKUP DATABASE: steman_alumni
[2026-04-05 14:30:00] Menjalankan mysqldump...
[2026-04-05 14:30:02] ✅ SUKSES: Backup tersimpan di /opt/.../steman_alumni_20260405_143000.sql.gz (Ukuran: 1.2M)
[2026-04-05 14:30:02] BACKUP SELESAI
```

### 13.3 Melihat Daftar Backup yang Tersedia

```bash
# Lihat semua file backup beserta ukurannya
ls -lh /opt/steman-alumni/backups/database/

# Lihat log aktivitas backup
tail -50 /opt/steman-alumni/backups/backup.log
```

### 13.4 Cara Restore Database dari Backup

> [!CAUTION]
> **PERINGATAN KERAS**: Proses restore akan **MENGHAPUS SELURUH DATA SAAT INI** di database dan menggantinya dengan data dari file backup. Pastikan Anda yakin sebelum melanjutkan!

**Langkah-langkah restore:**

1. Pastikan Anda berada di folder project:
```bash
cd /opt/steman-alumni
```

2. Lihat daftar file backup yang tersedia:
```bash
ls -lh backups/database/
```

3. Jalankan perintah restore dengan nama file yang ingin dikembalikan:
```bash
bash docker/scripts/restore-db.sh steman_alumni_20260405_020000.sql.gz
```

4. Sistem akan meminta konfirmasi. Ketik `YA` (huruf kapital) lalu tekan Enter:
```
⚠️  PERINGATAN: Proses ini akan MENGHAPUS semua data saat ini...
Ketik 'YA' untuk melanjutkan: YA
```

5. Tunggu proses selesai. Output sukses:
```
[2026-04-05 15:00:00] ✅ SUKSES: Database berhasil di-restore.
```

### 13.5 Memeriksa Jadwal Backup Otomatis

Untuk memastikan crontab backup berjalan dengan benar:

```bash
# Lihat jadwal crontab yang aktif
crontab -l
```

Output yang diharapkan:
```cron
# Backup database Steman Alumni - setiap hari jam 02:00 dini hari
0 2 * * * /bin/bash /opt/steman-alumni/docker/scripts/backup-db.sh >> /opt/steman-alumni/backups/backup.log 2>&1

# Backup mingguan ekstra - setiap Minggu jam 03:00
0 3 * * 0 /bin/bash /opt/steman-alumni/docker/scripts/backup-db.sh >> /opt/steman-alumni/backups/backup.log 2>&1
```

### 13.6 Ringkasan Jadwal Backup

| Jadwal | Waktu | Keterangan |
|--------|-------|------------|
| 🔄 **Harian** | Setiap hari pukul 02:00 | Backup rutin harian |
| 🔄 **Mingguan** | Setiap Minggu pukul 03:00 | Backup tambahan mingguan |
| 👤 **Manual** | Kapan saja (by Admin) | Dijalankan manual via terminal |
| 🗑️ **Penghapusan Otomatis** | Bersamaan dengan backup | File > 30 hari dihapus otomatis |

---

## 14. KEAMANAN & PEMELIHARAAN

### 13.1 Mengganti Password Admin

1. Klik nama pengguna di sudut kanan atas.
2. Pilih **"Profil"** atau **"Ubah Password"**.
3. Masukkan password lama dan password baru.
4. Klik **"Simpan"**.

### 13.2 Pencatatan Aktivitas (Activity Log)

Semua aktivitas perubahan data dicatat secara otomatis oleh sistem (waktu, pengguna, aksi). Log ini dapat dilihat oleh Superadmin.

### 13.3 Batasan Keamanan Akses

Sistem dilindungi dengan:
- **Captcha Matematika** di halaman login dan daftar
- **Rate Limiting**: Login dibatasi 20 kali per menit per IP
- **Firewall VPS (UFW)**: Hanya port 80, 443, dan 22 yang terbuka
- **HTTPS/SSL**: Seluruh akses menggunakan enkripsi

### 13.4 Pemeliharaan Rutin (Disarankan Bulanan)

Jalankan perintah ini di terminal VPS setiap bulan untuk membersihkan sistem:

```bash
cd /opt/steman-alumni

# Bersihkan cache sistem
docker compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker compose -f docker-compose.prod.yml exec app php artisan view:clear
docker compose -f docker-compose.prod.yml exec app php artisan config:clear

# Optimasi ulang
docker compose -f docker-compose.prod.yml exec app php artisan optimize

# Cek log error (opsional)
docker compose -f docker-compose.prod.yml logs --tail 50 app
```

---

## 15. FAQ & PEMECAHAN MASALAH

### ❓ Saya lupa password admin. Apa yang harus dilakukan?

Hubungi developer atau jalankan perintah berikut di terminal VPS untuk mereset password:
```bash
docker compose -f docker-compose.prod.yml exec app php artisan tinker
# Di dalam tinker, jalankan:
# User::where('email', 'admin@steman.ac.id')->update(['password' => Hash::make('PasswordBaru123!')]);
# exit
```

### ❓ Foto yang saya unggah tidak muncul di website.

1. Pastikan ukuran file tidak melebihi **50MB**.
2. Pastikan format file adalah **JPG, PNG, atau WEBP**.
3. Jalankan perintah berikut di VPS:
```bash
docker compose -f docker-compose.prod.yml exec app php artisan storage:link
```

### ❓ Perubahan di Settings tidak langsung terlihat di website.

Cache situs menyimpan tampilan selama 60 menit. Untuk memaksanya berubah seketika, jalankan:
```bash
docker compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker compose -f docker-compose.prod.yml exec app php artisan view:clear
```

### ❓ Website menampilkan error 500.

1. Cek log error di VPS: `docker compose -f docker-compose.prod.yml logs --tail 50 app`
2. Pastikan file `.env` sudah terisi dengan benar.
3. Hubungi developer jika error berlanjut.

### ❓ Muncul error "429 Too Many Requests" saat login.

Ini berarti sistem mendeteksi terlalu banyak percobaan login. Tunggu **1-2 menit** sebelum mencoba lagi. Atau bersihkan cache rate limit di VPS:
```bash
docker compose -f docker-compose.prod.yml exec app php artisan cache:clear
```

### ❓ Mengakses Data API (Untuk Developer)

Aplikasi memiliki Endpoint API versi 1 di `/api/v1/auth/login` (Untuk otentikasi) dan `/api/v1/alumni` untuk pengambilan data massal.
Fitur API sepenuhnya dilindungi oleh Laravel Sanctum Token.

### ❓ Apakah Data yang Dihapus Benar-benar Hilang? (Soft Deletes)

Tidak. Sistem v6 ini dilengkapi dengan fitur *Soft Deletes*. Jika Anda secara tidak sengaja menghapus Alumni, Berita, Acara, Loker, maupun Pesan, maka data tersebut hanya **"disembunyikan"** dari tampilan dan pindah ke dalam ruang arsip (Sampah) di server (tidak hilang permanen). Hubungi tim developer teknis bila Ingin melakukan pemulihan *(Restore Data).*

---

## 📞 KONTAK DUKUNGAN TEKNIS

Untuk bantuan teknis lebih lanjut, hubungi:
- **Developer**: Tim Teknis STEMAN
- **Email**: `admin@steman.ac.id`
- **Server VPS**: `103.175.219.57`

---

*© 2026 Portal Alumni SMKN 2 Ternate. Seluruh hak cipta dilindungi.*

*Dokumen ini bersifat RAHASIA dan hanya untuk kalangan Administrator yang berwenang.*
