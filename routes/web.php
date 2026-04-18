<?php

use Illuminate\Support\Facades\Route;



use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\ChairmanController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AIController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProgramRegistrationController;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\PublicVerificationController;
use App\Services\AIPredictionService;

// --- 1. Global Public Routes (Rate Limited) ---
// GET /logout: Performs logout directly for users who visit the URL manually or when CSRF has expired
Route::get('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login')->with('success', 'Anda berhasil keluar.');
});

Route::middleware(['throttle:global'])->group(function () {
    
    // Public Home
    Route::get('/', function (\App\Services\AlumniService $alumniService) {
        $data = $alumniService->getWelcomeData();
        return view('welcome', $data);
    })->name('home');

    // Official Digital Verification (Premium)
    Route::get('/v/{token}', [PublicVerificationController::class, 'verify'])->name('public.verification');

    Route::get('/profil', function () { return view('profil'); })->name('public.profile');

    // Leaderboard
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
    Route::get('/kontak', function () { return view('kontak'); })->name('kontak');
    Route::post('/kontak/pesan', [ContactMessageController::class, 'store'])->name('kontak.pesan');

    // Programs Public
    Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('/programs/{slug}', [ProgramController::class, 'show'])->name('programs.show');

    // Jobs Public
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{slug}', [JobController::class, 'show'])->name('jobs.show');

    // News Public
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
    Route::get('/jejak-sukses/{successStory}', [\App\Http\Controllers\AlumniController::class, 'successStoryDetail'])->name('success-stories.show');

    // Advertisement Click Tracker
    Route::get('/ads/click/{id}', function ($id) {
        $ad = \App\Models\Ad::findOrFail($id);
        $ad->increment('click');
        return redirect($ad->link ?: '/');
    })->name('ads.click');

    // --- Public Content (Articles, Programs, Jobs) ---
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    // Global Network & Mesh Map (Leaflet)
    Route::get('/global-network', [MapController::class, 'index'])->name('global.network');
    Route::get('/api/v1/map-data', [MapController::class, 'data'])->name('api.map.data');
    Route::get('/api/v1/map-ai-insight', [MapController::class, 'aiInsight'])->name('api.map.ai');
});

// --- 2. Authentication Routes (Stricter Rate Limiting) ---
Route::post('/api/v1/guardian/log-error', [\App\Http\Controllers\GuardianController::class, 'logError'])->name('guardian.log');

