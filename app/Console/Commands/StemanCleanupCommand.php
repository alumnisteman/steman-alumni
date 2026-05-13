<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class StemanCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steman:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up garbage files, old logs, and expired tokens to maintain system performance.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting System Cleanup...');

        $this->cleanLogs();
        $this->cleanTokens();
        $this->cleanTempFolders();
        $this->pruneActivityLogs();

        $this->info('Cleanup completed successfully.');
    }

    private function cleanLogs()
    {
        $logPath = storage_path('logs');
        $files = File::files($logPath);
        $deleted = 0;

        foreach ($files as $file) {
            // Only target .log files
            if ($file->getExtension() === 'log') {
                $lastModified = Carbon::createFromTimestamp($file->getMTime());
                
                // If file is older than 7 days
                if ($lastModified->diffInDays(now()) > 7) {
                    File::delete($file->getPathname());
                    $deleted++;
                }
            }
        }

        $this->line("Deleted {$deleted} old log files.");
    }

    private function cleanTokens()
    {
        // Delete password reset tokens older than 24 hours (1440 minutes)
        try {
            $deletedResets = DB::table('password_reset_tokens')
                ->where('created_at', '<', now()->subHours(24))
                ->delete();
            $this->line("Deleted {$deletedResets} expired password reset tokens.");
        } catch (\Exception $e) {
            $this->error("Failed to clean password reset tokens: " . $e->getMessage());
        }

        // Delete personal access tokens that expired or haven't been used in a long time (optional)
        try {
            $deletedTokens = DB::table('personal_access_tokens')
                ->where(function($query) {
                    $query->whereNotNull('expires_at')
                          ->where('expires_at', '<', now());
                })
                ->orWhere('last_used_at', '<', now()->subDays(30)) // Clean tokens not used in 30 days
                ->delete();
            $this->line("Deleted {$deletedTokens} unused/expired API tokens.");
        } catch (\Exception $e) {
            // Ignore if table doesn't exist or other error
        }
    }

    private function cleanTempFolders()
    {
        $tempPaths = [
            storage_path('app/public/temp/*'),
            base_path('scratch/*'),
            storage_path('debugbar/*'),
        ];

        $deleted = 0;
        foreach ($tempPaths as $pattern) {
            foreach (File::glob($pattern) as $file) {
                if (File::isFile($file) && (time() - File::lastModified($file) > 86400 * 3)) { // 3 days
                    File::delete($file);
                    $deleted++;
                }
            }
        }
        $this->line("Deleted {$deleted} temporary/scratch files.");
    }

    private function pruneActivityLogs()
    {
        try {
            $deleted = DB::table('activity_logs')
                ->where('created_at', '<', now()->subMonths(3))
                ->delete();
            $this->line("Pruned {$deleted} activity logs older than 3 months.");
        } catch (\Exception $e) {}
    }
}
