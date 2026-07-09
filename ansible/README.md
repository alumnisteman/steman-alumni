# Ansible Deployment — Steman Alumni

Playbook ini melakukan provisioning server dari kondisi kosong (fresh Ubuntu)
sampai aplikasi Laravel "Alumni STEMAN" jalan di production menggunakan Docker,
mengikuti struktur yang sudah ada di repo ini (`Dockerfile.prod`,
`docker-compose.prod.yml`, Nginx + Certbot, MariaDB, Redis, Reverb, Meilisearch).

## ⚠️ Peringatan keamanan sebelum mulai

1. **Ganti password root VPS sekarang.** Password sempat dikirim polos di chat —
   anggap sudah bocor dan segera diganti (`passwd` di server, atau lewat panel VPS).
2. **`scripts/production.env` di repo ini berisi APP_KEY dan DB_PASSWORD asli dalam
   bentuk plaintext, ter-commit ke git.** Ini bocor kredensial. Sebaiknya:
   - Hapus file itu dari repo (`git rm scripts/production.env`) dan tambahkan ke `.gitignore`.
   - Generate APP_KEY baru dan ganti semua password yang sempat tersimpan di sana.
3. Gunakan SSH key, bukan password, untuk akses server jika memungkinkan.

## Struktur

```
ansible/
├── ansible.cfg
├── site.yml                     # playbook utama
├── requirements.yml              # collection yang dibutuhkan
├── inventories/production/
│   ├── hosts.ini                 # daftar server target
│   └── group_vars/
│       ├── all.yml               # variabel non-sensitif
│       └── vault.yml.example     # contoh variabel sensitif (salin -> vault.yml -> encrypt)
└── roles/
    ├── common/    # update sistem, timezone, swap, paket dasar
    ├── firewall/  # UFW + fail2ban
    ├── docker/    # install Docker Engine + Compose plugin
    └── app/       # clone repo, .env, build image, docker compose up, migrate, SSL
```

## Persiapan (sekali saja)

```bash
cd ansible

# 1. Install Ansible di komputer/Replit yang menjalankan playbook (BUKAN di server target)
pip install --user ansible

# 2. Install collection yang dibutuhkan
ansible-galaxy collection install -r requirements.yml

# 3. Siapkan vault (variabel sensitif)
cp inventories/production/group_vars/vault.yml.example inventories/production/group_vars/vault.yml
# isi semua nilai GANTI_... di vault.yml dengan nilai asli, lalu enkripsi:
echo "PASSWORD_VAULT_ANDA" > .vault_pass.txt
chmod 600 .vault_pass.txt
ansible-vault encrypt inventories/production/group_vars/vault.yml --vault-password-file .vault_pass.txt

# 4. Isi variabel non-sensitif
#    - inventories/production/group_vars/all.yml -> git_repo_url, letsencrypt_email
#    - inventories/production/hosts.ini -> IP server sudah terisi (103.175.219.57)

# 5. Pastikan SSH key Anda sudah ada di server (ssh-copy-id root@103.175.219.57)
#    atau siapkan akses password lewat --ask-pass (kurang direkomendasikan untuk root).
```

## Menjalankan playbook

Uji dulu tanpa eksekusi nyata (dry-run):

```bash
ansible-playbook site.yml --check --diff
```

Jika sudah yakin, jalankan sungguhan:

```bash
ansible-playbook site.yml
```

Jika akses masih pakai password SSH (bukan key):

```bash
ansible-playbook site.yml --ask-pass --ask-become-pass
```

## Yang dilakukan playbook, langkah demi langkah

1. **common** — update apt, install paket dasar (git, ufw, fail2ban, dll), set timezone Asia/Jakarta, siapkan swap 1GB untuk VPS kecil.
2. **firewall** — buka port 22/80/443 di UFW, aktifkan UFW + fail2ban untuk proteksi brute-force SSH.
3. **docker** — install Docker Engine & Docker Compose plugin resmi dari repo Docker.
4. **app** —
   - clone/pull kode dari `git_repo_url` ke `/var/www/steman-alumni`
   - generate `.env` production dari vault (APP_KEY, DB, mail, OAuth, dll — tidak ada yang hardcoded)
   - `composer install --no-dev` lewat container sementara
   - `docker compose -f docker-compose.prod.yml up -d --build` (app, queue, reverb, redis, db, nginx, certbot, meilisearch, prometheus)
   - jalankan migrasi (`php artisan migrate --force`)
   - cache config/route/view, `storage:link`
   - request sertifikat SSL Let's Encrypt lewat certbot, reload nginx

## Setelah deploy pertama

- Cek `https://alumni-steman.my.id/health` harus mengembalikan `healthy`.
- Untuk deploy update berikutnya, cukup jalankan ulang `ansible-playbook site.yml`
  (semua task idempotent — aman dijalankan berkali-kali) atau gunakan
  `docker/scripts/deploy-prod.sh` yang sudah ada di repo untuk update cepat tanpa provisioning ulang.

## Catatan

- Playbook ini idempotent: aman dijalankan ulang, tidak akan merusak instalasi yang sudah ada.
- Jika server yang dituju **bukan server kosong** (sudah ada layanan lain jalan di port 80/443),
  sesuaikan dulu `firewall_allowed_tcp_ports` dan cek konflik port sebelum menjalankan role `app`.
