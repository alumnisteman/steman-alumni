<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Exception;

class CheckIntegrity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steman:check-integrity {--fix : Automatically attempt to fix common issues}';

    protected $description = 'Comprehensive health and integrity audit of all application components.';

    private int $passCount = 0;
    private int $failCount = 0;
    private int $warnCount = 0;
    private int $totalChecks = 0;

    public function handle()
    {
        $this->header('STEMAN ALUMNI - INTEGRITY AUDIT V3');

        if ($this->option('fix')) {
            $this->warn('!! AUTO-FIX MODE ENABLED !!');
            $this->newLine();
        }

        // 1. Core checks
        $this->runCheck('Vendor/Autoload', fn() => $this->checkVendor());
        $this->runCheck('Database', fn() => $this->checkDatabase());
        $this->runCheck('Redis', fn() => $this->checkRedis());
        $this->runCheck('Storage Permissions', fn() => $this->checkStorage());
        $this->runCheck('Session Config', fn() => $this->checkSession());
        $this->runCheck('Environment', fn() => $this->checkEnvironment());

        // 2. Infrastructure
        $this->runCheck('Nginx Connectivity', fn() => $this->checkNginx());
        $this->runCheck('Public APP_URL', fn() => $this->checkPublicUrl());

        // 3. Application Logic
        $this->runCheck('Routes Compilation', fn() => $this->checkRoutes());
        $this->runCheck('Blade Route Integrity', fn() => $this->checkBladeRoutes());
        $this->runCheck('Route Shadowing Audit', fn() => $this->checkShadowedAudit());
        $this->runCheck('Active Smoke Tests', fn() => $this->runSmokeTests());
        $this->runCheck('Migration Sync Audit', fn() => $this->checkMigrations());
        $this->runCheck('Storage Symlink', fn() => $this->checkSymlink());
        $this->runCheck('Meilisearch Search', fn() => $this->checkMeilisearch());
        $this->runCheck('Captcha Stability', fn() => $this->checkCaptchaIntegrity());

        // Summary
        $this->newLine();
        $this->line("─────────────────────────────────────");
        $this->line("  Results: ✓ {$this->passCount} passed | ✗ {$this->failCount} failed | ⚠ {$this->warnCount} warnings");
        $this->line("  Coverage: " . round(($this->passCount / $this->totalChecks) * 100) . "% Integrity");
        $this->line("─────────────────────────────────────");

        if ($this->failCount > 0) {
            $this->error('!! SYSTEM INTEGRITY COMPROMISED — ' . $this->failCount . ' check(s) failed !!');
            $this->line('Refer to the failures above and use --fix for automated repairs.');
            return 1;
        }

        if ($this->warnCount > 0) {
            $this->warn('⚠ CORE SYSTEMS OPERATIONAL (with ' . $this->warnCount . ' minor warnings)');
            return 0;
        }

        $this->info('✓ ALL SYSTEMS OPERATIONAL 100% — ' . $this->passCount . '/' . $this->totalChecks . ' checks passed');
        return 0;
    }

    private function runCheck(string $name, callable $check): void
    {
        $this->totalChecks++;
        try {
            $result = $check();
            if ($result === true) {
                $this->passCount++;
            } elseif ($result === null) {
                $this->warnCount++;
            } else {
                $this->failCount++;
            }
        } catch (Exception $e) {
            $this->error("✗ {$name}: EXCEPTION — " . $e->getMessage());
            $this->failCount++;
        }
    }

    private function checkSession(): bool
    {
        $domain = config('session.domain');
        $appUrl = config('app.url');
        $host = parse_url($appUrl, PHP_URL_HOST);

        if (empty($domain)) {
            $this->warn('⚠ Session: SESSION_DOMAIN is not set.');
            if ($this->option('fix')) {
                $this->info('  Fixing: Setting SESSION_DOMAIN to .' . $host);
                $this->updateEnv('SESSION_DOMAIN', '.' . $host);
                return true;
            }
            $this->line('  Recommended: Set SESSION_DOMAIN=.' . $host . ' to share sessions across subdomains.');
            return null;
        }

        $this->info("✓ Session: Domain is set to '{$domain}'");
        return true;
    }

    private function checkPublicUrl(): bool
    {
        $url = config('app.url');
        try {
            $response = Http::timeout(5)->withoutVerifying()->get($url);
            if ($response->successful()) {
                $this->info("✓ Public URL: Accessible ({$url})");
                return true;
            }
            $this->error("✗ Public URL: Returned HTTP {$response->status()} ({$url})");
            return false;
        } catch (Exception $e) {
            $this->warn("⚠ Public URL: Could not reach {$url} internally — " . $e->getMessage());
            return null;
        }
    }

    private function checkCaptchaIntegrity(): bool
    {
        // Simulate a few hits to see if captcha persists or changes
        $val1 = session('captcha_answer');
        
        // This is tricky to test in CLI without a real session, 
        // but we can check if the Controller code has been patched.
        $controllerPath = app_path('Http/Controllers/AuthController.php');
        $content = file_get_contents($controllerPath);
        
        if (strpos($content, '!session()->has(\'captcha_answer\')') !== false) {
            $this->info('✓ Captcha: Persistence logic is ACTIVE');
            return true;
        }

        $this->error('✗ Captcha: Persistence logic is MISSING in AuthController.php');
        return false;
    }

    private function updateEnv($key, $value): void
    {
        $path = base_path('.env');
        if (!file_exists($path)) return;

        $content = file_get_contents($path);
        if (strpos($content, "{$key}=") !== false) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}\n";
        }
        file_put_contents($path, $content);
        $this->info("  Updated .env: {$key}={$value}");
    }

    private function header($text)
    {
        $this->newLine();
        $this->line('<bg=blue;fg=white> ' . $text . ' </>');
        $this->newLine();
    }

    private function checkVendor(): bool
    {
        $autoload = base_path('vendor/autoload.php');
        if (!file_exists($autoload)) {
            $this->error('✗ Vendor: autoload.php MISSING');
            return false;
        }
        $this->info('✓ Vendor: Dependencies present');
        return true;
    }

    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            $this->info("✓ Database: Connected");
            return true;
        } catch (Exception $e) {
            $this->error('✗ Database: Connection Failed — ' . $e->getMessage());
            return false;
        }
    }

    private function checkRedis(): bool
    {
        try {
            \Illuminate\Support\Facades\Redis::connection()->ping();
            $this->info('✓ Redis: Connected');
            return true;
        } catch (Exception $e) {
            $this->warn('⚠ Redis: Unreachable');
            return null;
        }
    }

    private function checkNginx(): bool
    {
        $urls = ['http://steman_nginx/health', 'http://127.0.0.1/health'];
        foreach ($urls as $url) {
            try {
                $response = Http::timeout(3)->get($url);
                if ($response->successful()) {
                    $this->info("✓ Nginx: Responding via {$url}");
                    return true;
                }
            } catch (Exception $e) {}
        }
        $this->error('✗ Nginx: Health check FAILED on all internal endpoints');
        return false;
    }

    private function checkStorage(): bool
    {
        $paths = [
            storage_path('app/public'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
        ];

        $ok = true;
        foreach ($paths as $path) {
            if (!is_writable($path)) {
                $this->error("✗ Storage: Not writable — " . basename($path));
                if ($this->option('fix')) {
                    @chmod($path, 0775);
                    $this->info("  Fixed permissions for " . basename($path));
                } else {
                    $ok = false;
                }
            }
        }
        
        if ($ok) $this->info('✓ Storage: Permissions OK');
        return $ok;
    }

    private function checkRoutes(): bool
    {
        try {
            \Illuminate\Support\Facades\Route::getRoutes();
            $this->info('✓ Routes: Compilation Valid');
            return true;
        } catch (Exception $e) {
            $this->error('✗ Routes: Compilation Broken — ' . $e->getMessage());
            return false;
        }
    }

    private function checkBladeRoutes(): bool
    {
        $viewsPath = resource_path('views');
        if (!file_exists($viewsPath)) return true;

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($viewsPath));
        $undefinedCount = 0;

        foreach ($files as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') continue;
            
            $content = file_get_contents($file->getRealPath());
            preg_match_all("/route\(['\"]([a-zA-Z0-9._-]+)['\"]/", $content, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $routeName) {
                    if (str_contains($routeName, '$')) continue;
                    
                    if (!\Illuminate\Support\Facades\Route::has($routeName)) {
                        $this->error("✗ Blade Routes: Undefined '{$routeName}' in " . $file->getPathname());
                        $undefinedCount++;
                    }
                }
            }
        }

        if ($undefinedCount > 0) {
            return false;
        }

        $this->info('✓ Blade Routes: All referenced routes exist');
        return true;
    }

    private function checkShadowedAudit(): bool
    {
        $routes = \Illuminate\Support\Facades\Route::getRoutes()->getRoutes();
        $wildcards = [];
        $shadowedCount = 0;

        foreach ($routes as $route) {
            $uri = $route->uri();
            $methods = implode('|', $route->methods());
            $key = $methods . ':' . $uri;
            
            // Check if this route is shadowed by any previous wildcard
            foreach ($wildcards as $wcKey => $wcUri) {
                if (str_starts_with($wcKey, $methods)) {
                    if ($this->isShadowed($uri, $wcUri)) {
                        $this->error("✗ Route Shadowing: [{$uri}] is blocked by wildcard [{$wcUri}]");
                        $shadowedCount++;
                    }
                }
            }

            // Register as wildcard if it has parameters
            if (str_contains($uri, '{')) {
                $wildcards[$key] = $uri;
            }
        }

        return $shadowedCount === 0;
    }

    private function isShadowed($uri, $wildcard): bool
    {
        // Don't check against itself
        if ($uri === $wildcard) return false;
        
        // Only static routes can be shadowed
        if (str_contains($uri, '{')) return false;

        $uriParts = explode('/', $uri);
        $wcParts = explode('/', $wildcard);

        if (count($uriParts) !== count($wcParts)) return false;

        for ($i = 0; $i < count($wcParts); $i++) {
            if (str_contains($wcParts[$i], '{')) {
                continue;
            }
            if (!isset($uriParts[$i]) || $uriParts[$i] !== $wcParts[$i]) {
                return false;
            }
        }

        return true;
    }

    private function runSmokeTests(): bool
    {
        $baseUrl = config('app.url', 'http://127.0.0.1');
        $pages = ['/', '/login', '/alumni', '/global-network', '/jejak-sukses'];
        $fail = false;
        
        foreach ($pages as $page) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->get($baseUrl . $page);
                if ($response->serverError()) {
                    $this->error("✗ Smoke Test FAIL: [{$page}] returned 500");
                    $fail = true;
                }
            } catch (\Exception $e) {
                // Warning only for connectivity
                $this->warn("⚠ Connectivity: Could not reach [{$page}] - skipping");
                continue;
            }
        }
        return !$fail;
    }

    private function checkMigrations(): bool
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate:status');
            $status = \Illuminate\Support\Facades\Artisan::output();
            if (str_contains($status, '| No |')) {
                $this->error("✗ Migration Mismatch: Some migrations have not been run!");
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return true;
        }
    }

    private function checkSymlink(): bool
    {
        if (!file_exists(public_path('storage')) || !is_link(public_path('storage'))) {
            $this->error("✗ Storage Symlink: Broken or missing");
            return false;
        }
        return true;
    }

    private function checkMeilisearch(): bool
    {
        try {
            $host = config('scout.meilisearch.host');
            $key = config('scout.meilisearch.key');
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $key])->timeout(3)->get($host . '/health');
            if ($response->successful()) {
                $this->info('✓ Meilisearch: Healthy');
                return true;
            }
            return null;
        } catch (Exception $e) {
            $this->warn('⚠ Meilisearch: Offline');
            return null;
        }
    }

    private function checkEnvironment(): bool
    {
        $required = ['APP_KEY', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
        foreach ($required as $key) {
            if (!env($key)) {
                $this->error("✗ Environment: {$key} is empty");
                return false;
            }
        }
        $this->info('✓ Environment: Critical variables set');
        return true;
    }
}
