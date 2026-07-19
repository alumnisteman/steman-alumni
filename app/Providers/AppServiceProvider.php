<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use App\Models\Major;
use App\Models\News;
use App\Models\Gallery;
use App\Models\User;
use App\Observers\ActivityObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\AlumniRepositoryInterface::class,
            \App\Repositories\AlumniRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\AuthRepositoryInterface::class,
            \App\Repositories\AuthRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    
    public function boot(): void
    {
        // Suppress benign Blade view compile race-condition errors.
        // When multiple requests hit an un-cached view simultaneously, PHP's
        // rename() can fail with "No such file or directory" because another
        // process already renamed the temp file first. The view is already
        // compiled at that point, so this error is harmless — but noisy.
        $originalHandler = set_error_handler(null);
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) use ($originalHandler): bool {
            if (str_contains($errstr, 'rename(') && str_contains($errstr, 'No such file or directory')) {
                return true; // suppress, the competing process already wrote the file
            }
            if ($originalHandler) {
                return (bool) call_user_func($originalHandler, $errno, $errstr, $errfile, $errline);
            }
            return false;
        });

        // Activate Laravel's Strict Mode (Fail-Fast) in non-production environments
        // This catches: Lazy Loading (N+1), Missing Attributes (image vs image_Desktop), and Silent Mass Assignment errors.
        \Illuminate\Database\Eloquent\Model::shouldBeStrict(!app()->isProduction());

        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        // Register Global Observers
        News::observe(ActivityObserver::class);
        Gallery::observe(ActivityObserver::class);
        User::observe(ActivityObserver::class);

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(300)->by($request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });


        View::composer(['auth.register', 'alumni.profile', 'admin.users'], function ($view) {
            try {
                $majors = \Illuminate\Support\Facades\Cache::remember('active_majors', 3600, function () {
                    return Major::where('status', 'active')->orderBy('group')->orderBy('name')->get();
                });
                $view->with('activeMajors', $majors);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Provider Major Error: ' . $e->getMessage());
                $view->with('activeMajors', collect());
            }
        });

        // Cache settings globally
        View::composer(['layouts.app', 'welcome'], function ($view) {
            try {
                $settings = \Illuminate\Support\Facades\Cache::remember('site_settings', 3600, function () {
                    return \App\Models\Setting::pluck('value', 'key')->toArray();
                });
                $view->with('settings', $settings);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Provider Settings Error: ' . $e->getMessage());
                $view->with('settings', []);
            }
        });

        // Use dedicated Composer class for ads
        // View::composer('*', \App\Http\ViewComposers\AdViewComposer::class);
    }
}
