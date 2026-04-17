# ==============================================================
# STEMAN ALUMNI - WINDOWS DEPLOYMENT SCRIPT (v3.0)
# ==============================================================

param(
    [switch]$SkipUpload,
    [switch]$MigrateOnly,
    [switch]$NoBackup
)

$SERVER    = "103.175.219.57"
$USER      = "root"
$PASS      = "M4ruw4h3@"
$APP_DIR   = "/var/www/steman-alumni"
$LOCAL_DIR = $PSScriptRoot
$PLINK     = "C:\Program Files\PuTTY\plink.exe"
$PSCP      = "C:\Program Files\PuTTY\pscp.exe"

Write-Host "======================================================" -ForegroundColor Cyan
Write-Host " STEMAN ALUMNI - DEPLOYMENT GUARD V3.0 (HARDENED)" -ForegroundColor Cyan
Write-Host "======================================================" -ForegroundColor Cyan

# --- 1. PRE-DEPLOYMENT BACKUP ---
if (-not $NoBackup -and -not $SkipUpload) {
    Write-Host "[0/3] Menyiapkan Backup Database..." -ForegroundColor Yellow
    $backupCmd = "docker exec steman_db mariadb-dump -u app_user -p'strongpassword' steman_alumni > $APP_DIR/storage/backups/pre_deploy_$(Get-Date -Format 'yyyyMMdd_HHmm').sql"
    & $PLINK -ssh -l $USER -pw $PASS -batch $SERVER "mkdir -p $APP_DIR/storage/backups && $backupCmd"
    Write-Host "      Backup selesai disimpan di server." -ForegroundColor Green
}

if ($MigrateOnly) {
    & $PLINK -ssh -l $USER -pw $PASS -batch $SERVER "docker exec steman-alumni-app-1 php artisan migrate --force"
    exit 0
}

if (-not $SkipUpload) {
    Write-Host "[1/3] Menyinkronkan file ke server..." -ForegroundColor Yellow
    
    $dirs = @("app", "resources", "routes", "config", "database")
    foreach ($dir in $dirs) {
        & $PSCP -r -pw $PASS -batch "$LOCAL_DIR\$dir" "${USER}@${SERVER}:${APP_DIR}" | Out-Null
    }
    
    $files = @("composer.json", "composer.lock", "artisan", "deploy.sh")
    foreach ($file in $files) {
        if (Test-Path "$LOCAL_DIR\$file") {
            & $PSCP -pw $PASS -batch "$LOCAL_DIR\$file" "${USER}@${SERVER}:${APP_DIR}/" | Out-Null
        }
    }
    Write-Host "      Upload selesai." -ForegroundColor Green
}

Write-Host "[2/3] Menjalankan validasi dan rebuild di server..." -ForegroundColor Yellow
Write-Host "      (Mengecek sintaksis & membangun image...)" -ForegroundColor Gray

# Jalankan deploy.sh dan tampilkan output secara real-time
& $PLINK -ssh -l $USER -pw $PASS -batch $SERVER "chmod +x $APP_DIR/deploy.sh && bash $APP_DIR/deploy.sh" 2>&1 | ForEach-Object {
    if ($_ -match "\[ERROR\]") {
        Write-Host "  FAILED: $_" -ForegroundColor Red
    } elseif ($_ -match "OK" -or $_ -match "sukses" -or $_ -match "DONE") {
        Write-Host "  $_" -ForegroundColor Green
    } else {
        Write-Host "  $_" -ForegroundColor Gray
    }
}

Write-Host "[3/3] Menjalankan Health Check pasca-deployment..." -ForegroundColor Yellow
Start-Sleep -Seconds 10
$response = & $PLINK -ssh -l $USER -pw $PASS -batch $SERVER "curl -s -o /dev/null -w '%{http_code}' https://alumni-steman.my.id/"
if ($response -eq "200" -or $response -eq "302") {
    Write-Host "      Website UP ($response). Deployment Sempurna!" -ForegroundColor Green
} else {
    Write-Host "      WARNING: Website mengembalikan code $response. Periksa log segera!" -ForegroundColor Red
}

Write-Host ""
Write-Host "======================================================" -ForegroundColor Cyan
Write-Host " VERIFIKASI SELESAI" -ForegroundColor Gray
Write-Host "======================================================" -ForegroundColor Cyan
