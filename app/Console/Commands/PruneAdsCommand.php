<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PruneAdsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ads:prune {--dry-run : Only show files that would be deleted}';

    /**
     * The console command description.
     */
    protected $description = 'Remove orphaned advertisement images from storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting advertisement image audit...');

        $files = Storage::disk('public')->files('ads');
        $totalFiles = count($files);

        if ($totalFiles === 0) {
            $this->info('No advertisement images found in storage/ads.');
            return;
        }

        $this->info("Found {$totalFiles} files. Checking database references...");

        // CRITICAL: Use raw DB query to get stored paths WITHOUT triggering
        // Eloquent accessors which would transform paths into full URLs.
        $rows = DB::table('ads')->select(['image_desktop', 'image_mobile'])->get();

        $dbImages = $rows->flatMap(fn($row) => [$row->image_desktop, $row->image_mobile])
            ->filter()
            ->map(function ($path) {
                // Normalize: strip any leading slash or 'storage/' prefix
                $path = ltrim($path, '/');
                if (str_starts_with($path, 'storage/')) {
                    $path = substr($path, 8);
                }
                return $path;
            })
            ->unique()
            ->values()
            ->toArray();

        $orphanedCount = 0;
        foreach ($files as $file) {
            if (!in_array($file, $dbImages)) {
                $orphanedCount++;
                if ($this->option('dry-run')) {
                    $this->line(" <comment>[Dry Run]</comment> Found ghost file: {$file}");
                } else {
                    Storage::disk('public')->delete($file);
                    $this->line(" <fg=red>[-] Deleted orphaned file: {$file}</>");
                }
            }
        }

        if ($orphanedCount === 0) {
            $this->info('✔ No ghost files found. System is clean.');
        } else {
            $action = $this->option('dry-run') ? 'Found' : 'Deleted';
            $this->info("✔ Audit complete. {$action} {$orphanedCount} ghost file(s).");
        }
    }
}

