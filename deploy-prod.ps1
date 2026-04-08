# deploy-prod.ps1
# Usage: ./deploy-prod.ps1 -LocalPath "resources/views/admin/dashboard.blade.php" -RemotePath "/var/www/resources/views/admin/dashboard.blade.php"

param (
    [Parameter(Mandatory=$true)]
    [string]$LocalPath,
    
    [Parameter(Mandatory=$true)]
    [string]$RemotePath
)

$ServerIP = "103.175.219.57"
$Password = "M4ruw4h3@"
$Container = "steman-alumni-app-1"
$TempPath = "/var/www/steman-alumni/deploy_temp"

Write-Host "--- STARTING AUTOMATED SYNC ---" -ForegroundColor Cyan
Write-Host "Target: $LocalPath -> $RemotePath"

# 1. Secure Transfer to Server Host
Write-Host "[1/4] Transferring file to host..."
pscp -batch -pw $Password "$LocalPath" "root@$ServerIP`:$TempPath"

if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: File transfer failed." -ForegroundColor Red
    exit $LASTEXITCODE
}

# 2. Inject into Container & Flush Cache & Restart Cluster
Write-Host "[2/4] Injecting into container and flushing caches..."
plink -batch -no-antispoof -pw $Password "root@$ServerIP" "docker cp $TempPath $Container`:$RemotePath && docker exec $Container php artisan optimize:clear && docker exec $Container php artisan view:clear && docker restart $Container steman_nginx && rm $TempPath"

if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Container injection or restart failed." -ForegroundColor Red
    exit $LASTEXITCODE
}

Write-Host "--- DEPLOYMENT SUCCESSFUL ---" -ForegroundColor Green
Write-Host "Changes are now LIVE and OpCache has been flushed."
