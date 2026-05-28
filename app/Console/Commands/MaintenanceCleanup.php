<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class MaintenanceCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus log lama (>30 hari), cache, dan view yang tidak terpakai.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai cleanup...');
        $this->cleanLogs();
        $this->cleanCache();
        $this->info('Cleanup selesai');
    }

    protected function cleanLogs()
    {
        $logPath = storage_path('logs');
        $files = File::files($logPath);
        $now = Carbon::now();
        $retentionDays = env('LOG_RETENTION_DAYS', 30);
        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp(File::lastModified($file));
            if ($now->diffInDays($lastModified) > $retentionDays) {
                File::delete($file);
                $this->line("Deleted log: {$file->getFilename()}");
            }
        }
    }

    protected function cleanCache()
    {
        $this->call('view:clear');
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
    }
}
