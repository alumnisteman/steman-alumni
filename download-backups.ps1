# download-backups.ps1
# Usage: ./download-backups.ps1

$ServerIP = "103.175.219.57"
$Password = "M4ruw4h3@"
$RemoteDbPath = "/var/www/steman-alumni/backups/database/*.sql.gz"
$RemoteAppPath = "/var/www/steman-alumni-backups/*.tar.gz"
$LocalDir = "./backups_downloaded"

Write-Host "--- STARTING BACKUP RETRIEVAL ---" -ForegroundColor Cyan

# Ensure local directory exists
if (-not (Test-Path $LocalDir)) {
    New-Item -ItemType Directory -Path $LocalDir
}

Write-Host "[1/2] Downloading latest Database Snapshots..."
pscp -batch -pw $Password "root@$ServerIP`:$RemoteDbPath" "$LocalDir"

Write-Host "[2/2] Downloading latest Application Snapshots..."
pscp -batch -pw $Password "root@$ServerIP`:$RemoteAppPath" "$LocalDir"

if ($LASTEXITCODE -eq 0) {
    Write-Host "--- RETRIEVAL SUCCESSFUL ---" -ForegroundColor Green
    Write-Host "Backups are stored in: $(Get-Location)/backups_downloaded"
} else {
    Write-Host "ERROR: Retrieval failed. Please check connectivity or server paths." -ForegroundColor Red
}
