# deploy.ps1 - Automated Deployment for Steman Alumni
# =============================================================================
#   Usage: .\deploy.ps1
#   Requirements: OpenSSH client (installed by default in Windows 10/11)
# =============================================================================

$REMOTE_USER = "root"
$REMOTE_HOST = "192.168.1.5"
$REMOTE_PATH = "/var/www/steman-alumni"
$ZIP_FILE = "steman_deploy.zip"

Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "  STEMAN ALUMNI - AUTOMATED DEPLOY V5" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan

# 1. Cleaning up old files on local
if (Test-Path $ZIP_FILE) { Remove-Item $ZIP_FILE }

# 2. Local Builds (Node.js/NPM only)
Write-Host "[1/5] Running Local Frontend Build (Node.js)..." -ForegroundColor Yellow
try {
    npm install --silent
    npm run build
    Write-Host "  -> NPM Build Success." -ForegroundColor Green
} catch {
    Write-Host "  -> NPM Build skipped or failed. (Docker will handle it if needed)." -ForegroundColor Gray
}

# 3. Zipping files (Clean source)
# 3. Zipping files (Clean source)
Write-Host "[2/5] Zipping application source code..." -ForegroundColor Yellow
$EXCLUDES = @("node_modules", "vendor", $ZIP_FILE)
# Simple zip of the current directory (including heavy stuff)
Compress-Archive -Path "app", "bootstrap", "config", "database", "public", "resources", "routes", "docker", ".env", "docker-compose.yml", "docker-compose.dev.yml", "docker-compose.prod.yml", "Dockerfile", "artisan", "composer.json", "package.json", "vite.config.js" -DestinationPath $ZIP_FILE

# 4. Uploading to Server
Write-Host "[3/5] Uploading Source to $REMOTE_HOST..." -ForegroundColor Yellow
Write-Host "(Mohon masukkan password '1' jika diminta)" -ForegroundColor Gray
scp $ZIP_FILE "${REMOTE_USER}@${REMOTE_HOST}:/tmp/"

# 5. Remote Extraction & Rebuild (Ultra Resilient)
Write-Host "[4/5] Extracting and Building on server (Safe Mode)..." -ForegroundColor Yellow
$REMOTE_CMD = @"
mkdir -p $REMOTE_PATH
# Clean old Nginx config before unzip
rm -rf $REMOTE_PATH/docker/nginx/conf.d/*
unzip -o /tmp/$ZIP_FILE -d $REMOTE_PATH
cd $REMOTE_PATH
# Rebuild with new resilient Dockerfile
docker compose -f docker-compose.prod.yml up -d --build
"@

ssh "${REMOTE_USER}@${REMOTE_HOST}" $REMOTE_CMD

# 6. Cleanup
Write-Host "[5/5] Cleanup..." -ForegroundColor Yellow
Remove-Item $ZIP_FILE
ssh "${REMOTE_USER}@${REMOTE_HOST}" "rm /tmp/$ZIP_FILE"

Write-Host "=============================================" -ForegroundColor Green
Write-Host "  DEPLOY SELESAI! SILAKAN CEK DASHBOARD" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green
