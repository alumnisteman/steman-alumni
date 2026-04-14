# 🛠️ MANUAL MAINTENANCE: Steman Alumni Portal (v4.0)

Sistem sekarang menggunakan **Steman Management CLI** untuk mempermudah perawatan. Anda tidak perlu lagi menghafal perintah Docker yang panjang.

---

## 🚀 1. Perintah Cepat (Server CLI)
Masuk ke terminal server (`/var/www/steman-alumni`) dan gunakan perintah berikut:

| Perintah | Fungsi |
| :------- | :----- |
| `./steman-cli.sh restart` | Merestart seluruh sistem jika ada kendala koneksi. |
| `./steman-cli.sh clean` | Membersihkan cache Laravel (gunakan jika ada perubahan UI yang tidak muncul). |
| `./steman-cli.sh logs` | Melihat log error secara real-time. |
| `./steman-cli.sh ssl-fix` | Memaksa pembaruan sertifikat SSL dan reload Nginx. |
| `./steman-cli.sh update` | Melakukan Full Update (Git Pull + Migrate + Restart). |
| `./steman-cli.sh db-shell` | Masuk ke terminal database MariaDB. |

---

## 📤 2. Update Aplikasi (dari Laptop Lokal)
Selalu gunakan skrip PowerShell untuk update agar sinkronisasi file tetap terjaga:

```powershell
# Untuk Update File Spesifik (Cepat)
./deploy-prod.ps1 -LocalPath "app/Models/User.php" -RemotePath "/var/www/steman-alumni/app/Models/User.php"

# Untuk Full Sync Folder Core (App, Config, Routes, Resources)
./deploy-prod.ps1
```

---

## 🔒 3. Keamanan & SSL
- **SSL Auto-Renewal**: Sudah terpasang secara otomatis. Jika muncul peringatan "Connection is not private", jalankan `./steman-cli.sh ssl-fix`.
- **Database Security**: Database sekarang terlindungi password penuh (tidak lagi menggunakan mode unlocked).

---

## 💾 4. Backup Data
Data Anda sangat berharga. Lakukan backup mingguan dengan menjalankan skrip ini dari laptop Anda:
```powershell
./download-backups.ps1
```

---
> [!IMPORTANT]
> **Hanya gunakan folder `/var/www/steman-alumni`**. Folder lama di `/opt/steman-alumni` sudah dihapus untuk menghindari konflik versi kode.
