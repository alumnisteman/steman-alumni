<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanTempFiles extends Command
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
    protected $description = 'Clean up temporary, backup, and junk files from the codebase';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting System Cleanup...');

        $junkPatterns = [
            '*.tmp',
            '*.bak',
            '*.swp',
            '~*',
            '.DS_Store'
        ];

        $dirs = [
            app_path(),
            resource_path('views'),
            public_path(),
            base_path('routes'),
            base_path('database'),
        ];

        $count = 0;

        foreach ($dirs as $dir) {
            if (!File::isDirectory($dir)) continue;

            $this->comment("Scanning: $dir");
            
            try {
                $finder = new \Symfony\Component\Finder\Finder();
                $finder->files()
                    ->in($dir)
                    ->ignoreDotFiles(false)
                    ->name($junkPatterns);

                foreach ($finder as $file) {
                    $path = $file->getRealPath();
                    $this->warn("Removing: $path");
                    File::delete($path);
                    $count++;
                }
            } catch (\Exception $e) {
                $this->error("Error scanning $dir: " . $e->getMessage());
            }
        }

        // Clean stale view cache if it exists
        if ($this->confirm('Clean compiled view cache too?', false)) {
            \Artisan::call('view:clear');
            $this->info('View cache cleared.');
        }

        $this->info("Cleanup completed. Total files removed: $count");
    }
}
