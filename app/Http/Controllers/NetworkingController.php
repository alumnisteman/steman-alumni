<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NetworkingController extends Controller
{
    /**
     * Get "People You May Know" recommendations for the current alumni.
     * Falls back to Eloquent if Meilisearch is unavailable.
     */
    public function getRecommendations()
    {
        $user = Auth::user();

        if ($user->role !== 'alumni') {
            return response()->json(['success' => false, 'recommendations' => []]);
        }

        try {
            // Priority 1: Same Major & Same Graduation Year via Meilisearch
            $recommendations = User::search($user->major ?? '')
                ->where('id', '!=', $user->id)
                ->where('graduation_year', $user->graduation_year)
                ->take(6)
                ->get();

            // If not enough, fill with same major from different years
            if ($recommendations->count() < 4) {
                $more = User::search($user->major ?? '')
                    ->where('id', '!=', $user->id)
                    ->whereNotIn('id', $recommendations->pluck('id')->toArray())
                    ->take(4 - $recommendations->count())
                    ->get();
                $recommendations = $recommendations->concat($more);
            }
        } catch (\Throwable $e) {
            // Meilisearch unavailable — graceful fallback to Eloquent
            Log::warning('Meilisearch unavailable for recommendations, using Eloquent fallback: ' . $e->getMessage());
            $recommendations = User::where('id', '!=', $user->id)
                ->where('status', 'approved')
                ->where('role', 'alumni')
                ->where('major', $user->major)
                ->inRandomOrder()
                ->take(6)
                ->get();
        }

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations->map(function ($u) {
                return [
                    'id'              => $u->id,
                    'name'            => $u->name,
                    'major'           => $u->major,
                    'graduation_year' => $u->graduation_year,
                    'current_job'     => $u->current_job ?? 'Alumni',
                    'profile_picture' => $u->profile_picture_url,
                ];
            }),
        ]);
    }

    /**
     * Get alumni sorted by distance (Alumni Radar).
     * Falls back to Eloquent sort if Meilisearch is unavailable.
     */
    public function nearby(Request $request)
    {
        $lat = (float) $request->lat;
        $lng = (float) $request->lng;

        if (!$lat || !$lng) {
            return response()->json(['success' => false, 'message' => 'Lokasi tidak terdeteksi.']);
        }

        try {
            // Use raw Meilisearch geo-sort syntax
            $recommendations = User::search('')
                ->options([
                    'sort'   => ["_geoPoint({$lat},{$lng}):asc"],
                    'filter' => 'id != ' . Auth::id(),
                ])
                ->take(8)
                ->get();

            // Meilisearch returned results but they may lack the _geo field (not indexed yet)
            if ($recommendations->isEmpty()) {
                throw new \RuntimeException('Empty Meilisearch result, using Eloquent fallback.');
            }
        } catch (\Throwable $e) {
            Log::warning('Meilisearch radar fallback triggered: ' . $e->getMessage());

            // Fallback: load everyone with lat/lng and sort by Haversine in PHP
            $recommendations = User::where('id', '!=', Auth::id())
                ->where('status', 'approved')
                ->where('role', 'alumni')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get()
                ->sortBy(fn($u) => $this->calculateDistance($lat, $lng, $u->latitude, $u->longitude))
                ->take(8)
                ->values();
        }

        return response()->json([
            'success'         => true,
            'recommendations' => $recommendations->map(function ($u) use ($lat, $lng) {
                $distance = ($u->latitude && $u->longitude)
                    ? round($this->calculateDistance($lat, $lng, $u->latitude, $u->longitude), 1)
                    : null;

                return [
                    'id'              => $u->id,
                    'name'            => $u->name,
                    'graduation_year' => $u->graduation_year,
                    'current_job'     => $u->current_job ?? 'Alumni',
                    'distance'        => $distance ?? '?',
                    'profile_picture' => $u->profile_picture_url,
                ];
            }),
        ]);
    }

    /**
     * Haversine formula — great-circle distance in km.
     */
    private function calculateDistance(float $lat1, float $lon1, float|string $lat2, float|string $lon2): float
    {
        if (!$lat2 || !$lon2) {
            return 99999;
        }

        $earthRadius = 6371;
        $dLat = deg2rad((float)$lat2 - $lat1);
        $dLon = deg2rad((float)$lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($lat1)) * cos(deg2rad((float)$lat2)) * sin($dLon / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
