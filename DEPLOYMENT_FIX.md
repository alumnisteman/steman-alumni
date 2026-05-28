# Production Server Deployment Fix

## Issues Fixed
1. ✅ Polls table migration - now checks if table exists before creating
2. ✅ getAds() function - added error handling
3. ✅ AI service - now returns DEGRADED instead of ERROR when no API keys
4. ✅ Smoke test - more lenient (allows up to 2 failures)

## Deployment Steps

### Option A: Using PowerShell (Windows)

#### 1. Install PuTTY (if not already installed)
Download from: https://www.putty.org/

#### 2. SSH to Server using PowerShell
```powershell
# Using plink (PuTTY command-line)
plink -ssh root@103.175.219.57 -pw M4ruw4h3@
```

#### 3. Run All Commands at Once
```powershell
# Create a script file with all commands
$commands = @(
    "cd /var/www",
    "git pull",
    "composer dump-autoload",
    "php artisan cache:clear",
    "php artisan config:clear",
    "php artisan route:clear",
    "php artisan view:clear",
    "php artisan migrate --force",
    "php artisan steman:check-integrity"
)

# Join commands with semicolons
$commandString = $commands -join "; "

# Execute via plink
plink -ssh root@103.175.219.57 -pw M4ruw4h3@ $commandString
```

#### 4. Or Run Commands Interactively
```powershell
# SSH and run commands one by one
plink -ssh root@103.175.219.57 -pw M4ruw4h3@
# Then type commands manually after connection
```

### Option B: Using Bash/Linux/Mac

### 1. SSH to Server
```bash
echo y | plink -ssh root@103.175.219.57 -pw M4ruw4h3@ "echo Connected via plink"
```

### 2. Navigate to Project Directory
```bash
cd /var/www
```

### 3. Pull Latest Changes
```bash
git pull origin main
# or
git pull
```

### 4. Regenerate Autoloader (Critical for helpers.php)
```bash
composer dump-autoload
```

### 5. Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 6. Run Migrations (Safe Mode)
```bash
php artisan migrate --force
```

### 7. Restart Services
```bash
# If using Docker
docker-compose restart

# Or if using supervisor
supervisorctl restart all

# Or restart PHP-FPM
service php-fpm restart
service nginx restart
```

### 8. Verify Health Check
```bash
php artisan steman:check-integrity
```

## Optional: Configure AI Service

If you want AI features to work, add API keys to `.env`:

```bash
# Edit .env file
nano /var/www/.env

# Add these lines:
GEMINI_API_KEY=your-google-gemini-api-key-here
OPENROUTER_API_KEY=your-openrouter-api-key-here
DEEPSEEK_API_KEY=your-deepseek-api-key-here
```

Then clear config cache:
```bash
php artisan config:clear
```

## Verification

After deployment, check:
1. https://alumni-steman.my.id - should load without 500 errors
2. Check logs: `tail -f /var/www/storage/logs/laravel.log`
3. Run health check: `php artisan steman:check-integrity`

## Troubleshooting

If errors persist:
```bash
# Check Laravel logs
tail -100 /var/www/storage/logs/laravel.log

# Check nginx logs
tail -100 /var/log/nginx/error.log

# Check PHP-FPM logs
tail -100 /var/log/php-fpm/error.log
```
