# 🚀 Script Pemindahan Kode ke GitHub - Alumni STEMAN

Write-Host "--- Memulai Proses Upload ke GitHub ---" -ForegroundColor Cyan

# --- 🛠️ DETEKSI GIT ---
$gitPath = "git"
if (!(Get-Command "git" -ErrorAction SilentlyContinue)) {
    $commonPaths = @(
        "C:\Program Files\Git\cmd\git.exe",
        "C:\Program Files\Git\bin\git.exe",
        "C:\Program Files (x86)\Git\cmd\git.exe",
        "$env:LOCALAPPDATA\Programs\Git\cmd\git.exe"
    )

    foreach ($path in $commonPaths) {
        if (Test-Path $path) {
            $gitPath = "& '$path'"
            break
        }
    }

    if ($gitPath -eq "git") {
        Write-Host "❌ Git tidak ditemukan! Bapak perlu instal Git dulu." -ForegroundColor Red
        exit
    }
}

# Fungsi untuk menjalankan Git dengan rapi
function Run-Git {
    param([string]$arguments)
    if ($gitPath.StartsWith("&")) {
        $cmd = "$gitPath $arguments"
        Invoke-Expression $cmd
    } else {
        & git ( [regex]::Matches($arguments, '"[^"]+"|\S+') | ForEach-Object { $_.Value.Trim('"') } )
    }
}

# --- ⚙️ KONFIGURASI IDENTITAS ---
$userName = Run-Git "config user.name"
if ([string]::IsNullOrWhiteSpace($userName)) {
    Write-Host "[0/4] Set identitas Git otomatis..." -ForegroundColor Yellow
    Run-Git "config --global user.name 'Admin Alumni'"
    Run-Git "config --global user.email 'admin@alumnisteman.com'"
}

# 1. Pastikan Git sudah terinisialisasi
if (!(Test-Path .git)) {
    Write-Host "[1/4] Inisialisasi Git Lokal..." -ForegroundColor Yellow
    Run-Git "init"
}

# 2. Atur Remote Origin (Ganti URL jika salah)
$remoteUrl = "https://github.com/alumnisteman/web_forsa.git"
$remotes = Run-Git "remote" 
if ($remotes -notmatch "origin") {
    Write-Host "[2/4] Menyambungkan ke GitHub..." -ForegroundColor Yellow
    Run-Git "remote add origin $remoteUrl"
}

# 3. Menambahkan File & Commit
Write-Host "[3/4] Menyiapkan file & membuat catatan (Commit)..." -ForegroundColor Yellow
Run-Git "add ."
# Pastikan ada yang di-commit
$commitMsg = "Initial Push: Modern Infrastructure and Performance Optimization"
Run-Git "commit -m `"$commitMsg`""
Run-Git "branch -M main"

# 4. Push ke GitHub
Write-Host "[4/4] Mengirim kode (Push)..." -ForegroundColor Yellow
Write-Host "Jendela login GitHub mungkin akan muncul..." -ForegroundColor Yellow
Run-Git "push -u origin main --force"

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n✅ BERHASIL! Kode Bapak sudah mendarat di GitHub." -ForegroundColor Green
} else {
    Write-Host "`n❌ TERJADI KESALAHAN saat mengirim." -ForegroundColor Red
    Write-Host "Kemungkinan: Bapak belum login di jendela yang muncul tadi."
}

Write-Host "Tekan enter untuk menutup..."
Read-Host
