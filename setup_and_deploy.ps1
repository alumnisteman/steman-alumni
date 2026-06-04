# -------------------------------------------------
# setup_and_deploy.ps1  –  otomatisasi deploy Steman Alumni (final stable)
# -------------------------------------------------

# 1. & 2. PHP dan Composer sudah terinstal di sistem
$composerExe = "composer"
Write-Host "Versi Composer:"
& $composerExe -V

# 3. Pindah ke folder proyek Laravel
Set-Location "D:\STM\steman-alumni"

# 4. Install semua dependensi via Composer
Write-Host "Menjalankan composer install..."
& $composerExe install --no-interaction --prefer-dist

# 5. Pastikan paket Carbon ada (jika belum)
Write-Host "Menambahkan paket Carbon..."
& $composerExe require nesbot/carbon

# 6. Regenerasi autoload
Write-Host "Regenerasi autoload..."
& $composerExe dump-autoload

# 7. Uji perintah Artisan (logs:clean)
Write-Host "Menguji perintah Artisan logs:clean..."
php artisan logs:clean

# 8. Tes koneksi SSH ke server (plink)
Write-Host "Menguji koneksi SSH ke server..."
plink -ssh root@103.175.219.57 -pw M4ruw4h3@ "echo Connected via plink"

# 9. Backup data & UI pada server (opsional)
$remoteBackupDir = "/tmp/steman_backup"
$remoteCmdBackup = @"
mkdir -p $remoteBackupDir
mysqldump -u root --all-databases > $remoteBackupDir/backup.sql
if [ -d /var/www/steman/storage ]; then
  tar -czf $remoteBackupDir/storage.tar.gz /var/www/steman/storage
fi
"@
Write-Host "Membuat backup data di server..."
plink -ssh root@103.175.219.57 -pw M4ruw4h3@ "$remoteCmdBackup"

# 10. Transfer kode ke server
$remotePath = "/var/www/steman"
Write-Host "Menyalin kode ke server..."
scp -r . root@103.175.219.57:$remotePath

# 11. Jalankan perintah di server untuk instalasi Composer, Laravel, dan restore data/UI
$remoteCmdDeploy = @"
cd $remotePath
php composer.phar install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
if [ -f /tmp/steman_backup/storage.tar.gz ]; then
  tar -xzf /tmp/steman_backup/storage.tar.gz -C $remotePath
fi
systemctl restart nginx
"@
Write-Host "Menjalankan perintah deploy di server..."
plink -ssh root@103.175.219.57 -pw M4ruw4h3@ "$remoteCmdDeploy"

Write-Host "Selesai! Periksa https://alumni-steman.my.id/ untuk memastikan tidak ada lagi HTTP ERROR 521."
