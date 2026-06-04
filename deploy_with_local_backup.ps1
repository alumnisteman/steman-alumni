# -------------------------------------------------
# deploy_with_local_backup.ps1 – Deploy Steman Alumni
#   + restore backup 2 minggu terakhir (storage & database)
# -------------------------------------------------

# ==================== PREREQUISITES (di mesin Windows) ====================
# 1. PHP dan Composer sudah terpasang dan berada di PATH
# 2. plink dan scp (bagian dari paket PuTTY) tersedia
# 3. Anda memiliki akses SSH ke server:
#      user : root
#      host : 103.175.219.57
#      password : M4ruw4h3@   (untuk demo; gunakan key‑auth pada produksi)
# ===========================================================================

# ==================== KONFIGURASI ====================
$remoteUser   = "root"
$remoteHost   = "103.175.219.57"
$remotePass   = "M4ruw4h3@"
$remotePath   = "/var/www/steman"
$localBackup  = "D:\\THOY STEMAN FILE\\steman-alumni-v4.1\\steman-alumni\\storage\\backups\\archive\\app_backup_20260408_141723.tar.gz"
$localDbBackup= "D:\\THOY STEMAN FILE\\steman-alumni-v4.1\\steman-alumni\\storage\\backups\\archive\\db_backup_20260408_141631.sql.gz"
# ====================================================

function Remote-Run ($script) {
    $escaped = $script.Replace('"','\"')
    plink -ssh "${remoteUser}@${remoteHost}" -pw $remotePass "${escaped}"
}

# =============== STEP 1 : Transfer backup ke server ===============
Write-Host "=== STEP 1: Upload backup archive ke server ==="
scp -P 22 "${localBackup}" "${remoteUser}@${remoteHost}:/tmp/restore_backup.tar.gz"
if ($LASTEXITCODE -ne 0) { Write-Error "Gagal mengirim backup archive."; exit 1 }

# =============== STEP 2 : Extract backup di server ==============
Write-Host "=== STEP 2: Extract backup di server ==="
$extractCmd = @"
mkdir -p /tmp/restore_tmp
cd /tmp/restore_tmp
tar -xzf /tmp/restore_backup.tar.gz
"@
Remote-Run $extractCmd

# =============== STEP 3 : Restore database ======================
Write-Host "=== STEP 3: Upload dan restore database MySQL ==="
scp -P 22 "${localDbBackup}" "${remoteUser}@${remoteHost}:/tmp/db_backup.sql.gz"
$dbRestoreCmd = @"
gzip -d -c /tmp/db_backup.sql.gz | mysql -u root
"@
Remote-Run $dbRestoreCmd

# =============== STEP 4 : Deploy kode ke server ===============
Write-Host "=== STEP 4: Transfer kode Laravel ke server ==="
scp -r -P 22 . "${remoteUser}@${remoteHost}:${remotePath}"
if ($LASTEXITCODE -ne 0) { Write-Error "Gagal mengirim kode ke server."; exit 1 }

# =============== STEP 5 : Composer & Laravel commands ==========
Write-Host "=== STEP 5: Jalankan Composer & Artisan di server ==="
$deployCmd = @"
cd ${remotePath}
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Restore folder storage jika ada di backup
if [ -d /tmp/restore_tmp/storage ]; then
    cp -r /tmp/restore_tmp/storage/* ${remotePath}/storage/
fi

systemctl restart nginx
"@
Remote-Run $deployCmd

# =============== STEP 6 : Cleanup temporer di server ==========
Write-Host "=== STEP 6: Cleanup file temporer di server ==="
Remote-Run "rm -rf /tmp/restore_tmp /tmp/restore_backup.tar.gz /tmp/db_backup.sql.gz"

Write-Host "\nDeploy dan restore selesai! Silakan cek https://alumni-steman.my.id/ dan https://admin.alumni-steman.my.id/ untuk memastikan aplikasi berjalan dengan baik."
