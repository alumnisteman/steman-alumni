<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GeocodingController extends Controller
{
    public function index(Request $request)
    {
        $filter  = $request->get('filter', 'all');
        $query   = User::where('role', 'alumni');
        $total   = User::where('role', 'alumni')->count();
        $success = User::where('role', 'alumni')->whereNotNull('latitude')->whereNotNull('longitude')->count();
        $missing = User::where('role', 'alumni')
            ->where(function ($q) { $q->whereNull('latitude')->orWhereNull('longitude'); })
            ->where(function ($q) { $q->whereNotNull('city_name')->orWhereNotNull('address'); })
            ->count();
        $failed  = User::where('role', 'alumni')
            ->where(function ($q) { $q->whereNull('latitude')->orWhereNull('longitude'); })
            ->whereNull('city_name')->whereNull('address')
            ->count();

        $stats = [
            'total'   => $total,
            'success' => $success,
            'missing' => $missing,
            'failed'  => $failed,
            'pending' => $missing,
        ];

        switch ($filter) {
            case 'success':
                $query->whereNotNull('latitude')->whereNotNull('longitude');
                break;
            case 'missing':
                $query->where(function ($q) { $q->whereNull('latitude')->orWhereNull('longitude'); })
                      ->where(function ($q) { $q->whereNotNull('city_name')->orWhereNotNull('address'); });
                break;
            case 'failed':
                $query->where(function ($q) { $q->whereNull('latitude')->orWhereNull('longitude'); })
                      ->whereNull('city_name')->whereNull('address');
                break;
        }

        $users        = $query->orderBy('name')->paginate(50);
        $groupedStats = ['all' => $total, 'success' => $success, 'missing' => $missing, 'failed' => $failed];

        return view('admin.system.geocoding', compact('users', 'stats', 'filter', 'groupedStats'));
    }

    public function retryAll()
    {
        try {
            $count = User::where('role', 'alumni')
                ->where(function ($q) { $q->whereNull('latitude')->orWhereNull('longitude'); })
                ->where(function ($q) { $q->whereNotNull('city_name')->orWhereNotNull('address'); })
                ->count();

            Artisan::call('app:geocode-alumni');
            Log::info("GeocodingController: Retry-all dipicu untuk {$count} alumni.");
            return back()->with('success', "Geocoding dijadwalkan ulang untuk {$count} alumni.");
        } catch (\Exception $e) {
            Log::error('GeocodingController retry-all: ' . $e->getMessage());
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function retry(User $user)
    {
        try {
            if (empty($user->city_name) && empty($user->address)) {
                return back()->with('error', 'Alumni ini tidak memiliki data alamat untuk di-geocode.');
            }
            $address  = $user->city_name ?? $user->address;
            $response = Http::timeout(5)
                ->withHeaders(['User-Agent' => 'StEMAN-Alumni-App/1.0'])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q'      => $address . ', Indonesia',
                    'format' => 'json',
                    'limit'  => 1,
                ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                $user->update(['latitude' => (float) $data['lat'], 'longitude' => (float) $data['lon']]);
                Log::info("GeocodingController: Berhasil geocode alumni #{$user->id}.");
                return back()->with('success', "Koordinat {$user->name} berhasil diperbarui.");
            }
            return back()->with('error', "Geocoding gagal untuk {$user->name} — alamat tidak ditemukan.");
        } catch (\Exception $e) {
            Log::error('GeocodingController retry: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
