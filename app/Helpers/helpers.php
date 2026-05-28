<?php
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
if (!function_exists('e')) {
    function e($value) {
        return $value;
    }
}
if (!function_exists('now')) {
    function now($tz = null) {
        return \Illuminate\Support\Carbon::now($tz);
    }
}
if (!function_exists('base_path')) {
    function base_path($path = '') {
        return app()->basePath($path);
    }
}
if (!function_exists('storage_path')) {
    function storage_path($path = '') {
        return app()->storagePath($path);
    }
}
function setting(string $key, $default = null) {
    return config('settings.' . $key, $default);
}
?>
