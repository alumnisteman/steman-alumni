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
        // Run system health check every 5 minutes
        // Run system health check every 5 minutes
        // $schedule->command('system:health')->everyFiveMinutes()->withoutOverlapping();
        // Daily MySQL backup at 02:15
        $schedule->command('steman:backup')->dailyAt('02:15')->withoutOverlapping();
        // Clean old logs daily at 02:30
        $schedule->command('logs:clean')->dailyAt('02:30');
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
