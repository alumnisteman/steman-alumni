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

        // 1. Cleanup Garbage Files & Log Management
        $this->performAction("Cleaning garbage files", function() {
            $deletedCount = 0;
            // Clear logs older than 7 days if they are large
            $logFiles = File::glob(storage_path('logs/*.log'));
            foreach ($logFiles as $logFile) {
                if (File::exists($logFile) && File::size($logFile) > 20 * 1024 * 1024) { // 20MB
                    File::put($logFile, '');
                    $this->info("  [FIXED] " . basename($logFile) . " was too large (>20MB), truncated.");
                    $deletedCount++;
                }
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

            // Clean stale sessions (older than 2 days)
            $sessionPath = storage_path('framework/sessions');
            if (File::isDirectory($sessionPath)) {
                $files = File::files($sessionPath);
                foreach ($files as $file) {
                    if (time() - $file->getMTime() > 86400 * 2) {
                        File::delete($file->getPathname());
                        $deletedCount++;
                    }
                }
            }

            $this->info("  [OK] Garbage cleanup complete. Total handled: {$deletedCount} items.");
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

        // 5. Heal null/broken user data & Data Mismatch Guard
        $this->performAction("Healing Data Mismatch", function() {
            // Users with null names
            $fixed = \App\Models\User::whereNull('name')->orWhere('name', '')->update(['name' => 'Alumni Anonim']);
            if ($fixed > 0) $this->info("  [FIXED] {$fixed} user(s) with missing name.");

            // Alumni with null major
            $fixed2 = \App\Models\User::where('role', 'alumni')
                ->where(function($q) { $q->whereNull('major')->orWhere('major', ''); })
                ->update(['major' => 'Umum']);
            if ($fixed2 > 0) $this->info("  [FIXED] {$fixed2} alumni with missing major.");

            // PRUNING ORPHANED RECORDS (Data Mismatch Prevention)
            // 1. Delete posts whose users no longer exist
            $orphanedPosts = DB::table('posts')
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('users')
                          ->whereRaw('users.id = posts.user_id');
                })->delete();
            if ($orphanedPosts > 0) $this->info("  [CLEANED] {$orphanedPosts} orphaned post(s) removed.");

            // 2. Delete messages where sender/receiver are missing
            $orphanedMsgs = DB::table('messages')
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('users')
                          ->whereRaw('users.id = messages.sender_id');
                })
                ->orWhereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('users')
                          ->whereRaw('users.id = messages.receiver_id');
                })->delete();
            if ($orphanedMsgs > 0) $this->info("  [CLEANED] {$orphanedMsgs} orphaned message(s) removed.");

            $this->info("  [OK] Data mismatch guard finished.");
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

            // Hardening Ownership
            try {
                $this->info("  [ACTION] Ensuring www-data ownership...");
                @exec('chown -R www-data:www-data ' . storage_path());
                @exec('chown -R www-data:www-data ' . base_path('bootstrap/cache'));
            } catch (\Exception $e) {
                // Ignore if not permitted
            }

            if ($ok) $this->info("  [OK] Permissions check complete.");
        });

        // 7. Optimize application
        $this->performAction("Optimizing Application", function() {
            Artisan::call('optimize');
            $this->info("  [OK] Application optimized.");
        });

        // 8. Run Deep Integrity Audit (Watchdog)
        $this->performAction("Running Deep Integrity Audit", function() {
            Artisan::call('app:audit-integrity', ['--fix' => true]);
            $this->info("  [OK] Deep integrity audit completed.");
        });

        $this->newLine();
        $this->info("--- SYSTEM SELF-HEALING COMPLETE ---");
        Log::info("SystemAutoFix executed successfully at " . now()->toIso8601String());
        $this->notify("SystemAutoFix successfully executed on " . config('app.url'));
    }

    private function notify($message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        if (!$token || !$chatId) return;

        try {
            \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => "🛠️ *[SystemAutoFix]*\n" . $message,
                'parse_mode' => 'Markdown'
            ]);
        } catch (\Exception $e) {
            // Silently fail
        }
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
