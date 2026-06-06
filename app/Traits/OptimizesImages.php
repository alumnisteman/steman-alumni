<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait OptimizesImages
{
    /**
     * Optimize and convert an uploaded image.
     * Uses WebP if GD supports it, falls back to JPEG otherwise.
     *
     * @param UploadedFile $file The uploaded file instance.
     * @param string $directory The storage directory path (relative to the disk).
     * @param string $disk The storage disk (default: 'public').
     * @param int $quality The image quality (0-100).
     * @param int|null $maxWidth The maximum width to scale down to (maintains aspect ratio).
     * @return string The path to the stored image relative to the disk.
     */
    public function optimizeAndStoreImage(UploadedFile $file, string $directory, string $disk = 'public', int $quality = 80, ?int $maxWidth = 1920): string
    {
        $webpSupported = function_exists('imagewebp');

        $ext = $webpSupported ? 'webp' : 'jpg';
        $filename = uniqid() . '_' . time() . '.' . $ext;
        $path = trim($directory, '/') . '/' . $filename;

        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        if ($maxWidth !== null && $image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        try {
            if ($webpSupported) {
                $encoded = $image->toWebp($quality);
            } else {
                $encoded = $image->toJpeg($quality);
            }
            Storage::disk($disk)->put($path, (string) $encoded);
        } catch (\Exception $e) {
            $path = $file->store(trim($directory, '/'), $disk);
        }

        return $path;
    }
}
