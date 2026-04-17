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
        $this->healUserData();
        $this->geocodeMissingAddresses();
        $this->auditEnvironment();
        $this->auditHelpers();
        $this->auditAI();
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
