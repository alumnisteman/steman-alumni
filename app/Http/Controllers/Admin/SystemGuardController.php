<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SystemGuard\HealthChecker;
use App\Services\SystemGuard\Rules;
use Illuminate\Support\Facades\Cache;

class SystemGuardController extends Controller
{
    /**
     * Realtime monitoring dashboard data endpoint (JSON API).
     */
    public function status()
    {
        $checker = new HealthChecker();
        $issues  = $checker->run();
        $results = $checker->getResults();
        $circuits = Rules::getAllStats();

        // Anomaly detection: flag checks that have high total failures
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

        $overallOk = empty($issues);

        return \response()->json([
            'status'    => $overallOk ? 'OPERATIONAL' : 'DEGRADED',
            'timestamp' => \now()->toIso8601String(),
            'checks'    => $results,
            'issues'    => $issues,
            'circuits'  => $circuits,
            'anomalies' => $anomalies,
            'summary'   => [
                'total'   => count($results),
                'passed'  => count(array_filter($results, fn($v) => $v === 'OK')),
                'failed'  => count($issues),
                'percent' => count($results) > 0
                    ? round((count(array_filter($results, fn($v) => $v === 'OK')) / count($results)) * 100)
                    : 100,
            ],
        ]);
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
