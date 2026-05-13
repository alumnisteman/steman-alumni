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
     * Interactive Digital Yearbook (GSAP + PageFlip)
     */
    public function yearbook(Request $request)
    {
        $selectedYear = $request->input('year');

        // Ambil semua tahun angkatan yang tersedia
        $years = \Illuminate\Support\Facades\Cache::remember('yearbook_years_list', 1800, function () {
            return User::where('role', 'alumni')
                ->whereNotNull('graduation_year')
                ->whereIn('status', ['active', 'approved'])
                ->distinct()
                ->orderByDesc('graduation_year')
                ->pluck('graduation_year');
        });

        // Ambil alumni berdasarkan tahun yang dipilih (atau tahun terbaru jika belum dipilih)
        $activeYear = $selectedYear ?? ($years->isNotEmpty() ? $years->first() : null);

        $alumni = collect();
        if ($activeYear) {
            $alumni = \Illuminate\Support\Facades\Cache::remember("yearbook_alumni_{$activeYear}", 1800, function () use ($activeYear) {
                return User::where('role', 'alumni')
                    ->whereIn('status', ['active', 'approved'])
                    ->where('graduation_year', $activeYear)
                    ->select(['id', 'name', 'major', 'graduation_year', 'profile_picture', 'bio', 'current_job', 'company_university'])
                    ->orderBy('name')
                    ->get();
            });

            $posts = \Illuminate\Support\Facades\Cache::remember("yearbook_posts_{$activeYear}", 1800, function () use ($activeYear) {
                return \App\Models\Post::with(['user' => function($q) {
                        $q->select('id', 'name', 'profile_picture');
                    }])
                    ->whereHas('user', function($q) use ($activeYear) {
                        $q->where('role', 'alumni')
                          ->whereIn('status', ['active', 'approved'])
                          ->where('graduation_year', $activeYear);
                    })
                    ->latest()
                    ->take(10) // Ambil 10 cerita terbaik/terbaru
                    ->get();
            });
        }

        return view('alumni.yearbook', compact('years', 'alumni', 'posts', 'activeYear'));
    }

    /**
     * Store Yearbook Message
     */
    public function storeYearbookMessage(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048'
        ]);

        $user = auth()->user();

        if ($user->role !== 'alumni' || !in_array($user->status, ['active', 'approved']) || !$user->graduation_year) {
            return redirect()->back()->with('error', 'Hanya alumni terverifikasi dengan tahun kelulusan yang dapat menulis di Buku Kenangan.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('yearbook_images', 'public');
            $imagePath = '/storage/' . $imagePath;
        }

        \App\Models\Post::create([
            'user_id' => $user->id,
            'content' => $request->content,
            'image_url' => $imagePath,
            'type' => 'memory',
            'visibility' => 'public',
            'is_anonymous' => false
        ]);

        \Illuminate\Support\Facades\Cache::forget("yearbook_posts_{$user->graduation_year}");

        return redirect()->route('alumni.yearbook', ['year' => $user->graduation_year])
            ->with('success', 'Pesan Buku Kenangan berhasil ditambahkan!');
    }

    /**
     * Update Yearbook Message
     */
    public function updateYearbookMessage(Request $request, $id)
    {
        $post = \App\Models\Post::findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak diizinkan mengubah kenangan ini.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post->update([
            'content' => $request->content,
        ]);

        $user = auth()->user();
        \Illuminate\Support\Facades\Cache::forget("yearbook_posts_{$user->graduation_year}");

        return redirect()->route('alumni.yearbook', ['year' => $user->graduation_year])
            ->with('success', 'Kenangan berhasil diperbarui!');
    }

    /**
     * Destroy Yearbook Message
     */
    public function destroyYearbookMessage($id)
    {
        $post = \App\Models\Post::findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak diizinkan menghapus kenangan ini.');
        }

        $post->delete();

        $user = auth()->user();
        \Illuminate\Support\Facades\Cache::forget("yearbook_posts_{$user->graduation_year}");

        return redirect()->route('alumni.yearbook', ['year' => $user->graduation_year])
            ->with('success', 'Kenangan berhasil dihapus.');
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
        
        // 1. Data untuk Leaderboard Wilayah (Top Regions)
        $topRegions = User::where('role', 'alumni')
            ->whereNotNull('city_name')
            ->select('city_name', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('city_name')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // 2. Data untuk Live Activity (Active in last 15 mins)
        $liveActivities = User::where('role', 'alumni')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('last_active_at', '>=', now()->subMinutes(15))
            ->get(['id', 'name', 'latitude', 'longitude', 'profile_picture']);

        // 3. Data untuk Heatmap (Aggregated by city/coords)
        $heatmapData = User::where('role', 'alumni')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('latitude', 'longitude', \Illuminate\Support\Facades\DB::raw('count(*) as weight'))
            ->groupBy('latitude', 'longitude')
            ->get();

        // 4. Data untuk Job Satellites (Active Vacancies)
        $jobVacancies = \App\Models\JobVacancy::where('status', 'active')
            ->with(['user' => fn($q) => $q->select('id', 'name', 'latitude', 'longitude')])
            ->latest()
            ->take(10)
            ->get()
            ->filter(fn($job) => $job->user && $job->user->latitude);

        // 5. Data untuk Major Constellations (Lines within same major)
        $majorConstellations = User::where('role', 'alumni')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'name', 'major', 'latitude', 'longitude'])
            ->groupBy('major')
            ->filter(fn($group) => $group->count() > 1);

        // 6. Data untuk Time Capsules (Memory Posts)
        $timeCapsules = \App\Models\Post::where('type', 'memory')
            ->with(['user' => fn($q) => $q->select('id', 'name', 'latitude', 'longitude')])
            ->latest()
            ->take(15)
            ->get()
            ->filter(fn($post) => $post->user && $post->user->latitude);

        // 7. Live Feed for Pulse Chat (Latest Public Posts)
        $liveFeed = \App\Models\Post::where('visibility', 'public')
            ->with(['user' => fn($q) => $q->select('id', 'name', 'latitude', 'longitude', 'profile_picture', 'city_name')])
            ->latest()
            ->take(10)
            ->get()
            ->filter(fn($post) => $post->user && $post->user->latitude);

        return view('network.index', [
            'locations' => $mapAnalytics['alumniLocations'],
            'nationalCount' => $mapAnalytics['nationalCount'],
            'internationalCount' => $mapAnalytics['internationalCount'],
            'topRegions' => $topRegions,
            'liveActivities' => $liveActivities,
            'heatmapData' => $heatmapData,
            'jobVacancies' => $jobVacancies,
            'majorConstellations' => $majorConstellations,
            'timeCapsules' => $timeCapsules,
            'liveFeed' => $liveFeed
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
