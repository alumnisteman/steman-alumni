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
use App\Http\Controllers\DonationController;
use App\Http\Controllers\TicketController;
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
use App\Http\Controllers\StoryController;
use App\Services\AIPredictionService;

// --- 1. Global Public Routes (Rate Limited via bootstrap/app.php) ---
Route::get('/img-opt/{path}', [\App\Http\Controllers\ImageOptimizerController::class, 'optimize'])
    ->where('path', '.*')
    ->name('image.optimize');

// GET /logout: Performs logout directly for users who visit the URL manually or when CSRF has expired
Route::get('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login')->with('success', 'Anda berhasil keluar.');
});


// --- 4. ADMIN SUBDOMAIN (admin.alumni-steman.my.id) ---
    Route::domain('admin.alumni-steman.my.id')->group(function () {
        Route::middleware(['role:admin,editor'])->group(function () {
            Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
            Route::get('/dashboard', [AdminDashboardController::class, 'index']);
            
            // User Management
            Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
            Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
            Route::match(['PUT', 'PATCH'], '/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
            Route::match(['PUT', 'PATCH'], '/users/{user}/status', [UserController::class, 'updateStatus'])->name('admin.users.updateStatus');
            Route::match(['PUT', 'PATCH'], '/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggleActive');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
            Route::get('/users/verification', [UserController::class, 'verification'])->name('admin.users.verification');
            Route::resource('/success-stories', \App\Http\Controllers\Admin\SuccessStoryController::class)->except(['show'])->names('admin.success-stories');
            Route::get('/export', [\App\Http\Controllers\Admin\AlumniExportController::class, 'export'])->name('admin.export');

            // News Management
            Route::get('/news', [NewsController::class, 'adminIndex'])->name('admin.news.index');
            Route::get('/news/create', [NewsController::class, 'create'])->name('admin.news.create');
            Route::post('/news', [NewsController::class, 'store'])->name('admin.news.store');
            Route::get('/news/{news}/edit', [NewsController::class, 'edit'])->name('admin.news.edit');
            Route::put('/news/{news}', [NewsController::class, 'update'])->name('admin.news.update');
            Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('admin.news.destroy');
            Route::post('/news/{news}/toggle', [NewsController::class, 'togglePublish'])->name('admin.news.toggle');

            // Ads Management
            Route::get('/ads', [AdController::class, 'index'])->name('admin.ads.index');
            Route::get('/ads/create', [AdController::class, 'create'])->name('admin.ads.create');
            Route::post('/ads', [AdController::class, 'store'])->name('admin.ads.store');
            Route::get('/ads/{ad}', [AdController::class, 'show'])->name('admin.ads.show');
            Route::get('/ads/{ad}/edit', [AdController::class, 'edit'])->name('admin.ads.edit');
            Route::put('/ads/{ad}', [AdController::class, 'update'])->name('admin.ads.update');
            Route::delete('/ads/{ad}', [AdController::class, 'destroy'])->name('admin.ads.destroy');

            // Gallery Management
            Route::get('/gallery', [GalleryController::class, 'adminIndex'])->name('admin.gallery.index');
            Route::post('/gallery', [GalleryController::class, 'store'])->name('admin.gallery.store');
            Route::delete('/gallery/{gallery}', [GalleryController::class, 'destroy'])->name('admin.gallery.destroy');
            Route::get('/gallery/{gallery}/edit', [GalleryController::class, 'edit'])->name('admin.gallery.edit');
            Route::put('/gallery/{gallery}', [GalleryController::class, 'update'])->name('admin.gallery.update');

            // Job Vacancy Management
            Route::get('/jobs', [JobController::class, 'adminIndex'])->name('admin.jobs.index');
            Route::get('/jobs/create', [JobController::class, 'create'])->name('admin.jobs.create');
            Route::post('/jobs', [JobController::class, 'store'])->name('admin.jobs.store');
            Route::get('/jobs/{vacancy}/edit', [JobController::class, 'edit'])->name('admin.jobs.edit');
            Route::put('/jobs/{vacancy}', [JobController::class, 'update'])->name('admin.jobs.update');
            Route::delete('/jobs/{vacancy}', [JobController::class, 'destroy'])->name('admin.jobs.destroy');

            // Program Management
            Route::get('/programs', [ProgramController::class, 'adminIndex'])->name('admin.programs.index');
            Route::get('/programs/create', [ProgramController::class, 'create'])->name('admin.programs.create');
            Route::post('/programs', [ProgramController::class, 'store'])->name('admin.programs.store');
            Route::get('/programs/{program}/edit', [ProgramController::class, 'edit'])->name('admin.programs.edit');
            Route::put('/programs/{program}', [ProgramController::class, 'update'])->name('admin.programs.update');
            Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])->name('admin.programs.destroy');
            Route::get('/programs/registrations', [ProgramRegistrationController::class, 'adminIndex'])->name('admin.registrations.index');
            Route::patch('/programs/registrations/{registration}', [ProgramRegistrationController::class, 'updateStatus'])->name('admin.registrations.updateStatus');
            Route::delete('/programs/registrations/{registration}', [ProgramRegistrationController::class, 'destroy'])->name('admin.registrations.destroy');

            // Settings & CMS
            Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
            Route::post('/settings/store', [SettingController::class, 'store'])->name('admin.settings.store');
            Route::put('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
            Route::get('/contact', [SettingController::class, 'contact'])->name('admin.contact.index');

            // Messages / Inbox
            Route::get('/messages', [ContactMessageController::class, 'index'])->name('admin.messages.index');
            Route::get('/messages/{id}', [ContactMessageController::class, 'show'])->name('admin.messages.show');
            Route::post('/messages/{id}/reply', [ContactMessageController::class, 'reply'])->name('admin.messages.reply');
            Route::delete('/messages/{id}', [ContactMessageController::class, 'destroy'])->name('admin.messages.destroy');

            Route::get('/hero/edit', [HeroController::class, 'edit'])->name('admin.hero.edit');
            Route::put('/hero', [HeroController::class, 'update'])->name('admin.hero.update');
            Route::get('/chairman/edit', [ChairmanController::class, 'edit'])->name('admin.chairman.edit');
            Route::put('/chairman/update', [ChairmanController::class, 'update'])->name('admin.chairman.update');

            // Moderation & Additional Management
            Route::get('/businesses', [\App\Http\Controllers\Admin\BusinessController::class, 'index'])->name('admin.business.index');
            Route::post('/businesses/{business}/approve', [\App\Http\Controllers\Admin\BusinessController::class, 'approve'])->name('admin.business.approve');
            Route::post('/businesses/{business}/reject', [\App\Http\Controllers\Admin\BusinessController::class, 'reject'])->name('admin.business.reject');
            Route::get('/majors', [MajorController::class, 'index'])->name('admin.majors.index');
            Route::post('/majors', [MajorController::class, 'store'])->name('admin.majors.store');
            Route::put('/majors/{major}', [MajorController::class, 'update'])->name('admin.majors.update');
            Route::delete('/majors/{major}', [MajorController::class, 'destroy'])->name('admin.majors.destroy');

            // AI & Scanner
            Route::get('/ai', [AIController::class, 'dashboard'])->name('admin.ai.dashboard');
            Route::post('/ai/generate', [AIController::class, 'generateNow'])->name('admin.ai.generate');
            Route::post('/ai/publish/{news}', [AIController::class, 'publish'])->name('admin.ai.publish');
            Route::get('/scanner', [\App\Http\Controllers\Admin\ScannerController::class, 'index'])->name('admin.scanner');
            Route::post('/scanner/verify', [\App\Http\Controllers\Admin\ScannerController::class, 'verify'])->name('admin.scanner.verify');
            Route::post('/scanner/award', [\App\Http\Controllers\Admin\ScannerController::class, 'awardPoints'])->name('admin.scanner.award');
            Route::get('/system/logs', [\App\Http\Controllers\Admin\SystemController::class, 'logs'])->name('admin.system.logs');
            Route::post('/system/logs/clear', [\App\Http\Controllers\Admin\SystemController::class, 'clearLogs'])->name('admin.system.logs.clear');
            Route::get('/system/pulse', [\App\Http\Controllers\Admin\SystemController::class, 'pulse'])->name('admin.system.pulse');
            Route::get('/api/system/health', [\App\Http\Controllers\Admin\SystemController::class, 'healthApi'])->name('admin.api.system.health');
            Route::get('/health/trends', [\App\Http\Controllers\Admin\HealthDashboardController::class, 'index'])->name('admin.health.trends');

            // System Guard — Realtime Monitoring & Auto-Healing Dashboard
            Route::get('/system/guard', [\App\Http\Controllers\Admin\SystemGuardController::class, 'dashboard'])->name('admin.guard.dashboard');
            Route::get('/system/guard/status', [\App\Http\Controllers\Admin\SystemGuardController::class, 'status'])->name('admin.guard.status');
            Route::post('/system/guard/maintenance', [\App\Http\Controllers\Admin\SystemGuardController::class, 'maintenance'])->name('admin.guard.maintenance');

            // Donation & Fund Management
            Route::get('/donations', [DonationController::class, 'adminIndex'])->name('admin.donations.index');
            Route::post('/donations/{donation}/verify', [DonationController::class, 'verify'])->name('admin.donations.verify');
            
            // Campaign Management (Funds)
            Route::get('/campaigns', [DonationController::class, 'campaignIndex'])->name('admin.campaigns.index');
            Route::get('/campaigns/create', [DonationController::class, 'campaignCreate'])->name('admin.campaigns.create');
            Route::post('/campaigns', [DonationController::class, 'campaignStore'])->name('admin.campaigns.store');
            Route::get('/campaigns/{campaign}/edit', [DonationController::class, 'campaignEdit'])->name('admin.campaigns.edit');
            Route::put('/campaigns/{campaign}', [DonationController::class, 'campaignUpdate'])->name('admin.campaigns.update');
            Route::delete('/campaigns/{campaign}', [DonationController::class, 'campaignDestroy'])->name('admin.campaigns.destroy');
        });
    });

    // Public Home (Dynamic Launch Management)
    Route::get('/', function (\App\Services\AlumniService $alumniService) {
        $isComingSoon = setting('coming_soon_mode', 'off') === 'on';
        $isAdmin = auth()->check() && in_array(auth()->user()->role, ['admin', 'editor']);

        if ($isComingSoon && !$isAdmin) {
            return view('coming_soon');
        }

        $data = $alumniService->getWelcomeData();
        return view('welcome', $data);
    })->name('home')->middleware('cache_response');

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
    Route::post('/jobs/{slug}/apply', [JobController::class, 'apply'])->name('jobs.apply');

    // News Public
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/news', [NewsController::class, 'index'])->name('news.index')->middleware('cache_response');
    Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show')->middleware('cache_response');

    Route::get('/jejak-sukses', [AlumniController::class, 'successStories'])->name('success_stories.index');
    Route::get('/jejak-sukses/{successStory}', [AlumniController::class, 'successStoryDetail'])->name('success_stories.show');

    // Donations Public Transparency
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::get('/alumni-fund', [DonationController::class, 'mobileFund'])->name('alumni.fund.mobile');
    Route::get('/donations/audit', [DonationController::class, 'audit'])->name('donations.audit');
    Route::get('/donations/campaign/{campaign:slug}', [DonationController::class, 'show'])->name('donations.show');
    Route::get('/api/donations', function() {
        return \App\Models\Donation::where('status', 'verified')->with('user')->latest()->take(10)->get()->map(function($d) {
            return [
                'name' => $d->user ? $d->user->name : ($d->is_anonymous ? 'Anonim' : 'Alumni'),
                'amount' => $d->amount
            ];
        });
    });

    // Advertisement Click Tracker
    Route::get('/ads/click/{id}', function ($id) {
        $ad = \App\Models\Ad::findOrFail($id);
        $ad->increment('click');
        return redirect($ad->link ?: '/');
    })->name('ads.click');

    // --- Public Content (Articles, Programs, Jobs) ---
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index')->middleware('cache_response');
    // Global Network & Mesh Map (Leaflet)
    Route::get('/global-network', [MapController::class, 'index'])->name('global.network');
    Route::get('/api/v1/map-data', [MapController::class, 'data'])->name('api.map.data');
    Route::get('/api/v1/map-ai-insight', [MapController::class, 'aiInsight'])->name('api.map.ai');

