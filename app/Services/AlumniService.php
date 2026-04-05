<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AlumniService
{
    protected $alumniRepository;

    public function __construct(\App\Repositories\Contracts\AlumniRepositoryInterface $alumniRepository)
    {
        $this->alumniRepository = $alumniRepository;
    }

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
        return $this->alumniRepository->getGraduationYears();
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
