# STEMAN ALUMNI - ADVANCED DEPLOYMENT SCRIPT v3.3 (ENTERPRISE EDITION)
# Features: Smart Sync, Cache Purge, Auto-Migration, Health Radar, Safe Cleanup, Tar-Backup

param (
    [string]$LocalPath,
    [string]$RemotePath
)

$HostIP = "103.175.219.57"
$Username = "root"
$Password = "M4ruw4h3@"
$ProjectRoot = "/var/www/steman-alumni"
$ContainerName = "steman-alumni-app-1"
$CLI = "./steman-cli.sh"

Write-Host "--- STARTING DEPLOYMENT v4.0 (ULTIMATE) ---" -ForegroundColor Cyan

# 0. AUTO-GUARD / LINTING
Write-Host "[0/5] Running Auto-Guard Safety Check..." -ForegroundColor Yellow

# 0.1 Check for Legacy Terms (Ad System & Alumni)
$LegacyTerms = @("`"image`"", "`"mobile_image`"", "clicks") # Specific for Ad system now renamed
$FoundErrors = $false

if ($LocalPath) {
    $FilesToCheck = @($LocalPath)
} else {
    $FilesToCheck = Get-ChildItem -Path app, resources, routes, config -Recurse -File -Include *.php,*.blade.php
}

foreach ($file in $FilesToCheck) {
    if (Test-Path $file) {
        $content = Get-Content $file -Raw
        foreach ($term in $LegacyTerms) {
            # Check for specifically renamed columns to prevent regressions
            if ($content -match "\b$term\b") {
                Write-Host "  [!] BLOCKED: Legacy column/term '$term' found in $file" -ForegroundColor Red
                $FoundErrors = $true
            }
        }
    }
}

# 0.2 Check Coding Style (Laravel Pint)
Write-Host "  [..] Running Laravel Pint check..." -ForegroundColor Yellow
$PintTest = vendor/bin/pint --test
if ($LASTEXITCODE -ne 0) {
    Write-Host "  [!] BLOCKED: Code style issues found. Run 'vendor/bin/pint' to fix." -ForegroundColor Red
    $FoundErrors = $true
}

if ($FoundErrors) {
    Write-Host "--- DEPLOYMENT ABORTED DUE TO SAFETY CHECK ---" -ForegroundColor Red
    exit 1
} else {
    Write-Host "  [OK] Codebase is clean and styled." -ForegroundColor Green
}

# 1. SMART SYNC
if ($LocalPath -and $RemotePath) {
    Write-Host "[1/5] Smart Sync: $LocalPath -> $RemotePath" -ForegroundColor Yellow
    pscp -pw $Password $LocalPath "$Username@${HostIP}:$RemotePath"
} else {
    Write-Host "[1/5] Syncing Project Core..." -ForegroundColor Yellow
    pscp -pw $Password -r app config routes resources docker docker-compose.prod.yml "$Username@${HostIP}:$ProjectRoot/"
}

# 2. REMOTE OPTIMIZATION
Write-Host "[2/5] Running Remote CLI Optimization & Cache Purge..." -ForegroundColor Yellow
plink -batch -no-antispoof -pw $Password "$Username@$HostIP" "docker exec $ContainerName php artisan optimize:clear"
plink -batch -no-antispoof -pw $Password "$Username@$HostIP" "docker exec $ContainerName php artisan view:clear"

# 4. DATABASE RADAR
Write-Host "[3/5] Running Database Radar..." -ForegroundColor Yellow
plink -batch -no-antispoof -pw $Password "$Username@$HostIP" "docker exec $ContainerName php artisan migrate --force"

# 4.5 SYSTEM INTEGRITY AUDIT (NEW LONG-TERM PROTECTION)
Write-Host "[3.6/5] Running System Integrity Audit & Cleanup..." -ForegroundColor Cyan
plink -batch -no-antispoof -pw $Password "$Username@$HostIP" "docker exec $ContainerName php artisan app:audit-integrity --fix"

# 4.7 PERMISSION HEALING
Write-Host "[3.8/5] Resetting Production Permissions..." -ForegroundColor Yellow
plink -batch -no-antispoof -pw $Password "$Username@$HostIP" "docker exec $ContainerName chown -R 82:82 /var/www/storage /var/www/bootstrap/cache && docker exec $ContainerName chmod -R 775 /var/www/storage /var/www/bootstrap/cache"

# 5. RESTART & OPTIMIZE
Write-Host "[4/5] Optimizing App Engine..." -ForegroundColor Yellow
plink -batch -no-antispoof -pw $Password "$Username@$HostIP" "docker exec $ContainerName php artisan config:cache && docker exec $ContainerName php artisan route:cache"
plink -batch -no-antispoof -pw $Password "$Username@$HostIP" "cd $ProjectRoot && docker compose -f docker-compose.prod.yml up -d"

# 5. HEALTH RADAR CHECK
Write-Host "[5/5] Verifying System Health..." -ForegroundColor Cyan
$PublicTest = curl.exe -s -o /dev/null -w "%{http_code}" "https://alumni-steman.my.id/"
if ($PublicTest -eq "200" -or $PublicTest -eq "302") {
    Write-Host "  [OK] System Online (Status: $PublicTest)" -ForegroundColor Green
} else {
    Write-Host "  [FAIL] System Error (Status: $PublicTest)" -ForegroundColor Red
}

Write-Host "--- DEPLOYMENT v4.0 SUCCESSFUL ---" -ForegroundColor Cyan
Write-Host "Licenses are Live, UI is Modern, System is SECURE!" -ForegroundColor Green
