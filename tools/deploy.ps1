# ==============================================================
# STEMAN ALUMNI - WINDOWS DEPLOYMENT SCRIPT (v4.0 UNIFIED)
# Single Source of Truth - no .prod.yml variants
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
Write-Host " STEMAN ALUMNI - DEPLOYMENT GUARD V4.0 (UNIFIED)" -ForegroundColor Cyan
Write-Host "======================================================" -ForegroundColor Cyan

# --- 1. PRE-DEPLOYMENT BACKUP ---
if (-not $NoBackup -and -not $SkipUpload) {
    Write-Host "[0/4] Menyiapkan Backup Database..." -ForegroundColor Yellow
    $backupCmd = "docker exec steman_db mariadb-dump -u app_user -p'strongpassword' steman_alumni > $APP_DIR/storage/backups/pre_deploy_$(Get-Date -Format 'yyyyMMdd_HHmm').sql"
    & $PLINK -ssh -l $USER -pw $PASS -batch $SERVER "mkdir -p $APP_DIR/storage/backups && $backupCmd"
    Write-Host "      Backup selesai disimpan di server." -ForegroundColor Green
}

if ($MigrateOnly) {
    & $PLINK -ssh -l $USER -pw $PASS -batch $SERVER "docker exec app php artisan migrate --force"
    exit 0
}

if (-not $SkipUpload) {
    Write-Host "[1/4] Menyinkronkan file ke server..." -ForegroundColor Yellow
    
    # Upload directories
    $dirs = @("app", "resources", "routes", "config", "database", "public", "docker")
    foreach ($dir in $dirs) {
        & $PSCP -r -pw $PASS -batch "$LOCAL_DIR\$dir" "${USER}@${SERVER}:${APP_DIR}" | Out-Null
    }
    
    # Upload root files (CRITICAL: docker-compose.yml and Dockerfile MUST be synced)
    $files = @("composer.json", "composer.lock", "artisan", "deploy.sh", "docker-compose.yml", "Dockerfile", ".dockerignore")
    foreach ($file in $files) {
        if (Test-Path "$LOCAL_DIR\$file") {
            & $PSCP -pw $PASS -batch "$LOCAL_DIR\$file" "${USER}@${SERVER}:${APP_DIR}/" | Out-Null
        }
    }
    Write-Host "      Upload selesai." -ForegroundColor Green
}

Write-Host "[2/4] Menjalankan validasi dan rebuild di server..." -ForegroundColor Yellow
Write-Host "      (Mengecek sintaksis dan membangun image...)" -ForegroundColor Gray

# Run deploy.sh and stream output
& $PLINK -ssh -l $USER -pw $PASS -batch $SERVER "chmod +x $APP_DIR/deploy.sh && bash $APP_DIR/deploy.sh" 2>&1 | ForEach-Object {
    $line = $_
    if ($line -match "\[ERROR\]" -or $line -match "\[CRITICAL\]") {
        Write-Host "  $line" -ForegroundColor Red
    }
    elseif ($line -match "OK" -or $line -match "sukses" -or $line -match "DONE") {
        Write-Host "  $line" -ForegroundColor Green
    }
    else {
        Write-Host "  $line" -ForegroundColor Gray
    }
}

# --- 3. COMPREHENSIVE HEALTH CHECK ---
Write-Host "[3/4] Menjalankan Health Check komprehensif..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Check 1: HTTP response from main domain
$httpCode = & $PLINK -ssh -l $USER -pw $PASS -batch $SERVER "curl -s -o /dev/null -w '%{http_code}' --max-time 10 https://alumni-steman.my.id/"
$httpColor = if ($httpCode -eq "200" -or $httpCode -eq "302") { "Green" } else { "Red" }
Write-Host "      Portal HTTP: $httpCode" -ForegroundColor $httpColor

# Check 2: HTTP response from admin subdomain
$adminCode = & $PLINK -ssh -l $USER -pw $PASS -batch $SERVER "curl -s -o /dev/null -w '%{http_code}' --max-time 10 https://admin.alumni-steman.my.id/login"
$adminColor = if ($adminCode -eq "200" -or $adminCode -eq "302") { "Green" } else { "Red" }
Write-Host "      Admin HTTP: $adminCode" -ForegroundColor $adminColor

# Check 3: Container status (detect crash-loops)
$containerStatus = & $PLINK -ssh -l $USER -pw $PASS -batch $SERVER "docker ps --format '{{.Names}} {{.Status}}' | grep -E 'Restarting|Exited'; echo DONE"
if ($containerStatus -match "DONE" -and $containerStatus.Trim() -eq "DONE") {
    Write-Host "      Containers: All healthy" -ForegroundColor Green
} else {
    Write-Host "      WARNING: Unhealthy containers detected" -ForegroundColor Red
    Write-Host "      $containerStatus" -ForegroundColor Red
}

# Check 4: Nginx specifically
$nginxStatus = & $PLINK -ssh -l $USER -pw $PASS -batch $SERVER "docker inspect steman_nginx --format '{{.State.Status}}'"
$nginxColor = if ($nginxStatus -match "running") { "Green" } else { "Red" }
Write-Host "      Nginx: $nginxStatus" -ForegroundColor $nginxColor

# --- 4. FINAL SUMMARY ---
Write-Host ""
Write-Host "[4/4] Deployment Summary" -ForegroundColor Yellow
if (($httpCode -eq "200" -or $httpCode -eq "302") -and $nginxStatus -match "running") {
    Write-Host "      DEPLOYMENT SEMPURNA - Portal dan Admin aktif" -ForegroundColor Green
} elseif ($httpCode -eq "200" -or $httpCode -eq "302") {
    Write-Host "      PARTIAL - Portal up tapi Nginx memerlukan perhatian" -ForegroundColor Yellow
} else {
    Write-Host "      DEPLOYMENT BERMASALAH - HTTP Code: $httpCode" -ForegroundColor Red
    Write-Host "      Periksa log: docker logs steman_nginx --tail 20" -ForegroundColor Red
}

Write-Host ""
Write-Host "======================================================" -ForegroundColor Cyan
Write-Host " VERIFIKASI SELESAI" -ForegroundColor Gray
Write-Host "======================================================" -ForegroundColor Cyan
