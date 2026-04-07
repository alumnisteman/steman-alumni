<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    public function index()
    {
        $cacheKey = 'analytics_data';
        $cacheTime = 3600; // 1 hour

        $data = Cache::remember($cacheKey, $cacheTime, function() {
            $totalAlumni = User::where('role', 'alumni')->count();
            
            $alumniByMajor = User::where('role', 'alumni')
                ->selectRaw('jurusan as major_name, count(*) as total')
                ->groupBy('jurusan')
                ->get()
                ->map(function($item) {
                    return (object) [
                        'jurusan' => $item->major_name ?: 'Tidak Terisi',
                        'total' => $item->total
                    ];
                });

            $alumniByYear = User::where('role', 'alumni')
                ->selectRaw('tahun_lulus as graduation_year, count(*) as total')
                ->groupBy('tahun_lulus')
                ->orderBy('tahun_lulus')
                ->get()
                ->map(function($item) {
                    return (object) [
                        'tahun_lulus' => $item->graduation_year ?: '?',
                        'total' => $item->total
                    ];
                });

            $employmentStats = User::where('role', 'alumni')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('CASE WHEN pekerjaan_sekarang IS NOT NULL AND pekerjaan_sekarang != "" THEN "Bekerja / Studi Lanjut" ELSE "Lainnya" END as status')
                ->groupByRaw('CASE WHEN pekerjaan_sekarang IS NOT NULL AND pekerjaan_sekarang != "" THEN "Bekerja / Studi Lanjut" ELSE "Lainnya" END')
                ->get();

            $careerPaths = User::where('role', 'alumni')
                ->whereNotNull('pekerjaan_sekarang')
                ->where('pekerjaan_sekarang', '!=', '')
                ->selectRaw('jurusan, pekerjaan_sekarang, count(*) as total')
                ->groupBy('jurusan', 'pekerjaan_sekarang')
                ->orderBy('total', 'desc')
                ->get()
                ->groupBy('jurusan')
                ->map(function($items) {
                    return $items->take(3); // Top 3 career paths per major
                });

            return [
                'totalAlumni' => $totalAlumni,
                'alumniByMajor' => $alumniByMajor,
                'alumniByYear' => $alumniByYear,
                'employmentStats' => $employmentStats,
                'careerPaths' => $careerPaths
            ];
        });

        return view('analytics.index', $data);
    }
}