// --- 2. Authentication Routes (Stricter Rate Limiting) ---
Route::post('/api/v1/guardian/log-error', [\App\Http\Controllers\GuardianController::class, 'logError'])->name('guardian.log');

Route::middleware(['throttle:login'])->group(function () {
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
    Route::redirect('/alumni/nearby', '/alumni/networking/nearby');
    
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
            Route::post('/alumni/mentor/register', [\App\Http\Controllers\MentorController::class, 'register'])->name('alumni.mentor.register');
            Route::get('/alumni/networking/recommendations', [\App\Http\Controllers\NetworkingController::class, 'getRecommendations'])->name('alumni.networking.recommendations');
            Route::get('/alumni/networking/nearby', [\App\Http\Controllers\NetworkingController::class, 'nearby'])->name('alumni.networking.nearby');
            Route::get('/alumni/network', [AlumniController::class, 'network'])->name('alumni.network');
            Route::get('/alumni/messages', [AlumniController::class, 'messages'])->name('alumni.messages');
            Route::get('/api/dashboard/ai-data', [\App\Http\Controllers\Api\DashboardApiController::class, 'getAIData'])->name('dashboard.ai.data');
            Route::get('/alumni/card', [CardController::class, 'index'])->name('alumni.card');
            
            // Alumni Donations (Actions)
            Route::get('/donations/{campaign}', [DonationController::class, 'donate'])->name('donations.donate');
            Route::post('/donations/{campaign}', [DonationController::class, 'store'])->name('donations.store');

            // Event Tickets
            Route::get('/tickets/{ticket_code}', [TicketController::class, 'show'])->name('tickets.show');
            Route::get('/events/scanner', [TicketController::class, 'scanner'])->name('events.scanner');
            Route::post('/events/scan', [TicketController::class, 'scan'])->name('events.scan');
            
            Route::get('/chat', function () {
        return view('alumni.chat.index');
    })->name('alumni.chat');

    Route::prefix('api/chat')->group(function () {
        Route::get('/conversations', [\App\Http\Controllers\Api\ChatController::class, 'getConversations']);
        Route::get('/unread-count', [\App\Http\Controllers\Api\ChatController::class, 'getUnreadCount']);
        Route::get('/messages/{userId}', [\App\Http\Controllers\Api\ChatController::class, 'getMessages']);
        Route::post('/messages', [\App\Http\Controllers\Api\ChatController::class, 'sendMessage']);
        Route::delete('/messages/{id}', [\App\Http\Controllers\Api\ChatController::class, 'deleteMessage']);
        Route::post('/messages/{userId}/read', [\App\Http\Controllers\Api\ChatController::class, 'markAsRead']);
    });

    Route::get('/api/users/{id}', function($id) {
        $user = \App\Models\User::findOrFail($id);
        $isOnline = \DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('last_activity', '>=', now()->subMinutes(15)->timestamp)
            ->exists();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->profile_picture_url,
            'is_online' => $isOnline
        ]);
    });
            
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

        // Skill Matchmaking (Tinder UI) - MUST BE BEFORE Wildcard
        Route::get('/alumni/matchmaking', [\App\Http\Controllers\MatchmakingController::class, 'index'])->name('matchmaking.index');
        Route::post('/alumni/matchmaking/swipe', [\App\Http\Controllers\MatchmakingController::class, 'swipe'])->name('matchmaking.swipe');

        // Wildcard Profile Route (MUST BE LAST in the /alumni/ group)
        Route::get('/alumni/{user}', [AlumniController::class, 'show'])->name('alumni.show');

        // Alumni Social Feed (New High Performance System)
        Route::get('/feed', [\App\Http\Controllers\FeedController::class, 'index'])->name('feed.index');
        Route::post('/feed', [\App\Http\Controllers\FeedController::class, 'store'])->name('feed.store');
        Route::post('/feed/follow/{user}', [\App\Http\Controllers\FeedController::class, 'toggleFollow'])->name('feed.follow');
        
        // Alumni Stories
        Route::get('/stories', [StoryController::class, 'index']);
        Route::get('/stories/{story}', [StoryController::class, 'showShared'])->name('stories.show');
        Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
        Route::post('/stories/note', [StoryController::class, 'storeNote'])->name('stories.note');
        Route::get('/api/stories/active', [StoryController::class, 'getActiveStories'])->name('api.stories.active');
        Route::get('/api/stories/{story}', [StoryController::class, 'show'])->name('api.stories.show');
        Route::post('/api/story/view', [StoryController::class, 'view'])->name('api.story.view');
        Route::get('/api/story/{id}/viewers', [StoryController::class, 'viewers'])->name('api.story.viewers');

        // Notifications API
        Route::get('/api/notifications', [AlumniController::class, 'getNotifications'])->name('api.notifications.index');
        Route::post('/api/notifications/read-all', [AlumniController::class, 'markNotificationsRead'])->name('api.notifications.readAll');

        // Nostalgia Feed Routes (Legacy Support)
        Route::get('/nostalgia', [\App\Http\Controllers\PostController::class, 'index'])->name('nostalgia.index');
        Route::post('/nostalgia', [\App\Http\Controllers\PostController::class, 'store'])->name('nostalgia.store');
        Route::delete('/nostalgia/{post}', [\App\Http\Controllers\PostController::class, 'destroy'])->name('nostalgia.destroy');
        Route::post('/nostalgia/{post}/like', [\App\Http\Controllers\PostController::class, 'toggleLike'])->name('nostalgia.like');
        Route::post('/nostalgia/{post}/comment', [\App\Http\Controllers\PostController::class, 'storeComment'])->name('nostalgia.comment.store');
        Route::get('/api/alumni/search', [\App\Http\Controllers\PostController::class, 'searchAlumni'])->name('api.alumni.search');
        Route::post('/api/track', [\App\Http\Controllers\Api\TrackingController::class, 'store'])->name('api.track');
        Route::post('/api/track/view', [\App\Http\Controllers\Api\TrackingController::class, 'trackView'])->name('api.track.view');

        // WebAR Nostalgia Scanner
        Route::get('/ar-experience', function () {
            return view('portal.ar-scanner');
        })->name('ar.scanner');

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

    // --- Legacy / Compatibility: Redirect old admin paths to subdomain ---
    Route::get('/admin/{any?}', function($any = null) {
        return redirect()->to('https://admin.' . parse_url(config('app.url'), PHP_URL_HOST) . '/' . $any);
    })->where('any', '.*');

    // Admin & Editor Panel (Legacy Middleware check)
    Route::middleware(['role:admin,editor'])->group(function () {
        // Keep some routes here if they must be on main domain, but we move them to subdomain above
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
