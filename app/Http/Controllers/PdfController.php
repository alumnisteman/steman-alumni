<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    /**
     * Serve PDF files securely (inline viewer, no direct download link exposed).
     *
     * URL  : GET /pdf/view?f=campaign-docs%2FLPJ_Reuni2026.pdf
     * Akses: Publik (dokumen resmi yang memang untuk alumni & masyarakat umum)
     *
     * Security:
     *  - Hanya file .pdf yang diizinkan
     *  - Path traversal (..) diblokir
     *  - Hanya direktori yang ada di ALLOWED_DIRS yang bisa diakses
     *  - Content-Disposition: inline → tampil di browser, tidak di-download otomatis
     *  - X-Frame-Options: SAMEORIGIN → hanya bisa di-embed dari domain yang sama
     */
    private const ALLOWED_DIRS = [
        'campaign-docs/',
        'donations/',
        'reports/',
    ];

    public function view(Request $request)
    {
        $file = $request->query('f', '');

        // 1. Harus ada parameter file
        if (empty($file)) {
            abort(400, 'Parameter file tidak ditemukan.');
        }

        // 2. Blokir path traversal
        if (str_contains($file, '..') || str_contains($file, '//') || str_contains($file, "\0")) {
            abort(403, 'Akses ditolak.');
        }

        // 3. Hanya file .pdf
        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'pdf') {
            abort(403, 'Hanya file PDF yang diizinkan.');
        }

        // 4. Hanya dari direktori yang diizinkan
        $allowed = false;
        foreach (self::ALLOWED_DIRS as $dir) {
            if (str_starts_with($file, $dir)) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            abort(403, 'Direktori tidak diizinkan.');
        }

        // 5. File harus ada di disk public
        if (!Storage::disk('public')->exists($file)) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        $contents = Storage::disk('public')->get($file);
        $filename  = basename($file);

        return response($contents, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Length'      => strlen($contents),
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'X-Frame-Options'     => 'SAMEORIGIN',
            'Cache-Control'       => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
