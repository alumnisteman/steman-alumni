<?php
use App\Models\Setting;

if (! function_exists('setting')) {
    function setting($key, $default = null) {
        try {
            return \App\Models\Setting::get($key, $default);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Setting Helper Error: ' . $e->getMessage());
            return $default;
        }
    }
}

if (! function_exists('getAds')) {
    function getAds($position)
    {
        return \App\Models\Ad::active()
            ->where('position', $position)
            ->inRandomOrder()
            ->select('id','title','image_desktop','image_mobile','link')
            ->get();
    }
}
