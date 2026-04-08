<?php

use Illuminate\Support\Facades\Route;

// FINAL DIAGNOSTIC ROUTES
Route::get('/ping-test', function() { return 'PONG'; });
Route::get('/nostalgia-isolated', [\App\Http\Controllers\PostController::class, 'index'])->name('nostalgia.isolated');

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
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MapController;
use App\Services\AIPredictionService;

// --- 1. Global Public Routes (Rate Limited) ---
Route::middleware(['throttle:global'])->group(function () {
    
    // Public Home
    Route::get('/', function (\App\Services\AlumniService $alumniService) {
        $data = $alumniService->getWelcomeData();
        return view('welcome', $data);
    })->name('home');

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

    // --- Public Content (Articles, Programs, Jobs) ---
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    // Global Network & Mesh Map (Leaflet)
    Route::get('/global-network', [MapController::class, 'index'])->name('global.network');
    Route::get('/api/v1/map-data', [MapController::class, 'data'])->name('api.map.data');
});

// --- 2. Authentication Routes (Stricter Rate Limiting) ---
Route::middleware(['throttle:5,1'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Social Login
Route::get('/auth/{provider}/redirect', [App\Http\Controllers\SocialController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [App\Http\Controllers\SocialController::class, 'callback'])->name('social.callback');

// --- 3. Authenticated Routes ---
Route::middleware(['auth', 'verified_alumni', 'throttle:global'])->group(function () {
    
    Route::get('/pending-notice', function () {
        if (auth()->check() && auth()->user()->status !== 'pending') {
            return redirect('/alumni/dashboard');
        }
        return view('auth.pending');
    })->name('pending.notice');

    // Alumni Features
    Route::middleware(['alumni'])->group(function () {
        Route::get('/alumni/dashboard', [AlumniController::class, 'dashboard'])->name('alumni.dashboard');
        Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
        Route::get('/alumni/network', [AlumniController::class, 'network'])->name('alumni.network');
        Route::get('/alumni/messages', [AlumniController::class, 'messages'])->name('alumni.messages');
        Route::get('/api/dashboard/ai-data', [\App\Http\Controllers\Api\DashboardApiController::class, 'getAIData'])->name('dashboard.ai.data');
        Route::get('/alumni/card', [CardController::class, 'index'])->name('alumni.card');

        // Nostalgia Feed Routes
        Route::get('/nostalgia', [\App\Http\Controllers\PostController::class, 'index'])->name('nostalgia.index');
        Route::post('/nostalgia', [\App\Http\Controllers\PostController::class, 'store'])->name('nostalgia.store');
        Route::delete('/nostalgia/{post}', [\App\Http\Controllers\PostController::class, 'destroy'])->name('nostalgia.destroy');
        Route::post('/nostalgia/{post}/like', [\App\Http\Controllers\PostController::class, 'toggleLike'])->name('nostalgia.like');
        Route::post('/nostalgia/{post}/comment', [\App\Http\Controllers\PostController::class, 'storeComment'])->name('nostalgia.comment.store');
        Route::get('/api/alumni/search', [\App\Http\Controllers\PostController::class, 'searchAlumni'])->name('api.alumni.search');
    });

    // Mentoring
    Route::get('/mentors', function() {
        $mentors = \App\Models\User::where('mentoring', true)->latest()->get();
        return view('mentors.index', compact('mentors'));
    })->name('mentors.index');

    // Shared Profile
    Route::get('/alumni/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/alumni/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Public wildcard alumni profile (moved here to prevent 404 on specific alumni paths above)
    Route::get('/alumni/{user}', [AlumniController::class, 'show'])->name('alumni.show');
    
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
        
        // User Management (Admin Only)
        Route::middleware(['role:admin'])->group(function() {
            Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
            Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
            Route::match(['PUT', 'PATCH'], '/admin/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
            Route::match(['PUT', 'PATCH'], '/admin/users/{user}/status', [UserController::class, 'updateStatus'])->name('admin.users.updateStatus');
            Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
            Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
            Route::get('/admin/users/verification', [UserController::class, 'verification'])->name('admin.users.verification');
            Route::resource('/admin/success-stories', \App\Http\Controllers\Admin\SuccessStoryController::class)->names('admin.success-stories');
        });

        // News Management
        Route::get('/admin/news', [NewsController::class, 'adminIndex'])->name('admin.news.index');
        Route::get('/admin/news/create', [NewsController::class, 'create'])->name('admin.news.create');
        Route::post('/admin/news', [NewsController::class, 'store'])->name('admin.news.store');
        Route::get('/admin/news/{news}/edit', [NewsController::class, 'edit'])->name('admin.news.edit');
        Route::put('/admin/news/{news}', [NewsController::class, 'update'])->name('admin.news.update');
        Route::delete('/admin/news/{news}', [NewsController::class, 'destroy'])->name('admin.news.destroy');
        Route::post('/admin/news/{news}/publish', [NewsController::class, 'quickPublish'])->name('admin.news.publish');

        // Gallery Management
        Route::get('/admin/gallery', [GalleryController::class, 'adminIndex'])->name('admin.gallery.index');
        Route::post('/admin/gallery', [GalleryController::class, 'store'])->name('admin.gallery.store');
        Route::delete('/admin/gallery/{gallery}', [GalleryController::class, 'destroy'])->name('admin.gallery.destroy');
        Route::get('/admin/gallery/{gallery}/edit', [GalleryController::class, 'edit'])->name('admin.gallery.edit');
        Route::put('/admin/gallery/{gallery}', [GalleryController::class, 'update'])->name('admin.gallery.update');

        // CMS / Settings (Admin Only)
        Route::middleware(['role:admin'])->group(function() {
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
        });

        // Programs Management
        Route::get('/admin/programs', [ProgramController::class, 'adminIndex'])->name('admin.programs.index');
        Route::get('/admin/programs/create', [ProgramController::class, 'create'])->name('admin.programs.create');
        Route::post('/admin/programs', [ProgramController::class, 'store'])->name('admin.programs.store');
        Route::get('/admin/programs/{program}/edit', [ProgramController::class, 'edit'])->name('admin.programs.edit');
        Route::put('/admin/programs/{program}', [ProgramController::class, 'update'])->name('admin.programs.update');
        Route::delete('/admin/programs/{program}', [ProgramController::class, 'destroy'])->name('admin.programs.destroy');
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

        Route::get('/admin/export', [\App\Http\Controllers\Admin\AlumniExportController::class, 'export'])->name('admin.export');

        // AI Control Panel Routes
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/admin/ai', [AIController::class, 'dashboard'])->name('admin.ai.dashboard');
            Route::post('/admin/ai/generate', [AIController::class, 'generateNow'])->name('admin.ai.generate');
            Route::post('/admin/ai/publish/{news}', [AIController::class, 'publish'])->name('admin.ai.publish');
        });
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
