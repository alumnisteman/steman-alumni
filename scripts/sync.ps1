# sync.ps1 - Real-time Auto-Sync Tool for Steman Alumni
# =============================================================================
#   Usage: .\sync.ps1
#   Automatically syncs changes to the remote server 192.168.1.5
# =============================================================================

$REMOTE_USER = "root"
$REMOTE_HOST = "192.168.1.5"
$REMOTE_PATH = "/var/www/steman-alumni"

Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "  STEMAN ALUMNI - LIVE AUTO-SYNC ACTIVE" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "Watching for changes in app, resources, routes, etc..." -ForegroundColor Gray

# Define directories and files to watch
$watcher = New-Object System.IO.FileSystemWatcher
$watcher.Path = $PSScriptRoot
$watcher.IncludeSubdirectories = $true
$watcher.EnableRaisingEvents = $true

# Sync Function
$syncAction = {
    param($path)
    $relativePath = $path.Replace($PSScriptRoot + "\", "").Replace("\", "/")
    
    # Filter: Only sync relevant files/folders
    if ($relativePath -match '^(app|bootstrap|config|database|public|resources|routes|\.env|docker-compose.*\.yml|Dockerfile|artisan)') {
        Write-Host "[$(Get-Date -Format 'HH:mm:ss')] Change detected: $relativePath" -ForegroundColor Yellow
        
        # 1. Sync file
        try {
            scp "$path" "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/$relativePath"
            Write-Host "  [OK] File Synced!" -ForegroundColor Green
            
            # 2. Clear cache on server (if it's a code/config change)
            if ($relativePath -match '\.php$|\.env$') {
                ssh "${REMOTE_USER}@${REMOTE_HOST}" "cd ${REMOTE_PATH} && docker compose -f docker-compose.prod.yml exec -T app php artisan config:clear && docker compose -f docker-compose.prod.yml exec -T app php artisan view:clear"
                Write-Host "  [OK] Server Cache Cleared!" -ForegroundColor Cyan
            }
        } catch {
            Write-Host "  [ERR] Failed to sync. Is the server reachable?" -ForegroundColor Red
        }
    }
}

# Register Events
$onChanged = Register-ObjectEvent $watcher "Changed" -Action { $syncAction.Invoke($Event.SourceEventArgs.FullPath) }
$onCreated = Register-ObjectEvent $watcher "Created" -Action { $syncAction.Invoke($Event.SourceEventArgs.FullPath) }

# Wait Loop
try {
    while ($true) { Start-Sleep -Seconds 1 }
} finally {
    # Unregister on Exit
    Unregister-Event -SourceIdentifier $onChanged.Name
    Unregister-Event -SourceIdentifier $onCreated.Name
    $watcher.Dispose()
    Write-Host "Sync Stopped." -ForegroundColor Red
}
