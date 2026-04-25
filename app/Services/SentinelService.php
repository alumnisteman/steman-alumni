<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SentinelService
{
    /**
     * Perform a deep audit of the system
     */
    public function performAudit()
    {
        $report = [
            'timestamp' => now()->toIso8601String(),
            'status' => 'NOMINAL',
            'issues' => [],
            'fixes_applied' => 0
        ];

        // 1. Audit Images & Assets
        $this->auditImages($report);

        // 2. Audit Accessibility Patterns
        $this->auditA11yPatterns($report);

        // 3. Audit Performance Settings
        $this->auditPerformance($report);

        if (count($report['issues']) > 0) {
            $report['status'] = 'WARNING';
            Log::warning('Sentinel Audit found issues:', $report);
        }

        return $report;
    }

    private function auditImages(&$report)
    {
        // Check if public storage is linked
        if (!file_exists(public_path('storage'))) {
            $report['issues'][] = 'Missing storage symbolic link';
            @\Illuminate\Support\Facades\Artisan::call('storage:link');
            $report['fixes_applied']++;
        }

        // Check for common broken settings images
        $keys = ['chairman_photo', 'event_chairman_photo', 'secretary_photo', 'hero_background'];
        foreach ($keys as $key) {
            $path = setting($key);
            if ($path && !str_starts_with($path, 'http')) {
                $cleanPath = preg_replace('#^/?storage/#', '', $path);
                if (!Storage::disk('public')->exists($cleanPath)) {
                    $report['issues'][] = "Setting image missing: {$key} at {$path}";
                }
            }
        }
    }

    private function auditA11yPatterns(&$report)
    {
        // Check if certain critical settings are empty
        $criticalSettings = ['site_name', 'school_name'];
        foreach ($criticalSettings as $key) {
            if (!setting($key)) {
                $report['issues'][] = "Critical setting empty: {$key}";
                Setting::updateOrCreate(['key' => $key], ['value' => $key === 'site_name' ? 'Alumni Portal' : 'SMK Negeri']);
                $report['fixes_applied']++;
            }
        }
    }

    private function auditPerformance(&$report)
    {
        // 1. Ensure Launch / Maintenance Settings Exist
        $launchSettings = [
            ['key' => 'coming_soon_mode', 'label' => 'Mode Launching Soon (Aktifkan Halaman Penahan)', 'value' => 'on', 'group' => 'launch'],
            ['key' => 'launch_date', 'label' => 'Tanggal & Waktu Launching', 'value' => now()->addDays(5)->format('Y-m-d\TH:i'), 'group' => 'launch'],
            ['key' => 'launch_title', 'label' => 'Judul Halaman Launching', 'value' => 'THE NEW STEMAN PORTAL', 'group' => 'launch'],
        ];

        foreach ($launchSettings as $s) {
            if (!Setting::where('key', $s['key'])->exists()) {
                Setting::create($s);
                $report['issues'][] = "Added missing launch setting: {$s['key']}";
                $report['fixes_applied']++;
            }
        }

        // Ensure cache drivers are optimal for production
        if (config('cache.default') === 'file' && app()->environment('production')) {
            $report['issues'][] = 'Performance: Using file cache in production. Consider Redis/Database.';
        }

        // Check for large log files
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath) && filesize($logPath) > 50 * 1024 * 1024) { // 50MB
            $report['issues'][] = 'Maintenance: log file exceeds 50MB';
            // We don't auto-clear logs here for safety, just report
        }
    }

    /**
     * Global Error Hardener: Ensures settings always return valid data
     */
    public static function safeSetting($key, $default = null)
    {
        try {
            $val = Setting::get($key, $default);
            return $val ?: $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}
