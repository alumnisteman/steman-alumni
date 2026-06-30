<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeocodeUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int   $tries   = 3;
    public int   $timeout = 60;
    public array $backoff = [120, 600]; // retry setelah 2 menit, lalu 10 menit

    public function __construct(public readonly int $userId, public readonly string $query) {}

    public function handle(AIService $aiService): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            Log::warning("GeocodeUserJob: User {$this->userId} tidak ditemukan — skip.");
            return;
        }

        if ($user->latitude && $user->longitude) {
            Log::info("GeocodeUserJob: User {$this->userId} sudah punya koordinat — skip.");
            // Tetap tandai sebagai sudah dicoba agar tidak masuk hitungan mismatch
            $this->markAttempted($user);
            return;
        }

        Log::info("GeocodeUserJob: Memulai geocoding user {$this->userId} dengan query: {$this->query}");

        $coords = $aiService->geocode($this->query);

        if ($coords && isset($coords['lat'], $coords['lng']) && $coords['lat'] !== null) {
            $update = [
                'latitude'             => (float) $coords['lat'],
                'longitude'            => (float) $coords['lng'],
                'geocode_attempted_at' => now(),
            ];

            if (empty($user->city_name) && !empty($user->address)) {
                $update['city_name'] = trim(explode(',', $user->address)[0]);
            }

            User::withoutEvents(function () use ($user, $update) {
                $user->update($update);
            });

            app(\App\Services\AlumniService::class)->clearCache();
            Log::info("GeocodeUserJob: Sukses geocoding user {$this->userId}: lat={$coords['lat']}, lng={$coords['lng']}");
        } else {
            // Geocoding gagal — tandai waktu percobaan agar tidak di-flag mismatch terus-menerus.
            // Setelah 24 jam, sistem akan mencoba lagi secara otomatis.
            $this->markAttempted($user);
            Log::warning("GeocodeUserJob: Geocoding gagal untuk user {$this->userId} (query: {$this->query}). Akan dicoba ulang setelah 24 jam.");
        }
    }

    /**
     * Tandai user sudah pernah dicoba geocoding (tanpa trigger observer).
     */
    private function markAttempted(User $user): void
    {
        User::withoutEvents(function () use ($user) {
            $user->update(['geocode_attempted_at' => now()]);
        });
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GeocodeUserJob: Job gagal untuk user {$this->userId}: " . $exception->getMessage());
        // Tandai tetap sudah dicoba agar tidak spam
        $user = User::find($this->userId);
        if ($user) $this->markAttempted($user);
    }
}
