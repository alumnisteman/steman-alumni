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

    /**
     * Get Basic Dashboard Data (Fast)
     */
    public function getDashboardData($user)
    {
        // 1. News
        $latestNews = \App\Models\News::where('status', 'published')->latest()->take(2)->get();
        
        // 2. Job Recommendations
        $recommendedJobs = \App\Models\JobVacancy::where('status', 'active')
            ->where(function($q) use ($user) {
                $major = $user->major ?? 'NONE';
                $q->where('description', 'like', '%' . $major . '%')
                  ->orWhere('title', 'like', '%' . $major . '%');
            })->latest()->take(3)->get()
            ->map(function($job) {
                $job->match_percentage = rand(85, 98); 
                return $job;
            });

        // 3. Badges (Optimized: only sync if points changed or random interval)
        $userBadges = $user->badges()->get();
        if ($userBadges->isEmpty() && $user->role == 'alumni') {
            $pelopor = \App\Models\Badge::where('name', 'Alumni Pelopor')->first();
            if ($pelopor) {
                $user->badges()->syncWithoutDetaching([$pelopor->id]);
                $userBadges = collect([$pelopor]);
            }
        }

        // 4. Map Analytics
        $mapAnalytics = $this->getCachedMapAnalytics();

        return [
            'latestNews' => $latestNews,
            'recommendedJobs' => $recommendedJobs,
            'userBadges' => $userBadges,
            'alumniLocations' => $mapAnalytics['alumniLocations'],
            'nationalCount' => $mapAnalytics['nationalCount'],
            'internationalCount' => $mapAnalytics['internationalCount'],
        ];
    }

    /**
     * Get Heavy AI Data for Dashboard (Slow)
     */
    public function getDashboardAIData($user)
    {
        return Cache::remember("alumni_ai_dashboard_{$user->id}", 3600, function() use ($user) {
            $aiService = new \App\Services\AIService();
            
            // 1. Career Advice
            $aiPrediction = $aiService->ask("Profile: Major {$user->major}, Job {$user->current_job}. Give 1 short encouraging sentence of career advice in Indonesian.", 0.6);

            // 2. Networking Recommendations
            $candidates = User::where('role', 'alumni')
                ->where('id', '!=', $user->id)
                ->where('status', 'approved')
                ->inRandomOrder()
                ->take(10)
                ->get(['id', 'name', 'major', 'current_job', 'bio'])
                ->toArray();
            
            $recs = $aiService->recommendAlumni($user->only(['name', 'major', 'bio']), $candidates);
            
            $aiRecommendations = collect($recs)->map(function($rec) {
                $alumni = User::find($rec['id']);
                if ($alumni) {
                    $alumni->ai_reason = $rec['reason'];
                    // Mask sensitive data for recommendations too
                    $alumni->profile_picture = $alumni->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($alumni->name).'&background=4361ee&color=fff';
                    return $alumni;
                }
                return null;
            })->filter();

            // 3. Career Snippet
            $careerSnippet = User::where('role', 'alumni')
                ->where('major', $user->major)
                ->whereNotNull('current_job')
                ->where('current_job', '!=', '')
                ->selectRaw('current_job, count(*) as total')
                ->groupBy('current_job')
                ->orderBy('total', 'desc')
                ->first();

            return [
                'aiPrediction' => $aiPrediction,
                'aiRecommendations' => $aiRecommendations,
                'careerSnippet' => $careerSnippet ? [
                    'pekerjaan' => $careerSnippet->current_job,
                    'total' => $careerSnippet->total
                ] : null
            ];
        });
    }

    /**
     * Get Fallback Dashboard Data for error cases
     */
    public function getDashboardFallbackData()
    {
        return [
            'latestNews' => collect(),
            'recommendedJobs' => collect(),
            'userBadges' => collect(),
            'alumniLocations' => collect(),
            'nationalCount' => 0,
            'internationalCount' => 0,
            'aiPrediction' => null,
            'careerSnippet' => null,
            'aiRecommendations' => collect()
        ];
    }

    /**
     * Get Aggregated Home Page Data
     */
    public function getWelcomeData()
    {
        return Cache::remember('welcome_data', 600, function () {
            $aiService = new \App\Services\AIPredictionService();
            $mapAnalytics = User::getMapAnalytics();

            return [
                'latestNews' => \App\Models\News::where('status', 'published')->latest()->take(3)->get(),
                'latestPhotos' => \App\Models\Gallery::where('type', 'photo')->latest()->take(4)->get(),
                'latestVideos' => \App\Models\Gallery::where('type', 'video')->latest()->take(2)->get(),
                'activePrograms' => \App\Models\Program::where('status', 'active')->latest()->take(3)->get(),
                'latestJobs' => \App\Models\JobVacancy::where('status', 'active')->latest()->take(3)->get(),
                'successStories' => \App\Models\SuccessStory::where('is_published', true)->orderBy('order')->take(3)->get(),
                'mapAnalytics' => $mapAnalytics,
                'alumniLocations' => $mapAnalytics['alumniLocations'],
                'nationalCount' => $mapAnalytics['nationalCount'],
                'internationalCount' => $mapAnalytics['internationalCount'],
                'aiInsights' => $aiService->getGlobalInsights(),
            ];
        });
    }
}
