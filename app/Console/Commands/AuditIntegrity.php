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
        $this->header('SYSTEM INTEGRITY AUDIT');
        
        $this->auditPermissions();
        $this->auditDatabase();
        $this->auditEnvironment();

        $this->info("\nAudit complete! ✅");
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
                $this->info("Config entry [$key] exists.");
            }
        }

        // Check for debug mode in production
        if (config('app.env') === 'production' && config('app.debug')) {
            $this->warn("Security Risk: APP_DEBUG is enabled in production environment.");
        }
    }
}
