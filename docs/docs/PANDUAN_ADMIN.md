# PANDUAN ADMIN

Panduan ini menjelaskan cara mengelola sistem dari backend, termasuk:

- **Menyetujui Alumni Baru**: Masuk ke *Admin Dashboard → Approvals*, pilih alumni, klik **Approve**.
- **Mengelola Forum & Berita**: Gunakan menu *Forum* atau *News* untuk menambah, mengedit, atau menghapus postingan.
- **Pengaturan Sistem**: Di *Settings* dapat mengubah konfigurasi global, mengelola nilai cache, dan mengatur hak akses.
- **Log Aktivitas**: Pada *Activity Log* dapat memfilter berdasarkan pengguna, tipe aksi, atau rentang tanggal.
- **Statistik & Dashboard**: Halaman utama menampilkan statistik real‑time (jumlah alumni, posting, kunjungan).

### Tips
- Selalu gunakan *preview* sebelum mempublikasikan perubahan.
- Pastikan gambar thumbnail sudah di‑optimasi agar tidak menimbulkan **504 Gateway Timeout**.
- Setelah perubahan besar, jalankan `php artisan cache:clear` untuk memuat ulang data.
