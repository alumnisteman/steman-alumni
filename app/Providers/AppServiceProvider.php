<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use App\Models\Major;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    
    public function boot(): void
    {
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
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
    }
}
