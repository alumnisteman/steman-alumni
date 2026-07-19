<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\HolidayTheme;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ThemeServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot()
    {
        // Share active theme name to all Blade views
        view()->composer('*', function ($view) {
            $active = Cache::remember('active_theme', now()->addMinutes(5), function () {
                $now = Carbon::now();
                $theme = HolidayTheme::where('is_active', true)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('start_date')
                          ->orWhere(function ($qq) use ($now) {
                              $qq->where('start_date', '<=', $now)
                                 ->where('end_date', '>=', $now);
                          });
                    })
                    ->orderByDesc('priority')
                    ->first();
                return $theme ? $theme->name : 'default';
            });
            $view->with('activeTheme', $active);
        });
    }
}
?>
