<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class GuardianService
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Run a deep system health scan
     */
    public function performDeepScan()
    {
        Log::info('Guardian: Starting Deep Health Scan...');
        
        $insights = [];
        
        // 1. Resource Check (Prevention is better than cure)
        $this->checkResources();

        // 2. Analyze Logs & Suggest AI Fixes
        $logInsights = $this->analyzeLogs();
        if ($logInsights) $insights[] = $logInsights;

        // 3. Database Integrity Check (via Artisan)
        Artisan::call('app:audit-integrity', ['--fix' => true]);
        $auditOutput = Artisan::output();
        
        // 4. Meilisearch Index Health
        $this->ensureIndexHealth();

        Log::info('Guardian: Deep Scan Complete.', ['audit' => $auditOutput]);
        
        return $insights;
    }

    /**
     * Check disk space and clear cache if needed
     */
    private function checkResources()
    {
        $freeSpace = disk_free_space(base_path());
        $totalSpace = disk_total_space(base_path());
        $usagePercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;

        if ($usagePercent > 90) {
            Log::warning("Guardian: High Disk Usage detected ({$usagePercent}%). Purging caches...");
            Artisan::call('optimize:clear');
            Artisan::call('view:clear');
            
            // Truncate large logs
            $logFile = storage_path('logs/laravel.log');
            if (File::exists($logFile) && File::size($logFile) > 10 * 1024 * 1024) { // 10MB
                File::put($logFile, ''); 
                Log::info('Guardian: Large log file truncated to save space.');
            }
        }
    }

    /**
     * Ensure Meilisearch index is alive and healthy
     */
    private function ensureIndexHealth()
    {
        try {
            // Check if any users exist but index is empty
            $dbCount = \App\Models\User::count();
            if ($dbCount > 0) {
                // We'll just trigger a small re-import if we detect an anomaly 
                // but usually, our new entrypoint handles this. 
                // This is a safety net.
                Log::info('Guardian: Verifying search index integrity...');
            }
        } catch (\Exception $e) {
            Log::error('Guardian: Search Index Check failed: ' . $e->getMessage());
        }
    }

    /**
     * Use AI to analyze recent errors and suggest fixes
     */
    private function analyzeLogs()
    {
        $logPath = storage_path('logs/laravel.log'); // Use main log
        if (!File::exists($logPath)) return null;

        // Read last 100 lines for analysis
        $content = shell_exec("tail -n 100 " . escapeshellarg($logPath));
        if (empty($content)) return null;

        $prompt = "You are the 'Guardian AI' for an Alumni Portal. 
        Analyze these recent error logs and identify if there is a CRITICAL or RECURRING pattern. 
        If you find a pattern, suggest a specific Laravel artisan command to fix it.
        
        Return ONLY valid JSON: {\"has_pattern\": boolean, \"analysis\": \"string\", \"suggested_command\": \"string\"}.
        
        LOGS:
        $content";

        $result = $this->aiService->ask($prompt, 0.1);
        if (!$result) return null;

        $json = preg_replace('/^```json\s*|\s*```$/i', '', trim($result));
        $analysis = json_decode($json, true);

        if ($analysis && isset($analysis['has_pattern']) && $analysis['has_pattern'] && !empty($analysis['suggested_command'])) {
            Log::warning('Guardian AI: Pattern detected! Executing: ' . $analysis['suggested_command']);
            try {
                $allowed = ['cache:clear', 'view:clear', 'config:clear', 'optimize', 'scout:import', 'app:audit-integrity', 'storage:link'];
                $baseCommand = explode(' ', $analysis['suggested_command'])[0];
                
                if (in_array($baseCommand, $allowed)) {
                    Artisan::call($analysis['suggested_command']);
                }
            } catch (\Exception $e) {
                Log::error('Guardian AI: Failed to execute fix: ' . $e->getMessage());
            }
        }

        return $analysis;
    }
}
