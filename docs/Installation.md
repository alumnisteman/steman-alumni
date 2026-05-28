# Tutorial Instalasi (Local/Dev)

## Prasyarat
- PHP ≥ 8.1
- Composer
- MySQL / MariaDB
- XAMPP, Laragon, atau PHP built‑in server

## Langkah‑langkah
1. **Clone repository**
   ```bash
   git clone https://github.com/alumnisteman/steman-alumni.git
   cd steman-alumni
   ```
2. **Install dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
3. **Copy env file**
   ```bash
   cp .env.example .env
   ```
   Edit `.env` sesuai database Anda (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
4. **Generate app key**
   ```bash
   php artisan key:generate
   ```
5. **Migrate & seed data**
   ```bash
   php artisan migrate --seed
   ```
6. **Jalankan server development**
   ```bash
   php artisan serve
   ```
   Akses http://127.0.0.1:8000 di browser.

## Catatan
- Untuk XAMPP/Laragon, pastikan **mod_rewrite** aktif.
- Jika menggunakan Docker lokal, lihat `docker-compose.dev.yml`.
