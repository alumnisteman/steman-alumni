@extends('layouts.app')

@section('title', 'Alumni Feed - Jalin Silaturahmi')

@push('styles')
<style>
    .feed-container { max-width: 680px; margin: 0 auto; }
    .post-card {
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 16px;
        transition: transform 0.2s;
        word-break: break-word;
    }
    .post-card:hover { transform: translateY(-2px); }
    .post-avatar { width: 48px; height: 48px; object-fit: cover; border-radius: 12px; }
    .post-image { border-radius: 12px; max-height: 500px; object-fit: cover; width: 100%; }
    
    @media (max-width: 575px) {
        .post-card { border-radius: 0; margin-left: -12px; margin-right: -12px; border-left: none; border-right: none; }
        .post-avatar { width: 40px; height: 40px; }
        .feed-container { padding: 0; }
    }
    .interaction-btn {
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        border: none;
        background: transparent;
        color: #64748b;
    }
    .interaction-btn:hover { background: rgba(0,0,0,0.05); color: #059669; }
    .interaction-btn.active { color: #059669; }
    
    .create-post-trigger {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 16px;
        padding: 15px;
        cursor: pointer;
    }
    .create-post-trigger:hover { background: #f8fafc; }
    
    /* Skeleton Loader */
    .skeleton { background: #e2e8f0; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0% { opacity: 0.5; } 50% { opacity: 1; } 100% { opacity: 0.5; } }

    /* Hide scrollbar for Chrome, Safari and Opera */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    /* Hide scrollbar for IE, Edge and Firefox */
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    .hover-scale { transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .hover-scale:hover { transform: scale(1.1); }
    .active-shrink:active { transform: scale(0.9) !important; }
    .bg-gradient-story {
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        padding: 2px;
    }
    #storyViewerModal { z-index: 99999 !important; }
    .modal-backdrop.show { z-index: 99998 !important; }
    .modal-fullscreen { width: 100vw !important; height: 100vh !important; margin: 0 !important; max-width: none !important; }

    /* Gen Z Overhaul Styles */
    .reaction-btn {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        font-size: 1.5rem;
        padding: 5px 12px;
        border-radius: 50px;
        transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), background 0.2s;
        backdrop-filter: blur(5px);
        color: white;
    }
    .reaction-btn:hover { transform: scale(1.2); background: rgba(255, 255, 255, 0.2); }
    .floating-emoji {
        position: absolute;
        font-size: 2.5rem;
        pointer-events: none;
        z-index: 100;
        animation: floatUp 1.5s forwards ease-out;
    }
    @keyframes floatUp {
        0% { transform: translateY(0) scale(1); opacity: 1; }
        100% { transform: translateY(-400px) translateX(var(--tx)) rotate(var(--tr)) scale(1.8); opacity: 0; }
    }
    .music-visualizer .v-bar {
        width: 5px;
        border-radius: 3px;
        animation: dance 0.8s infinite ease-in-out;
    }
    @keyframes dance {
        0%, 100% { height: 6px; }
        50% { height: 24px; }
    }
    .v-bar:nth-child(2) { animation-delay: 0.1s; }
    .v-bar:nth-child(3) { animation-delay: 0.2s; }
    .v-bar:nth-child(4) { animation-delay: 0.15s; }
    .v-bar:nth-child(5) { animation-delay: 0.25s; }

    .mood-sunset { background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%) !important; }
    .mood-ocean { background: linear-gradient(45deg, #2af598 0%, #009efd 100%) !important; }
    .mood-midnight { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
    .mood-forest { background: linear-gradient(to right, #43e97b 0%, #38f9d7 100%) !important; }
    .mood-candy { background: linear-gradient(to top, #ff9a9e 0%, #fecfef 99%, #fecfef 100%) !important; }

    /* Premium Spotify Card */
    .spotify-premium-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        padding: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        animation: cardFloat 3s ease-in-out infinite;
    }
    @keyframes cardFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .vinyl-record {
        width: 100px;
        height: 100px;
        background: #121212;
        border-radius: 50%;
        border: 2px solid #333;
        position: relative;
        animation: spin 3s linear infinite;
        box-shadow: 0 0 20px rgba(0,0,0,0.5);
    }
    .vinyl-record::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 30px;
        height: 30px;
        background: #1DB954;
        border-radius: 50%;
        border: 4px solid #121212;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .animate-ping {
        animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
    }
    @keyframes ping {
        75%, 100% { transform: scale(2); opacity: 0; }
    }
</style>
@endpush

@section('content')
<div class="container py-4" id="feed-data" data-user-id="{{ auth()->id() }}">
    <div class="feed-container">
        
        @auth
        {{-- DESKTOP CREATE POST BOX --}}
        <div class="create-post-trigger mb-4 d-none d-md-flex align-items-center gap-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createPostModal">
            <img src="{{ auth()->check() ? auth()->user()->profile_picture_url : 'https://ui-avatars.com/api/?name=Guest' }}" class="post-avatar">
            <div class="text-muted flex-grow-1">Apa yang sedang Anda pikirkan, {{ auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Alumni' }}?</div>
            <i class="bi bi-image text-success fs-4"></i>
        </div>
        @endauth

        {{-- ONLINE INDICATOR --}}
        <div class="d-flex align-items-center gap-2 mb-3 px-2">
            <span class="position-relative d-flex" style="width: 10px; height: 10px;">
                <span class="animate-ping position-absolute inline-flex h-100 w-100 rounded-circle bg-success opacity-75"></span>
                <span class="relative inline-flex rounded-circle h-100 w-100 bg-success"></span>
            </span>
            <span class="small fw-bold text-muted">{{ $onlineCount ?? 0 }} alumni sedang online</span>
        </div>

        {{-- STORY BAR --}}
        <div class="d-flex gap-3 overflow-x-auto pb-3 mb-4 no-scrollbar" style="scroll-snap-type: x mandatory;">
            @php
                try {
                    $activeStories = \App\Models\Story::active()->with('user')->orderBy('created_at', 'desc')->get()->groupBy('user_id');
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Story Error: ' . $e->getMessage());
                    $activeStories = collect();
                }
            @endphp

            @auth
            {{-- ADD STORY (Instagram Style) --}}
            @php
                $myStories = $activeStories->get(auth()->id());
                $hasMyClickableStory = $myStories && $myStories->count() > 0;
            @endphp
            <div class="flex-shrink-0 text-center" style="width: 72px; scroll-snap-align: start;">
                <div class="position-relative mb-1 cursor-pointer hover-scale" 
                     onclick="{{ $hasMyClickableStory ? 'viewStory('.auth()->id().')' : '' }}"
                     data-bs-toggle="{{ $hasMyClickableStory ? '' : 'modal' }}" 
                     data-bs-target="{{ $hasMyClickableStory ? '' : '#createStoryModal' }}">
                    
                    <div class="p-1 rounded-circle {{ $hasMyClickableStory ? 'bg-gradient-story' : '' }}">
                        <img src="{{ auth()->check() ? auth()->user()->profile_picture_url : 'https://ui-avatars.com/api/?name=Guest' }}" class="rounded-circle border border-2 border-white shadow-sm" style="width: 58px; height: 58px; object-fit: cover;">
                    </div>

                    @if($hasMyClickableStory && $myStories->where('type', 'spotify')->count() > 0)
                        <div class="position-absolute top-0 end-0 bg-success rounded-circle border border-2 border-white d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; z-index: 6;">
                            <i class="bi bi-music-note-beamed text-white" style="font-size: 0.7rem;"></i>
                        </div>
                    @endif

                    <div class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white d-flex align-items-center justify-content-center cursor-pointer" 
                         style="width: 22px; height: 22px; z-index: 5;"
                         data-bs-toggle="modal" data-bs-target="#createStoryModal"
                         onclick="event.stopPropagation();">
                        <i class="bi bi-plus-lg text-white" style="font-size: 0.7rem;"></i>
                    </div>
                </div>
                <div class="small fw-bold text-muted" style="font-size: 0.7rem;">Cerita Anda</div>
            </div>
            @endauth

            @foreach($activeStories as $userId => $userStories)
                @if($userId == auth()->id()) @continue @endif
                @php 
                    $storyUser = $userStories->first()->user; 
                    if (!$storyUser) continue; // Skip if user missing
                    $activeNote = $userStories->where('type', 'note')->first();
                    $hasClickableStory = $userStories->count() > 0;
                @endphp
                <div class="flex-shrink-0 text-center position-relative hover-scale active-shrink" 
                     style="width: 76px; scroll-snap-align: start; cursor: pointer; -webkit-tap-highlight-color: transparent; z-index: 5;" 
                     onclick="{{ $hasClickableStory ? 'viewStory('.$userId.')' : '' }}">
                    {{-- Note Bubble --}}
                    @if($activeNote)
                        <div class="position-absolute top-0 start-50 translate-middle-x bg-white text-dark rounded-pill px-2 py-1 shadow-sm border small fw-bold text-truncate" style="max-width: 80px; font-size: 0.6rem; z-index: 10; margin-top: -5px;">
                            {{ $activeNote->content }}
                        </div>
                    @endif

                    <div class="p-1 rounded-circle mb-1 {{ $hasClickableStory ? 'bg-gradient-story' : '' }}" style="width: 66px; height: 66px; margin: 0 auto;">
                        <img src="{{ $storyUser->profile_picture_url }}" class="rounded-circle border border-2 border-white shadow-sm" style="width: 58px; height: 58px; object-fit: cover;">
                    </div>
                    
                    @if($userStories->where('type', 'spotify')->count() > 0)
                        <div class="position-absolute top-0 end-0 bg-success rounded-circle border border-2 border-white d-flex align-items-center justify-content-center" style="width: 22px; height: 22px; z-index: 6; margin-right: 5px;">
                            <i class="bi bi-music-note-beamed text-white" style="font-size: 0.75rem;"></i>
                        </div>
                    @endif

                    <div class="small fw-bold text-truncate mx-auto" style="font-size: 0.7rem; width: 64px;">{{ $storyUser->name }}</div>
                </div>
            @endforeach
        </div>

        {{-- QUICK ACTIONS (Gen Z) --}}
        <div class="row g-2 mb-4">
            <div class="col-4">
                <button class="btn btn-light w-100 rounded-3 py-3 shadow-sm border-0 d-flex flex-column align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createStoryModal">
                    <i class="bi bi-chat-left-text text-primary fs-4"></i>
                    <span class="small fw-bold">Catatan</span>
                </button>
            </div>
            <div class="col-4">
                <button class="btn btn-light w-100 rounded-3 py-3 shadow-sm border-0 d-flex flex-column align-items-center gap-2" onclick="location.href='{{ route('alumni.networking.nearby') }}'">
                    <i class="bi bi-geo-alt text-danger fs-4"></i>
                    <span class="small fw-bold">Sekitar</span>
                </button>
            </div>
            <div class="col-4">
                <button class="btn btn-light w-100 rounded-3 py-3 shadow-sm border-0 d-flex flex-column align-items-center gap-2" onclick="location.href='/alumni/network'">
                    <i class="bi bi-globe-americas text-success fs-4"></i>
                    <span class="small fw-bold">Network</span>
                </button>
            </div>
        </div>

        {{-- POSTS FEED --}}
        <div id="posts-container">
            @include('alumni.feed.posts', ['posts' => $posts])
        </div>

        {{-- LOAD MORE --}}
        <div id="load-more-sentinel" class="py-4 text-center">
            <div class="spinner-border text-primary d-none" id="feed-loader"></div>
        </div>
    </div>
</div>

{{-- Post & Story Modals are now Global in app.blade.php --}}

{{-- INSTAGRAM-STYLE STORY VIEWER --}}
<div class="modal fade" id="storyViewerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="background: #000;">
    <div class="modal-dialog modal-fullscreen m-0">
        <div class="modal-content bg-black border-0 rounded-0">
            <div class="modal-body p-0 d-flex align-items-center justify-content-center position-relative overflow-hidden">
                
                {{-- Immersive Background Blur --}}
                <div id="story-bg-blur" class="position-absolute top-0 start-0 w-100 h-100" style="filter: blur(50px) brightness(0.4); background-size: cover; background-position: center; z-index: 1;"></div>

                {{-- Progress Bars Container --}}
                <div class="position-absolute top-0 start-0 w-100 px-2 py-3 d-flex gap-1" id="story-progress-segments" style="z-index: 20;">
                    {{-- Dynamically generated segments --}}
                </div>

                {{-- Header --}}
                <div class="position-absolute top-0 start-0 w-100 p-4 d-flex align-items-center justify-content-between" style="z-index: 20; margin-top: 15px;">
                    <div class="d-flex align-items-center gap-2">
                        <img id="story-user-avatar" src="" class="rounded-circle border border-2 border-white" style="width: 32px; height: 32px; object-fit: cover;">
                        <div>
                            <div id="story-user-name" class="text-white fw-bold small"></div>
                            <div id="story-time" class="text-white-50" style="font-size: 0.7rem;"></div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" onclick="closeStory()" style="width: 25px; height: 25px; cursor: pointer;"></button>
                </div>

                {{-- Main Content --}}
                <div class="position-relative d-flex align-items-center justify-content-center h-100 w-100" style="z-index: 10;">
                    <img id="story-display-img" src="" class="img-fluid rounded-4 shadow-lg d-none" style="max-height: 85vh; object-fit: contain;">
                    
                    <div id="story-note-container" class="w-100 h-100 d-none d-flex align-items-center justify-content-center p-5 text-center text-white" style="z-index: 11; font-size: 1.5rem; font-weight: 800; line-height: 1.4;">
                        <div id="story-note-text" class="animate__animated animate__zoomIn"></div>
                    </div>

                    <div id="story-media-container" class="w-100 px-4 d-none position-absolute top-50 start-50 translate-middle" style="max-width: 400px; z-index: 15;">
                        <div id="story-iframe-loader" class="d-none position-absolute top-50 start-50 translate-middle text-white" style="z-index: 20;">
                            <div class="spinner-border" role="status"></div>
                        </div>
                        <div class="ratio ratio-16x9 d-none" id="youtube-ratio">
                            <iframe id="story-youtube-iframe" src="" allow="autoplay; encrypted-media; fullscreen" class="rounded-4 shadow-lg border-0"></iframe>
                        </div>
                        <div id="spotify-wrapper" class="d-none">
                            <div class="spotify-premium-card text-center">
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="vinyl-record"></div>
                                </div>
                                <iframe id="story-spotify-iframe" src="" width="100%" height="352" frameBorder="0" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy" class="rounded-4 border-0"></iframe>
                                
                                <!-- Music Visualizer -->
                                <div class="music-visualizer d-flex align-items-end justify-content-center gap-1 mt-3" style="height: 30px;">
                                    <div class="v-bar bg-success"></div>
                                    <div class="v-bar bg-success"></div>
                                    <div class="v-bar bg-success"></div>
                                    <div class="v-bar bg-success"></div>
                                    <div class="v-bar bg-success"></div>
                                    <div class="v-bar bg-success"></div>
                                    <div class="v-bar bg-success"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Navigation & Gesture Areas --}}
                    <div id="story-gesture-area" class="position-absolute top-0 start-0 w-100 h-100 cursor-pointer" style="z-index: 12;">
                        <div class="h-100 w-50 position-absolute top-0 start-0" id="nav-left"></div>
                        <div class="h-100 w-50 position-absolute top-0 end-0" id="nav-right"></div>
                    </div>
                </div>

                {{-- Caption --}}
                <div id="story-display-caption" class="position-absolute bottom-0 start-0 w-100 p-5 text-white text-center fw-medium" style="z-index: 20; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding-bottom: 150px !important;">
                </div>

                {{-- Quick Reactions --}}
                <div id="quick-reactions" class="position-absolute w-100 d-flex justify-content-center gap-3" style="bottom: 85px; z-index: 40;">
                    <button onclick="sendReaction('🔥', event)" class="reaction-btn">🔥</button>
                    <button onclick="sendReaction('❤️', event)" class="reaction-btn">❤️</button>
                    <button onclick="sendReaction('😂', event)" class="reaction-btn">😂</button>
                    <button onclick="sendReaction('😮', event)" class="reaction-btn">😮</button>
                    <button onclick="sendReaction('🙌', event)" class="reaction-btn">🙌</button>
                </div>

                {{-- Reply Box / Viewer Stats --}}
                <div class="position-absolute bottom-0 start-0 w-100 p-3 d-flex align-items-center gap-2" style="z-index: 30; padding-bottom: 25px !important;">
                    <div id="story-reply-container" class="flex-grow-1 d-flex gap-2 align-items-center">
                        <input type="text" class="form-control bg-transparent border-white border-opacity-25 rounded-pill text-white px-4 py-2 small" placeholder="Kirim pesan..." style="backdrop-filter: blur(10px);">
                        <button class="btn btn-link text-white p-0" onclick="celebrateLike(this, event)"><i class="bi bi-heart fs-4"></i></button>
                    </div>
                    
                    {{-- Owner Only Viewer Button --}}
                    <div id="story-owner-stats" class="d-none w-100 d-flex justify-content-center">
                        <button class="btn btn-dark bg-opacity-50 rounded-pill px-4 py-2 text-white border-white border-opacity-25 d-flex align-items-center gap-2" onclick="showViewerList()">
                            <i class="bi bi-eye"></i> <span id="story-viewer-count">0</span> Penonton
                        </button>
                    </div>
                    
                    <button class="btn btn-link text-white p-0" id="story-share-btn"><i class="bi bi-send fs-4"></i></button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- STORY VIEWERS MODAL --}}
<div class="modal fade" id="storyViewersModal" tabindex="-1" aria-hidden="true" style="z-index: 2100;">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Penonton Cerita</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="viewer-list-container" class="list-group list-group-flush">
                    {{-- Dynamically loaded --}}
                </div>
                <div id="viewer-loader" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary"></div>
                </div>
                <div id="viewer-empty" class="text-center py-5 text-muted d-none">
                    <i class="bi bi-eye-slash fs-1 d-block mb-2"></i>
                    Belum ada yang melihat cerita ini.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Story Viewer Logic
    let currentStoryUserIndex = -1;
    let currentStoryMediaIndex = 0;
    let currentViewerStories = [];
    let allActiveStoryUsers = []; // List of user IDs who have stories
    let storyDuration = 5000; // 5 seconds per story
    let storyData = {}; // Store all fetched stories
    let isPaused = false;
    let storyProgress = 0;
    let touchStartY = 0;
    
    // Animation state
    let startTime = null;
    let elapsedBeforePause = 0;
    let rafId = null;

    // Story variables
    window.storyData = {};
    window.allActiveStoryUsers = [];
    window.currentViewerStories = [];
    window.currentStoryUserIndex = -1;
    window.currentStoryMediaIndex = 0;
    
    window.viewStory = function(userId) {
        if (rafId) cancelAnimationFrame(rafId);
        document.body.style.overflow = 'hidden';
        
        const modalEl = document.getElementById('storyViewerModal');
        if (!modalEl) return;
        
        // Robust Bootstrap detection
        const BS = window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
        let modal;
        try {
            if (BS && BS.Modal) {
                modal = BS.Modal.getOrCreateInstance(modalEl);
            }
        } catch (e) { console.warn('Bootstrap instance failed', e); }
        
        fetch(`/api/stories/active`)
            .then(r => {
                if (!r.ok) throw new Error('API Error: ' + r.status);
                return r.json();
            })
            .then(data => {
                storyData = data;
                allActiveStoryUsers = Object.keys(data);
                currentStoryUserIndex = allActiveStoryUsers.findIndex(id => String(id).trim() === String(userId).trim());
                
                if (currentStoryUserIndex === -1) {
                    document.body.style.overflow = 'auto';
                    return;
                }
                
                loadUserStories(currentStoryUserIndex);
                
                if (modal) {
                    modal.show();
                } else {
                    // Manual show fallback
                    modalEl.classList.add('show');
                    modalEl.style.display = 'block';
                    document.body.classList.add('modal-open');
                    if (!document.querySelector('.modal-backdrop')) {
                        const b = document.createElement('div');
                        b.className = 'modal-backdrop fade show';
                        document.body.appendChild(b);
                    }
                }
            })
            .catch(err => {
                console.error('Story Fetch Error:', err);
                document.body.style.overflow = 'auto';
            });
    }

    window.closeStory = function() {
        if (rafId) cancelAnimationFrame(rafId);
        document.body.style.overflow = 'auto';
        
        const modalEl = document.getElementById('storyViewerModal');
        if (!modalEl) return;
        
        const BS = window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
        try {
            if (BS && BS.Modal) {
                const modal = BS.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
        } catch (e) {}

        // Full cleanup for manual fallback
        modalEl.classList.remove('show');
        modalEl.style.display = 'none';
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    }

    window.loadUserStories = function(userIndex) {
        if (userIndex < 0 || userIndex >= allActiveStoryUsers.length) {
            closeStory();
            return;
        }

        currentStoryUserIndex = userIndex;
        const userId = allActiveStoryUsers[userIndex];
        const userStories = storyData[userId];
        
        // Filter media + notes
        currentViewerStories = userStories.filter(s => s.type === 'image' || s.type === 'spotify' || s.type === 'note');
        
        if (currentViewerStories.length === 0) {
            loadUserStories(userIndex + 1);
            return;
        }

        currentStoryMediaIndex = 0;
        renderStory(0);
    }

    window.sendReaction = function(emoji, event) {
        // Floating effect
        createFloatingEmoji(emoji);
        
        // Visual feedback on button
        const btn = event ? event.currentTarget : null;
        if (btn) {
            btn.style.transform = 'scale(1.5)';
            setTimeout(() => btn.style.transform = 'scale(1)', 200);
        }
    }

    window.createFloatingEmoji = function(emoji) {
        const container = document.getElementById('storyViewerModal');
        const el = document.createElement('div');
        el.className = 'floating-emoji';
        el.innerText = emoji;
        
        // Randomize direction and rotation
        const tx = Math.floor(Math.random() * 200) - 100;
        const tr = Math.floor(Math.random() * 90) - 45;
        el.style.setProperty('--tx', `${tx}px`);
        el.style.setProperty('--tr', `${tr}deg`);
        
        // Start from center bottom
        el.style.left = '50%';
        el.style.bottom = '100px';
        
        container.appendChild(el);
        setTimeout(() => el.remove(), 1500);
    }

    window.celebrateLike = function(btn, event) {
        const heart = btn.querySelector('i');
        heart.classList.replace('bi-heart', 'bi-heart-fill');
        heart.classList.add('text-danger', 'animate__animated', 'animate__heartBeat');
        
        // Burst emojis
        for(let i=0; i<8; i++) {
            setTimeout(() => createFloatingEmoji('❤️'), i * 50);
        }

        setTimeout(() => {
            heart.classList.remove('animate__heartBeat');
        }, 1000);
    }

    window.renderStory = function(index) {
        if (rafId) cancelAnimationFrame(rafId);
        if (index < 0 || index >= currentViewerStories.length) {
            loadUserStories(currentStoryUserIndex + 1);
            return;
        }

        currentStoryMediaIndex = index;
        const story = currentViewerStories[index];
        const modalEl = document.getElementById('storyViewerModal');
        modalEl.dataset.currentStoryId = story.id;

        // Update UI
        document.getElementById('story-user-avatar').src = story.user.profile_picture_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(story.user.name);
        document.getElementById('story-user-name').innerText = story.user.name;
        document.getElementById('story-time').innerText = story.created_at_human || 'Baru saja';
        document.getElementById('story-display-caption').innerText = story.caption && story.type !== 'note' ? story.caption : '';
        
        const imgDisplay = document.getElementById('story-display-img');
        const noteContainer = document.getElementById('story-note-container');
        const noteText = document.getElementById('story-note-text');
        const mediaContainer = document.getElementById('story-media-container');
        const spotifyWrapper = document.getElementById('spotify-wrapper');
        const youtubeRatio = document.getElementById('youtube-ratio');
        const spotifyIframe = document.getElementById('story-spotify-iframe');
        const youtubeIframe = document.getElementById('story-youtube-iframe');
        
        // Reset
        imgDisplay.classList.add('d-none');
        noteContainer.classList.add('d-none');
        noteContainer.className = 'w-100 h-100 d-none d-flex align-items-center justify-content-center p-5 text-center text-white';
        mediaContainer.classList.add('d-none');
        spotifyWrapper.classList.add('d-none');
        youtubeRatio.classList.add('d-none');
        const iframeLoader = document.getElementById('story-iframe-loader');
        if(iframeLoader) iframeLoader.classList.add('d-none');
        spotifyIframe.src = '';
        youtubeIframe.src = '';

        // BG Blur fallback
        document.getElementById('story-bg-blur').style.backgroundImage = story.image_url ? `url('${story.image_url}')` : 'linear-gradient(45deg, #121212, #242424)';

        if (story.type === 'note') {
            noteContainer.classList.remove('d-none');
            noteText.innerText = story.content;
            const mood = (story.caption || 'default').toLowerCase();
            if (mood !== 'default') {
                noteContainer.classList.add('mood-' + mood);
                document.getElementById('story-bg-blur').style.backgroundImage = 'none';
            }
        } else if (story.spotify_url) {
            mediaContainer.classList.remove('d-none');
            if(iframeLoader) iframeLoader.classList.remove('d-none');

            const handleIframeLoad = function() {
                if(iframeLoader) iframeLoader.classList.add('d-none');
                resumeStory();
                this.removeEventListener('load', handleIframeLoad);
            };

            if (story.spotify_url.includes('youtube.com') || story.spotify_url.includes('youtu.be')) {
                youtubeRatio.classList.remove('d-none');
                youtubeIframe.addEventListener('load', handleIframeLoad);
                youtubeIframe.src = story.spotify_url;
            } else {
                spotifyWrapper.classList.remove('d-none');
                spotifyIframe.addEventListener('load', handleIframeLoad);
                spotifyIframe.src = story.spotify_url;
            }
        }

        if (story.image_url && story.type !== 'note') {
            imgDisplay.classList.remove('d-none');
            imgDisplay.src = story.image_url;
        }

        // Track View
        const currentUserId = document.getElementById('feed-data').dataset.userId;
        if (story.user_id != currentUserId) {
            trackStoryView(story.id);
            document.getElementById('story-owner-stats').classList.add('d-none');
            document.getElementById('story-reply-container').classList.remove('d-none');
        } else {
            // Owner view: show stats button
            document.getElementById('story-owner-stats').classList.remove('d-none');
            document.getElementById('story-reply-container').classList.add('d-none');
            document.getElementById('story-viewer-count').innerText = story.views_count || 0;
        }

        // Dynamic Duration Logic (Gen Z Experience)
        if (story.spotify_url || story.type === 'spotify') {
            storyDuration = 30000; // 30 seconds for music/video
        } else {
            storyDuration = 7000; // 7 seconds for images/notes
        }

        // Render Progress Segments
        const progressContainer = document.getElementById('story-progress-segments');
        progressContainer.innerHTML = '';
        currentViewerStories.forEach((_, i) => {
            const segment = document.createElement('div');
            segment.className = 'flex-grow-1 bg-white bg-opacity-25 rounded-pill overflow-hidden';
            segment.style.height = '2px';
            
            const bar = document.createElement('div');
            bar.className = 'h-100 bg-white';
            bar.style.width = i < index ? '100%' : (i === index ? '0%' : '0%');
            bar.id = `story-bar-${i}`;
            
            segment.appendChild(bar);
            progressContainer.appendChild(segment);
        });

        // Start animation (RAF for smooth feel)
        storyProgress = 0;
        elapsedBeforePause = 0;
        startTime = Date.now();
        isPaused = false;
        
        if (story.spotify_url) {
            isPaused = true; // Pause until iframe loads
        }
        
        if (rafId) cancelAnimationFrame(rafId);
        
        const animate = () => {
            if (!isPaused) {
                const elapsed = (Date.now() - startTime) + elapsedBeforePause;
                const progress = Math.min((elapsed / storyDuration) * 100, 100);
                
                const currentBar = document.getElementById(`story-bar-${index}`);
                if (currentBar) currentBar.style.width = progress + '%';

                if (progress >= 100) {
                    nextStory();
                    return;
                }
            }
            rafId = requestAnimationFrame(animate);
        };
        
        rafId = requestAnimationFrame(animate);

        // Preload next media
        preloadNextMedia(index);
    }

    function preloadNextMedia(currentIndex) {
        let nextIndex = currentIndex + 1;
        let nextUserIndex = currentStoryUserIndex;

        if (nextIndex >= currentViewerStories.length) {
            nextUserIndex++;
            if (nextUserIndex < allActiveStoryUsers.length) {
                const nextUserStories = storyData[allActiveStoryUsers[nextUserIndex]];
                const nextMedia = nextUserStories.find(s => s.image_url);
                if (nextMedia) (new Image()).src = nextMedia.image_url;
            }
        } else {
            const nextMedia = currentViewerStories[nextIndex];
            if (nextMedia && nextMedia.image_url) (new Image()).src = nextMedia.image_url;
        }
    }

    function pauseStory() {
        if (isPaused) return;
        isPaused = true;
        elapsedBeforePause += (Date.now() - startTime);
        
        const img = document.getElementById('story-display-img');
        if (img) img.style.transform = 'scale(0.98)';
    }

    function resumeStory() {
        if (!isPaused) return;
        isPaused = false;
        startTime = Date.now();
        
        const img = document.getElementById('story-display-img');
        if (img) img.style.transform = 'scale(1)';
    }

    window.nextStory = function() {
        if (currentStoryMediaIndex < currentViewerStories.length - 1) {
            renderStory(currentStoryMediaIndex + 1);
        } else {
            // Move to next user
            loadUserStories(currentStoryUserIndex + 1);
        }
    }

    window.prevStory = function() {
        if (currentStoryMediaIndex > 0) {
            renderStory(currentStoryMediaIndex - 1);
        } else {
            // Move to previous user
            if (currentStoryUserIndex > 0) {
                loadUserStories(currentStoryUserIndex - 1);
            } else {
                renderStory(0); // Reset progress if at first user, first story
            }
        }
    }

    // Initialize Story Interactions
    document.addEventListener('DOMContentLoaded', () => {
        const gestureArea = document.getElementById('story-gesture-area');
        const leftNav = document.getElementById('nav-left');
        const rightNav = document.getElementById('nav-right');
        const viewerModal = document.getElementById('storyViewerModal');

        if (gestureArea) {
            // Hold to Pause
            gestureArea.addEventListener('mousedown', pauseStory);
            gestureArea.addEventListener('mouseup', resumeStory);
            gestureArea.addEventListener('touchstart', (e) => {
                pauseStory();
                touchStartY = e.touches[0].clientY;
            });
            gestureArea.addEventListener('touchend', (e) => {
                resumeStory();
                const touchEndY = e.changedTouches[0].clientY;
                // Swipe down detection
                if (touchEndY - touchStartY > 100) {
                    closeStory();
                }
            });

            // Tap Navigation
            leftNav.addEventListener('click', (e) => {
                e.stopPropagation();
                prevStory();
            });
            rightNav.addEventListener('click', (e) => {
                e.stopPropagation();
                nextStory();
            });
        }

        if (viewerModal) {
            viewerModal.addEventListener('hidden.bs.modal', () => {
                if (rafId) cancelAnimationFrame(rafId);
                document.body.style.overflow = 'auto';
                resumeStory();
            });
        }

        // Visibility API
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                pauseStory();
            } else {
                resumeStory();
            }
        });
    });

    // Share Story Logic
    function shareStory(platform) {
        const storyId = document.getElementById('storyViewerModal').dataset.currentStoryId;
        if (!storyId) return;

        const shareUrl = `${window.location.origin}/stories/${storyId}`;
        const title = 'Lihat Story Alumni ini!';

        if (platform === 'whatsapp') {
            window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(title + ' ' + shareUrl)}`);
        } else if (platform === 'facebook') {
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`);
        } else if (platform === 'copy') {
            navigator.clipboard.writeText(shareUrl).then(() => {
                alert('Link story berhasil disalin!');
            });
        }
    }

    function trackStoryView(storyId) {
        const url = "{{ route('api.story.view') }}";
        const data = JSON.stringify({
            story_id: storyId,
            _token: "{{ csrf_token() }}"
        });

        if (navigator.sendBeacon) {
            const blob = new Blob([data], { type: 'application/json' });
            navigator.sendBeacon(url, blob);
        } else {
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: data,
                keepalive: true
            });
        }
    }

    function showViewerList() {
        const storyId = document.getElementById('storyViewerModal').dataset.currentStoryId;
        if (!storyId) return;

        // Pause story
        if (window.storyInterval) clearInterval(window.storyInterval);

        const bs = window.bootstrap || bootstrap;
        if (!bs) return;

        const modal = new bs.Modal(document.getElementById('storyViewersModal'));
        const container = document.getElementById('viewer-list-container');
        const loader = document.getElementById('viewer-loader');
        const empty = document.getElementById('viewer-empty');

        container.innerHTML = '';
        loader.classList.remove('d-none');
        empty.classList.add('d-none');
        modal.show();

        fetch(`/api/story/${storyId}/viewers`)
            .then(r => r.json())
            .then(data => {
                loader.classList.add('d-none');
                if (data.length === 0) {
                    empty.classList.remove('d-none');
                } else {
                    data.forEach(view => {
                        const item = document.createElement('div');
                        item.className = 'list-group-item d-flex align-items-center gap-3 border-0 py-3';
                        item.innerHTML = `
                            <img src="${view.viewer.profile_picture_url}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <div class="fw-bold small">${view.viewer.name}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Dilihat ${view.created_at_human || 'Baru saja'}</div>
                            </div>
                        `;
                        container.appendChild(item);
                    });
                }
            })
            .catch(() => {
                loader.classList.add('d-none');
                alert('Gagal mengambil daftar penonton.');
            });
    }

    // Infinite Scroll logic
    let page = 1;
    let loading = false;
    let hasMore = true;

    window.addEventListener('scroll', () => {
        if (loading || !hasMore) return;
        
        if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 500) {
            loadMore();
        }
    });

    function loadMore() {
        loading = true;
        document.getElementById('feed-loader').classList.remove('d-none');
        
        page++;
        fetch(`/feed?page=${page}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.html) {
                const container = document.getElementById('posts-container');
                container.insertAdjacentHTML('beforeend', data.html);
                hasMore = data.hasMore;
                
                // Re-init tracking for new posts
                initTracking();
            } else {
                hasMore = false;
            }
            loading = false;
            document.getElementById('feed-loader').classList.add('d-none');
        })
        .catch(() => {
            loading = false;
            document.getElementById('feed-loader').classList.add('d-none');
        });
    }

    // IG-Style Behavioral Tracking
    const postStartTimes = new Map();
    const trackedPosts = new Set();

    function initTracking() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const postId = entry.target.dataset.postId;
                if (entry.isIntersecting) {
                    postStartTimes.set(postId, Date.now());
                } else {
                    if (postStartTimes.has(postId)) {
                        const duration = Date.now() - postStartTimes.get(postId);
                        if (duration >= 1000) { // Min 1 second
                            sendTrackingData(postId, duration);
                        }
                        postStartTimes.delete(postId);
                    }
                }
            });
        }, { threshold: 0.6 });

        document.querySelectorAll('.post-card').forEach(card => {
            if (!trackedPosts.has(card.dataset.postId)) {
                observer.observe(card);
                trackedPosts.add(card.dataset.postId);
            }
        });
    }

    function sendTrackingData(postId, duration) {
        const url = "{{ route('api.track.view') }}";
        const data = JSON.stringify({
            post_id: postId,
            view_time: duration,
            _token: "{{ csrf_token() }}"
        });

        if (navigator.sendBeacon) {
            const blob = new Blob([data], { type: 'application/json' });
            navigator.sendBeacon(url, blob);
        } else {
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: data,
                keepalive: true
            });
        }
    }

    // Initialize tracking on load
    document.addEventListener('DOMContentLoaded', initTracking);
</script>
@endpush
