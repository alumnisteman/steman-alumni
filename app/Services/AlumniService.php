<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AlumniService
{
    /**
     * Get Cached Map Analytics
     */
    public function getCachedMapAnalytics()
    {
        return Cache::remember('alumni_map_analytics', 3600, function () {
            return User::getMapAnalytics();
        });
    }

    /**
     * Get Cached Graduation Years
     */
    public function getCachedGraduationYears()
    {
        return Cache::remember('alumni_graduation_years', 3600, function () {
            return User::where('role', 'alumni')
                ->whereNotNull('tahun_lulus')
                ->distinct()
                ->orderBy('tahun_lulus', 'desc')
                ->pluck('tahun_lulus');
        });
    }

    /**
     * Clear Alumni Related Cache
     */
    public function clearCache()
    {
        Cache::forget('alumni_map_analytics');
        Cache::forget('alumni_graduation_years');
        Cache::forget('welcome_data');
    }
}
