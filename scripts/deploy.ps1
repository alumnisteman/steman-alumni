# deploy.ps1 - Automated Deployment for Steman Alumni
# =============================================================================
#   Usage: .\deploy.ps1
#   Requirements: OpenSSH client (installed by default in Windows 10/11)
# =============================================================================

$REMOTE_USER = "root"
$REMOTE_HOST = "103.175.219.57"
$REMOTE_PATH = "/var/www/steman-alumni"
$ZIP_FILE = "steman_deploy.zip"

Write-Host "==============================================" -ForegroundColor Cyan
Write-Host "  STEMAN ALUMNI - SAFE DEPLOY V5 (HEALTH)" -ForegroundColor Cyan
Write-Host "==============================================" -ForegroundColor Cyan

# 0.1 Automatic IP Detection and .env sync
Write-Host "[1/8] Detecting Local IP and updating .env..." -ForegroundColor Yellow
if (Test-Path "update-ip.ps1") {
    .\update-ip.ps1
}

# 1. Cleaning up old files on local
if (Test-Path $ZIP_FILE) { Remove-Item $ZIP_FILE }

# 2. Local Builds (Node.js/NPM only)
Write-Host "[2/8] Running Local Frontend Build (Node.js)..." -ForegroundColor Yellow
try {
    npm install --silent
    npm run build
    Write-Host "  -> NPM Build Success." -ForegroundColor Green
} catch {
    Write-Host "  -> NPM Build skipped." -ForegroundColor Gray
}

# 3. Zipping files (Clean source)
Write-Host "[3/8] Zipping application source code..." -ForegroundColor Yellow
Compress-Archive -Path "app", "bootstrap", "config", "database", "public", "resources", "routes", "docker", ".env", "docker-compose.yml", "docker-compose.dev.yml", "docker-compose.prod.yml", "Dockerfile", "artisan", "composer.json", "package.json", "vite.config.js", "update-ip.ps1" -DestinationPath $ZIP_FILE

# 4. Uploading to Server
Write-Host "[4/8] Uploading Source to $REMOTE_HOST..." -ForegroundColor Yellow
Write-Host "(Mohon masukkan password jika diminta)" -ForegroundColor Gray
scp $ZIP_FILE "${REMOTE_USER}@${REMOTE_HOST}:/tmp/"

# 5. Remote Extraction & Rebuild
Write-Host "[5/8] Extracting and Building on server..." -ForegroundColor Yellow
$REMOTE_CMD = @"
mkdir -p $REMOTE_PATH
rm -rf $REMOTE_PATH/docker/nginx/conf.d/*
unzip -o /tmp/$ZIP_FILE -d $REMOTE_PATH
cd $REMOTE_PATH
docker compose -f docker-compose.prod.yml build app
docker compose -f docker-compose.prod.yml up -d
"@

ssh "${REMOTE_USER}@${REMOTE_HOST}" $REMOTE_CMD

# 6. Post-Deploy & Health Check
Write-Host "[6/8] Post-Deploy: Health Check, Cache & Settings..." -ForegroundColor Yellow
Start-Sleep -Seconds 5
ssh "${REMOTE_USER}@${REMOTE_HOST}" "docker exec steman_app php artisan config:clear; docker exec steman_app php artisan cache:clear; docker exec steman_app php artisan db:seed --class=SettingSeeder"

Write-Host "  -> Verifikasi Website http://$REMOTE_HOST:8000 ..." -ForegroundColor Cyan
try {
    $response = Invoke-WebRequest -Uri "http://$REMOTE_HOST:8000" -Method Get -TimeoutSec 10 -ErrorAction Stop
    $statusCode = $response.StatusCode
    if ($statusCode -eq 200) {
        Write-Host "  -> Website Berjalan Normal (HTTPS 200 OK)" -ForegroundColor Green
    } else {
        Write-Host "  -> Website Berstatus: $statusCode" -ForegroundColor Yellow
    }
} catch {
    Write-Host "  -> Website Belum Merespon / Ada Kendala! (Cek Logs Server)" -ForegroundColor Red
    $statusCode = 500
}

# 7. GitHub Synchronization (Conditional)
Write-Host "[7/8] Sinkronisasi GitHub (main branch)..." -ForegroundColor Yellow
if ($statusCode -eq 200) {
    $confirm = Read-Host "Server sudah siap & OK. PUSH ke GitHub sekarang? (Ketik 'Y' untuk Iya, lainnya untuk Lewati)"
    if ($confirm -eq "Y" -or $confirm -eq "y") {
        $gitStatus = git status --porcelain
        if ($gitStatus) {
            git add .
            git commit -m "Safe Auto-deploy: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
            git push origin main
            Write-Host "  -> GitHub Berhasil Disinkronkan." -ForegroundColor Green
        } else {
            Write-Host "  -> Tidak ada perubahan baru di Git." -ForegroundColor Gray
        }
    } else {
        Write-Host "  -> Sinkronisasi GitHub dilewati oleh User." -ForegroundColor Gray
    }
} else {
    Write-Host "  -> [WARNING] Skip Push GitHub karena Server Masih Bermasalah/Belum Hidup." -ForegroundColor Red
}

# 8. Cleanup
Write-Host "[8/8] Cleanup..." -ForegroundColor Yellow
Remove-Item $ZIP_FILE
ssh "${REMOTE_USER}@${REMOTE_HOST}" "rm /tmp/$ZIP_FILE"

Write-Host "==============================================" -ForegroundColor Green
Write-Host "  DEPLOY SELESAI! (DNGAN PROTEKSI GITHUB)" -ForegroundColor Green
Write-Host "==============================================" -ForegroundColor Green
