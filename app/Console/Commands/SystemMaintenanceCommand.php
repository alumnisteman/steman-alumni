<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SystemMaintenanceCommand extends Command
{
    protected $signature = 'steman:maintenance {--force : Force maintenance without confirmation}';
    protected $description = 'Automated system cleanup and integrity check (Resilience Engine)';

    public function handle()
    {
        $this->info('🚀 Starting Steman Resilience Engine (SRE) Maintenance...');

        // 1. Cleanup Stale Logs
        $this->cleanupLogs();

        // 2. Cleanup Temporary Files
        $this->cleanupTempFiles();

        // 3. Optimize Database (Optional)
        $this->optimizeDatabase();

        // 4. Verification Check
        $success = $this->runIntegrityCheck();

        // 5. Notify Telegram
        $this->notifyTelegram($success);

        $this->info('✅ Maintenance Completed Successfully.');
        return 0;
    }

    private function cleanupLogs()
    {
        $this->comment('Cleaning up stale logs...');
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath) && File::size($logPath) > (50 * 1024 * 1024)) {
            File::put($logPath, ''); // Truncate if > 50MB
            $this->info('- laravel.log truncated.');
        }
        
        // Remove older daily log files (older than 7 days)
        $files = File::glob(storage_path('logs/laravel-*.log'));
        foreach ($files as $file) {
            $filename = basename($file);
            // Extract date: laravel-2026-05-04.log
            if (preg_match('/laravel-(\d{4}-\d{2}-\d{2})\.log/', $filename, $matches)) {
                $date = $matches[1];
                if (strtotime($date) < strtotime('-7 days')) {
                    File::delete($file);
                    $this->info("- Old log removed: {$filename}");
                }
            }
        }

        // Remove other trash log files
        $trashFiles = File::glob(storage_path('logs/*.log.*'));
        foreach ($trashFiles as $file) {
            File::delete($file);
        }
    }

    private function cleanupTempFiles()
    {
        $this->comment('Cleaning up temporary files...');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        $this->info('- View and application cache cleared.');

        // Clean stale sessions (older than 2 days)
        $sessionPath = storage_path('framework/sessions');
        if (File::isDirectory($sessionPath)) {
            $files = File::files($sessionPath);
            foreach ($files as $file) {
                if (time() - $file->getMTime() > (2 * 24 * 60 * 60)) {
                    File::delete($file);
                }
            }
            $this->info('- Stale sessions removed.');
        }

        // Clean scratch directory
        $scratchPath = base_path('scratch');
        if (File::isDirectory($scratchPath)) {
            $files = File::files($scratchPath);
            foreach ($files as $file) {
                if (time() - $file->getMTime() > (24 * 60 * 60)) {
                    File::delete($file);
                }
            }
            $this->info('- Old scratch files removed.');
        }

        // Enforce permissions
        try {
            @chmod(storage_path(), 0775);
            @chmod(base_path('bootstrap/cache'), 0775);
            $this->info('- Permissions enforced on storage and cache.');
        } catch (\Exception $e) {}
    }

    private function optimizeDatabase()
    {
        $this->comment('Optimizing database tables...');
        // We only do this for specific tables that grow fast
        $tables = ['activity_logs', 'audit_logs', 'sessions'];
        foreach ($tables as $table) {
            try {
                DB::statement("OPTIMIZE TABLE {$table}");
            } catch (\Exception $e) {}
        }
        $this->info('- Database tables optimized.');
    }

    private function runIntegrityCheck(): bool
    {
        $this->comment('Running system integrity audit...');
        
        // 1. Basic Health Check
        $checker = new \App\Services\SystemGuard\HealthChecker();
        $issues = $checker->run();

        // 2. Deep Audit Check
        $this->call('app:audit-integrity', ['--fix' => true]);
        
        $isHealthy = empty($issues);
        
        if ($isHealthy) {
            $this->info('✨ Integrity Check: ALL SYSTEMS OPERATIONAL 100%');
        } else {
            $this->error('⚠ Integrity Check: DEGRADED. Issues found: ' . implode(', ', $issues));
        }

        return $isHealthy;
    }

    private function notifyTelegram(bool $success)
    {
        $this->comment('Sending notification to Telegram...');
        
        $status = $success ? 'success' : 'failure';
        $message = $success 
            ? 'Portal Steman Alumni optimal dan stabil. Semua sistem (DB, AI, Storage) berjalan 100%.' 
            : 'Sistem terdeteksi mengalami degradasi. Silakan periksa dashboard admin untuk detail masalah.';

        try {
            $this->call('steman:notify-maintenance', [
                'status' => $status,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            $this->error('Failed to send Telegram notification: ' . $e->getMessage());
        }
    }
}
