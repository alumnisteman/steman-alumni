<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SystemAutoFix extends Command
{
    protected $signature = 'system:autofix {--force : Run without interaction}';
    protected $description = 'Analyze and automatically fix common system errors, clean garbage, and optimize performance';

    public function handle()
    {
        $this->header("STEMAN ALUMNI - AUTOMATED SELF-HEALING SYSTEM");

        // 1. Cleanup Garbage Files
        $this->performAction("Cleaning garbage files", function() {
            $deletedCount = 0;
            // Clear logs older than 7 days if they are large
            $logFile = storage_path('logs/laravel.log');
            if (File::exists($logFile) && File::size($logFile) > 10 * 1024 * 1024) { // 10MB
                File::put($logFile, '');
                $this->info("  [FIXED] laravel.log was too large (>10MB), truncated.");
                $deletedCount++;
            }

            // Clean compiled views older than 30 days
            $viewPath = storage_path('framework/views');
            if (File::isDirectory($viewPath)) {
                $files = File::files($viewPath);
                foreach ($files as $file) {
                    if (time() - $file->getMTime() > 86400 * 30) {
                        File::delete($file->getPathname());
                        $deletedCount++;
                    }
                }
            }

            $this->info("  [OK] Garbage cleanup complete. Deleted: {$deletedCount} files.");
        });

        // 2. Fix Database Issues (Migrations & Cache)
        $this->performAction("Optimizing Database & Cache", function() {
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            $this->info("  [FIXED] Application caches cleared.");
            
            // Check for pending migrations
            Artisan::call('migrate', ['--force' => true]);
            $this->info("  [OK] Database migrations synchronized.");
        });

        // 3. Verify Critical DB Columns
        $this->performAction("Verifying Critical Database Schema", function() {
            $criticalColumns = [
                'messages' => ['is_read', 'parent_id', 'sender_id', 'receiver_id', 'message'],
                'users' => ['name', 'email', 'role', 'status'],
                'posts' => ['user_id', 'content', 'type'],
                'stories' => ['user_id', 'type'],
                'sessions' => ['user_id', 'last_activity'],
            ];

            $issues = 0;
            foreach ($criticalColumns as $table => $columns) {
                if (!Schema::hasTable($table)) {
                    $this->error("  [MISSING TABLE] {$table}");
                    $issues++;
                    continue;
                }
                foreach ($columns as $column) {
                    if (!Schema::hasColumn($table, $column)) {
                        $this->error("  [MISSING COLUMN] {$table}.{$column}");
                        $issues++;
                    }
                }
            }

            if ($issues === 0) {
                $this->info("  [OK] All critical database columns are present.");
            } else {
                $this->warn("  [WARN] {$issues} schema issue(s) detected. Run php artisan migrate.");
            }
        });

        // 4. Integrity Check: Controllers & Classes (with correct namespaces)
        $this->performAction("Verifying Class Integrity", function() {
            $requiredClasses = [
                'App\Http\Controllers\FeedController',
                'App\Http\Controllers\AlumniController',
                'App\Http\Controllers\AuthController',
                'App\Http\Controllers\PostController',
                'App\Http\Controllers\StoryController',
                'App\Http\Controllers\Api\ChatController',
                'App\Models\User',
                'App\Models\Message',
                'App\Models\Post',
                'App\Services\AlumniService',
            ];
            $missing = 0;
            foreach ($requiredClasses as $class) {
                if (!class_exists($class)) {
                    $this->error("  [MISSING CLASS] {$class}");
                    $missing++;
                }
            }
            if ($missing === 0) {
                $this->info("  [OK] All essential classes are present.");
            } else {
                $this->warn("  [WARN] {$missing} class(es) missing. Run composer dump-autoload.");
                Artisan::call('clear-compiled');
            }
        });

        // 5. Heal null/broken user data
        $this->performAction("Healing User Data Integrity", function() {
            // Users with null names
            $fixed = \App\Models\User::whereNull('name')->orWhere('name', '')->update(['name' => 'Alumni Anonim']);
            if ($fixed > 0) $this->info("  [FIXED] {$fixed} user(s) with missing name.");

            // Alumni with null major
            $fixed2 = \App\Models\User::where('role', 'alumni')
                ->where(function($q) { $q->whereNull('major')->orWhere('major', ''); })
                ->update(['major' => 'Umum']);
            if ($fixed2 > 0) $this->info("  [FIXED] {$fixed2} alumni with missing major.");

            $this->info("  [OK] User data integrity check complete.");
        });

        // 6. Secure Permissions
        $this->performAction("Hardening Permissions", function() {
            $paths = [storage_path(), base_path('bootstrap/cache')];
            $ok = true;
            foreach ($paths as $path) {
                if (File::exists($path) && !is_writable($path)) {
                    @chmod($path, 0775);
                    if (!is_writable($path)) {
                        $this->warn("  [WARN] Path {$path} is NOT writable. Requires manual intervention.");
                        $ok = false;
                    } else {
                        $this->info("  [FIXED] Permissions corrected for: {$path}");
                    }
                }
            }
            if ($ok) $this->info("  [OK] Permissions check complete.");
        });

        // 7. Optimize application
        $this->performAction("Optimizing Application", function() {
            Artisan::call('optimize');
            $this->info("  [OK] Application optimized.");
        });

        $this->newLine();
        $this->info("--- SYSTEM SELF-HEALING COMPLETE ---");
        Log::info("SystemAutoFix executed successfully at " . now()->toIso8601String());
    }

    private function header($text)
    {
        $this->line("<options=bold;fg=green>========================================</>");
        $this->line("<options=bold;fg=green> $text </>");
        $this->line("<options=bold;fg=green>========================================</>");
    }

    private function performAction($name, $callback)
    {
        $this->comment("\nStarting: $name...");
        try {
            $callback();
        } catch (\Exception $e) {
            $this->error("  [ERROR] " . $e->getMessage());
            Log::error("SystemAutoFix Error during $name: " . $e->getMessage());
        }
    }
}
