# STEMAN Alumni - Automation Guide

## Overview
Dokumentasi ini menjelaskan automation scripts yang tersedia untuk maintenance STEMAN Alumni Portal.

## Automation Scripts

### 1. maintenance.sh
**Lokasi:** `/var/www/steman-alumni/scripts/maintenance.sh`

**Fungsi:** Script pemeliharaan harian untuk optimalisasi aplikasi dan sistem.

**Task yang dilakukan:**
- Optimalisasi Laravel Cache (optimize:clear, optimize, route:cache, config:cache, view:cache)
- Pembersihan Log Laravel (laravel.log, emergency_fatal.log)
- Optimalisasi Docker (image prune)
- Optimalisasi Database (ANALYZE TABLE)
- Pengecekan versi framework

**Jadwal:** Daily at 3 AM (via cron)

**Cara menjalankan manual:**
```bash
cd /var/www/steman-alumni
./scripts/maintenance.sh
```

### 2. backup_database.sh
**Lokasi:** `/var/www/steman-alumni/scripts/backup_database.sh`

**Fungsi:** Script backup database dengan retention policy.

**Task yang dilakukan:**
- Backup database MySQL ke file SQL
- Kompresi backup dengan gzip
- Pembersihan backup lama (lebih dari 7 hari)
- Notifikasi Telegram (jika dikonfigurasi)

**Jadwal:** Daily at 2 AM (via cron)

**Cara menjalankan manual:**
```bash
cd /var/www/steman-alumni
./scripts/backup_database.sh
```

**Lokasi backup:** `/var/www/steman-alumni/backups/database/`

### 3. monitor_errors.sh
**Lokasi:** `/var/www/steman-alumni/scripts/monitor_errors.sh`

**Fungsi:** Script monitoring error untuk deteksi masalah.

**Task yang dilakukan:**
- Cek Laravel Log untuk ERROR (threshold: 10)
- Cek Emergency Log untuk error fatal
- Notifikasi Telegram jika error melebihi threshold
- Tampilkan 5 error terakhir

**Jadwal:** Every 6 hours (via cron)

**Cara menjalankan manual:**
```bash
cd /var/www/steman-alumni
./scripts/monitor_errors.sh
```

### 4. cleanup_storage.sh
**Lokasi:** `/var/www/steman-alumni/scripts/cleanup_storage.sh`

**Fungsi:** Script pembersihan storage untuk optimalisasi ruang.

**Task yang dilakukan:**
- Clear Laravel Cache (cache:clear, config:clear, route:clear, view:clear)
- Clear Compiled Views
- Clear Session Files (lebih dari 1 hari)
- Clear Log Files
- Clear Temporary Files (*.tmp, *.temp)

**Jadwal:** Weekly on Sunday at 4 AM (via cron)

**Cara menjalankan manual:**
```bash
cd /var/www/steman-alumni
./scripts/cleanup_storage.sh
```

### 5. setup_cron.sh
**Lokasi:** `/var/www/steman-alumni/scripts/setup_cron.sh`

**Fungsi:** Script setup otomatis untuk cron jobs.

**Task yang dilakukan:**
- Setup maintenance cron (daily at 3 AM)
- Setup backup cron (daily at 2 AM)
- Setup error monitoring cron (every 6 hours)
- Setup storage cleanup cron (weekly on Sunday at 4 AM)
- Buat direktori logs

**Cara menjalankan:**
```bash
cd /var/www/steman-alumni
./scripts/setup_cron.sh
```

## Environment Variables untuk Notifikasi

Untuk mengaktifkan notifikasi Telegram, tambahkan ke `.env`:

```env
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_CHAT_ID=your_chat_id
```

## Container Names

Pastikan container names sesuai dengan konfigurasi:
- App container: `steman-alumni-app-1`
- Database container: `steman_db`
- Nginx container: `steman_nginx`

## Troubleshooting

### Script tidak berjalan
1. Pastikan script memiliki permission execute: `chmod +x scripts/*.sh`
2. Pastikan Docker containers berjalan: `docker ps`
3. Cek log script: `tail -f logs/maintenance.log`

### Backup gagal
1. Pastikan database container berjalan
2. Cek kredensial database di script
3. Pastikan direktori backups ada dan writable

### Error monitoring tidak mengirim notifikasi
1. Pastikan TELEGRAM_BOT_TOKEN dan TELEGRAM_CHAT_ID di-set di .env
2. Test koneksi ke Telegram API
3. Cek firewall tidak memblokir outgoing connections

## Best Practices

1. **Jalankan setup_cron.sh sekali saja** untuk setup awal
2. **Monitor log files** secara regular untuk memastikan scripts berjalan dengan baik
3. **Test backup restore** secara berkala untuk memastikan backup valid
4. **Review error logs** setelah menerima notifikasi error
5. **Update scripts** jika ada perubahan struktur aplikasi atau database

## Maintenance Schedule

| Script | Jadwal | Fungsi |
|--------|--------|--------|
| backup_database.sh | Daily 2 AM | Backup database |
| maintenance.sh | Daily 3 AM | Optimalisasi sistem |
| monitor_errors.sh | Every 6 hours | Monitoring error |
| cleanup_storage.sh | Weekly Sunday 4 AM | Cleanup storage |

## Log Files

Semua log scripts disimpan di `/var/www/steman-alumni/logs/`:
- `maintenance.log` - Log maintenance script
- `backup.log` - Log backup script
- `monitor.log` - Log error monitoring
- `cleanup.log` - Log storage cleanup
