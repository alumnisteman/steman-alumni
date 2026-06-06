<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SystemGuard\Fixer;
use App\Services\SystemGuard\HealthChecker;
use App\Services\SystemGuard\Notifier;
use App\Services\SystemGuard\Rules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemGuardController extends Controller
{
    /**
     * Realtime monitoring dashboard data endpoint (JSON API).
     * Menggunakan cache 30 detik agar tidak membebani server saat banyak admin membuka dashboard.
     */
    public function status()
    {
        $cacheKey = 'system_guard:status_cache';

        $payload = Cache::remember($cacheKey, 30, function () {
            $checker  = new HealthChecker();
            $issues   = $checker->run();
            $results  = $checker->getResults();
            $circuits = Rules::getAllStats();

            $anomalies = [];
            foreach ($circuits as $key => $data) {
                if ($data['total_failures'] >= 3) {
                    $anomalies[] = [
                        'issue'  => $key,
                        'total'  => $data['total_failures'],
                        'state'  => $data['state'],
                    ];
                }
            }

            $passed    = count(array_filter($results, fn($v) => $v === 'OK'));
            $total     = count($results);
            $overallOk = empty($issues);

            return [
                'status'    => $overallOk ? 'OPERATIONAL' : 'DEGRADED',
                'timestamp' => now()->toIso8601String(),
                'checks'    => $results,
                'issues'    => $issues,
                'circuits'  => $circuits,
                'anomalies' => $anomalies,
                'summary'   => [
                    'total'   => $total,
                    'passed'  => $passed,
                    'failed'  => count($issues),
                    'percent' => $total > 0 ? round(($passed / $total) * 100) : 100,
                ],
            ];
        });

        return \response()->json($payload);
    }

    /**
     * Jalankan auto-fix untuk semua issue yang terdeteksi saat ini.
     * Endpoint ini dipanggil dari tombol di dashboard.
     */
    public function autofix()
    {
        try {
            // Invalidate status cache dulu agar check fresh
            Cache::forget('system_guard:status_cache');

            $checker = new HealthChecker();
            $issues  = $checker->run();

            if (empty($issues)) {
                return \response()->json([
                    'status'  => 'success',
                    'message' => 'Semua sistem dalam kondisi optimal. Tidak ada yang perlu diperbaiki.',
                    'fixed'   => [],
                    'failed'  => [],
                ]);
            }

            $fixed  = [];
            $failed = [];

            foreach ($issues as $issue) {
                $result = Fixer::handle($issue);
                if ($result) {
                    $fixed[] = $issue;
                } else {
                    $failed[] = $issue;
                }
            }

            // Invalidate cache lagi setelah fix
            Cache::forget('system_guard:status_cache');
            Cache::forget('healthcheck:news_api');

            $msg = count($fixed) > 0
                ? '✅ Auto-fix berhasil: ' . implode(', ', $fixed)
                : '';
            if (!empty($failed)) {
                $msg .= (empty($msg) ? '' : "\n") . '⚠️ Perlu perhatian manual: ' . implode(', ', $failed);
            }

            Log::info('SystemGuard Manual AutoFix: fixed=[' . implode(',', $fixed) . '] failed=[' . implode(',', $failed) . ']');

            return \response()->json([
                'status'  => empty($failed) ? 'success' : 'partial',
                'message' => $msg,
                'fixed'   => $fixed,
                'failed'  => $failed,
            ]);
        } catch (\Throwable $e) {
            Log::error('SystemGuard autofix exception: ' . $e->getMessage());
            return \response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat auto-fix: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Realtime monitoring HTML dashboard.
     */
    public function dashboard()
    {
        return \view('admin.system.guard');
    }

    /**
     * Trigger system optimization.
     */
    public function optimize()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize');
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            return \response()->json([
                'status'  => 'success',
                'message' => 'System Optimization completed.',
                'details' => $output
            ]);
        } catch (\Exception $e) {
            return \response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all caches safely.
     */
    public function clearCache()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            return \response()->json([
                'status'  => 'success',
                'message' => 'Cache and Configuration cleared.',
                'details' => $output
            ]);
        } catch (\Exception $e) {
            return \response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manual maintenance run.
     */
    public function maintenance()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('steman:maintenance', ['--force' => true]);
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            return \response()->json([
                'status'  => 'success',
                'message' => 'SRE Maintenance completed.',
                'details' => $output
            ]);
        } catch (\Exception $e) {
            return \response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
