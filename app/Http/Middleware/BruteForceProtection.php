<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware Proteksi Brute Force
 *
 * Cara kerja:
 * - Setelah 10 kali gagal login dalam 5 menit → blokir IP selama 15 menit
 * - Setelah 30 kali gagal dalam 1 jam → blokir IP selama 24 jam
 * - Setelah 100 kali gagal total → blokir permanen (manual reset oleh admin)
 * - Semua IP yang terblokir dicatat di log untuk audit
 */
class BruteForceProtection
{
    // Batas sebelum diblokir
    const SOFT_BAN_THRESHOLD  = 10;   // gagal dalam 5 menit → blokir 15 menit
    const HARD_BAN_THRESHOLD  = 30;   // gagal dalam 1 jam → blokir 24 jam
    const PERM_BAN_THRESHOLD  = 100;  // gagal total → blokir permanen

    // Durasi blokir (detik)
    const SOFT_BAN_DURATION   = 15 * 60;    // 15 menit
    const HARD_BAN_DURATION   = 24 * 60 * 60; // 24 jam
    const PERM_BAN_DURATION   = 30 * 24 * 60 * 60; // 30 hari

    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // Cek apakah IP sudah diblokir
        if ($this->isBlocked($ip)) {
            Log::warning("BruteForce: Blocked IP {$ip} mencoba akses ke " . $request->path());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'IP Anda diblokir sementara karena terlalu banyak percobaan login yang gagal. Coba lagi nanti.',
                ], 429);
            }

            return response()->view('errors.429', [
                'message' => 'IP Anda diblokir sementara karena terlalu banyak percobaan login yang gagal.',
                'retry_after' => $this->getBlockExpiry($ip),
            ], 429);
        }

        $response = $next($request);

        // Catat kegagalan login hanya pada POST /login dengan response redirect (bukan sukses)
        if ($request->isMethod('POST') && $request->is('login') && $response->getStatusCode() === 302) {
            $redirectUrl = $response->headers->get('Location', '');
            // Redirect kembali ke /login = gagal login
            if (str_contains($redirectUrl, 'login')) {
                $this->recordFailure($ip);
            }
        }

        return $response;
    }

    private function isBlocked(string $ip): bool
    {
        return Cache::has("brute_perm:{$ip}")
            || Cache::has("brute_hard:{$ip}")
            || Cache::has("brute_soft:{$ip}");
    }

    private function getBlockExpiry(string $ip): ?int
    {
        foreach (['brute_perm', 'brute_hard', 'brute_soft'] as $type) {
            if (Cache::has("{$type}:{$ip}")) {
                return Cache::get("{$type}_expiry:{$ip}");
            }
        }
        return null;
    }

    private function recordFailure(string $ip): void
    {
        $now = time();

        // Counter rolling 5 menit (soft ban check)
        $softKey   = "brute_attempts_soft:{$ip}";
        $softCount = Cache::increment($softKey);
        if ($softCount === 1) {
            Cache::put($softKey, 1, 5 * 60);
        }

        // Counter rolling 1 jam (hard ban check)
        $hardKey   = "brute_attempts_hard:{$ip}";
        $hardCount = Cache::increment($hardKey);
        if ($hardCount === 1) {
            Cache::put($hardKey, 1, 60 * 60);
        }

        // Counter total keseluruhan (permanent ban check)
        $totalKey   = "brute_attempts_total:{$ip}";
        $totalCount = Cache::increment($totalKey);
        if ($totalCount === 1) {
            Cache::put($totalKey, 1, self::PERM_BAN_DURATION);
        }

        // Evaluasi level blokir
        if ($totalCount >= self::PERM_BAN_THRESHOLD) {
            Cache::put("brute_perm:{$ip}", true, self::PERM_BAN_DURATION);
            Cache::put("brute_perm_expiry:{$ip}", $now + self::PERM_BAN_DURATION, self::PERM_BAN_DURATION);
            Log::critical("BruteForce: IP {$ip} diblokir PERMANEN setelah {$totalCount} percobaan gagal total.");

        } elseif ($hardCount >= self::HARD_BAN_THRESHOLD) {
            Cache::put("brute_hard:{$ip}", true, self::HARD_BAN_DURATION);
            Cache::put("brute_hard_expiry:{$ip}", $now + self::HARD_BAN_DURATION, self::HARD_BAN_DURATION);
            Log::critical("BruteForce: IP {$ip} diblokir 24 JAM setelah {$hardCount} percobaan gagal dalam 1 jam.");

        } elseif ($softCount >= self::SOFT_BAN_THRESHOLD) {
            Cache::put("brute_soft:{$ip}", true, self::SOFT_BAN_DURATION);
            Cache::put("brute_soft_expiry:{$ip}", $now + self::SOFT_BAN_DURATION, self::SOFT_BAN_DURATION);
            Log::warning("BruteForce: IP {$ip} diblokir 15 MENIT setelah {$softCount} percobaan gagal dalam 5 menit.");
        }
    }
}
