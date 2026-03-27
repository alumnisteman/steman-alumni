# 🚀 Script Pemindahan Kode ke GitHub - Alumni STEMAN

Write-Host "--- Memulai Proses Upload ke GitHub ---" -ForegroundColor Cyan

# --- 🛠️ LANGKAH 0: Deteksi & Aktifkan Git ---
Write-Host "[0] Mendeteksi Git..." -ForegroundColor Yellow

$gitExe = ""
$searchPaths = @(
    "C:\Program Files\Git\cmd\git.exe",
    "C:\Program Files\Git\bin\git.exe",
    "C:\Program Files (x86)\Git\cmd\git.exe",
    "$env:LOCALAPPDATA\Programs\Git\cmd\git.exe"
)

# Cek apakah git sudah ada di PATH
if (Get-Command "git" -ErrorAction SilentlyContinue) {
    $gitExe = (Get-Command "git").Source
    Write-Host "    OK Git terdeteksi di PATH: $gitExe" -ForegroundColor Green
} else {
    # Cari di lokasi instalasi umum
    foreach ($path in $searchPaths) {
        if (Test-Path $path) {
            $gitExe = $path
            # Tambahkan folder-nya ke PATH sesi ini
            $gitFolder = Split-Path $path -Parent
            $env:PATH = "$gitFolder;$env:PATH"
            Write-Host "    OK Git ditemukan, diaktifkan dari: $path" -ForegroundColor Green
            break
        }
    }
}

if ([string]::IsNullOrEmpty($gitExe)) {
    Write-Host "GAGAL: Git tidak ditemukan! Silakan instal dari https://git-scm.com/download/win" -ForegroundColor Red
    Read-Host; exit 1
}

# --- LANGKAH 1: Set Identitas (wajib untuk commit) ---
Write-Host "[1/4] Memastikan identitas Git..." -ForegroundColor Yellow
$userName = git config user.name
if ([string]::IsNullOrWhiteSpace($userName)) {
    git config --global user.name "Admin Alumni STEMAN"
    git config --global user.email "admin@alumnisteman.com"
    Write-Host "    OK Identitas diset otomatis." -ForegroundColor Green
}

# --- LANGKAH 2: Inisialisasi & Sambungkan ke GitHub ---
Write-Host "[2/4] Menghubungkan ke GitHub..." -ForegroundColor Yellow

if (!(Test-Path .git)) {
    git init
}

$remoteUrl = "https://github.com/alumnisteman/web_forsa.git"
$existingOrigin = git remote get-url origin 2>$null

if ($existingOrigin -ne $remoteUrl) {
    git remote remove origin 2>$null
    git remote add origin $remoteUrl
}
Write-Host "    OK Terhubung ke: $remoteUrl" -ForegroundColor Green

# --- LANGKAH 3: Commit ---
Write-Host "[3/4] Menyimpan semua kode (Commit)..." -ForegroundColor Yellow
git add .
$commitOutput = git commit -m "Initial Push: Portal Alumni STEMAN - Modern & Optimized" 2>&1
if ($commitOutput -match "nothing to commit") {
    Write-Host "    INFO: Tidak ada file baru. Kode sudah sinkron." -ForegroundColor Cyan
}
git branch -M main

# --- LANGKAH 4: Push ke GitHub ---
Write-Host "[4/4] Mengirim ke GitHub (Push)..." -ForegroundColor Yellow
Write-Host "    PENTING: Jika muncul jendela login, silakan Bapak login!" -ForegroundColor Yellow
git push -u origin main --force

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "BERHASIL! Kode Bapak sudah ada di GitHub!" -ForegroundColor Green
    Write-Host "Cek di: $remoteUrl" -ForegroundColor Cyan
} else {
    Write-Host ""
    Write-Host "GAGAL mengirim ke GitHub." -ForegroundColor Red
    Write-Host "   Saran: Pastikan Bapak login ke GitHub saat jendela muncul." -ForegroundColor Red
}

Write-Host ""
Write-Host "Tekan Enter untuk menutup..."
Read-Host
