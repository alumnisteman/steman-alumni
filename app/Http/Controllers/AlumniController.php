<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Major;
use App\Models\News;
use App\Services\AIPredictionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\AlumniService;

class AlumniController extends Controller
{
    protected $alumniService;
    protected $alumniRepository;

    public function __construct(AlumniService $alumniService, \App\Repositories\Contracts\AlumniRepositoryInterface $alumniRepository)
    {
        $this->alumniService = $alumniService;
        $this->alumniRepository = $alumniRepository;
    }

    public function dashboard()
    {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user) {
                return redirect()->route('login');
            }

            // 1. News - Safe retrieval
            try {
                $latestNews = News::where('status', 'published')->latest()->take(2)->get() ?? collect();
            } catch (\Exception $e) {
                $latestNews = collect();
            }
            
            // 2. Job Recommendations - Safe with null-check
            try {
                $recommendedJobs = \App\Models\JobVacancy::where('status', 'active')
                    ->where(function($q) use ($user) {
                        $jurusan = $user->jurusan ?? 'NONE';
                        $q->where('description', 'like', '%' . $jurusan . '%')
                          ->orWhere('title', 'like', '%' . $jurusan . '%');
                    })->latest()->take(3)->get()
                    ->map(function($job) {
                        $job->match_percentage = rand(85, 98); 
                        return $job;
                    });
            } catch (\Exception $e) {
                $recommendedJobs = collect();
            }

            // 3. Badges System - Safe
            try {
                $userBadges = $user->badges()->get() ?? collect();
                if ($userBadges->isEmpty() && $user->role == 'alumni') {
                    $pelopor = \App\Models\Badge::where('name', 'Alumni Pelopor')->first();
                    if ($pelopor) {
                        $user->badges()->syncWithoutDetaching([$pelopor->id]);
                        $userBadges = collect([$pelopor]);
                    }
                }
            } catch (\Exception $e) {
                $userBadges = collect();
            }

            // 4. Map Analytics - REQUIRED for Dashboard Map (Cached)
            $mapAnalytics = $this->alumniService->getCachedMapAnalytics();

            // 5. AI Personalized Prediction & Career Snippet
            $aiPrediction = null;
            $careerSnippet = null;
            try {
                $aiService = new AIPredictionService();
                $aiPrediction = $aiService->getUserPrediction($user);
                
                // Fetch career paths snippet
                $careerSnippet = User::where('role', 'alumni')
                    ->where('jurusan', $user->jurusan)
                    ->whereNotNull('pekerjaan_sekarang')
                    ->where('pekerjaan_sekarang', '!=', '')
                    ->selectRaw('pekerjaan_sekarang, count(*) as total')
                    ->groupBy('pekerjaan_sekarang')
                    ->orderBy('total', 'desc')
                    ->first();
            } catch (\Exception $e) {
                // Skip if error
            }

            return view('alumni.dashboard', [
                'user' => $user,
                'latestNews' => $latestNews,
                'recommendedJobs' => $recommendedJobs,
                'userBadges' => $userBadges,
                'alumniLocations' => $mapAnalytics['alumniLocations'],
                'nationalCount' => $mapAnalytics['nationalCount'],
                'internationalCount' => $mapAnalytics['internationalCount'],
                'aiPrediction' => $aiPrediction,
                'careerSnippet' => $careerSnippet
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard Mega Error: ' . $e->getMessage());
            return view('alumni.dashboard', [
                'user' => \Illuminate\Support\Facades\Auth::user(),
                'latestNews' => collect(),
                'recommendedJobs' => collect(),
                'userBadges' => collect(),
                'alumniLocations' => collect(),
                'nationalCount' => 0,
                'internationalCount' => 0,
                'aiPrediction' => null,
                'careerSnippet' => null
            ]);
        }
    }

    public function messages()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $messages = \App\Models\ContactMessage::where('email', $user->email)->latest()->paginate(10);
        return view('alumni.messages', compact('messages'));
    }

    public function index(Request $request)
    {
        $alumni = $this->alumniRepository->getPaginatedAlumni($request->all(), 12)->withQueryString();
        
        $majors = Major::orderBy('name')->get();
        $years = $this->alumniService->getCachedGraduationYears();

        return view('alumni.index', compact('alumni', 'majors', 'years'));
    }

    public function show($identifier)
    {
        $user = $this->alumniRepository->findByIdentifier($identifier);
        abort_if(!$user, 404);
        return view('alumni.show', compact('user'));
    }

    /**
     * Show the 3D Global Network Globe
     */
    public function network()
    {
        $mapAnalytics = $this->alumniService->getCachedMapAnalytics();
        return view('network.index', [
            'locations' => $mapAnalytics['alumniLocations'],
            'nationalCount' => $mapAnalytics['nationalCount'],
            'internationalCount' => $mapAnalytics['internationalCount']
        ]);
    }
}
