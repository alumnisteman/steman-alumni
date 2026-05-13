# STEMAN ALUMNI - ADVANCED DEPLOYMENT SCRIPT v6.0 (GUARDIAN EDITION)
# Features: Smart Sync, Cache Purge, System Audit, Zombie Folder Killer, Health Radar

param (
    [string]$LocalPath,
    [string]$RemotePath
)

$HostIP        = "103.175.219.57"
$Username      = "root"
$Password      = "M4ruw4h3@"
$ProjectRoot   = "/var/www/steman-alumni"
$ContainerName = "steman-alumni-app-1"

Write-Host "`n--- STARTING DEPLOYMENT v6.0 (GUARDIAN) ---" -ForegroundColor Cyan

# 0a. PRE-FLIGHT CHECK (STATIC ANALYSIS)
Write-Host "[0a/7] Running Pre-Flight Code Analysis..." -ForegroundColor Yellow
if (Test-Path "vendor\bin\phpstan") {
    .\vendor\bin\phpstan analyse --memory-limit=2G
    if ($LASTEXITCODE -ne 0) {
        Write-Host "  [CRITICAL] Code contains errors! Deployment aborted to prevent bugs." -ForegroundColor Red
        exit 1
    }
    Write-Host "  [OK] Static Analysis Passed." -ForegroundColor Green
} else {
    Write-Host "  [WARNING] PHPStan not found, skipping pre-flight check." -ForegroundColor Yellow
}

# 0. ZOMBIE FOLDER KILLER (Prevent duplication confusion)
Write-Host "[0/7] Purging local cache files..." -ForegroundColor Yellow
if (Test-Path "bootstrap/cache/*.php") {
    Remove-Item "bootstrap/cache/*.php" -Force
}
Write-Host "[0/7] Cleaning up Zombie Folders..." -ForegroundColor Yellow
plink -batch -no-antispoof -pw $Password "${Username}@${HostIP}" "rm -rf /home/steman/steman-alumni 2>/dev/null; rm -rf /root/steman-alumni 2>/dev/null; rm -rf ${ProjectRoot}/public/build"

# 1. SMART SYNC
if ($LocalPath -and $RemotePath) {
    Write-Host "[1/7] Smart Sync: $LocalPath -> $RemotePath" -ForegroundColor Yellow
    pscp -pw $Password -r $LocalPath "${Username}@${HostIP}:$RemotePath"
} else {
    Write-Host "[1/7] Syncing Project Core..." -ForegroundColor Yellow
    pscp -pw $Password -r app config routes resources database docker docker-compose.prod.yml .env "${Username}@${HostIP}:${ProjectRoot}/"
}

# 2. CACHE NUCLEAR OPTION
Write-Host "[2/7] Executing Cache Nuclear Option..." -ForegroundColor Yellow
plink -batch -no-antispoof -pw $Password "${Username}@${HostIP}" "docker exec $ContainerName rm -f bootstrap/cache/config.php; docker exec $ContainerName php artisan optimize:clear"

# 2b. REBUILD ROUTE + VIEW CACHE (prevents 'Route not defined' race condition)
Write-Host "[2b/7] Rebuilding route & view cache..." -ForegroundColor Yellow
plink -batch -no-antispoof -pw $Password "${Username}@${HostIP}" "docker exec -u 82 $ContainerName php artisan config:cache; docker exec -u 82 $ContainerName php artisan route:cache; docker exec -u 82 $ContainerName php artisan view:cache"

# 3. DATABASE RADAR & SENTINEL AUDIT
Write-Host "[3/7] Running Migrations & Sentinel Audit..." -ForegroundColor Yellow
plink -batch -no-antispoof -pw $Password "${Username}@${HostIP}" "docker exec $ContainerName php artisan migrate --force; docker exec $ContainerName php artisan sentinel:audit --fix"

# 4. DOCKER FORCE-SYNC (Volumes + Config)
Write-Host "[4/7] Synchronizing Docker Volumes..." -ForegroundColor Cyan
plink -batch -no-antispoof -pw $Password "${Username}@${HostIP}" "cd $ProjectRoot && docker compose -f docker-compose.prod.yml up -d --no-deps app queue reverb"

# 4b. PUSH COMPILED ASSETS INTO DOCKER VOLUME (pscp cannot reach named volumes)
Write-Host "[4b/7] Pushing compiled assets into Docker volume..." -ForegroundColor Cyan
pscp -pw $Password -r public/build "${Username}@${HostIP}:${ProjectRoot}/public/"
plink -batch -no-antispoof -pw $Password "${Username}@${HostIP}" "docker cp ${ProjectRoot}/public/build/. ${ContainerName}:/var/www/public/build/"

# 5. SYSTEM HEALTH AUDIT (The Physician)
Write-Host "[5/7] Running Deep System Audit..." -ForegroundColor Cyan
plink -batch -no-antispoof -pw $Password "${Username}@${HostIP}" "docker exec $ContainerName php artisan app:system-check"

# 6. PERMISSION HEALING & REBOOT
Write-Host "[6/7] Healing Permissions & Rebooting Nginx..." -ForegroundColor Yellow
plink -batch -no-antispoof -pw $Password "${Username}@${HostIP}" "docker exec $ContainerName chown -R 82:82 /var/www/storage /var/www/bootstrap/cache; docker exec $ContainerName chmod -R 775 /var/www/storage /var/www/bootstrap/cache; docker restart steman_nginx; docker exec -u 82 $ContainerName php artisan optimize"

# 7. HEALTH RADAR CHECK
Write-Host "[7/7] Verifying Live Status..." -ForegroundColor Cyan
Start-Sleep -Seconds 2
$PublicTest = curl.exe -s -o /dev/null -w "%{http_code}" "https://alumni-steman.my.id/"
if ($PublicTest -eq "200" -or $PublicTest -eq "302") {
    Write-Host "  [SUCCESS] Portal is Online (HTTP $PublicTest)" -ForegroundColor Green
} else {
    Write-Host "  [CRITICAL] Portal returned $PublicTest! Please check logs immediately." -ForegroundColor Red
    exit 1
}

Write-Host "--- DEPLOYMENT v6.0 COMPLETE ---" -ForegroundColor Cyan
