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
        $this->runIntegrityCheck();

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
        
        // Remove older log files if any
        $files = File::glob(storage_path('logs/*.log.*'));
        foreach ($files as $file) {
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

    private function runIntegrityCheck()
    {
        $this->comment('Running system integrity audit...');
        
        // 1. Basic Health Check
        $checker = new \App\Services\SystemGuard\HealthChecker();
        $issues = $checker->run();

        // 2. Deep Audit Check
        Artisan::call('steman:audit', ['--fix' => true]);
        $auditOutput = Artisan::output();
        $this->line($auditOutput);

        if (empty($issues) && Artisan::call('steman:audit') === 0) {
            $this->info('✨ Integrity Check: ALL SYSTEMS OPERATIONAL 100%');
        } else {
            $this->error('⚠ Integrity Check: DEGRADED. Issues found.');
        }
    }
}
