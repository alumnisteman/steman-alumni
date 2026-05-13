<?php

namespace App\Services\SystemGuard;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class Fixer
{
    /**
     * Handle a specific issue with auto-healing logic.
     */
    public static function handle(string $issue): bool
    {
        if (!Rules::canHandle($issue)) {
            Log::warning("SystemGuard Fixer: Issue [{$issue}] blocked — max retries or cooldown reached.");
            return false;
        }

        Rules::record($issue);
        $fixed = false;

        try {
            switch ($issue) {

                case 'db_down':
                    // Log and notify — don't blindly restart DB without human review
                    Notifier::send("🚨 *DATABASE DOWN*\nMySQL tidak merespons. Periksa container `db` segera.", 'critical');
                    Log::critical('SystemGuard: Database is down!');
                    $fixed = false; // Needs human intervention
                    break;

                case 'redis_down':
                    Notifier::send("🚨 *REDIS DOWN*\nRedis tidak merespons. Session/Cache akan bermasalah.", 'critical');
                    Log::critical('SystemGuard: Redis is down!');
                    $fixed = false;
                    break;

                case 'queue_overload':
                    Artisan::call('queue:restart');
                    Notifier::send("⚠️ *Queue Overload*\nQueue melebihi 1000 job. Queue worker telah di-restart.", 'warning');
                    Log::warning('SystemGuard: Queue overload — worker restarted.');
                    $fixed = true;
                    break;

                case 'meili_down':
                    Notifier::send("⚠️ *Meilisearch DOWN*\nSearch engine offline. Pencarian fallback ke Eloquent.", 'warning');
                    Log::warning('SystemGuard: Meilisearch is offline.');
                    $fixed = false;
                    break;

                case 'disk_low':
                    // Safely clean: only logs, never app files
                    $cleaned = self::cleanLogs();
                    Notifier::send("⚠️ *Disk Space Rendah*\nDisk hampir penuh. {$cleaned} MB log telah dibersihkan.", 'warning');
                    Log::warning("SystemGuard: Disk low — cleaned {$cleaned} MB logs.");
                    $fixed = true;
                    break;

                case 'storage_broken':
                    @chmod(storage_path('logs'), 0775);
                    @chmod(storage_path('framework/views'), 0775);
                    @chmod(storage_path('framework/sessions'), 0775);
                    try { Artisan::call('storage:link'); } catch (\Exception $e) {}
                    Notifier::send("⚠️ *Storage Permission Fix*\nPermission storage diperbaiki otomatis.", 'warning');
                    $fixed = true;
                    break;

                case 'log_bloated':
                    $mb = self::truncateLog();
                    Notifier::send("⚠️ *Log Bloated*\nlaravel.log melebihi 50 MB. Dipotong otomatis ({$mb} MB).", 'warning');
                    $fixed = true;
                    break;

                case 'session_domain':
                    // Auto-set SESSION_DOMAIN if missing
                    $host = parse_url(config('app.url'), PHP_URL_HOST);
                    self::updateEnv('SESSION_DOMAIN', '.' . $host);
                    Notifier::send("⚠️ *Session Domain Missing*\nSESSION_DOMAIN diset ke .{$host} otomatis.", 'warning');
                    $fixed = true;
                    break;

                case 'captcha_patch':
                    Notifier::send("🚨 *Captcha Patch HILANG*\nLogika persistensi captcha hilang dari AuthController.php!", 'critical');
                    Log::critical('SystemGuard: Captcha persistence patch is missing from AuthController!');
                    $fixed = false;
                    break;

                case 'nginx_down':
                    Notifier::send("🚨 *NGINX DOWN*\nNginx tidak merespons health check. Portal tidak bisa diakses!", 'critical');
                    Log::critical('SystemGuard: Nginx health check failed!');
                    $fixed = false;
                    break;

                case 'earth_data_mismatch':
                    Artisan::call('app:audit-integrity', ['--fix' => true]);
                    Notifier::send("⚠️ *Data Steman Earth Mismatch*\nKoordinat alumni hilang ditemukan. Auto-healing geocoding dijalankan.", 'warning');
                    $fixed = true;
                    break;

                case 'ai_offline':
                    Notifier::send("🚨 *AI SERVICE OFFLINE*\nSemua layanan AI (Gemini & Fallback) tidak merespons. Fitur cerdas dimatikan sementara.", 'critical');
                    $fixed = false;
                    break;

                default:
                    Log::warning("SystemGuard Fixer: Unknown issue [{$issue}]");
                    break;
            }

            if ($fixed) {
                Rules::reset($issue);
            }

        } catch (\Throwable $e) {
            Log::error("SystemGuard Fixer: Exception while fixing [{$issue}]: " . $e->getMessage());
            Notifier::send("🚨 *Auto-Fix Gagal*\nIssue: `{$issue}`\nError: " . $e->getMessage(), 'critical');
        }

        return $fixed;
    }

    private static function cleanLogs(): int
    {
        $cleaned = 0;
        $logDir = storage_path('logs');
        foreach (glob($logDir . '/*.log') as $file) {
            if (filesize($file) > 5 * 1024 * 1024) {
                $mb = round(filesize($file) / 1024 / 1024, 1);
                file_put_contents($file, '');
                $cleaned += $mb;
            }
        }
        return $cleaned;
    }

    private static function truncateLog(): int
    {
        $log = storage_path('logs/laravel.log');
        $mb = round(filesize($log) / 1024 / 1024, 1);
        file_put_contents($log, '');
        return $mb;
    }

    private static function updateEnv(string $key, string $value): void
    {
        $path = base_path('.env');
        if (!file_exists($path)) return;
        $content = file_get_contents($path);
        if (str_contains($content, $key . '=')) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}\n";
        }
        file_put_contents($path, $content);
    }
}
