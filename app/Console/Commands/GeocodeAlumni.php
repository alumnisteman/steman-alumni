<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeocodeAlumni extends Command
{
    protected $signature = 'alumni:geocode {--limit=20 : Jumlah alumni yang diproses per satu kali jalan} {--force : Paksa geocode ulang semua, termasuk yang sudah punya koordinat}';
    protected $description = 'Geocode otomatis koordinat alumni berdasarkan nama kota/alamat via Nominatim (OpenStreetMap)';

    // Koordinat default jika geocoding gagal total
    const HUB_LAT = 0.7935;
    const HUB_LNG = 127.3765;

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $force = $this->option('force');

        $query = User::where('role', 'alumni');

        if (!$force) {
            // Hanya alumni yang belum punya koordinat dan punya nama kota/alamat
            $query->where(function ($q) {
                $q->whereNull('latitude')->orWhereNull('longitude');
            })->where(function ($q) {
                $q->whereNotNull('city_name')->orWhereNotNull('address');
            });
        }

        $alumni = $query->take($limit)->get(['id', 'name', 'city_name', 'address']);

        if ($alumni->isEmpty()) {
            $this->info('✅ Semua alumni yang memiliki data kota/alamat sudah punya koordinat.');
            return 0;
        }

        $this->info("🌍 Memproses geocoding untuk {$alumni->count()} alumni...");
        $this->newLine();

        $berhasil = 0;
        $gagal    = 0;

        $bar = $this->output->createProgressBar($alumni->count());
        $bar->start();

        foreach ($alumni as $user) {
            $searchQuery = $user->city_name ?: $user->address;
            if (empty($searchQuery)) {
                $bar->advance();
                $gagal++;
                continue;
            }

            $coords = $this->geocodeViaNoMatim($searchQuery);

            if ($coords) {
                User::withoutEvents(function () use ($user, $coords) {
                    User::where('id', $user->id)->update([
                        'latitude'  => $coords['lat'],
                        'longitude' => $coords['lng'],
                    ]);
                });
                $berhasil++;
            } else {
                $gagal++;
                Log::info("GeocodeAlumni: Tidak bisa geocode '{$searchQuery}' untuk alumni ID {$user->id}");
            }

            $bar->advance();

            // Hormati rate limit Nominatim: maks 1 request/detik
            usleep(1100000); // 1.1 detik
        }

        $bar->finish();
        $this->newLine(2);

        // Hapus cache peta agar data terbaru langsung terlihat
        Cache::forget('alumni_map_analytics');
        Cache::forget('welcome_data_static');
        Cache::forget('network_map_data_v2');
        Cache::forget('global_network_data');

        $this->info("✅ Selesai: {$berhasil} berhasil | {$gagal} gagal");
        $this->info('🔄 Cache peta sudah dibersihkan — peta akan menampilkan data terbaru.');

        return 0;
    }

    /**
     * Geocode via Nominatim OpenStreetMap (gratis, tanpa API key)
     * Sudah sesuai dengan kebijakan penggunaan Nominatim (1 req/detik, User-Agent jelas)
     */
    private function geocodeViaNoMatim(string $query): ?array
    {
        $cacheKey = 'geocode_nominatim_' . md5($query);

        if (Cache::has('geocode_fail_' . md5($query))) {
            return null;
        }

        // Cek cache dulu sebelum request ke API
        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        $searchQuery = $query;
        if (!str_contains(strtolower($searchQuery), 'indonesia')) {
            $searchQuery .= ', Indonesia';
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'StemanAlumniPortal/2.0 (alumni-steman.my.id; geocode-batch)',
                'Accept-Language' => 'id,en',
            ])->timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                'q'              => $searchQuery,
                'format'         => 'json',
                'limit'          => 1,
                'countrycodes'   => 'id',
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data   = $response->json()[0];
                $result = ['lat' => (float) $data['lat'], 'lng' => (float) $data['lon']];

                // Simpan di cache 30 hari supaya tidak request ulang
                Cache::put($cacheKey, $result, 86400 * 30);

                return $result;
            }

            // Tandai sebagai gagal selama 24 jam agar tidak spam ke API
            Cache::put('geocode_fail_' . md5($query), true, 86400);
            return null;
        } catch (\Exception $e) {
            Log::warning("GeocodeAlumni Nominatim error: " . $e->getMessage());
            return null;
        }
    }
}