Route::middleware(['throttle:30,1'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Password Reset Routes (Moved for better stability)
Route::get('password/reset', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [PasswordResetController::class, 'reset'])->name('password.update');

// Magic QR Login Route
Route::get('/auth/qr-login/{token}', [AuthController::class, 'qrLogin'])->name('auth.qr-login');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Social Login
Route::get('/auth/{provider}/redirect', [App\Http\Controllers\SocialController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [App\Http\Controllers\SocialController::class, 'callback'])->name('social.callback');

// --- 3. Authenticated Routes ---
Route::middleware(['auth', 'verified_alumni', 'throttle:global'])->group(function () {
    
    Route::get('/pending-notice', function () {
        if (auth()->check() && auth()->user()->status !== 'pending') {
            return redirect(auth()->user()->dashboardUrl());
        }
        return view('auth.pending');
    })->name('pending.notice');

        // Alumni Directory & Profiles
        Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
        
        // Alumni-Only Features (Specific paths first)
        Route::middleware(['alumni'])->group(function () {
            Route::get('/alumni/dashboard', [AlumniController::class, 'dashboard'])->name('alumni.dashboard');
            Route::get('/alumni/mentor', [\App\Http\Controllers\MentorController::class, 'index'])->name('alumni.mentor.index');
            Route::post('/alumni/mentor/find', [\App\Http\Controllers\MentorController::class, 'find'])->name('alumni.mentor.find');
            Route::get('/alumni/networking/recommendations', [\App\Http\Controllers\NetworkingController::class, 'getRecommendations'])->name('alumni.networking.recommendations');
            Route::get('/alumni/networking/nearby', [\App\Http\Controllers\NetworkingController::class, 'nearby'])->name('alumni.networking.nearby');
            Route::get('/alumni/network', [AlumniController::class, 'network'])->name('alumni.network');
            Route::get('/alumni/messages', [AlumniController::class, 'messages'])->name('alumni.messages');
            Route::get('/api/dashboard/ai-data', [\App\Http\Controllers\Api\DashboardApiController::class, 'getAIData'])->name('dashboard.ai.data');
            Route::get('/alumni/card', [CardController::class, 'index'])->name('alumni.card');
            
            // Programs Registration (Alumni)
            Route::post('/programs/{program}/register', [ProgramRegistrationController::class, 'store'])->name('programs.register');
            
            // Health AI Features
            Route::get('/alumni/health', [\App\Http\Controllers\HealthController::class, 'index'])->name('alumni.health.index');
            Route::post('/alumni/health/lifestyle', [\App\Http\Controllers\HealthController::class, 'updateLifestyle'])->name('alumni.health.lifestyle');
            Route::post('/alumni/health/symptoms', [\App\Http\Controllers\HealthController::class, 'checkSymptoms'])->name('alumni.health.symptoms');
            Route::post('/alumni/health/chat', [\App\Http\Controllers\HealthController::class, 'chat'])->name('alumni.health.chat');
        });

        // Business Directory Routes - Secured by standard Role Middleware
        Route::middleware(['role:admin,editor,alumni'])->group(function () {
            Route::get('/alumni/business', [\App\Http\Controllers\BusinessController::class, 'index'])->name('alumni.business.index');
            Route::get('/alumni/business/create', [\App\Http\Controllers\BusinessController::class, 'create'])->name('alumni.business.create');
            Route::post('/alumni/business', [\App\Http\Controllers\BusinessController::class, 'store'])->name('alumni.business.store');
            Route::get('/alumni/business/{business}', [\App\Http\Controllers\BusinessController::class, 'show'])->name('alumni.business.show');
            Route::get('/alumni/business/{business}/edit', [\App\Http\Controllers\BusinessController::class, 'edit'])->name('alumni.business.edit');
            Route::put('/alumni/business/{business}', [\App\Http\Controllers\BusinessController::class, 'update'])->name('alumni.business.update');
            Route::delete('/alumni/business/{business}', [\App\Http\Controllers\BusinessController::class, 'destroy'])->name('alumni.business.destroy');
            Route::delete('/alumni/business-photo/{photo}', [\App\Http\Controllers\BusinessController::class, 'deletePhoto'])->name('alumni.business.photo.delete');
        });

        // Shared Profile
        Route::get('/alumni/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/alumni/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Wildcard Profile Route (MUST BE LAST in the /alumni/ group)
        Route::get('/alumni/{user}', [AlumniController::class, 'show'])->name('alumni.show');

        // Alumni Social Feed (New High Performance System)
        Route::get('/feed', [\App\Http\Controllers\FeedController::class, 'index'])->name('feed.index');
        Route::post('/feed/post', [\App\Http\Controllers\FeedController::class, 'store'])->name('feed.store');
        Route::post('/feed/follow/{user}', [\App\Http\Controllers\FeedController::class, 'toggleFollow'])->name('feed.follow');
        
        // Alumni Stories
        Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
        Route::post('/stories/note', [StoryController::class, 'storeNote'])->name('stories.note');
        Route::get('/api/stories/active', [StoryController::class, 'getActiveStories'])->name('api.stories.active');
        Route::get('/api/stories/{story}', [\App\Http\Controllers\StoryController::class, 'show'])->name('api.stories.show');

        // Nostalgia Feed Routes (Legacy Support)
        Route::get('/nostalgia', [\App\Http\Controllers\PostController::class, 'index'])->name('nostalgia.index');
        Route::post('/nostalgia', [\App\Http\Controllers\PostController::class, 'store'])->name('nostalgia.store');
        Route::delete('/nostalgia/{post}', [\App\Http\Controllers\PostController::class, 'destroy'])->name('nostalgia.destroy');
        Route::post('/nostalgia/{post}/like', [\App\Http\Controllers\PostController::class, 'toggleLike'])->name('nostalgia.like');
        Route::post('/nostalgia/{post}/comment', [\App\Http\Controllers\PostController::class, 'storeComment'])->name('nostalgia.comment.store');
        Route::get('/api/alumni/search', [\App\Http\Controllers\PostController::class, 'searchAlumni'])->name('api.alumni.search');



    // Mentoring
    Route::get('/mentors', function() {
        $mentors = \App\Models\User::where('mentoring', true)->latest()->get();
        return view('mentors.index', compact('mentors'));
    })->name('mentors.index');


    
    // Forums
    Route::get('/forums', [\App\Http\Controllers\ForumController::class, 'index'])->name('forums.index');
    Route::post('/forums', [\App\Http\Controllers\ForumController::class, 'store'])->name('forums.store');
    Route::get('/forums/{forum}', [\App\Http\Controllers\ForumController::class, 'show'])->name('forums.show');
    Route::post('/forums/{forum}/comments', [\App\Http\Controllers\ForumController::class, 'storeComment'])->name('forums.comments.store');

    // Admin & Editor Panel
    Route::middleware(['role:admin,editor'])->group(function () {
        Route::get('/admin', function() {
            return redirect()->route('admin.dashboard');
        });
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        
        // User Management (Admin & Editor)
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::match(['PUT', 'PATCH'], '/admin/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
        Route::match(['PUT', 'PATCH'], '/admin/users/{user}/status', [UserController::class, 'updateStatus'])->name('admin.users.updateStatus');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::get('/admin/users/verification', [UserController::class, 'verification'])->name('admin.users.verification');
        Route::resource('/admin/success-stories', \App\Http\Controllers\Admin\SuccessStoryController::class)->except(['show'])->names('admin.success-stories');

            Route::get('/admin/export', [\App\Http\Controllers\Admin\AlumniExportController::class, 'export'])->name('admin.export');


        // News Management
        Route::get('/admin/news', [NewsController::class, 'adminIndex'])->name('admin.news.index');
        Route::get('/admin/news/create', [NewsController::class, 'create'])->name('admin.news.create');
        Route::post('/admin/news', [NewsController::class, 'store'])->name('admin.news.store');
        Route::get('/admin/news/{news}/edit', [NewsController::class, 'edit'])->name('admin.news.edit');
        Route::put('/admin/news/{news}', [NewsController::class, 'update'])->name('admin.news.update');
        Route::delete('/admin/news/{news}', [NewsController::class, 'destroy'])->name('admin.news.destroy');
        Route::post('/admin/news/{news}/toggle', [NewsController::class, 'togglePublish'])->name('admin.news.toggle');

        // Ads Management
        Route::get('/admin/ads', [AdController::class, 'index'])->name('admin.ads.index');
        Route::get('/admin/ads/create', [AdController::class, 'create'])->name('admin.ads.create');
        Route::post('/admin/ads', [AdController::class, 'store'])->name('admin.ads.store');
        Route::get('/admin/ads/{ad}', [AdController::class, 'show'])->name('admin.ads.show');
        Route::get('/admin/ads/{ad}/edit', [AdController::class, 'edit'])->name('admin.ads.edit');
        Route::put('/admin/ads/{ad}', [AdController::class, 'update'])->name('admin.ads.update');
        Route::delete('/admin/ads/{ad}', [AdController::class, 'destroy'])->name('admin.ads.destroy');

        // Gallery Management
        Route::get('/admin/gallery', [GalleryController::class, 'adminIndex'])->name('admin.gallery.index');
        Route::post('/admin/gallery', [GalleryController::class, 'store'])->name('admin.gallery.store');
        Route::delete('/admin/gallery/{gallery}', [GalleryController::class, 'destroy'])->name('admin.gallery.destroy');
        Route::get('/admin/gallery/{gallery}/edit', [GalleryController::class, 'edit'])->name('admin.gallery.edit');
        Route::put('/admin/gallery/{gallery}', [GalleryController::class, 'update'])->name('admin.gallery.update');

        // Health Dashboard (Admin)
        Route::get('/admin/health-trends', [\App\Http\Controllers\Admin\HealthDashboardController::class, 'index'])->name('admin.health.trends');

        // CMS / Settings (Admin & Editor)
        Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/admin/settings/store', [SettingController::class, 'store'])->name('admin.settings.store');
        Route::put('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
        Route::get('/admin/contact', [SettingController::class, 'contact'])->name('admin.contact.index');

        // Messages / Inbox
        Route::get('/admin/messages', [ContactMessageController::class, 'index'])->name('admin.messages.index');
        Route::get('/admin/messages/{id}', [ContactMessageController::class, 'show'])->name('admin.messages.show');
        Route::post('/admin/messages/{id}/reply', [ContactMessageController::class, 'reply'])->name('admin.messages.reply');
        Route::delete('/admin/messages/{id}', [ContactMessageController::class, 'destroy'])->name('admin.messages.destroy');

        Route::get('/admin/hero/edit', [HeroController::class, 'edit'])->name('admin.hero.edit');
        Route::put('/admin/hero', [HeroController::class, 'update'])->name('admin.hero.update');

        Route::get('/admin/chairman/edit', [ChairmanController::class, 'edit'])->name('admin.chairman.edit');
        Route::put('/admin/chairman/update', [ChairmanController::class, 'update'])->name('admin.chairman.update');

        // Admin Business Moderation (Admin & Editor)
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/businesses', [\App\Http\Controllers\Admin\BusinessController::class, 'index'])->name('business.index');
            Route::post('/businesses/{business}/approve', [\App\Http\Controllers\Admin\BusinessController::class, 'approve'])->name('business.approve');
            Route::post('/businesses/{business}/reject', [\App\Http\Controllers\Admin\BusinessController::class, 'reject'])->name('business.reject');
        });

        // Programs Management
        Route::get('/admin/programs', [ProgramController::class, 'adminIndex'])->name('admin.programs.index');
        Route::get('/admin/programs/create', [ProgramController::class, 'create'])->name('admin.programs.create');
        Route::post('/admin/programs', [ProgramController::class, 'store'])->name('admin.programs.store');
        Route::get('/admin/programs/{program}/edit', [ProgramController::class, 'edit'])->name('admin.programs.edit');
        Route::put('/admin/programs/{program}', [ProgramController::class, 'update'])->name('admin.programs.update');
        Route::delete('/admin/programs/{program}', [ProgramController::class, 'destroy'])->name('admin.programs.destroy');

        // Programs Registrations Management
        Route::get('/admin/registrations', [ProgramRegistrationController::class, 'adminIndex'])->name('admin.registrations.index');
        Route::put('/admin/registrations/{registration}', [ProgramRegistrationController::class, 'updateStatus'])->name('admin.registrations.update');
        Route::delete('/admin/registrations/{registration}', [ProgramRegistrationController::class, 'destroy'])->name('admin.registrations.destroy');
        // Jobs Management
        Route::get('/admin/jobs', [JobController::class, 'adminIndex'])->name('admin.jobs.index');
        Route::get('/admin/jobs/create', [JobController::class, 'create'])->name('admin.jobs.create');
        Route::post('/admin/jobs', [JobController::class, 'store'])->name('admin.jobs.store');
        Route::get('/admin/jobs/{vacancy}/edit', [JobController::class, 'edit'])->name('admin.jobs.edit');
        Route::put('/admin/jobs/{vacancy}', [JobController::class, 'update'])->name('admin.jobs.update');
        Route::delete('/admin/jobs/{vacancy}', [JobController::class, 'destroy'])->name('admin.jobs.destroy');
        // Major Management
        Route::get('/admin/majors', [MajorController::class, 'index'])->name('admin.majors.index');
        Route::post('/admin/majors', [MajorController::class, 'store'])->name('admin.majors.store');
        Route::put('/admin/majors/{major}', [MajorController::class, 'update'])->name('admin.majors.update');
        Route::delete('/admin/majors/{major}', [MajorController::class, 'destroy'])->name('admin.majors.destroy');


        // AI Control Panel Routes (Admin & Editor)
        Route::get('/admin/ai', [AIController::class, 'dashboard'])->name('admin.ai.dashboard');
        Route::post('/admin/ai/generate', [AIController::class, 'generateNow'])->name('admin.ai.generate');
        Route::post('/admin/ai/publish/{news}', [AIController::class, 'publish'])->name('admin.ai.publish');

        // SYSTEM LOGS (Admin & Editor Debug Tool)
        Route::get('/admin/system/logs', [\App\Http\Controllers\Admin\SystemController::class, 'logs'])->name('admin.system.logs');
        Route::post('/admin/system/logs/clear', [\App\Http\Controllers\Admin\SystemController::class, 'clearLogs'])->name('admin.system.logs.clear');

        // NEW: QR Scanner & Point System
        Route::get('/admin/scanner', [\App\Http\Controllers\Admin\ScannerController::class, 'index'])->name('admin.scanner');
        Route::post('/admin/scanner/verify', [\App\Http\Controllers\Admin\ScannerController::class, 'verify'])->name('admin.scanner.verify');
        Route::post('/admin/scanner/award', [\App\Http\Controllers\Admin\ScannerController::class, 'awardPoints'])->name('admin.scanner.award');
    });
});

// Media Proxy (Fix for Nginx Volume Sync)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!\Illuminate\Support\Facades\File::exists($fullPath)) {
        abort(404);
    }
    $file = \Illuminate\Support\Facades\File::get($fullPath);
    $type = \Illuminate\Support\Facades\File::mimeType($fullPath);
    $response = \Illuminate\Support\Facades\Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
})->where('path', '.*');



// Health Check Endpoint
Route::get('/health', function () {
    $status = [
        'status' => 'healthy',
        'timestamp' => now()->toIso8601String(),
        'services' => [
            'database' => 'down',
            'redis' => 'down',
        ],
    ];

    try {
        \DB::connection()->getPdo();
        $status['services']['database'] = 'up';
    } catch (\Exception $e) {
        $status['status'] = 'unhealthy';
    }

    try {
        \Illuminate\Support\Facades\Redis::connection()->ping();
        $status['services']['redis'] = 'up';
    } catch (\Exception $e) {
        $status['services']['redis'] = 'down';
        if (config('database.redis.default.host') !== '127.0.0.1') {
             $status['status'] = 'unhealthy';
        }
    }

    return response()->json($status, $status['status'] === 'healthy' ? 200 : 503);
});

// --- AI Chat Assistant API ---
Route::post('/api/ai/chat', [AIChatController::class, 'ask'])->middleware('throttle:global');
