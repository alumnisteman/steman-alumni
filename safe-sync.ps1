# safe-sync.ps1 - Secure GitHub Synchronization with Audit 🛡️
# ==========================================================
#   Usage: .\safe-sync.ps1
#   Function: Audit PHP Syntax & Tests before Pushing code.
# ==========================================================

Write-Host "==============================================" -ForegroundColor Cyan
Write-Host "  STEMAN ALUMNI - SAFE SYNC GUARD 🛡️" -ForegroundColor Cyan
Write-Host "==============================================" -ForegroundColor Cyan

# 1. Check for Changes
Write-Host "[1/4] Checking for local changes..." -ForegroundColor Yellow
$gitStatus = git status --porcelain
if (-not $gitStatus) {
    Write-Host "    INFO: No changes detected. Your code is already in sync." -ForegroundColor Gray
    Read-Host "Press Enter to exit..."; exit 0
}

# 2. PHP Syntax Audit (Lint)
Write-Host "[2/4] Auditing PHP Syntax (Lint)..." -ForegroundColor Yellow
$changedFiles = git status --porcelain | ForEach-Object { $_.Substring(3) } | Where-Object { $_ -like "*.php" }

if ($changedFiles) {
    foreach ($file in $changedFiles) {
        if (Test-Path $file) {
            $lintResult = php -l $file 2>&1
            if ($lintResult -match "Errors parsing" -or $LASTEXITCODE -ne 0) {
                Write-Host "    CRITICAL ERROR: Syntax Error found in $file" -ForegroundColor Red
                Write-Host "    Details: $lintResult" -ForegroundColor Red
                Write-Host "    SYNC ABORTED. Please fix the error first." -ForegroundColor Gray
                Read-Host "Press Enter to exit..."; exit 1
            }
        }
    }
    Write-Host "    OK! Syntax checks passed." -ForegroundColor Green
} else {
    Write-Host "    SKIPPED: No PHP files changed." -ForegroundColor Gray
}

# 3. Laravel Functional Audit (Tests)
Write-Host "[3/4] Auditing Functional Integrity (Artisan Test)..." -ForegroundColor Yellow
try {
    $testResult = php artisan test --stop-on-failure
    if ($LASTEXITCODE -ne 0) {
        Write-Host "    CRITICAL ERROR: Application tests failed!" -ForegroundColor Red
        Write-Host "    SYNC ABORTED. Fix logic errors before pushing." -ForegroundColor Gray
        Read-Host "Press Enter to exit..."; exit 1
    }
    Write-Host "    OK! Functional tests passed." -ForegroundColor Green
} catch {
    Write-Host "    WARNING: Could not run tests. Skipping functional audit." -ForegroundColor Yellow
}

# 4. Final Push to GitHub
Write-Host "[4/4] Sending verified changes to GitHub..." -ForegroundColor Yellow
$timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
git add .
git commit -m "Safe Sync Guard: Audit Passed ($timestamp)"
git push origin main

if ($LASTEXITCODE -eq 0) {
    Write-Host "==============================================" -ForegroundColor Green
    Write-Host "  SUCCESS! Your code is SAFE & SYNCED! 🥂" -ForegroundColor Green
    Write-Host "==============================================" -ForegroundColor Green
} else {
    Write-Host "    ERROR: Git push failed! Check your connection/permissions." -ForegroundColor Red
}

Read-Host "Press Enter to finish..."
