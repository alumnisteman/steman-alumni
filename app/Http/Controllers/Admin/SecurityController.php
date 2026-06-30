<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SecurityController extends Controller
{
    /**
     * Tampilkan dashboard firewall / brute force monitor
     */
    public function firewall()
    {
        $blockedIps = $this->getBlockedIps();
        $stats      = $this->getStats();

        return view('admin.security.firewall', compact('blockedIps', 'stats'));
    }

    /**
     * API: ambil data terbaru (untuk auto-refresh tanpa reload halaman)
     */
    public function firewallApi()
    {
        return response()->json([
            'blocked' => $this->getBlockedIps(),
            'stats'   => $this->getStats(),
        ]);
    }

    /**
     * Buka blokir satu IP secara manual
     */
    public function unblock(Request $request)
    {
        $request->validate(['ip' => 'required|ip']);
        $ip = $request->ip;

        $keys = [
            "brute_perm:{$ip}",        "brute_perm_expiry:{$ip}",
            "brute_hard:{$ip}",        "brute_hard_expiry:{$ip}",
            "brute_soft:{$ip}",        "brute_soft_expiry:{$ip}",
            "brute_attempts_soft:{$ip}",
            "brute_attempts_hard:{$ip}",
            "brute_attempts_total:{$ip}",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Log::warning("Security: Admin " . auth()->user()->name . " membuka blokir IP {$ip}.");

        return response()->json([
            'success' => true,
            'message' => "IP {$ip} berhasil dibuka blokirnya.",
        ]);
    }

    /**
     * Buka blokir semua IP sekaligus
     */
    public function unblockAll()
    {
        $blocked = $this->getBlockedIps();
        foreach ($blocked as $entry) {
            $ip   = $entry['ip'];
            $keys = [
                "brute_perm:{$ip}",        "brute_perm_expiry:{$ip}",
                "brute_hard:{$ip}",        "brute_hard_expiry:{$ip}",
                "brute_soft:{$ip}",        "brute_soft_expiry:{$ip}",
                "brute_attempts_soft:{$ip}",
                "brute_attempts_hard:{$ip}",
                "brute_attempts_total:{$ip}",
            ];
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }

        Log::warning("Security: Admin " . auth()->user()->name . " membuka blokir SEMUA " . count($blocked) . " IP.");

        return response()->json([
            'success' => true,
            'message' => count($blocked) . " IP berhasil dibuka blokirnya.",
        ]);
    }

    /**
     * Jalankan geocoding manual dari panel admin
     */
    public function runGeocode(Request $request)
    {
        $limit = (int) $request->input('limit', 20);
        $limit = max(1, min(50, $limit)); // batasi 1-50

        try {
            $output = [];
            \Illuminate\Support\Facades\Artisan::call('alumni:geocode', [
                '--limit' => $limit,
            ], new \Symfony\Component\Console\Output\BufferedOutput());

            $result = \Illuminate\Support\Facades\Artisan::output();

            return response()->json([
                'success' => true,
                'message' => "Geocoding selesai untuk maks {$limit} alumni.",
                'output'  => strip_tags($result),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Geocoding gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function getBlockedIps(): array
    {
        $blocked = [];

        // Cari semua key yang relevan di cache Redis
        try {
            $redis  = \Illuminate\Support\Facades\Redis::connection();
            $prefix = config('cache.prefix') . ':';

            $patterns = ['brute_perm:*', 'brute_hard:*', 'brute_soft:*'];

            foreach ($patterns as $pattern) {
                $keys = $redis->keys($prefix . $pattern);
                foreach ($keys as $fullKey) {
                    // Hindari key expiry helper
                    if (str_contains($fullKey, '_expiry:')) continue;

                    $ip    = preg_replace('/^.*brute_(perm|hard|soft):/', '', $fullKey);
                    $level = match (true) {
                        str_contains($fullKey, 'brute_perm:') => 'PERMANENT',
                        str_contains($fullKey, 'brute_hard:') => 'HARD (24 jam)',
                        default                               => 'SOFT (15 menit)',
                    };

                    $total   = (int) Cache::get("brute_attempts_total:{$ip}", 0);
                    $expiry  = Cache::get(preg_replace('/brute_(perm|hard|soft):/', 'brute_$1_expiry:', str_replace($prefix, '', $fullKey)));

                    // Hindari duplikat (IP bisa kena soft + hard sekaligus, tampilkan level tertinggi)
                    $existing = array_search($ip, array_column($blocked, 'ip'));
                    if ($existing !== false) {
                        // Hanya update jika level baru lebih tinggi
                        $priority = ['SOFT (15 menit)' => 1, 'HARD (24 jam)' => 2, 'PERMANENT' => 3];
                        if (($priority[$level] ?? 0) > ($priority[$blocked[$existing]['level']] ?? 0)) {
                            $blocked[$existing]['level']  = $level;
                            $blocked[$existing]['expiry'] = $expiry;
                        }
                        continue;
                    }

                    $blocked[] = [
                        'ip'     => $ip,
                        'level'  => $level,
                        'total'  => $total,
                        'expiry' => $expiry,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("SecurityController: Gagal baca Redis untuk blocked IPs: " . $e->getMessage());
        }

        // Urutkan: permanent dulu, lalu hard, lalu soft
        usort($blocked, function ($a, $b) {
            $priority = ['PERMANENT' => 3, 'HARD (24 jam)' => 2, 'SOFT (15 menit)' => 1];
            return ($priority[$b['level']] ?? 0) <=> ($priority[$a['level']] ?? 0);
        });

        return $blocked;
    }

    private function getStats(): array
    {
        $blocked = $this->getBlockedIps();

        $perm = count(array_filter($blocked, fn($b) => $b['level'] === 'PERMANENT'));
        $hard = count(array_filter($blocked, fn($b) => $b['level'] === 'HARD (24 jam)'));
        $soft = count(array_filter($blocked, fn($b) => $b['level'] === 'SOFT (15 menit)'));

        // Hitung alumni tanpa koordinat
        $alumniTanpaKoordinat = \App\Models\User::where('role', 'alumni')
            ->where(function ($q) {
                $q->whereNull('latitude')->orWhereNull('longitude');
            })
            ->where(function ($q) {
                $q->whereNotNull('city_name')->orWhereNotNull('address');
            })
            ->count();

        $alumniTotal     = \App\Models\User::where('role', 'alumni')->count();
        $alumniGeocoded  = \App\Models\User::where('role', 'alumni')
            ->whereNotNull('latitude')->whereNotNull('longitude')->count();

        return [
            'total_blocked'            => count($blocked),
            'perm'                     => $perm,
            'hard'                     => $hard,
            'soft'                     => $soft,
            'alumni_total'             => $alumniTotal,
            'alumni_geocoded'          => $alumniGeocoded,
            'alumni_tanpa_koordinat'   => $alumniTanpaKoordinat,
            'geocode_pct'              => $alumniTotal > 0 ? round(($alumniGeocoded / $alumniTotal) * 100) : 0,
        ];
    }
}
