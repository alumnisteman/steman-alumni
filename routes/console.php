<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('ai:generate-news')->weeklyOn(1, '08:00');

// AI Feed & Auto Viral Engine (Trending & NewsAPI Aggregator)
Schedule::call(function () {
    app(\App\Services\NewsAggregator::class)->get();
})->everyTenMinutes()->name('ai:news-aggregator');

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
Schedule::command('system:autofix --force')->dailyAt('01:30')->name('system:autofix-daily');

// ─── SystemGuard: Auto-healing every 5 minutes ──────────────────────
// Detects DB/Redis/Meilisearch/Disk/Storage issues, fixes what it can,
// and sends a Telegram alert for anything needing human attention.
Schedule::command('system:guard')->everyFiveMinutes()->name('system:guard');

// Daily morning health report to Telegram (confirms all-clear or flags issues)
Schedule::command('system:guard --report')->dailyAt('07:00')->name('system:guard-daily-report');


// Manual Testing Command for Autonomous Agent
Artisan::command('agent:heal {file} {line} {--error=Manual trigger}', function ($file, $line) {
    $this->info("Triggering AI Agent on $file:$line");
    \App\Jobs\AIAgentDiagnoseJob::dispatch($this->option('error'), $file, $line);
    $this->info('Job dispatched to background queue.');
})->purpose('Manually trigger the Autonomous AI Agent to heal a specific file');

// Prune expired database sessions daily at 04:00 (keeps sessions table small)
Schedule::call(function () {
    $pruned = \Illuminate\Support\Facades\DB::table('sessions')
        ->where('last_activity', '<', now()->subHours(24)->timestamp)
        ->delete();
    \Illuminate\Support\Facades\Log::info("Session pruning complete. Removed {$pruned} expired sessions.");
})->dailyAt('04:00')->name('sessions:prune');

// Soft-delete orphaned messages (where sender or receiver was deleted) weekly
Schedule::call(function () {
    $orphaned = \App\Models\Message::whereNull('sender_id')
        ->whereNull('deleted_at')
        ->count();
    if ($orphaned > 0) {
        \App\Models\Message::whereNull('sender_id')->delete();
        \Illuminate\Support\Facades\Log::info("Pruned {$orphaned} orphaned messages.");
    }
})->weeklyOn(0, '03:00')->name('messages:prune-orphans');

// Guard: Auto-truncate laravel.log if it exceeds 20MB (prevents disk full crashes)
Schedule::call(function () {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile) && filesize($logFile) > 20 * 1024 * 1024) {
        file_put_contents($logFile, '');
        \Illuminate\Support\Facades\Log::warning('laravel.log auto-truncated: exceeded 20MB limit.');
    }
})->hourly()->name('logs:guard-size');
