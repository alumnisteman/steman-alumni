# deploy-bulk.ps1
# Usage: ./deploy-bulk.ps1 -Files @("app/Services/AlumniService.php", "routes/web.php")

param (
    [Parameter(Mandatory=$true)]
    [string[]]$Files
)

$ServerIP = "103.175.219.57"
$Password = "M4ruw4h3@"
$Container = "steman_app"
$RemoteBase = "/var/www"
$TempBase = "/var/www/steman-alumni/deploy_temp_bulk"

Write-Host "=== BATCH DEPLOYMENT STARTED ===" -ForegroundColor Cyan
Write-Host "Total Files to Sync: $($Files.Count)"

# 1. Create Temp Directory on Host
plink -batch -no-antispoof -pw $Password "root@$ServerIP" "mkdir -p $TempBase"

# 2. Transfer all files to Host
foreach ($file in $Files) {
    $remoteFilePath = "$TempBase/" + (Split-Path $file -Leaf)
    Write-Host "-> Transferring: $file ..."
    pscp -batch -pw $Password "$file" "root@$ServerIP`:$remoteFilePath"
}

# 3. Inject all files into Container in one transaction
Write-Host "[3/4] Injecting files into container..."
foreach ($file in $Files) {
    # Determine the remote path inside /var/www based on the local structure
    $destPath = "$RemoteBase/$file"
    $tempFile = "$TempBase/" + (Split-Path $file -Leaf)
    
    # Ensure remote directory exists
    $remoteDir = Split-Path $destPath -Parent
    plink -batch -no-antispoof -pw $Password "root@$ServerIP" "docker exec $Container mkdir -p $remoteDir"
    
    # Copy from host temp to container
    plink -batch -no-antispoof -pw $Password "root@$ServerIP" "docker cp $tempFile $Container`:$destPath"
}

# 4. Final Cache Flush & Cleanup
Write-Host "[4/4] Finalizing and flushing caches..."
plink -batch -no-antispoof -pw $Password "root@$ServerIP" "docker exec $Container php artisan optimize:clear && docker exec $Container php artisan view:clear && rm -rf $TempBase && docker restart $Container steman_queue"

Write-Host "=== BATCH DEPLOYMENT SUCCESSFUL ===" -ForegroundColor Green
