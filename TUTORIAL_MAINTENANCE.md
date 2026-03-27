# 🛠️ Panduan Pemeliharaan & Cadangan STEMAN Alumni v5

Pastikan Portal Alumni Anda tetap berjalan 100% lancar dengan mengikuti panduan rutin ini.

---

## 💾 1. Sistem Cadangan (Backup)
Jangan biarkan data alumni Anda hilang. Kami menyediakan script backup otomatis.

### Cara Menjalankan Backup:
Gunakan SSH/Terminal ke server Anda, lalu jalankan:
```bash
./scripts/backup.sh
```
Script ini akan:
- Mencadangkan Database (MySQL Dump).
- Mencadangkan File Upload (Foto Profil & Lampiran).
- Hasilnya disimpan di folder `./backups/`.

### Otomatisasi (Cron Job):
Agar backup berjalan otomatis setiap hari jam 2 pagi, tambahkan baris ini ke `crontab -e`:
```bash
0 2 * * * /path/ke/proyek/scripts/backup.sh > /dev/null 2>&1
```

---

## ⚡ 2. Pemeliharaan Rutin (Maintenance)
Jalankan script `maintenance.sh` minimal seminggu sekali untuk performa optimal.

### Perintah:
```bash
./scripts/maintenance.sh
```
Script ini akan:
- Mengosongkan file log yang membengkak.
- Menghapus image Docker yang tidak terpakai.
- Memperbarui cache Laravel untuk kecepatan maksimal.

---

## 🔍 3. Pemantauan (Monitoring)
Gunakan perintah berikut untuk memeriksa status portal:

### Cek Kesehatan Container:
```bash
docker compose ps
```
Pastikan status `Up (healthy)`.

### Cek Log Error:
```bash
# Log Sistem PHP
docker compose logs -f app

# Log Notifikasi Real-time
docker compose logs -f reverb
```

---

## 🚨 4. Pemecahan Masalah (Troubleshooting)

### Q: Aplikasi terasa lambat?
Jalankan: `bash scripts/maintenance.sh`

### Q: Peta Heatmap tidak muncul?
- Pastikan alumni sudah mengisi data kota/provinsi.
- Cek koneksi internet server untuk memuat Google/Leaflet Maps.

### Q: Notifikasi tidak masuk?
Pastikan container `steman_reverb` berjalan dan port 8080 terbuka di Firewall server.

---

## 🔄 5. Pembaruan (Update)
Jika ada pembaruan kode dari repositori:
```bash
git pull origin main
docker compose up -d --build
```

---

> _Keamanan data alumni adalah tanggung jawab bersama._
> **Ikatan Alumni SMKN 2 Ternate — Maintenance Group**
