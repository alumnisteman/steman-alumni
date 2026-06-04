<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CleanLogs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'logs:clean';

    /**
     * The console command description.
     */
    protected $description = 'Delete Laravel log files older than 30 days';

    public function handle()
    {
        $logPath = storage_path('logs');
        if (!File::exists($logPath)) {
            $this->info('Log directory does not exist.');
            return 0;
        }

        $files = File::files($logPath);
        $now = Carbon::now();
        $deleted = 0;
        foreach ($files as $file) {
            $modified = Carbon::createFromTimestamp(File::lastModified($file));
            if ($now->diffInDays($modified) > 30) {
                File::delete($file);
                $deleted++;
            }
        }
        $this->info("Deleted {$deleted} log file(s) older than 30 days.");
        return 0;
    }
}
?>
