<?php

namespace App\Services\SystemGuard;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class HealthChecker
{
    /** @var array<string, string> */
    private array $results = [];

    /**
     * Run all health checks and return a list of issue keys.
     * @return array<string>
     */
    public function run(): array
    {
        $issues = [];

        $checks = [
            'db_down'         => fn() => $this->checkDatabase(),
            'redis_down'      => fn() => $this->checkRedis(),
            'queue_overload'  => fn() => $this->checkQueue(),
            'meili_down'      => fn() => $this->checkMeilisearch(),
            'disk_low'        => fn() => $this->checkDisk(),
            'storage_broken'  => fn() => $this->checkStorage(),
            'log_bloated'     => fn() => $this->checkLogSize(),
            'session_domain'  => fn() => $this->checkSessionDomain(),
            'captcha_patch'   => fn() => $this->checkCaptchaPatch(),
            'nginx_down'      => fn() => $this->checkNginx(),
            'audit_broken'    => fn() => $this->checkAuditIntegrity(),
            'route_mismatch'  => fn() => $this->checkRouteIntegrity(),
            'route_shadowing' => fn() => $this->checkShadowedRoutes(),
        ];

        foreach ($checks as $issueKey => $checkFn) {
            try {
                $result = $checkFn();
                $this->results[$issueKey] = $result ? 'OK' : 'FAIL';
                if (!$result) {
                    $issues[] = $issueKey;
                }
            } catch (\Throwable $e) {
                $this->results[$issueKey] = 'EXCEPTION: ' . $e->getMessage();
                $issues[] = $issueKey;
                Log::error("SystemGuard HealthChecker [{$issueKey}]: " . $e->getMessage());
            }
        }

        return $issues;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    private function checkDatabase(): bool
    {
        DB::select('SELECT 1');
        return true;
    }

    private function checkRedis(): bool
    {
        return Redis::connection()->ping() !== false;
    }

    private function checkQueue(): bool
    {
        try {
            $len = Redis::llen('queues:default');
            return $len < 1000;
        } catch (\Exception $e) {
            return true; // Redis might not have a queue yet — not critical
        }
    }

    private function checkMeilisearch(): bool
    {
        $host = config('scout.meilisearch.host', env('MEILISEARCH_HOST', 'http://steman_meilisearch:7700'));
        $key  = config('scout.meilisearch.key', env('MEILISEARCH_KEY', ''));
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $key])->timeout(3)->get($host . '/health');
        return $response->successful();
    }

    private function checkDisk(): bool
    {
        $free = @disk_free_space('/');
        // Warn if < 1 GB free
        return $free === false || $free > (1024 * 1024 * 1024);
    }

    private function checkStorage(): bool
    {
        $paths = [
            storage_path('logs'),
            storage_path('framework/views'),
            storage_path('framework/sessions'),
        ];
        foreach ($paths as $p) {
            if (!is_writable($p)) return false;
        }
        return true;
    }

    private function checkLogSize(): bool
    {
        $log = storage_path('logs/laravel.log');
        if (!file_exists($log)) return true;
        // Flag if > 50 MB
        return filesize($log) < (50 * 1024 * 1024);
    }

    private function checkSessionDomain(): bool
    {
        return !empty(config('session.domain'));
    }

    private function checkCaptchaPatch(): bool
    {
        $path = app_path('Http/Controllers/AuthController.php');
        if (!file_exists($path)) return false;
        return str_contains(file_get_contents($path), "!session()->has('captcha_answer')");
    }

    private function checkNginx(): bool
    {
        foreach (['http://steman_nginx/health', 'http://127.0.0.1/health'] as $url) {
            try {
                if (Http::timeout(3)->get($url)->successful()) return true;
            } catch (\Exception $e) {}
        }
        return false;
    }

    private function checkAuditIntegrity(): bool
    {
        $auditService = new \App\Services\AuditService();
        $latestLogs = \App\Models\AuditLog::latest()->take(20)->get();
        
        foreach ($latestLogs as $log) {
            if (!$auditService->verifyIntegrity($log)) {
                return false;
            }
        }
        return true;
    }

    private function checkRouteIntegrity(): bool
    {
        return \Illuminate\Support\Facades\Cache::remember('integrity:route_check', 3600, function() {
            $viewsPath = resource_path('views');
            if (!file_exists($viewsPath)) return true;

            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($viewsPath));
            $foundUndefined = false;

            foreach ($files as $file) {
                if ($file->isDir() || $file->getExtension() !== 'php') continue;
                
                $content = file_get_contents($file->getRealPath());
                // Regex to find route('name') or route("name")
                preg_match_all("/route\(['\"]([a-zA-Z0-9._-]+)['\"]/", $content, $matches);

                if (!empty($matches[1])) {
                    foreach ($matches[1] as $routeName) {
                        // Skip if it contains variables or dots indicating dynamic names (though common ones like news.show are fine)
                        if (str_contains($routeName, '$')) continue;
                        
                        if (!\Illuminate\Support\Facades\Route::has($routeName)) {
                            Log::error("Integrity Alert: Undefined route '{$routeName}' found in " . $file->getPathname());
                            $foundUndefined = true;
                        }
                    }
                }
            }

            return !$foundUndefined;
        });
    }

    /**
     * Deep check for routes that might be shadowed by wildcards (e.g. /alumni/{user} before /alumni/matchmaking)
     */
    private function checkShadowedRoutes(): bool
    {
        $routes = \Illuminate\Support\Facades\Route::getRoutes()->getRoutes();
        $wildcards = [];
        $shadowedCount = 0;

        foreach ($routes as $route) {
            $uri = $route->uri();
            
            // Check if this route is shadowed by any previous wildcard
            foreach ($wildcards as $wc) {
                if ($this->isShadowed($uri, $wc)) {
                    Log::warning("Integrity Alert: Route [{$uri}] is shadowed by wildcard [{$wc}] and might be unreachable.");
                    $shadowedCount++;
                }
            }

            // Register as wildcard if it has parameters
            if (str_contains($uri, '{')) {
                $wildcards[] = $uri;
            }
        }

        return $shadowedCount === 0;
    }

    private function isShadowed($uri, $wildcard): bool
    {
        $uriParts = explode('/', $uri);
        $wcParts = explode('/', $wildcard);

        if (count($uriParts) !== count($wcParts)) return false;

        for ($i = 0; $i < count($wcParts); $i++) {
            if (str_contains($wcParts[$i], '{')) {
                // This is a parameter, it matches anything in the URI at this position
                continue;
            }
            if ($uriParts[$i] !== $wcParts[$i]) {
                return false;
            }
        }

        return true;
    }
}
