<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SystemController extends Controller
{
    /**
     * Display the last few entries of the system logs.
     * This provides a "no-ssh" way for admins to debug issues.
     */
    public function logs(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        $emergencyPath = storage_path('logs/emergency_fatal.log');

        $logs = "";
        $emergencyLogs = "";

        if (File::exists($logPath)) {
            $logs = $this->tailCustom($logPath, 200);
        } else {
            $logs = "Log file not found at: " . $logPath;
        }

        if (File::exists($emergencyPath)) {
            $emergencyLogs = $this->tailCustom($emergencyPath, 50);
        }

        return view('admin.system.logs', compact('logs', 'emergencyLogs', 'logPath', 'emergencyPath'));
    }

    /**
     * Simple PHP implementation of tail -n
     */
    private function tailCustom($filepath, $lines = 100)
    {
        $data = file($filepath);
        $lineCount = count($data);
        $start = max(0, $lineCount - $lines);
        $subset = array_slice($data, $start);
        
        return implode("", $subset);
    }

    /**
     * Clear all log files to start fresh.
     */
    /**
     * Display the System Pulse (Architecture Diagram).
     */
    public function pulse()
    {
        return view('admin.system.pulse');
    }

    /**
     * API for live health status of all architecture nodes.
     */
    public function healthApi()
    {
        $status = [
            'nodes' => [
                'user' => ['status' => 'up', 'label' => 'User Device'],
                'nginx' => ['status' => 'up', 'label' => 'Nginx Reverse Proxy'],
                'laravel' => ['status' => 'up', 'label' => 'Laravel App'],
                'mysql' => ['status' => 'down', 'label' => 'MySQL Database'],
                'redis' => ['status' => 'down', 'label' => 'Redis Cache'],
                'newsapi' => ['status' => 'down', 'label' => 'News API'],
                'rsshub' => ['status' => 'down', 'label' => 'RSSHub'],
            ],
            'timestamp' => now()->toIso8601String()
        ];

        // Check DB
        try {
            \DB::connection()->getPdo();
            $status['nodes']['mysql']['status'] = 'up';
        } catch (\Exception $e) {}

        // Check Redis
        try {
            \Illuminate\Support\Facades\Redis::connection()->ping();
            $status['nodes']['redis']['status'] = 'up';
        } catch (\Exception $e) {}

        // Check External APIs (Simple ping or cache check)
        $status['nodes']['newsapi']['status'] = config('services.newsapi.key') ? 'up' : 'down';
        
        // Check RSSHub (Assume up if reachable)
        try {
            $res = \Illuminate\Support\Facades\Http::timeout(3)->get('https://rsshub.app/');
            if ($res->status() < 500) $status['nodes']['rsshub']['status'] = 'up';
        } catch (\Exception $e) {}

        return response()->json($status);
    }

    public function clearLogs()
    {
        $files = [
            storage_path('logs/laravel.log'),
            storage_path('logs/emergency_fatal.log')
        ];

        foreach ($files as $file) {
            if (File::exists($file)) {
                File::put($file, "");
            }
        }

        return back()->with('success', 'Semua file log telah dibersihkan.');
    }
}
