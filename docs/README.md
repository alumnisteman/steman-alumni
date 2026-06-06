# Portal Alumni STEMAN
**Sistem Informasi Alumni SMKN 2 Ternate**

Portal berbasis web untuk mengelola data, kegiatan, dan komunikasi alumni SMKN 2 Ternate (STEMAN). Dibangun dengan Laravel 12 dan arsitektur Docker.

---

## Fitur Utama

- Manajemen data alumni dengan verifikasi admin
- Publikasi berita, pengumuman, dan lowongan kerja
- Program dan kegiatan organisasi alumni
- Galeri foto dan video
- Peta sebaran alumni global interaktif
- Social feed nostalgia antar alumni
- Notifikasi real-time
- API untuk integrasi mobile app (Laravel Sanctum)
- System Guard — pengawas kesehatan sistem otomatis 24/7
- Notifikasi otomatis ke Telegram saat ada masalah

---

## Dokumentasi

Semua panduan tersimpan di folder `docs/`:

| Dokumen | Isi |
|---|---|
| [Panduan Instalasi](docs/TUTORIAL_INSTALASI.md) | Cara install di lokal dan VPS, termasuk konfigurasi Telegram |
| [Panduan Maintenance](docs/TUTORIAL_MAINTENANCE.md) | Backup, deploy update, troubleshooting, reset password |
| [Panduan Admin](docs/PANDUAN_ADMIN.md) | Cara menggunakan semua fitur panel admin |
| [Panduan Deployment VPS](docs/TUTORIAL_VPS_DEPLOYMENT.md) | Setup CI/CD GitHub Actions, SSL, crontab |
| [Panduan Otomasi](docs/AUTOMATION_GUIDE.md) | System Guard, scheduler, script autoheal, notifikasi |

---

## Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Framework Backend | Laravel 12 |
| Database | MariaDB / MySQL |
| Cache & Session | Redis |
| Search Engine | Meilisearch |
| Frontend | Bootstrap 5 + Vite |
| Real-time | Laravel Reverb + Redis |
| Containerisasi | Docker + Docker Compose |
| Web Server | Nginx |
| SSL | Let's Encrypt (Certbot) |

---

## Akses Server Production

| Item | Nilai |
|---|---|
| IP Server | `103.175.219.57` |
| Domain | `https://alumni-steman.my.id` |
| Admin Panel | `https://admin.alumni-steman.my.id` |
| System Guard | `https://admin.alumni-steman.my.id/system/guard` |

---

## Kontak

- **Email:** `admin@steman.ac.id`
- **Server:** `103.175.219.57`

---

*© 2026 Portal Alumni SMKN 2 Ternate (STEMAN)*
