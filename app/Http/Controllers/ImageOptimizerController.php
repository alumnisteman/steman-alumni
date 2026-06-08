<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Cache;

class ImageOptimizerController extends Controller
{
    // Ekstensi file yang diizinkan (whitelist)
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function optimize(Request $request, $path)
    {
        // ─── FIX CRITICAL: Path Traversal Prevention ──────────────────────────
        // Normalisasi path dan blokir traversal (/../, /./., symlink di luar root)
        $normalizedPath = ltrim($path, '/');

        // Blokir karakter berbahaya dan path traversal
        if (
            str_contains($normalizedPath, '..') ||
            str_contains($normalizedPath, "\0") ||
            preg_match('/[<>:"|?*]/', $normalizedPath)
        ) {
            abort(400, 'Path tidak valid.');
        }

        // Resolusi path absolut yang aman
        $storageBase = realpath(storage_path('app/public'));
        $fullPath    = realpath($storageBase . DIRECTORY_SEPARATOR . $normalizedPath);

        // Pastikan path yang diresolve masih di dalam storage/app/public/
        if (!$fullPath || !str_starts_with($fullPath, $storageBase . DIRECTORY_SEPARATOR)) {
            abort(403, 'Akses ditolak.');
        }

        // Hanya izinkan file dengan ekstensi gambar
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            abort(400, 'Tipe file tidak didukung.');
        }

        if (!file_exists($fullPath)) {
            abort(404);
        }
        // ─── END FIX ──────────────────────────────────────────────────────────

        $width   = $request->query('w') ? (int) $request->query('w') : null;
        $height  = $request->query('h') ? (int) $request->query('h') : null;
        $quality = max(1, min(100, (int) $request->query('q', 75)));
        $format  = $request->query('f', 'webp');

        // Sanitasi format output — hanya izinkan format yang aman
        if (!in_array($format, ['webp', 'jpg', 'jpeg', 'png'], true)) {
            $format = 'webp';
        }

        // Fallback ke jpeg jika GD tidak support WebP
        if ($format === 'webp' && !function_exists('imagewebp')) {
            $format = 'jpg';
        }

        // Batasi dimensi maksimum untuk mencegah resource exhaustion
        if ($width  && $width  > 3000) abort(400, 'Lebar maksimum 3000px.');
        if ($height && $height > 3000) abort(400, 'Tinggi maksimum 3000px.');

        $cacheKey = 'img_opt_v2_' . md5($fullPath . $width . $height . $quality . $format);

        $imageData = Cache::remember($cacheKey, 86400 * 7, function () use ($fullPath, $width, $height, $quality, $format) {
            try {
                $manager = new ImageManager(new Driver());
                $image   = $manager->read($fullPath);

                if ($width || $height) {
                    if ($width && $height) {
                        $image->cover($width, $height);
                    } else {
                        $image->scale($width, $height);
                    }
                }

                return $image->encodeByExtension($format, $quality)->toString();
            } catch (\Exception $e) {
                // Jangan kembalikan file mentah jika gagal; abort dengan 500
                \Illuminate\Support\Facades\Log::warning('ImageOptimizer: Gagal proses gambar: ' . $e->getMessage());
                return null;
            }
        });

        if (!$imageData) {
            abort(500, 'Gagal memproses gambar.');
        }

        $mimeType = match ($format) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            default       => 'image/webp',
        };

        return response($imageData)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=604800, immutable');
    }
}
