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
