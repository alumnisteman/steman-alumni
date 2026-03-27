# update-ip.ps1 - Automatically update .env with current local IP
# =============================================================================

# 1. Detect current IPv4 Address (Excluding Loopback and Virtual adapters)
$ip = (Get-NetIPAddress -AddressFamily IPv4 | 
    Where-Object { $_.InterfaceAlias -notlike "*Loopback*" -and $_.InterfaceAlias -notlike "*VirtualHost*" -and $_.IPv4Address -notlike "169.*" } | 
    Select-Object -ExpandProperty IPAddress -First 1)

if (-not $ip) {
    Write-Host "-> Gagal mendeteksi IP Address. Menggunakan localhost." -ForegroundColor Red
    $ip = "localhost"
} else {
    Write-Host "-> Terdeteksi IP Aktif: $ip" -ForegroundColor Green
}

$envFile = ".env"
if (Test-Path $envFile) {
    $content = Get-Content $envFile
    
    # 2. Update APP_URL
    $content = $content -replace '^APP_URL=.*', "APP_URL=http://$($ip):8000"
    
    # 3. Update Socialite Redirect URIs
    $content = $content -replace '^GOOGLE_REDIRECT_URI=.*', "GOOGLE_REDIRECT_URI=http://$($ip):8000/auth/google/callback"
    $content = $content -replace '^LINKEDIN_REDIRECT_URI=.*', "LINKEDIN_REDIRECT_URI=http://$($ip):8000/auth/linkedin/callback"
    
    # 4. Save back to .env
    $content | Set-Content $envFile
    Write-Host "-> File .env berhasil diperbarui dengan IP: $ip" -ForegroundColor Cyan
} else {
    Write-Host "-> File .env tidak ditemukan!" -ForegroundColor Red
}
