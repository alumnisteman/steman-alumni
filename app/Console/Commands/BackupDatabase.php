<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steman:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a secure database backup to local storage.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Database Backup...');
        
        $filename = "steman-backup-" . now()->format('Y-m-d-H-i-s') . ".sql";
        $path = storage_path("app/backups/{$filename}");
        
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $command = sprintf(
            'mariadb-dump --skip-ssl -h %s -P %s -u %s -p%s %s > %s',
            config('database.connections.mysql.host'),
            config('database.connections.mysql.port'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $path
        );

        $output = [];
        $returnVar = 0;
        exec($command . " 2>&1", $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Backup successful: {$filename}");
            // Compress the backup
            exec("gzip {$path}");
            
            // Clean up backups older than 7 days
            $files = glob(storage_path('app/backups/*.gz'));
            $now = time();
            foreach ($files as $file) {
                if ($now - filemtime($file) >= 7 * 24 * 60 * 60) {
                    unlink($file);
                }
            }
            
            Log::info("System Pulse: Database backup completed successfully: {$filename}.gz");
        } else {
            $errorMsg = implode("\n", $output);
            $this->error("Backup failed: {$errorMsg}");
            Log::error("System Pulse: Database backup FAILED. Error: {$errorMsg}");
        }

        return $returnVar === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
