<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class AuditStability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steman:audit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a comprehensive stability audit of the Steman Alumni portal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Steman Stability Audit...');
        $errors = 0;

        // 1. Check Blade Includes
        $this->comment('🔍 Auditing Blade templates for broken includes...');
        $bladeFiles = File::allFiles(resource_path('views'));
        foreach ($bladeFiles as $file) {
            $content = File::get($file);
            // Match @include('path') and @includeIf('path')
            if (preg_replace_callback('/@(include|includeIf)\([\'"]([^\'"]+)[\'"]\)/', function($matches) use ($file, &$errors) {
                $path = str_replace('.', '/', $matches[2]);
                if (!File::exists(resource_path("views/{$path}.blade.php"))) {
                    $this->error("❌ Broken include in " . $file->getPathname() . ": {$matches[2]}");
                    $errors++;
                }
            }, $content));
        }

        // 2. Check Route Controllers
        $this->comment('🔍 Auditing routes for existing controllers...');
        $routes = Route::getRoutes();
        foreach ($routes as $route) {
            $action = $route->getAction();
            if (isset($action['controller'])) {
                $parts = explode('@', $action['controller']);
                $class = $parts[0];
                if (!class_exists($class)) {
                    $this->error("❌ Controller not found for route [{$route->uri()}]: {$class}");
                    $errors++;
                } elseif (isset($parts[1]) && !method_exists($class, $parts[1])) {
                    $this->error("❌ Method [{$parts[1]}] not found in controller: {$class}");
                    $errors++;
                }
            }
        }

        // 3. Check for Polymorphic Risks
        $this->comment('🔍 Checking for common polymorphic relationship risks...');
        $riskyFiles = [
            app_path('Services/RankingService.php'),
            app_path('Http/Controllers/Alumni/StoryController.php'),
        ];
        foreach ($riskyFiles as $path) {
            if (File::exists($path)) {
                $content = File::get($path);
                if (str_contains($content, '->post(') && !str_contains($content, 'whereHasMorph')) {
                    $this->warn("⚠️ Potential polymorphic bug in {$path}: Direct ->post() call detected without morph check.");
                    // We don't increment errors for warnings, but we notify the user.
                }
            }
        }

        // 4. Check Vite Manifest
        $this->comment('🔍 Verifying Vite manifest sync...');
        if (app()->environment('production')) {
            $manifestPath = public_path('build/manifest.json');
            if (!File::exists($manifestPath)) {
                $this->error('❌ Vite manifest missing in production! Frontend assets will fail.');
                $errors++;
            }
        }

        if ($errors === 0) {
            $this->info('✅ Audit complete. All core systems are stable.');
        } else {
            $this->error("🚨 Audit failed with {$errors} critical errors. Fix them before deploying!");
        }

        return $errors > 0 ? 1 : 0;
    }
}
