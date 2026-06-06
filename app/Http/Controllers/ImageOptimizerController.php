<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // Assuming GD is available, or use Imagick
use Illuminate\Support\Facades\Cache;

class ImageOptimizerController extends Controller
{
    public function optimize(Request $request, $path)
    {
        // Path should be relative to storage/app/public/
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            abort(404);
        }

        $width = $request->query('w') ? (int)$request->query('w') : null;
        $height = $request->query('h') ? (int)$request->query('h') : null;
        $quality = (int)$request->query('q', 75);
        $format = $request->query('f', 'webp');

        // Fallback ke jpeg jika GD tidak support WebP
        if ($format === 'webp' && !function_exists('imagewebp')) {
            $format = 'jpg';
        }

        $cacheKey = "img_opt_v2_" . md5($path . $width . $height . $quality . $format);

        $imageData = Cache::remember($cacheKey, 86400 * 7, function () use ($fullPath, $width, $height, $quality, $format) {
            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($fullPath);

                if ($width || $height) {
                    if ($width && $height) {
                        $image->cover($width, $height);
                    } else {
                        $image->scale($width, $height);
                    }
                }

                return $image->encodeByExtension($format, $quality)->toString();
            } catch (\Exception $e) {
                return file_get_contents($fullPath);
            }
        });

        $mimeType = match($format) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            default => 'image/' . $format,
        };

        return response($imageData)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=604800, immutable');
    }
}
