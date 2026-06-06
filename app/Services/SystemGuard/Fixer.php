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
                    // Coba reset cache AI gateway supaya bisa retry provider lain
                    \Illuminate\Support\Facades\Cache::forget('ai_gateway_health');
                    \Illuminate\Support\Facades\Cache::forget('ai_provider_status');
                    Artisan::call('cache:clear');
                    Notifier::send("🚨 *AI SERVICE OFFLINE*\nSemua layanan AI tidak merespons. Cache AI telah direset, sistem akan retry otomatis. Jika tetap gagal, periksa API key provider.", 'critical');
                    Log::critical('SystemGuard: AI service offline — cache reset, retrying next cycle.');
                    $fixed = true; // Reset cache = upaya penyembuhan sudah dilakukan
                    break;

                case 'route_mismatch':
                    Artisan::call('optimize:clear');
                    Artisan::call('route:clear');
                    Artisan::call('config:clear');
                    Notifier::send("⚠️ *Route Mismatch detected*\nSistem mendeteksi inkonsistensi route. Cache route & config telah dibersihkan otomatis.", 'warning');
                    Log::warning('SystemGuard: Route mismatch fixed via optimize:clear + route:clear');
                    $fixed = true;
                    break;

                case 'route_shadowing':
                    Notifier::send("⚠️ *Route Shadowing detected*\nBeberapa route mungkin tidak bisa diakses karena tertutup wildcard. Periksa `web.php`.", 'warning');
                    Log::warning('SystemGuard: Route shadowing detected in web.php');
                    $fixed = false;
                    break;

                case 'migration_mismatch':
                    // Jalankan migrasi yang pending secara aman
                    try {
                        Artisan::call('migrate', ['--force' => true]);
                        $output = Artisan::output();
                        Notifier::send("⚠️ *Migrasi Pending Dijalankan*\nSistem menemukan migrasi belum dijalankan dan mengeksekusinya otomatis.\n`{$output}`", 'warning');
                        Log::warning('SystemGuard: Pending migrations auto-ran: ' . $output);
                        $fixed = true;
                    } catch (\Exception $e) {
                        Notifier::send("🚨 *Migrasi Gagal*\nAuto-migrasi gagal: " . $e->getMessage(), 'critical');
                        $fixed = false;
                    }
                    break;

                case 'symlink_broken':
                    try {
                        Artisan::call('storage:link');
                        Notifier::send("⚠️ *Storage Symlink Diperbaiki*\nSymlink public/storage yang rusak telah dibuat ulang otomatis.", 'warning');
                        Log::warning('SystemGuard: Storage symlink recreated.');
                        $fixed = true;
                    } catch (\Exception $e) {
                        Notifier::send("🚨 *Storage Symlink Gagal*\nGagal membuat ulang symlink: " . $e->getMessage(), 'critical');
                        $fixed = false;
                    }
                    break;

                case 'audit_broken':
                    // Bersihkan cache audit dan coba perbaiki
                    \Illuminate\Support\Facades\Cache::forget('audit_integrity_cache');
                    Artisan::call('cache:clear');
                    Notifier::send("⚠️ *Audit Integrity Bermasalah*\nCache audit dibersihkan. Periksa tabel audit_logs untuk record yang korup.", 'warning');
                    Log::warning('SystemGuard: Audit integrity issue — cache cleared, needs manual review of audit_logs.');
                    $fixed = true; // Cache clear = sudah ada upaya penyembuhan
                    break;

                case 'smoke_test':
                    // Jika smoke test gagal, clear semua cache + restart queue
                    Artisan::call('optimize:clear');
                    Artisan::call('queue:restart');
                    Notifier::send("⚠️ *Smoke Test Gagal*\nBeberapa halaman utama mengembalikan error 500. Cache dibersihkan & queue di-restart. Periksa laravel.log untuk detail.", 'warning');
                    Log::warning('SystemGuard: Smoke test failed — cache cleared, queue restarted.');
                    $fixed = true;
                    break;

                case 'news_api_down':
                    // Hapus cache news API agar bisa dicek ulang, dan clear cache berita
                    \Illuminate\Support\Facades\Cache::forget('healthcheck:news_api');
                    \Illuminate\Support\Facades\Cache::forget('multi_news_v2');
                    $key = config('services.newsapi.key');
                    if (empty($key)) {
                        Notifier::send("🚨 *News API DOWN*\nNEWS_API_KEY tidak dikonfigurasi di `.env`. Berita eksternal tidak akan tampil.", 'critical');
                        $fixed = false;
                    } else {
                        Notifier::send("⚠️ *News API Tidak Merespons*\nCache berita dibersihkan. Sistem akan retry otomatis pada siklus berikutnya. Periksa kuota newsapi.org jika masalah berlanjut.", 'warning');
                        $fixed = true;
                    }
                    Log::warning('SystemGuard: News API health check failed — cache cleared for retry.');
                    break;

                case 'scheduler_dead':
                    // Tidak bisa restart cron dari PHP, tapi bisa kirim alert
                    Notifier::send("🚨 *Laravel Scheduler Mati*\nTidak ada tanda hidup dari scheduler dalam 15 menit. Cek `cron -l` di server dan pastikan `php artisan schedule:run` berjalan setiap menit.", 'critical');
                    Log::critical('SystemGuard: Laravel scheduler appears to be dead!');
                    $fixed = false; // Perlu intervensi manual
                    break;

                case 'queue_worker_dead':
                    Artisan::call('queue:restart');
                    Notifier::send("⚠️ *Queue Worker Bermasalah*\nTerlalu banyak failed jobs. Queue worker di-restart otomatis.", 'warning');
                    Log::warning('SystemGuard: Too many failed jobs — queue restarted.');
                    $fixed = true;
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
