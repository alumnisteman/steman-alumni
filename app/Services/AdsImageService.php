<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AdsImageService
{
    protected $manager;

    public function __construct()
    {
        // Use GD driver by default for compatibility
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Get dimensions based on ad position.
     */
    public static function getDimensions(string $position, bool $isMobile = false): array
    {
        $position = strtolower(trim($position));
        
        $dimensions = [
            'header'  => ['width' => 1200, 'height' => 300, 'mobile' => ['width' => 600, 'height' => 300]],
            'sidebar' => ['width' => 300,  'height' => 250, 'mobile' => ['width' => 300, 'height' => 300]],
            'content' => ['width' => 728,  'height' => 90,  'mobile' => ['width' => 320, 'height' => 100]],
            'footer'  => ['width' => 1200, 'height' => 150, 'mobile' => ['width' => 600, 'height' => 150]],
            'popup'   => ['width' => 600,  'height' => 800, 'mobile' => ['width' => 350, 'height' => 500]],
        ];

        $spec = $dimensions[$position] ?? $dimensions['sidebar'];

        return $isMobile ? $spec['mobile'] : ['width' => $spec['width'], 'height' => $spec['height']];
    }

    /**
     * Process and store advertisement image with automatic resizing and cropping.
     */
    public function process(UploadedFile $file, string $position, bool $isMobile = false, array $params = []): string
    {
        try {
            $dims = self::getDimensions($position, $isMobile);
            $w = $dims['width'];
            $h = $dims['height'];

            $offsetX = $params['offset_x'] ?? 50;
            $offsetY = $params['offset_y'] ?? 50;
            $zoom = $params['zoom'] ?? 1.0;

            $filename = 'ads/' . Str::random(40) . ($isMobile ? '_mobile' : '_desktop') . '.jpg';

            // Read image using the manager instance
            $img = $this->manager->read($file->getRealPath());
            
            // Apply zoom if needed
            if ($zoom > 1.0) {
                $img->scale(width: $img->width() * $zoom);
            }

            // Apply manual positioning if provided, otherwise center crop
            // Intervention Image v3 cover() uses percentages for focal point
            $img->cover($w, $h, $offsetX . '% ' . $offsetY . '%');

            // Encode to JPEG
            $encoded = $img->toJpeg(85);

            // Store to public disk
            Storage::disk('public')->put($filename, (string) $encoded);

            return $filename;
        } catch (\Exception $e) {
            Log::error('AdsImageService Process Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Automatically generate a mobile version from a desktop file.
     */
    public function autoGenerateMobile(string $desktopPath, string $position, array $params = []): ?string
    {
        try {
            if (!Storage::disk('public')->exists($desktopPath)) {
                return null;
            }

            $dims = self::getDimensions($position, true);
            $w = $dims['width'];
            $h = $dims['height'];

            $offsetX = $params['offset_x'] ?? 50;
            $offsetY = $params['offset_y'] ?? 50;
            $zoom = $params['zoom'] ?? 1.0;

            $filename = Str::replaceFirst('_desktop.jpg', '_mobile_auto.jpg', $desktopPath);
            if ($filename === $desktopPath) {
                $filename = 'ads/' . Str::random(40) . '_mobile_auto.jpg';
            }

            $fullPath = Storage::disk('public')->path($desktopPath);
            
            // Read image using the manager instance
            $img = $this->manager->read($fullPath);

            // Apply zoom if needed
            if ($zoom > 1.0) {
                $img->scale(width: $img->width() * $zoom);
            }

            $img->cover($w, $h, $offsetX . '% ' . $offsetY . '%');
            
            $encoded = $img->toJpeg(85);
            Storage::disk('public')->put($filename, (string) $encoded);

            return $filename;
        } catch (\Exception $e) {
            Log::warning('AdsImageService AutoGen Error: ' . $e->getMessage());
            return null; // Fallback to null so update can proceed without mobile image
        }
    }
}

