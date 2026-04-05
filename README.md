# 🎓 STEMAN Alumni Portal v6.0 (Hardened)

Portal Sistem Informasi Ikatan Alumni SMKN 2 Ternate. Dibangun menggunakan Laravel 12 Modular, API Sanctum, dan Dockerized Production Environment.

---

## ⚡ Quick Start (Windows)
Untuk menjalankan aplikasi dengan cepat di server lokal atau produksi:
1. **Inisialisasi IP**: Jalankan `.\update-ip.ps1`
2. **Deploy**: Jalankan `.\deploy.ps1`

---

## 📘 Panduan Dokumentasi
Kami menyediakan panduan lengkap untuk setiap operasional sistem:

1. 🚀 **[Panduan Instalasi](TUTORIAL_INSTALASI.md)**: Langkah awal setup Docker dan lingkungan server.
2. 🛠️ **[Panduan Maintenance](TUTORIAL_MAINTENANCE.md)**: Checklist pembersihan cache, data sampah, dan backup.
3. 🌐 **[Panduan VPS Deployment](TUTORIAL_VPS_DEPLOYMENT.md)**: Cara mengaktifkan Auto-Deploy via GitHub Actions.
4. ☁️ **[Panduan Upload GitHub](UPLOAD_TO_GITHUB.md)**: Instruksi sinkronisasi kode ke repositori.
5. 📘 **[Buku Panduan Admin](PANDUAN_ADMIN_STEMAN.md)**: Cara mengoperasikan Dashboard bagi Admin & Editor.

---

## 🛡️ Fitur Keamanan (v6.0 Hardened)
- **SoftDeletes**: Data tidak hilang permanen saat dihapus.
- **Strict Throttling**: Proteksi Brute-Force pada sistem login.
- **Nginx Hardening**: Blokade otomatis pada file sensitif (`.env`, `.git`).
- **Auto-Permissions**: Izin file `755/775` dikelola otomatis oleh Docker Entrypoint.

---
> _"Menghubungkan masa lalu, membangun masa depan."_
> **Ikatan Alumni SMKN 2 Ternate**

