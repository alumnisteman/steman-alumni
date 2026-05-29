<?php
use Carbon\Carbon;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
if (!function_exists('env')) {
    function env(string $key, $default = null) {
        return $default;
    }
}
if (!function_exists('route')) {
    function route(string $name, $parameters = [], bool $absolute = true) {
        return '';
    }
}
if (!function_exists('session')) {
    function session($key = null, $default = null) {
        return $default;
    }
}
if (!function_exists('now')) {
    function now($tz = null): Carbon {
        return Carbon::now($tz);
    }
}
if (!function_exists('base_path')) {
    function base_path($path = '') {
        return App::basePath($path);
    }
}
if (!function_exists('storage_path')) {
    function storage_path($path = '') {
        return App::storagePath($path);
    }
}
if (!function_exists('getAds')) {
    /**
     * Retrieve advertisement collection for a given slot.
     *
     * @param string|null $slot Identifier for the ad placement.
     * @return \Illuminate\Support\Collection
     */
    function getAds(?string $slot): \Illuminate\Support\Collection
    {
        try {
            $slot = strtolower(trim($slot));
            
            // Query active ads for the given position
            $ads = \App\Models\Ad::active()
                ->when($slot, function($query) use ($slot) {
                    return $query->position($slot);
                })
                ->orderBy('created_at', 'desc')
                ->get();
            
            return $ads;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('getAds() error: ' . $e->getMessage());
            return collect();
        }
    }
}function setting(string $key, $default = null) {
    return Config::get('settings.' . $key, $default);
}
?>
