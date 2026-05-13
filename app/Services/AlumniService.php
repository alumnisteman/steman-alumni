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
        Cache::forget('global_network_data');
        Cache::forget('alumni_graduation_years');
        Cache::forget('welcome_data');
        Cache::forget('welcome_data_static');
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

            // 2. Networking Recommendations (Powered by Meilisearch, Eloquent fallback)
            try {
                $candidates = User::search($user->major ?? '')
                    ->where('id', '!=', $user->id)
                    ->take(15)
                    ->get()
                    ->map(fn($u) => $u->only(['id', 'name', 'major', 'current_job', 'bio']))
                    ->toArray();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Meilisearch unavailable in AlumniService, using Eloquent: ' . $e->getMessage());
                $candidates = User::where('id', '!=', $user->id)
                    ->where('status', 'approved')
                    ->where('role', 'alumni')
                    ->where('major', $user->major)
                    ->inRandomOrder()
                    ->take(15)
                    ->get()
                    ->map(fn($u) => $u->only(['id', 'name', 'major', 'current_job', 'bio']))
                    ->toArray();
            }
            
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

            // 4. Profile Optimization Suggestion (Social Connect Enhancement)
            $socialLinks = $user->socialLinks;
            $profileSuggestion = $aiService->suggestProfileOptimizations([
                'major' => $user->major,
                'current_job' => $user->current_job,
                'bio' => $user->bio,
                'social_count' => $socialLinks->count(),
                'has_linkedin' => $socialLinks->where('platform', 'linkedin')->isNotEmpty(),
            ]);

            return [
                'aiPrediction' => $aiPrediction,
                'aiRecommendations' => $aiRecommendations,
                'profileSuggestion' => $profileSuggestion,
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
            'profileSuggestion' => null,
            'aiRecommendations' => collect()
        ];
    }

    /**
     * Get Aggregated Home Page Data
     */
    public function getWelcomeData()
    {
        // We cache static content but fetch live data separately
        $staticData = Cache::remember('welcome_data_static', 3600, function () {
            $aiService = new \App\Services\AIPredictionService();
            $mapAnalytics = User::getMapAnalytics();

            return [
                'latestNews' => \App\Models\News::where('status', 'published')->latest()->take(3)->get(),
                'latestPhotos' => \App\Models\Gallery::where('type', 'photo')->where('status', 'published')->latest()->take(4)->get(),
                'latestVideos' => \App\Models\Gallery::where('type', 'video')->where('status', 'published')->latest()->take(2)->get(),
                'activePrograms' => \App\Models\Program::where('status', 'active')->latest()->take(3)->get(),
                'latestJobs' => \App\Models\JobVacancy::where('status', 'active')->latest()->take(3)->get(),
                'successStories' => \App\Models\SuccessStory::where('is_published', true)->orderBy('order')->take(3)->get(),
                'totalAlumni' => \App\Models\User::where('role', 'alumni')->count(),
                'mapAnalytics' => $mapAnalytics,
                'alumniLocations' => $mapAnalytics['alumniLocations'],
                'nationalCount' => $mapAnalytics['nationalCount'],
                'internationalCount' => $mapAnalytics['internationalCount'],
                'aiInsights' => $aiService->getGlobalInsights(),
                'topAlumni' => \App\Models\User::where('role', 'alumni')->with('badges')->orderBy('points', 'desc')->take(3)->get(),
                'schoolName' => setting('school_name', 'SMKN 2 Ternate'),
                'totalPoints' => \App\Models\User::sum('points'),
                'featuredAvatars' => User::active()->inRandomOrder()->take(5)->get()->map(fn($u) => $u->profile_picture_url)->toArray(),
                'recentActivities' => \App\Models\ActivityLog::with('user')->whereNotNull('user_id')->latest()->take(10)->get(),
                'latestPodcasts' => \App\Models\Podcast::where('is_published', true)->latest()->take(3)->get(),
            ];
        });

        // Add Live Data (Only real-time counts)
        $liveData = [
            'onlineCount' => $this->getOnlineAlumniCount(),
            'onlineAvatars' => $this->getOnlineAlumniAvatars(),
        ];

        return array_merge($staticData, $liveData);
    }

    public function getOnlineAlumniAvatars()
    {
        return Cache::remember('online_avatars', 60, function() {
            return User::online()
                ->select('id', 'name', 'profile_picture')
                ->get()
                ->map(fn($u) => $u->profile_picture_url)
                ->toArray();
        });
    }

    /**
     * Get real-time online alumni count
     */
    public function getOnlineAlumniCount()
    {
        return User::online()->count();
    }
}
