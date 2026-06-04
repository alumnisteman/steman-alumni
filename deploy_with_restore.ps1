# -------------------------------------------------
# deploy_with_restore.ps1 – Deploy Steman Alumni with full restore (incl. 2‑week backup)
# -------------------------------------------------
# Prerequisites (on this Windows machine)
#   • PHP (compatible version) is installed and in PATH
#   • Composer is installed and in PATH
#   • plink (PuTTY) and scp are available (installed with PuTTY)
#   • A backup archive (including DB dump & storage) from ~2 weeks ago is present locally
#     e.g. C:\backups\steman_backup_20260602.tar.gz
# -------------------------------------------------
# CONFIGURATION -------------------------------------------------
$remoteUser = "root"
$remoteHost = "103.175.219.57"
$remotePass = "M4ruw4h3@"   # WARNING: consider using key‑auth instead of plaintext password
$remotePath = "/var/www/steman"
# Path to the local backup tar.gz (must contain backup.sql and storage.tar.gz)
$localBackup = "C:\\backups\\steman_backup_2weeks.tar.gz"
# -------------------------------------------------
# Helper to run remote commands via plink
function Remote-Run($script) {
    $escaped = $script.Replace('"','\"')
    plink -ssh $remoteUser@$remoteHost -pw $remotePass "${escaped}"
}
# -------------------------------------------------
Write-Host "=== STEP 1: Transfer backup archive to server ==="
scp -P 22 "$localBackup" $remoteUser@$remoteHost:/tmp/restore_backup.tar.gz
if ($LASTEXITCODE -ne 0) { Write-Error "Failed to copy backup file"; exit 1 }

Write-Host "=== STEP 2: Extract backup on server ==="
$extractCmd = @"
mkdir -p /tmp/restore_temp
cd /tmp/restore_temp
tar -xzf /tmp/restore_backup.tar.gz
"@
Remote-Run $extractCmd

Write-Host "=== STEP 3: Restore MySQL database ==="
# Assumes the backup tar contains backup.sql (full dump of all DBs)
$dbRestoreCmd = "mysql -u root < /tmp/restore_temp/backup.sql"
Remote-Run $dbRestoreCmd

Write-Host "=== STEP 4: Deploy code to server ==="
# Transfer current project files (exclude .git, node_modules, etc.)
scp -r -P 22 . $remoteUser@$remoteHost:$remotePath
if ($LASTEXITCODE -ne 0) { Write-Error "Failed to copy code"; exit 1 }

Write-Host "=== STEP 5: Run Composer & Laravel commands on server ==="
$deployCmd = @"
cd $remotePath
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
# Restore uploaded files if present in backup
if [ -f /tmp/restore_temp/storage.tar.gz ]; then
  tar -xzf /tmp/restore_temp/storage.tar.gz -C $remotePath
fi
systemctl restart nginx
"@
Remote-Run $deployCmd

Write-Host "=== CLEANUP: Remove temporary files on server ==="
Remote-Run "rm -rf /tmp/restore_temp /tmp/restore_backup.tar.gz"

Write-Host "Deployment & restore completed. Verify https://alumni-steman.my.id/"
