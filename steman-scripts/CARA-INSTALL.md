# Cara Install Self-Healing Infrastructure steman-alumni

## Langkah-langkah di Server

### 1. Upload semua file ke server

Via SCP dari komputer lokal:
```bash
scp -r steman-scripts/ root@IP_SERVER:/tmp/
```

Atau copy manual satu per satu via panel VPS.

### 2. Masuk ke server

```bash
ssh root@IP_SERVER
```

### 3. Pindah ke direktori upload

```bash
cd /tmp/steman-scripts
```

### 4. Salin docker-compose.yml ke project

```bash
cp docker-compose.yml /var/www/steman-alumni/docker-compose.yml
```

### 5. Jalankan setup otomatis

```bash
bash setup-cron.sh
```

Selesai! Script akan otomatis:
- Menyalin semua script ke `/usr/local/bin/`
- Memberikan izin eksekusi
- Memasang cron jobs

---

## Struktur Direktori Releases (untuk Auto Rollback)

```
/var/www/
├── current -> /var/www/releases/release-003  (symlink aktif)
└── releases/
    ├── release-001/
    ├── release-002/
    └── release-003/
```

Buat struktur ini:
```bash
mkdir -p /var/www/releases
mkdir -p /var/www/releases/release-001
cp -r /var/www/steman-alumni/* /var/www/releases/release-001/
ln -sfn /var/www/releases/release-001 /var/www/current
```

---

## Cek Log

```bash
# Log auto-repair
tail -f /var/log/steman-heal.log

# Log backup
tail -f /var/log/steman-backup.log
```

---

## Test Manual

```bash
# Test auto-repair
/usr/local/bin/steman-heal.sh

# Test backup
/usr/local/bin/backup.sh

# Test rollback
/usr/local/bin/steman-rollback.sh
```

---

## Ganti Password Database

Edit file `.env` di project:
```
DB_ROOT_PASSWORD=password_baru_anda
DB_PASSWORD=password_baru_anda
```

Lalu restart:
```bash
cd /var/www/steman-alumni
docker compose down
docker compose up -d
```
