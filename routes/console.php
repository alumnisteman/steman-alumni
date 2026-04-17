<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('ai:generate-news')->weeklyOn(1, '08:00');

// Advertisement Cleanup: Remove orphaned images weekly on Sunday at 1 AM
Schedule::command('ads:prune')->weeklyOn(0, '01:00');

// Ad Cache Auto-Refresh: Rebuild cache every 5 minutes so date-based
// activation/expiration is reflected without waiting for the 1-hour TTL.
Schedule::call(function () {
    \Illuminate\Support\Facades\Cache::forget('active_ads');
    $ads = \App\Models\Ad::active()->get()->groupBy(function ($item) {
        return strtolower(trim($item->position));
    });
    \Illuminate\Support\Facades\Cache::put('active_ads', $ads, 3600);
})->everyFiveMinutes()->name('ads:refresh-cache');

// Database Maintenance: Optimize tables weekly on Sunday at 2 AM
Schedule::call(function () {
    $tables = ['posts', 'forums', 'comments', 'users', 'contact_messages', 'log_activities', 'ads', 'sessions'];
    foreach ($tables as $table) {
        \Illuminate\Support\Facades\DB::statement("OPTIMIZE TABLE {$table}");
    }
    \Illuminate\Support\Facades\Log::info("Full Database Optimization Completed: " . implode(', ', $tables));
})->weeklyOn(0, '02:00');

// Log Cleanup: Prune old scheduler logs daily at 3 AM
Schedule::call(function () {
    $logFile = storage_path('logs/scheduler.log');
    if (file_exists($logFile) && filesize($logFile) > 5 * 1024 * 1024) {
        file_put_contents($logFile, ''); // Truncate if > 5MB
        \Illuminate\Support\Facades\Log::info('Scheduler log truncated (exceeded 5MB).');
    }
})->dailyAt('03:00')->name('logs:prune-scheduler');

// Automated System Health Audit: Hourly Check for Errors, Permissions, and Data Integrity (ULTIMATE GUARDIAN)
Schedule::call(function () {
    $guardian = app(\App\Services\GuardianService::class);
    $guardian->performDeepScan();
})->hourly()->name('app:guardian-deep-scan');

// Automated Daily System Check & Integrity Healing
Schedule::command('app:audit-integrity --fix')->dailyAt('00:00')->name('app:audit-integrity-fix');
Schedule::command('app:system-check')->dailyAt('00:30')->name('app:system-check-daily');
