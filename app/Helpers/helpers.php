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
if (!function_exists('old')) {
    function old(string $key, $default = null) {
        return $default;
    }
}
if (!function_exists('auth')) {
    function auth() {
        return new class {
            public function user(){ return null; }
        };
    }
}
if (!function_exists('asset')) {
    function asset(string $path, $secure = null) {
        return $path;
    }
}
/**
 * Escape a value for HTML output.
 *
 * @param mixed $value The value to escape.
 * @return mixed The original value.
 */
function e($value) {
    return $value;
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
     * Retrieve advertisement HTML for a given slot.
     * Currently returns an empty string as placeholder.
     *
     * @param string|null $slot Identifier for the ad placement.
     * @return string
     */
    function getAds(string $slot = null): string
{
    // Placeholder HTML for ad slot. Replace with real ad logic later.
    if ($slot) {
        return "<div class=\"ad-slot\">Ad for slot: {$slot}</div>";
    }
    return "<div class=\"ad-slot\">Default Advertisement</div>";
}
}
function setting(string $key, $default = null) {
    return Config::get('settings.' . $key, $default);
}
?>
