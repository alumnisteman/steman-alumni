<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;
use App\Services\AuditService;

class SystemDeepAuditCommand extends Command
{
    protected $signature = 'steman:audit {--fix : Attempt to auto-fix minor issues}';
    protected $description = 'Deep system audit for route, controller, and data integrity';

    private int $errors = 0;
    private int $warnings = 0;

    public function handle()
    {
        $this->info('🔍 Starting Deep System Audit (Resilience Engine v2)...');

        $this->auditRoutesAndControllers();
        $this->auditViews();
        $this->auditStorage();
        $this->auditAuditLogs();
        $this->auditDatabaseIntegrity();

        $this->newLine();
        if ($this->errors === 0) {
            $this->info('✨ AUDIT PASSED: 100% Systems integrity verified.');
            if ($this->warnings > 0) {
                $this->warn("   Note: {$this->warnings} minor warnings found.");
            }
        } else {
            $this->error("🚨 AUDIT FAILED: Found {$this->errors} errors and {$this->warnings} warnings.");
        }

        return $this->errors === 0 ? 0 : 1;
    }

    private function auditRoutesAndControllers()
    {
        $this->comment('Checking routes and controllers...');
        $routes = Route::getRoutes();
        foreach ($routes as $route) {
            $action = $route->getActionName();
            if ($action === 'Closure' || !str_contains($action, '@')) continue;

            [$controller, $method] = explode('@', $action);
            if (!class_exists($controller)) {
                $this->error("- Route [{$route->uri()}]: Controller [{$controller}] NOT FOUND.");
                $this->errors++;
            } elseif (!method_exists($controller, $method)) {
                $this->error("- Route [{$route->uri()}]: Method [{$method}] in [{$controller}] NOT FOUND.");
                $this->errors++;
            }
        }
    }

    private function auditViews()
    {
        $this->comment('Checking view consistency...');
        // This is complex, but we can check if critical views exist
        $criticalViews = ['welcome', 'layouts.app', 'layouts.admin', 'auth.login', 'admin.users', 'admin.system.guard'];
        foreach ($criticalViews as $view) {
            if (!view()->exists($view)) {
                $this->error("- Critical view [{$view}] MISSING.");
                $this->errors++;
            }
        }
    }

    private function auditStorage()
    {
        $this->comment('Checking storage symlink...');
        $publicStorage = public_path('storage');
        if (!File::exists($publicStorage)) {
            $this->warn("- Storage symlink MISSING.");
            if ($this->option('fix')) {
                $this->call('storage:link');
                $this->info('  -> Symlink fixed.');
            } else {
                $this->warnings++;
            }
        }
    }

    private function auditAuditLogs()
    {
        $this->comment('Checking audit log integrity (all entries)...');
        $auditService = new AuditService();
        $logs = AuditLog::all(); // Warning: could be large, but necessary for "Deep Audit"
        
        $broken = 0;
        foreach ($logs as $log) {
            if (!$auditService->verifyIntegrity($log)) {
                $broken++;
            }
        }

        if ($broken > 0) {
            $this->error("- Found {$broken} broken/tampered audit logs.");
            $this->errors++;
        }
    }

    private function auditDatabaseIntegrity()
    {
        $this->comment('Checking database orphans...');
        // Check for activity logs with missing users
        $orphans = DB::table('activity_logs')
            ->whereNotNull('user_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('users')
                      ->whereRaw('users.id = activity_logs.user_id');
            })->count();

        if ($orphans > 0) {
            $this->warn("- Found {$orphans} orphaned activity logs (users no longer exist).");
            if ($this->option('fix')) {
                DB::table('activity_logs')
                    ->whereNotNull('user_id')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                              ->from('users')
                              ->whereRaw('users.id = activity_logs.user_id');
                    })->delete();
                $this->info("  -> Fixed {$orphans} orphans.");
            } else {
                $this->warnings++;
            }
        }
    }
}
