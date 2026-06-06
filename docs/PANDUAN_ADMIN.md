# Buku Panduan Administrator
## Portal Alumni SMKN 2 Ternate (STEMAN)
**Versi Juni 2026 | Dokumen Resmi**

---

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Akses dan Login Dashboard](#2-akses-dan-login-dashboard)
3. [Tampilan Utama Dashboard](#3-tampilan-utama-dashboard)
4. [Manajemen Pengguna](#4-manajemen-pengguna)
5. [Manajemen Jurusan](#5-manajemen-jurusan)
6. [Manajemen Berita](#6-manajemen-berita)
7. [Manajemen Lowongan Kerja](#7-manajemen-lowongan-kerja)
8. [Manajemen Program / Kegiatan](#8-manajemen-program--kegiatan)
9. [Manajemen Galeri](#9-manajemen-galeri)
10. [Pesan Masuk (Inbox)](#10-pesan-masuk-inbox)
11. [Pengaturan Situs](#11-pengaturan-situs)
12. [Ekspor Data Alumni](#12-ekspor-data-alumni)
13. [Backup dan Restore Database](#13-backup-dan-restore-database)
14. [System Guard — Monitor Kesehatan Sistem](#14-system-guard--monitor-kesehatan-sistem)
15. [Keamanan Akun](#15-keamanan-akun)
16. [FAQ dan Pemecahan Masalah](#16-faq-dan-pemecahan-masalah)

---

## 1. Pendahuluan

Portal Alumni STEMAN adalah sistem informasi berbasis web untuk mengelola data alumni SMKN 2 Ternate. Fitur utama meliputi:

- Manajemen data alumni dan akun pengguna
- Publikasi berita dan pengumuman
- Lowongan kerja dari alumni dan mitra
- Program / kegiatan organisasi
- Galeri foto dan video
- Peta sebaran alumni global
- Pengaturan tampilan situs secara dinamis
- Kotak pesan dari publik
- System Guard — pengawas kesehatan sistem otomatis
- Notifikasi Telegram saat terjadi masalah di server

### Tingkatan Akses Pengguna

| Peran | Akses |
|---|---|
| **Admin** | Akses penuh ke seluruh fitur termasuk User Management, Settings, dan System Guard |
| **Editor** | Dapat mengelola Berita, Galeri, Program, Lowongan — tidak bisa akses User & Settings |
| **Alumni** | Hanya akses halaman alumni (profil, direktori, jejak karir) |

---

## 2. Akses dan Login Dashboard

### Membuka Halaman Login

1. Buka browser, kunjungi: `https://alumni-steman.my.id/login`
2. Masukkan **Email** dan **Password** akun Admin
3. Jawab pertanyaan keamanan Captcha (contoh: `5 + 3 = ?`)
4. Klik tombol **"Login Sekarang"**

### Menuju Panel Admin

Setelah login, klik **"Dashboard"** di menu navigasi, atau akses langsung:
`https://alumni-steman.my.id/admin/dashboard`

### Panel Admin Terpisah (Subdomain)

Panel admin juga bisa diakses melalui:
`https://admin.alumni-steman.my.id`

> ⚠️ Gunakan akun dengan role `admin` untuk akses penuh. Akun `editor` tidak bisa mengakses menu User dan Settings.

---

## 3. Tampilan Utama Dashboard

### Kartu Statistik

Di bagian atas dashboard terdapat kartu ringkasan:

| Kartu | Keterangan |
|---|---|
| Total Alumni | Jumlah alumni terdaftar dan sudah disetujui |
| Internasional | Alumni yang bekerja/tinggal di luar Indonesia |
| Jurusan | Total jurusan aktif yang terdaftar |
| Lowongan | Total lowongan kerja yang sedang aktif |

### Menu Manajemen Cepat

Tombol akses cepat ke semua halaman admin:

- **User** — Kelola akun alumni dan admin
- **Jurusan** — Tambah atau ubah jurusan
- **Lowongan** — Kelola informasi lowongan kerja
- **Settings** — Pengaturan konten dan tampilan situs
- **Berita** — Kelola artikel dan pengumuman
- **Program** — Kelola kegiatan alumni
- **Galeri** — Kelola foto dan video
- **Inbox** — Pesan masuk dari publik

### Peta Sebaran Alumni

Di bagian bawah dashboard terdapat peta interaktif yang menampilkan lokasi alumni di seluruh dunia berdasarkan data alamat yang diisi saat registrasi.

---

## 4. Manajemen Pengguna

> Hanya bisa diakses oleh **Admin** (bukan Editor).

**Akses:** Admin Panel → **User** → URL: `/admin/users`

### Melihat Daftar Pengguna

Tabel menampilkan semua pengguna dengan informasi:
- Nama dan email
- Status: Pending / Approved / Rejected
- Peran: Admin / Editor / Alumni
- Tanggal mendaftar

### Menyetujui Pendaftar Baru

1. Temukan alumni berstatus **"Pending"**
2. Klik tombol **"Setujui"** (✅) atau **"Tolak"** (❌)
3. Status berubah otomatis dan alumni mendapat notifikasi email

### Mengubah Peran Pengguna

1. Klik dropdown **"Role"** pada baris pengguna
2. Pilih peran: `admin`, `editor`, atau `alumni`
3. Perubahan tersimpan otomatis

### Menambah Admin atau Editor Baru

1. Klik tombol **"+ Tambah Pengguna"**
2. Isi formulir: Nama, Email, Password, Peran
3. Klik **"Simpan"**

### Menghapus Pengguna

1. Klik ikon 🗑️ pada baris pengguna
2. Konfirmasi penghapusan

> ⚠️ Penghapusan bersifat *soft delete* — data bisa dipulihkan oleh Admin Teknis jika diperlukan.

---

## 5. Manajemen Jurusan

**Akses:** Admin Panel → **Jurusan** → URL: `/admin/majors`

### Menambah Jurusan Baru

1. Isi kolom **"Nama Jurusan"** (contoh: Rekayasa Perangkat Lunak)
2. Isi **"Bidang/Kelompok"** (contoh: Teknologi Informasi)
3. Klik **"Tambah"**

### Mengedit Jurusan

1. Klik ikon ✏️ pada jurusan
2. Ubah nama atau bidang
3. Klik **"Simpan Perubahan"**

### Menonaktifkan Jurusan

Ubah status jurusan menjadi `inactive` agar tidak muncul di formulir pendaftaran alumni baru.

---

## 6. Manajemen Berita

**Akses:** Admin Panel → **Berita** → URL: `/admin/news`

### Membuat Artikel Baru

1. Klik **"+ Tulis Berita"**
2. Isi formulir:
   - **Judul** — Judul artikel
   - **Kategori** — Pilih kategori yang sesuai
   - **Konten** — Isi artikel lengkap
   - **Thumbnail** — Upload gambar sampul (sistem otomatis mengoptimasi ke format WebP)
   - **Status** — `Draft` (belum tayang) atau `Published` (langsung tayang)
3. Klik **"Simpan & Terbitkan"**

### Mengedit Artikel

1. Klik ikon ✏️ pada artikel
2. Lakukan perubahan
3. Klik **"Update"**

### Status Artikel

| Status | Keterangan |
|---|---|
| `Draft` | Artikel tersimpan tapi tidak terlihat oleh publik |
| `Published` | Artikel langsung tayang di halaman berita dan beranda |

### Menghapus Artikel

1. Klik ikon 🗑️ pada artikel
2. Konfirmasi penghapusan

Data artikel yang dihapus masuk ke *Trash* (bisa dipulihkan oleh Admin Teknis).

---

## 7. Manajemen Lowongan Kerja

**Akses:** Admin Panel → **Lowongan** → URL: `/admin/jobs`

### Menambah Lowongan Baru

1. Klik **"+ Tambah Lowongan"**
2. Isi formulir:
   - **Judul Posisi** — Nama pekerjaan
   - **Perusahaan** — Nama perusahaan pemberi lowongan
   - **Lokasi** — Kota / Provinsi / Negara
   - **Tipe** — `Full-time`, `Part-time`, `Magang`, atau `Remote`
   - **Deskripsi** — Detail persyaratan dan tanggung jawab pekerjaan
   - **Deadline** — Batas waktu pendaftaran
   - **Status** — `active` atau `closed`
3. Klik **"Simpan"**

### Menutup Lowongan

1. Klik ✏️ Edit pada lowongan
2. Ubah Status menjadi `closed`
3. Klik **"Update"**

---

## 8. Manajemen Program / Kegiatan

**Akses:** Admin Panel → **Program** → URL: `/admin/programs`

Program adalah kegiatan resmi organisasi alumni (reuni, webinar, beasiswa, pelatihan, dll.).

### Menambah Program Baru

1. Klik **"+ Buat Program"**
2. Isi:
   - **Judul Program**
   - **Deskripsi** — Penjelasan lengkap kegiatan
   - **Tanggal Mulai dan Selesai**
   - **Status** — `Published` (tampil) atau `Draft` (arsip)
3. Klik **"Simpan"**

---

## 9. Manajemen Galeri

**Akses:** Admin Panel → **Galeri** → URL: `/admin/gallery`

### Mengunggah Foto atau Video

1. Klik **"+ Unggah Media"**
2. Isi:
   - **Judul** — Nama foto atau video
   - **Tipe** — `photo` atau `video`
   - **File** — Pilih file (maksimal 10 MB untuk foto)
   - **Status** — `Published` (tampil di galeri) atau `Draft`
3. Klik **"Unggah"**

> Sistem secara otomatis mengkompresi dan mengoptimasi foto ke format WebP untuk performa lebih cepat.

---

## 10. Pesan Masuk (Inbox)

**Akses:** Admin Panel → **Inbox** → URL: `/admin/messages`

Pesan masuk berasal dari formulir kontak di halaman publik.

### Membaca Pesan

1. Klik pada baris pesan untuk membuka isi pesan
2. Pesan yang sudah dibaca ditandai secara otomatis

### Membalas Pesan

1. Buka pesan yang ingin dibalas
2. Isi kolom **"Balasan"** di bagian bawah
3. Klik **"Kirim Balasan"**

> Balasan dikirim otomatis ke email pengirim. Pastikan konfigurasi SMTP sudah benar di Settings.

---

## 11. Pengaturan Situs

> Hanya bisa diakses oleh **Admin** (bukan Editor).

**Akses:** Admin Panel → **Settings** → URL: `/admin/settings`

Halaman ini adalah pusat kendali konten situs. Semua teks dan gambar di halaman depan dapat diubah dari sini.

### Identitas dan Branding

| Kolom | Keterangan |
|---|---|
| Nama Situs | Nama organisasi (tampil di Navbar dan Footer) |
| Nama Sekolah | Nama sekolah (tampil di halaman pendaftaran) |

### Banner Utama (Hero Section)

| Kolom | Keterangan |
|---|---|
| Judul Banner | Teks besar di bagian atas halaman utama |
| Deskripsi Banner | Teks kecil di bawah judul |
| Gambar Latar | Upload foto untuk latar belakang banner |

### Profil Organisasi

| Kolom | Keterangan |
|---|---|
| Visi Organisasi | Teks visi tampil di halaman depan |
| Misi Utama | Teks misi (pisahkan setiap poin dengan baris baru) |

### Sambutan Ketua Umum

| Kolom | Keterangan |
|---|---|
| Nama Ketua Umum | Nama lengkap |
| Jabatan / Periode | Contoh: "Periode 2024–2028" |
| Sambutan Singkat | Teks sambutan |
| Foto Ketua Umum | Upload foto (disarankan 400×400 px) |

### Informasi Kontak

| Kolom | Keterangan |
|---|---|
| Alamat | Alamat sekretariat |
| Email Kontak | Email resmi organisasi |
| Nomor Telepon | Nomor yang bisa dihubungi |

### Cara Menyimpan Perubahan

1. Scroll ke bawah halaman Settings
2. Klik tombol biru **"SIMPAN & UNGGAH PERUBAHAN"**
3. Tunggu hingga muncul notifikasi sukses ✅

---

## 12. Ekspor Data Alumni

**Akses:** URL: `/admin/export`

1. Buka halaman ekspor
2. File otomatis terunduh dalam format `.xlsx` (Excel)

Data yang diekspor: Nama, Email, NISN, Jurusan, Tahun Lulus, Status, Lokasi Tempat Tinggal, Pekerjaan.

---

## 13. Backup dan Restore Database

> Backup adalah perlindungan terpenting data alumni. Lakukan backup sebelum melakukan perubahan besar!

### Sistem Backup Otomatis

Server sudah dikonfigurasi backup otomatis setiap hari pukul 02:00. File backup tersimpan di server dengan nama:
```
backup_steman_YYYYMMDD_HHMMSS.sql.gz
```

### Backup Manual via SSH

Hubungi Admin Teknis atau jalankan perintah berikut di server:

```bash
docker exec steman_db mysqldump -u app_user -pPASSWORD steman_alumni \
  | gzip > /root/backup_steman_$(date +%Y%m%d_%H%M%S).sql.gz
```

### Restore Database

> ⚠️ Restore menghapus semua data saat ini. Hanya dilakukan oleh Admin Teknis!

```bash
gunzip < /root/NAMA_FILE_BACKUP.sql.gz | \
  docker exec -i steman_db mysql -u app_user -pPASSWORD steman_alumni
```

---

## 14. System Guard — Monitor Kesehatan Sistem

System Guard adalah fitur otomatis yang memeriksa 21 titik kesehatan sistem setiap menit. Sistem ini akan:
- Mendeteksi masalah (database mati, disk penuh, scheduler macet, dll.)
- Mencoba memperbaiki otomatis jika memungkinkan
- Mengirim notifikasi ke Telegram jika ada masalah yang butuh perhatian manual

### Cara Cek Status Sistem

Sebagai Admin, Anda bisa melihat status sistem melalui:

**Via URL (browser):**
```
https://admin.alumni-steman.my.id/system/guard
```

**Via Terminal (SSH ke server):**
```bash
docker exec steman_app php artisan system:guard
```

### Kirim Laporan ke Telegram

```bash
docker exec steman_app php artisan system:guard --report
```

### Konfigurasi Notifikasi Telegram

Agar notifikasi berfungsi, isi di `.env` server:
```env
TELEGRAM_BOT_TOKEN=token_bot_anda
TELEGRAM_CHAT_ID=id_chat_anda
```

Lihat [Panduan Instalasi — Bagian 5](TUTORIAL_INSTALASI.md#5-konfigurasi-notifikasi-telegram) untuk cara mendapatkan token dan Chat ID.

---

## 15. Keamanan Akun

### Mengganti Password Admin

1. Klik nama pengguna di pojok kanan atas
2. Pilih **"Profil"** atau **"Ubah Password"**
3. Masukkan password lama dan password baru
4. Klik **"Simpan"**

> Gunakan password minimal 12 karakter kombinasi huruf besar, kecil, angka, dan simbol.

### Fitur Keamanan Aktif

| Fitur | Keterangan |
|---|---|
| Captcha Matematika | Wajib dijawab saat login dan daftar |
| Rate Limiting | Login dibatasi 20 kali per menit per IP |
| Session Aman | Cookie session hanya via HTTPS dengan domain `.alumni-steman.my.id` |
| HTTPS/SSL | Seluruh akses terenkripsi, sertifikat diperbarui otomatis |
| Firewall VPS | Hanya port 80, 443, dan 22 yang terbuka |
| Audit Log | Semua aksi admin dicatat otomatis (waktu, pengguna, aksi) |

---

## 16. FAQ dan Pemecahan Masalah

### Saya lupa password Admin

Hubungi Admin Teknis untuk reset password via server, atau jalankan:
```bash
docker exec steman_app php artisan tinker --execute \
  "\$u = \App\Models\User::where('email','email_anda@gmail.com')->first(); \$u->password = Hash::make('PasswordBaru'); \$u->save(); echo 'OK';"
```

### Foto yang saya upload tidak muncul di website

1. Pastikan ukuran file di bawah 10 MB
2. Format file harus JPG, PNG, atau WEBP
3. Jika masih tidak muncul, hubungi Admin Teknis untuk menjalankan:
   ```bash
   docker exec steman_app php artisan storage:link --force
   ```

### Perubahan di Settings tidak langsung tampil

Cache situs disimpan selama beberapa menit. Hubungi Admin Teknis untuk bersihkan cache:
```bash
docker exec steman_app php artisan cache:clear
docker exec steman_app php artisan view:clear
```

### Website menampilkan Error 500

1. Coba refresh halaman
2. Coba akses halaman lain
3. Jika masih error, hubungi Admin Teknis dengan menyebutkan: halaman mana yang error, jam kejadian, dan tindakan apa yang dilakukan sebelum error

### Muncul "429 Too Many Requests" saat login

Sistem mendeteksi terlalu banyak percobaan login. Tunggu **1–2 menit** sebelum mencoba lagi.

### Data alumni yang sudah dihapus bisa dipulihkan?

Ya! Sistem menggunakan *Soft Deletes* — data yang dihapus dari panel admin hanya disembunyikan, tidak dihapus permanen. Hubungi Admin Teknis untuk memulihkan data.

### Notifikasi Telegram tidak masuk

1. Pastikan `TELEGRAM_BOT_TOKEN` dan `TELEGRAM_CHAT_ID` sudah diisi di `.env` server
2. Pastikan bot sudah pernah Anda kirimi pesan (chat harus aktif)
3. Coba jalankan: `docker exec steman_app php artisan system:guard --report`

---

## Kontak Dukungan Teknis

Untuk bantuan teknis lebih lanjut:
- **Email:** `admin@steman.ac.id`
- **Server VPS:** `103.175.219.57`
- **Panel Admin:** `https://admin.alumni-steman.my.id`

---

*© 2026 Portal Alumni SMKN 2 Ternate. Dokumen ini bersifat internal dan hanya untuk Administrator yang berwenang.*
