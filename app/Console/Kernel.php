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
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Run cleanup daily at 02:00
        $schedule->command('maintenance:cleanup')->dailyAt('02:00');
        // Check logs every 30 minutes and notify via Telegram
        $schedule->command('monitor:telegram')->everyThirtyMinutes();
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
