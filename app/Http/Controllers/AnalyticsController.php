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
                ->selectRaw('major as major_name, count(*) as total')
                ->groupBy('major')
                ->get()
                ->map(function($item) {
                    return (object) [
                        'major' => $item->major_name ?: 'Tidak Terisi',
                        'total' => $item->total
                    ];
                });

            $alumniByYear = User::where('role', 'alumni')
                ->selectRaw('graduation_year as graduation_year, count(*) as total')
                ->groupBy('graduation_year')
                ->orderBy('graduation_year')
                ->get()
                ->map(function($item) {
                    return (object) [
                        'graduation_year' => $item->graduation_year ?: '?',
                        'total' => $item->total
                    ];
                });

            $employmentStats = User::where('role', 'alumni')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('CASE WHEN current_job IS NOT NULL AND current_job != "" THEN "Bekerja / Studi Lanjut" ELSE "Lainnya" END as status')
                ->groupByRaw('CASE WHEN current_job IS NOT NULL AND current_job != "" THEN "Bekerja / Studi Lanjut" ELSE "Lainnya" END')
                ->get();

            $careerPaths = User::where('role', 'alumni')
                ->whereNotNull('current_job')
                ->where('current_job', '!=', '')
                ->selectRaw('major, current_job, count(*) as total')
                ->groupBy('major', 'current_job')
                ->orderBy('total', 'desc')
                ->get()
                ->groupBy('major')
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
