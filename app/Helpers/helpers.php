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
if (! function_exists('thumbnail')) {
    /**
     * Generate optimized image URL
     * @param string $path Original storage path (e.g. storage/uploads/...)
     * @param int|null $width
     * @param int|null $height
     * @param string $format
     * @return string
     */
    function thumbnail($path, $width = null, $height = null, $format = 'webp')
    {
        if (!$path) return $path;
        
        // Convert /storage/path to path
        $cleanPath = preg_replace('#^/?storage/#', '', $path);
        
        try {
            return route('image.optimize', [
                'path' => $cleanPath,
                'w' => $width,
                'h' => $height,
                'f' => $format
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Thumbnail Route Error: ' . $e->getMessage());
            return $path; // Fallback to original
        }
    }
}
