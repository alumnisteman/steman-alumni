<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Show the Global Network Mesh Map.
     */
    public function index()
    {
        return view('portal.global_network');
    }

    /**
     * Get Alumni Data with Coordinates for Leaflet.
     */
    public function data()
    {
        $alumni = \Illuminate\Support\Facades\Cache::remember('global_network_data', 3600, function () {
            return User::where('role', 'alumni')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->select('id', 'name', 'major', 'graduation_year', 'latitude', 'longitude', 'city_name', 'profile_picture')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'major' => $user->major,
                        'year' => $user->graduation_year,
                        'lat' => (float) $user->latitude,
                        'lng' => (float) $user->longitude,
                        'city' => $user->city_name,
                        'avatar' => $user->profile_picture_url,
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'origin' => [
                'name' => 'SMKN 2 Ternate (Hub)',
                'lat' => 0.7856,
                'lng' => 127.3719
            ],
            'alumni' => $alumni
        ]);
    }

    /**
     * Get AI Insight for a specific location
     */
    public function aiInsight(Request $request)
    {
        $city = $request->get('city', 'Unknown');
        $count = $request->get('count', 0);
        $majors = $request->get('majors', '');

        $prompt = "Buatkan 1 kalimat narasi intelijen analitik tentang kekuatan alumni di {$city}. Terdapat {$count} alumni, mayoritas dari jurusan {$majors}. Beri kesan futuristik/high-tech bahwa daerah ini adalah hub penting. Gunakan bahasa Indonesia profesional. Jangan pakai bullet points.";

        $aiService = app(\App\Services\AIService::class);
        $insight = $aiService->ask($prompt, 0.7) ?? "Intelijen satelit mengonfirmasi {$count} alumni aktif di {$city}, menandakan wilayah ini sebagai titik kumpul strategis.";

        return response()->json([
            'success' => true,
            'insight' => $insight
        ]);
    }
}
