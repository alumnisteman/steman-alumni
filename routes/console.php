<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('ai:generate-news')->weeklyOn(1, '08:00');

// Database Maintenance: Optimize tables weekly on Sunday at 2 AM
Schedule::call(function () {
    $tables = ['posts', 'forums', 'comments', 'users', 'contact_messages', 'log_activities'];
    foreach ($tables as $table) {
        \Illuminate\Support\Facades\DB::statement("OPTIMIZE TABLE {$table}");
    }
    \Illuminate\Support\Facades\Log::info("Database Optimization Completed: " . implode(', ', $tables));
})->weeklyOn(0, '02:00');
