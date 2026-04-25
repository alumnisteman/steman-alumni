<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class HealthService
{
    /**
     * Perform a full system audit.
     * 
     * @return array
     */
    public function performAudit(): array
    {
        $results = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'storage' => $this->checkStorage(),
            'nginx' => $this->checkNginx(),
            'session' => $this->checkSession(),
        ];

        $results['overall_integrity'] = $this->calculateIntegrity($results);
        
        if ($results['overall_integrity'] < 100) {
            Log::warning('System Health Audit: Integrity is at ' . $results['overall_integrity'] . '%');
        }

        return $results;
    }

    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkRedis(): bool
    {
        try {
            Redis::connection()->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkStorage(): bool
    {
        return is_writable(storage_path('framework/sessions')) && 
               is_writable(storage_path('logs')) &&
               is_writable(storage_path('framework/views'));
    }

    private function checkNginx(): bool
    {
        try {
            $response = Http::timeout(2)->get('http://steman_nginx/health');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkSession(): bool
    {
        return !empty(config('session.domain'));
    }

    private function calculateIntegrity(array $results): int
    {
        $total = count($results);
        $passed = count(array_filter($results));
        return (int) (($passed / $total) * 100);
    }
}
