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
            $onlineCount = $this->alumniService->getOnlineAlumniCount();

            $view = view('alumni.dashboard', array_merge(['user' => $user, 'onlineCount' => $onlineCount], $dashboardData))->render();
            return response($view);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard Mega Error: ' . $e->getMessage());
            try {
                $view = view('alumni.dashboard', $this->alumniService->getDashboardFallbackData())->render();
                return response($view);
            } catch (\Throwable $e2) {
                \Illuminate\Support\Facades\Log::error('Dashboard Fallback Error: ' . $e2->getMessage());
                return response()->view('errors.500', [], 500);
            }
        }
    }

    /**
     * Show Public Success Stories Listing
     */
    public function successStories()
    {
        $stories = \App\Models\SuccessStory::where('is_published', true)->orderBy('order')->paginate(12);
        return view('success_stories.index', compact('stories'));
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
        try {
            $alumni = $this->alumniRepository->getPaginatedAlumni($request->all(), 12, ['stories'])->withQueryString();
            
            // Apply masking for privacy (unless admin)
            if (!auth()->user() || auth()->user()->role !== 'admin') {
                $alumni->getCollection()->transform(function($user) {
                    $user->email = PrivacyService::maskEmail($user->email);
                    $user->phone_number = PrivacyService::maskPhone($user->phone_number);
                    return $user;
                });
            }

            $majors = \Illuminate\Support\Facades\Cache::remember('active_majors_list', 3600, function() {
                try {
                    return Major::where('status', 'active')->orderBy('name')->get();
                } catch (\Throwable $e) {
                    return Major::orderBy('name')->get(); // Fallback if status missing
                }
            }); 
            $years = $this->alumniService->getCachedGraduationYears();

            $view = view('alumni.index', compact('alumni', 'majors', 'years'))->render();
            return response($view);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Alumni Index Error: ' . $e->getMessage());
            try {
                $alumni = User::where('role', 'alumni')->latest()->paginate(12);
                $majors = collect();
                $years = collect();
                $view = view('alumni.index', compact('alumni', 'majors', 'years'))->render();
                return response($view);
            } catch (\Throwable $e2) {
                \Illuminate\Support\Facades\Log::error('Alumni Index Fallback Error: ' . $e2->getMessage());
                return response()->view('errors.500', [], 500);
            }
        }
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
    /**
     * Get unread notifications for the current user
     */
    public function getNotifications()
    {
        $notifications = auth()->user()->unreadNotifications()->latest()->take(10)->get();
        return response()->json($notifications);
    }

    /**
     * Mark all notifications as read
     */
    public function markNotificationsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}
