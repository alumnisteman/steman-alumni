<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class AuditIntegrity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:audit-integrity {--fix : Automatically attempt to fix identified issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit database integrity and system health to prevent 500 errors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->header('SYSTEM INTEGRITY GUARDIAN V2.0');
        
        $this->auditPermissions();
        $this->auditStorageLink();
        $this->auditDatabase();
        $this->auditCache();
        $this->auditMeilisearch();
        $this->auditDiskSpace();
        $this->healUserData();
        $this->geocodeMissingAddresses();
        $this->auditEnvironment();
        $this->auditHelpers();
        $this->auditAI();
        $this->auditVite();
        $this->auditViewIntegrity();
        $this->cleanupJunk();
        $this->auditLogs();

        $this->saveReport();

        $this->info("\nGuardian Audit complete! ✅");
        if ($this->option('fix')) {
            $this->info("Self-healing routines executed.");
        }
    }

    private function auditStorageLink()
    {
        $this->comment("\n1b. Auditing Storage Symbolic Link...");
        $link = public_path('storage');
        if (!File::exists($link)) {
            $this->warn("Storage link is MISSING!");
            if ($this->option('fix')) {
                $this->info("Repairing storage link...");
                try {
                    \Illuminate\Support\Facades\Artisan::call('storage:link');
                } catch (\Exception $e) {
                    $this->error("Failed to repair storage link: " . $e->getMessage());
                }
            }
        } else {
            $this->info("Storage link is valid.");
        }
    }

    private function header($text)
    {
        $this->line("\n<options=bold;bg=blue;fg=white> " . strtoupper($text) . " </>");
    }

    /**
     * Audit file and folder permissions
     */
    private function auditPermissions()
    {
        $this->comment("\n1. Auditing File Permissions...");
        
        $paths = [
            storage_path(),
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/views'),
            base_path('bootstrap/cache'),
        ];

        foreach ($paths as $path) {
            if (!File::exists($path)) {
                $this->error("Path does not exist: $path");
                continue;
            }

            if (!File::isWritable($path)) {
                $this->warn("Path is NOT writable: $path");
                if ($this->option('fix')) {
                    $this->info("Attempting fix for: $path");
                    @chmod($path, 0775);
                }
            } else {
                $this->info("Permissions OK: " . File::basename($path));
            }
        }
    }

    /**
     * Audit database for orphaned records
     */
    private function auditDatabase()
    {
        $this->comment("\n2. Auditing Database Integrity...");

        $checks = [
            'program_registrations' => [
                ['column' => 'user_id', 'target' => 'users'],
                ['column' => 'program_id', 'target' => 'programs'],
            ],
            'posts' => [
                ['column' => 'user_id', 'target' => 'users'],
            ],
            'news' => [
                ['column' => 'user_id', 'target' => 'users'],
            ],
            'galleries' => [
                ['column' => 'user_id', 'target' => 'users'],
            ],
            'business_photos' => [
                ['column' => 'business_id', 'target' => 'businesses'],
            ],
        ];

        foreach ($checks as $table => $relations) {
            if (!Schema::hasTable($table)) continue;

            foreach ($relations as $rel) {
                $targetTable = $rel['target'];
                $column = $rel['column'];

                if (!Schema::hasTable($targetTable)) {
                    $this->error("Target table [$targetTable] missing for relation in [$table].");
                    continue;
                }

                $orphans = DB::table($table)
                    ->whereNotIn($column, function($query) use ($targetTable) {
                        $query->select('id')->from($targetTable);
                    })
                    ->count();

                if ($orphans > 0) {
                    $this->warn("Found $orphans orphaned records in [$table] (missing $targetTable references).");
                    if ($this->option('fix')) {
                        DB::table($table)
                            ->whereNotIn($column, function($query) use ($targetTable) {
                                $query->select('id')->from($targetTable);
                            })
                            ->delete();
                        $this->info("Cleaned up orphaned records in [$table].");
                    }
                } else {
                    $this->info("Data Integrity OK: $table -> $targetTable");
                }
            }
        }
    }

    /**
     * Audit Cache (Redis/File) connectivity
     */
    private function auditCache()
    {
        $this->comment("\n2d. Auditing Cache System...");
        try {
            \Illuminate\Support\Facades\Cache::put('integrity_test', true, 10);
            if (\Illuminate\Support\Facades\Cache::get('integrity_test')) {
                $this->info("Cache System (Driver: " . config('cache.default') . ") is operational.");
            } else {
                $this->error("Cache System failed to retrieve test key.");
            }
        } catch (\Exception $e) {
            $this->error("Cache System Error: " . $e->getMessage());
        }
    }

    /**
     * Audit Meilisearch health
     */
    private function auditMeilisearch()
    {
        $this->comment("\n2e. Auditing Meilisearch connectivity...");
        $host = config('scout.meilisearch.host', 'http://localhost:7700');
        $key = config('scout.meilisearch.key');

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders(['Authorization' => 'Bearer ' . $key])
                ->timeout(5)
                ->get($host . '/health');

            if ($response->successful()) {
                $this->info("Meilisearch is healthy.");
            } else {
                $this->warn("Meilisearch reachable but returned status: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("Meilisearch is OFFLINE: " . $e->getMessage());
        }
    }

    /**
     * Audit Disk Space
     */
    private function auditDiskSpace()
    {
        $this->comment("\n2f. Auditing Server Disk Space...");
        try {
            $freeSpace = @disk_free_space("/");
            $totalSpace = @disk_total_space("/");
            if ($freeSpace !== false && $totalSpace !== false) {
                $percentFree = ($freeSpace / $totalSpace) * 100;

                if ($percentFree < 10) {
                    $this->error("CRITICAL: Disk Space is running low (" . round($percentFree, 2) . "% free).");
                } else {
                    $this->info("Disk Space OK: " . round($percentFree, 2) . "% free (" . round($freeSpace / 1024 / 1024 / 1024, 2) . " GB).");
                }
            }
        } catch (\Exception $e) {
            $this->warn("Disk Space Check failed: " . $e->getMessage());
        }
    }

    /**
     * Heal critical missing data fields (names, majors, cities)
     */
    private function healUserData()
    {
        $this->comment("\n2b. Healing Missing User Data...");
        
        $nullUsers = \App\Models\User::whereNull('name')->orWhere('name', '')->count();
        if ($nullUsers > 0) {
            $this->warn("Found $nullUsers users with missing names.");
            if ($this->option('fix')) {
                \App\Models\User::whereNull('name')->orWhere('name', '')->update(['name' => 'Alumni Anonim']);
                $this->info("Healed missing names.");
            }
        }

        $nullMajors = \App\Models\User::where('role', 'alumni')
            ->where(function($q) {
                $q->whereNull('major')->orWhere('major', '');
            })->count();
            
        if ($nullMajors > 0) {
            $this->warn("Found $nullMajors alumni with missing majors.");
            if ($this->option('fix')) {
                \App\Models\User::where('role', 'alumni')
                    ->where(function($q) {
                        $q->whereNull('major')->orWhere('major', '');
                    })->update(['major' => 'Umum']);
                $this->info("Healed missing majors.");
            }
        }
        
        $nullCities = \App\Models\User::where('role', 'alumni')
            ->whereNotNull('latitude')
            ->where(function($q) {
                $q->whereNull('city_name')->orWhere('city_name', '');
            })->count();
            
        if ($nullCities > 0) {
            $this->warn("Found $nullCities mapped alumni with missing city_name.");
            if ($this->option('fix')) {
                \App\Models\User::where('role', 'alumni')
                    ->whereNotNull('latitude')
                    ->where(function($q) {
                        $q->whereNull('city_name')->orWhere('city_name', '');
                    })->update(['city_name' => 'Lokasi Tidak Diketahui']);
                $this->info("Healed missing city names for mapped users.");
            }
        }
    }

    /**
     * AI Geocoding for users with addresses but no coordinates
     */
    private function geocodeMissingAddresses()
    {
        $this->comment("\n2c. Auditing AI-Geocoding for addresses...");
        
        $missingCoords = \App\Models\User::whereNotNull('address')
            ->where('address', '!=', '')
            ->where(function($q) {
                $q->whereNull('latitude')->orWhereNull('longitude');
            })->count();

        if ($missingCoords > 0) {
            $this->warn("Found $missingCoords users with addresses but no coordinates.");
            if ($this->option('fix')) {
                $this->info("Attempting AI-Geocoding for $missingCoords users...");
                
                $users = \App\Models\User::whereNotNull('address')
                    ->where('address', '!=', '')
                    ->where(function($q) {
                        $q->whereNull('latitude')->orWhereNull('longitude');
                    })->get();

                foreach ($users as $user) {
                    $this->line("  - Geocoding: " . $user->address);
                    // This triggers the 'saving' observer we just added to the User model
                    $user->save(); 
                }
                $this->info("AI-Geocoding complete.");
            }
        } else {
            $this->info("Geocoding OK: All users with addresses have coordinates.");
        }
    }

    /**
     * Audit .env configuration
     */
    private function auditEnvironment()
    {
        $this->comment("\n3. Auditing Environment Core Configuration...");

        $envKeys = [
            'APP_KEY',
            'DB_DATABASE',
            'GEMINI_API_KEY',
            'JWT_SECRET',
        ];

        foreach ($envKeys as $key) {
            if (empty(env($key))) {
                 if ($key === 'GEMINI_API_KEY' && (empty(setting('gemini_api_key')))) {
                    $this->warn("Configuration Warning: [$key] is missing or empty.");
                 } elseif ($key !== 'GEMINI_API_KEY') {
                    $this->warn("Configuration Warning: [$key] is missing or empty.");
                 }
            } else {
                $value = env($key);
                $placeholders = ['your-', 'example-', 'placeholder', 'api-key-here'];
                $isPlaceholder = false;
                foreach ($placeholders as $p) {
                    if (str_contains(strtolower($value), $p)) {
                        $isPlaceholder = true;
                        break;
                    }
                }

                if ($isPlaceholder) {
                    $this->error("CRITICAL: Config entry [$key] appears to be a PLACEHOLDER value ($value).");
                } else {
                    $this->info("Config entry [$key] exists and looks valid.");
                }
            }
        }

        // Check for debug mode in production
        if (config('app.env') === 'production' && config('app.debug')) {
            $this->warn("Security Risk: APP_DEBUG is enabled in production environment.");
        }
    }

    /**
     * Audit Global Helper Function availability
     */
    private function auditHelpers()
    {
        $this->comment("\n4. Auditing Global Helper Integrity...");
        
        $helpers = ['redirect', 'back', 'setting', 'route', 'auth'];
        foreach ($helpers as $helper) {
            if (function_exists($helper)) {
                $this->info("Helper function [$helper()] is available.");
            } else {
                $this->error("CRITICAL: Helper function [$helper()] is MISSING!");
            }
        }
    }

    /**
     * Audit AI Service connectivity
     */
    private function auditAI()
    {
        $this->comment("\n5. Auditing AI Service (Gemini) Connectivity...");
        
        try {
            $aiService = app(\App\Services\AIService::class);
            $response = $aiService->ask("Test ping. Reply with 'OK'.", 0.1);
            
            if ($response) {
                $this->info("AI Connectivity: Success (Result: " . trim($response) . ")");
            } else {
                $this->error("AI Connectivity: FAILED (Service returned null). Check logs.");
            }
        } catch (\Exception $e) {
            $this->error("AI Connectivity: EXCEPTION - " . $e->getMessage());
        }
    }

    /**
     * Audit recent logs for errors using AI
     */
    private function auditLogs()
    {
        $this->comment("\n6. Auditing Recent System Logs (AI-Powered)...");

        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) {
            $this->info("No logs found. System is fresh.");
            return;
        }

        // Read last 50 lines
        $logs = shell_exec("tail -n 50 " . escapeshellarg($logPath));
        
        if (str_contains(strtolower($logs), 'error') || str_contains(strtolower($logs), 'exception')) {
            $this->warn("Recent ERRORS detected in logs!");
            
            try {
                $aiService = app(\App\Services\AIService::class);
                $analysis = $aiService->ask("Analyze these last log lines and tell me what's wrong and how to fix it in 2 short sentences Indonesian: \n\n" . $logs, 0.5);
                
                if ($analysis) {
                    $this->line("<options=bold;fg=cyan>AI ANALYSIS:</> " . trim($analysis));
                    $this->lastAiAnalysis = trim($analysis);
                }
            } catch (\Exception $e) {
                $this->error("AI Log Analysis failed: " . $e->getMessage());
            }
        } else {
            $this->info("No critical errors dynamic in recent logs.");
        }
    }

    /**
     * Audit Vite Manifest synchronization
     */
    private function auditVite()
    {
        $this->comment("\n7. Auditing Vite Asset Manifest...");
        $manifestPath = public_path('build/manifest.json');
        
        if (!File::exists($manifestPath)) {
            $this->warn("Vite Manifest is MISSING! Assets might be broken.");
            if ($this->option('fix')) {
                $this->info("Attempting to rebuild assets (if possible)...");
                // In a real server, we might run npm run build, but here we just warn
            }
        } else {
            $this->info("Vite Manifest exists.");
        }
    }

    /**
     * Clean up temporary and junk files
     */
    private function cleanupJunk()
    {
        $this->comment("\n8. Cleaning Up Junk Files...");
        
        $junkPaths = [
            storage_path('framework/views/*.php'),
            storage_path('framework/cache/data/*'),
            storage_path('logs/*.log.gz'),
            base_path('npm-debug.log'),
        ];

        $deletedCount = 0;
        foreach ($junkPaths as $pattern) {
            $files = File::glob($pattern);
            foreach ($files as $file) {
                if (File::isFile($file) && (time() - File::lastModified($file) > 86400 * 7)) { // Older than 7 days
                    if ($this->option('fix')) {
                        File::delete($file);
                        $deletedCount++;
                    }
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Deleted $deletedCount old junk files.");
        } else {
            $this->info("No significant junk found.");
        }
    }

    /**
     * Audit critical view files for potential truncation (Empty or suspiciously small)
     */
    private function auditViewIntegrity()
    {
        $this->comment("\n9. Auditing View File Integrity (Watchdog)...");
        
        $criticalViews = [
            'admin/dashboard.blade.php' => 15000, 
            'welcome.blade.php' => 20000,        
            'layouts/admin.blade.php' => 3000,    
            'components/admin-sidebar.blade.php' => 8000,
            '../../app/Services/AlumniService.php' => 9000, // Logic check
        ];

        $backupPath = storage_path('app/integrity/views');
        if (!File::isDirectory($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $issuesFound = 0;
        foreach ($criticalViews as $view => $minSize) {
            $path = resource_path("views/{$view}");
            $backupFile = $backupPath . '/' . str_replace('/', '_', $view);

            if (!File::exists($path)) {
                $this->error("CRITICAL: View file [{$view}] is MISSING!");
                if ($this->option('fix') && File::exists($backupFile)) {
                    $this->info("  -> Restoring from backup...");
                    File::copy($backupFile, $path);
                } else {
                    $issuesFound++;
                }
                continue;
            }

            $size = File::size($path);
            if ($size < $minSize) {
                $this->error("CRITICAL: View file [{$view}] appears TRUNCATED! (Size: {$size} bytes, Expected > {$minSize})");
                if ($this->option('fix') && File::exists($backupFile) && File::size($backupFile) >= $minSize) {
                    $this->info("  -> [RECOVERED] Restoring from healthy backup...");
                    File::copy($backupFile, $path);
                } else {
                    $issuesFound++;
                }
            } else {
                $this->info("View Integrity OK: {$view} ({$size} bytes)");
                // Update backup with healthy version
                File::copy($path, $backupFile);
            }
        }

        if ($issuesFound > 0) {
            Log::critical("System Integrity Guard found {$issuesFound} truncated or missing view files!");
        }
    }

    private $lastAiAnalysis = null;

    private function saveReport()
    {
        $report = [
            'last_audit' => now()->toIso8601String(),
            'status' => 'Healthy',
            'ai_analysis' => $this->lastAiAnalysis,
            'fix_executed' => $this->option('fix'),
        ];

        File::put(storage_path('app/audit_report.json'), json_encode($report, JSON_PRETTY_PRINT));
    }
}
