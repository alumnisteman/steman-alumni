# Panduan Deployment Otomatis ke VPS / Cloud Hosting (v6 API-Ready)

Dokumen ini menjelaskan langkah-langkah untuk mengubah sistem **CI/CD Localhost** (menggunakan *self-hosted runner*) menjadi **Full Auto-Deploy ke VPS Production**.

## Prasyarat Server (VPS)
1. **Sistem Operasi**: Ubuntu 22.04 LTS atau sejenisnya.
2. **Koneksi SSH**: Server harus dapat diakses melalui SSH menggunakan *Private Key* (bukan *password* teks biasa).
3. **Docker & Docker Compose**: Sudah harus terinstall di VPS Anda.

---

## Langkah 1: Persiapan Repositori GitHub
Agar GitHub Actions dapat mengakses VPS secara aman tanpa menyimpan kredensial di *source code*, Anda wajib mengatur **GitHub Secrets**.
1. Buka Repositori GitHub Anda, klik tab **Settings**.
2. Masuk ke **Secrets and variables** > **Actions**.
3. Klik tombol **New repository secret** untuk setiap item di bawah ini:

| Nama Secret | Contoh Nilai | Deskripsi |
| --- | --- | --- |
| `SERVER_HOST` | `103.250.xxx.xxx` | IP Address Publik VPS Anda atau nama domain (jika sudah diarahkan). |
| `SERVER_USER` | `root` atau `ubuntu` | Username login SSH VPS (disarankan `root` atau *user* yang memiliki hak akses `docker`). |
| `SERVER_PORT` | `22` | Port SSH server Anda. |
| `SERVER_SSH_KEY` | `-----BEGIN OPENSSH PRIVATE KEY----- ...` | Konten Private Key (`~/.ssh/id_rsa` atau file `.pem`) untuk otentikasi. Pastikan key ini *passwordless*. |

---

## Langkah 2: Memperbarui Skrip Deployment (`deploy.yml`)
Buka file `.github/workflows/deploy.yml` dan gantilah seluruh isinya menjadi seperti di bawah ini untuk mengaktifkan Remote Deployment:

```yaml
name: Steman Alumni Auto-Deploy VPS

on:
  push:
    branches: [ main, master ]

jobs:
  deploy:
    name: Deploy to SSH Server
    runs-on: ubuntu-latest
    
    steps:
      - name: Executing Remote Deployment
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SERVER_SSH_KEY }}
          port: ${{ secrets.SERVER_PORT }}
          # Menyesuaikan dengan path project di VPS Anda (contoh di /var/www/steman-alumni)
          script: |
            cd /opt/steman-alumni || exit 1
            git config --global --add safe.directory /opt/steman-alumni
            git pull origin main
            
            # Rebuild Images and restart containers
            docker compose -f docker-compose.prod.yml build
            docker compose -f docker-compose.prod.yml up -d --scale app=3
            
            # Eksekusi Command Utama Laravel
            docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
            docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
            docker compose -f docker-compose.prod.yml exec -T app php artisan route:clear
            docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache
```

> **Catatan Path Server (`cd /opt/steman-alumni`)**:
> Script di atas berasumsi bahwa Anda telah men-_clone_ *repository* ke dalam folder `/opt/steman-alumni` di VPS Anda. Jika diletakkan di folder lain seperti `/var/www/html/steman`, silakan ubah pada dua baris dengan tulisan `/opt/steman-alumni`.

---

## Langkah 3: Setup Awal pada VPS (Satu Kali Saja)

Sebelum menjalankan Auto-Deploy pertama kali, silakan login ke VPS menggunakan *Terminal/Putty* dan lakukan *clone repository* awal. Contoh:
```bash
# Login ke server
ssh root@<IP_VPS_ANDA>

# Pindah ke direktori instalasi
cd /opt

# Clone repository Private/Public GitHub Anda
# Pastikan Anda sudah setting SSH Key atau *Personal Access Token* di server agar bisa pull.
git clone git@github.com:alumnisteman/steman-alumni.git

# Menjalankan Docker pertama kali dan inisiasi Let's Encrypt / Certbot jika aktif
cd steman-alumni
docker compose -f docker-compose.prod.yml up -d --build
```

Setelah `git clone` pertama kali ini berhasil dan Docker berjalan, setiap kali ada **Commit ke Cabang Main**, GitHub Action otomatis akan melakukan `git pull` dan me-*restart* *container* produksi secara aman. Migrasi database dan optimasi API akan berjalan otomatis.
