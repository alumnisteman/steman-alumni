<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Register custom commands here
        \App\Console\Commands\MaintenanceCleanup::class,
        \App\Console\Commands\MonitorTelegram::class,
        \App\Console\Commands\SystemHealth::class,
        \App\Console\Commands\CleanLogs::class,

    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Heartbeat setiap menit — dipakai SystemGuard untuk deteksi scheduler mati
        $schedule->command('scheduler:heartbeat')->everyMinute();
        // Clean old logs daily at 02:30 (backup sudah ada di bootstrap/app.php 02:00)
        $schedule->command('logs:clean')->dailyAt('02:30')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
