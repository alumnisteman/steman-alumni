<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SchedulerHeartbeat extends Command
{
    protected $signature   = 'scheduler:heartbeat';
    protected $description = 'Tulis timestamp heartbeat ke cache agar SystemGuard tahu scheduler masih hidup';

    public function handle(): int
    {
        Cache::put('system_guard:scheduler_heartbeat', time(), now()->addMinutes(15));
        return 0;
    }
}
