<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Major;
use App\Models\News;
use App\Services\AIPredictionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\AlumniService;
use App\Services\PrivacyService;

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
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login');
            }

            $dashboardData = $this->alumniService->getDashboardData($user);

            return view('alumni.dashboard', array_merge(['user' => $user], $dashboardData));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard Mega Error: ' . $e->getMessage());
            return view('alumni.dashboard', $this->alumniService->getDashboardFallbackData());
        }
    }

    /**
     * Show Public Success Story Detail
     */
    public function successStoryDetail(\App\Models\SuccessStory $successStory)
    {
        if (!$successStory->is_published) {
            abort(404);
        }
        
        return view('success_stories.show', compact('successStory'));
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
        
        // Apply masking for privacy (unless admin)
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            $alumni->getCollection()->transform(function($user) {
                $user->email = PrivacyService::maskEmail($user->email);
                $user->phone_number = PrivacyService::maskPhone($user->phone_number);
                return $user;
            });
        }

        $majors = \Illuminate\Support\Facades\Cache::remember('active_majors_list', 3600, function() {
            return Major::where('status', 'active')->orderBy('name')->get();
        }); 
        $years = $this->alumniService->getCachedGraduationYears();

        return view('alumni.index', compact('alumni', 'majors', 'years'));
    }

    public function show($identifier)
    {
        $user = $this->alumniRepository->findByIdentifier($identifier);
        abort_if(!$user, 404);

        // Apply masking for privacy (unless admin)
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            $user->email = PrivacyService::maskEmail($user->email);
            $user->phone_number = PrivacyService::maskPhone($user->phone_number);
        }

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
