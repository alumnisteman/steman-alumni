<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait OptimizesImages
{
    /**
     * Optimize and convert an uploaded image to WebP format.
     *
     * @param UploadedFile $file The uploaded file instance.
     * @param string $directory The storage directory path (relative to the disk).
     * @param string $disk The storage disk (default: 'public').
     * @param int $quality The WebP quality (0-100).
     * @param int|null $maxWidth The maximum width to scale down to (maintains aspect ratio).
     * @return string The path to the stored image relative to the disk.
     */
    public function optimizeAndStoreImage(UploadedFile $file, string $directory, string $disk = 'public', int $quality = 80, ?int $maxWidth = 1920): string
    {
        // Generate a unique filename with .webp extension
        $filename = uniqid() . '_' . time() . '.webp';
        $path = trim($directory, '/') . '/' . $filename;

        // Initialize ImageManager with GD driver
        $manager = new ImageManager(new Driver());
        
        // Read the image
        $image = $manager->read($file);

        // Scale down if it exceeds max width
        if ($maxWidth !== null && $image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        // Encode to WebP format
        $encoded = $image->toWebp($quality);

        // Store the optimized image
        Storage::disk($disk)->put($path, (string) $encoded);

        return $path;
    }
}
