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
                        'avatar' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('assets/images/default-avatar.png'),
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
}
