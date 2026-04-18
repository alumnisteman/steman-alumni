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
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="feed-container">
        
        {{-- STORY BAR --}}
        <div class="d-flex gap-3 overflow-x-auto pb-3 mb-4 no-scrollbar" style="scroll-snap-type: x mandatory;">
            {{-- ADD STORY --}}
            <div class="flex-shrink-0 text-center" style="width: 72px; scroll-snap-align: start;" data-bs-toggle="modal" data-bs-target="#createStoryModal">
                <div class="position-relative mb-1">
                    <img src="{{ auth()->user()->profile_picture_url }}" class="rounded-circle border border-2 border-white shadow-sm" style="width: 64px; height: 64px; object-fit: cover;">
                    <div class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                        <i class="bi bi-plus-lg text-white" style="font-size: 0.7rem;"></i>
                    </div>
                </div>
                <div class="small fw-bold text-muted" style="font-size: 0.7rem;">Cerita Anda</div>
            </div>

            @php
                $activeStories = \App\Models\Story::active()->with('user')->get()->groupBy('user_id');
            @endphp

            @foreach($activeStories as $userId => $userStories)
                @php 
                    $storyUser = $userStories->first()->user; 
                    $activeNote = $userStories->where('type', 'note')->first();
                    $hasImageStory = $userStories->where('type', 'image')->count() > 0;
                @endphp
                <div class="flex-shrink-0 text-center position-relative" style="width: 72px; scroll-snap-align: start; cursor: pointer;" onclick="{{ $hasImageStory ? 'viewStory('.$userId.')' : '' }}">
                    {{-- Note Bubble --}}
                    @if($activeNote)
                        <div class="position-absolute top-0 start-50 translate-middle-x bg-white text-dark rounded-pill px-2 py-1 shadow-sm border small fw-bold text-truncate" style="max-width: 80px; font-size: 0.6rem; z-index: 10; margin-top: -5px;">
                            {{ $activeNote->content }}
                        </div>
                    @endif

                    <div class="p-1 rounded-circle mb-1 {{ $hasImageStory ? 'bg-gradient-story' : '' }}">
                        <img src="{{ $storyUser->profile_picture_url }}" class="rounded-circle border border-2 border-white shadow-sm" style="width: 58px; height: 58px; object-fit: cover;">
                    </div>
                    <div class="small fw-bold text-truncate mx-auto" style="font-size: 0.7rem; width: 64px;">{{ $storyUser->name }}</div>
                </div>
            @endforeach
        </div>

        {{-- QUICK ACTIONS (Gen Z) --}}
        <div class="row g-2 mb-4">
            <div class="col-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary text-white h-100 cursor-pointer" data-bs-toggle="modal" data-bs-target="#createNoteModal">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <i class="bi bi-chat-dots fs-4"></i>
                    </div>
                    <div class="fw-bold" style="font-size: 0.75rem;">Notes</div>
                    <div class="text-white-50 mt-1" style="font-size: 0.65rem;">Status Pendek</div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-dark text-white h-100 cursor-pointer" onclick="openConfession()">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <i class="bi bi-mask fs-4 text-warning"></i>
                    </div>
                    <div class="fw-bold" style="font-size: 0.75rem;">Curhat</div>
                    <div class="text-white-50 mt-1" style="font-size: 0.65rem;">Anonim</div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-success text-white h-100 cursor-pointer" onclick="openHelpRequest()">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <i class="bi bi-megaphone fs-4"></i>
                    </div>
                    <div class="fw-bold" style="font-size: 0.75rem;">Help</div>
                    <div class="text-white-50 mt-1" style="font-size: 0.65rem;">Bantuan</div>
                </div>
            </div>
        </div>

        {{-- FEED LIST --}}
        <div id="feed-list">
            @forelse($posts as $post)
                @php 
                    $isAnon = $post->is_anonymous;
                    $displayName = $isAnon ? 'Alumni Anonim' : $post->user->name;
                    $displayAvatar = $isAnon ? 'https://ui-avatars.com/api/?name=A&background=1e293b&color=fff' : $post->user->profile_picture_url;
                @endphp
                <div class="card post-card mb-4 shadow-sm border-0 overflow-hidden {{ $post->type === 'help_request' ? 'border-start border-4 border-success' : '' }} {{ $isAnon ? 'bg-dark bg-opacity-10' : '' }}">
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex gap-3 align-items-center">
                                @if(!$isAnon)
                                <a href="{{ route('alumni.show', $post->user) }}">
                                    <img src="{{ $displayAvatar }}" class="post-avatar shadow-sm">
                                </a>
                                @else
                                <img src="{{ $displayAvatar }}" class="post-avatar shadow-sm">
                                @endif
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $displayName }}</h6>
                                    <small class="text-muted">
                                        @if($isAnon)
                                            <i class="bi bi-mask me-1"></i> Anonymous Confession
                                        @else
                                            {{ $post->user->major }} '{{ $post->user->graduation_year }}
                                        @endif
                                        &bull; {{ $post->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-bookmark me-2"></i>Simpan</a></li>
                                    @if(auth()->id() === $post->user_id)
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i>Hapus</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="post-content mb-3" style="white-space: pre-wrap; line-height: 1.6;">{!! e($post->content) !!}</div>

                        @if($post->image_url)
                            <img src="{{ $post->image_url }}" class="post-image mb-3 shadow-sm">
                        @endif

                        <hr class="opacity-10 my-2">

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-1">
                                <button class="interaction-btn {{ $post->isLikedBy(auth()->user()) ? 'active' : '' }}" onclick="toggleLike(this, {{ $post->id }})">
                                    <i class="bi bi-heart{{ $post->isLikedBy(auth()->user()) ? '-fill' : '' }} me-1"></i>
                                    <span class="count">{{ $post->likes_count }}</span>
                                </button>
                                <button class="interaction-btn" onclick="showComments({{ $post->id }})">
                                    <i class="bi bi-chat-text me-1"></i>
                                    <span class="count">{{ $post->comments_count }}</span>
                                </button>
                            </div>
                            <button class="interaction-btn"><i class="bi bi-share"></i></button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="display-1 text-muted opacity-25 mb-4"><i class="bi bi-newspaper"></i></div>
                    <h5 class="fw-bold">Belum ada postingan</h5>
                    <p class="text-muted">Ikuti alumni lain untuk melihat aktivitas mereka di sini.</p>
                    <a href="{{ route('alumni.index') }}" class="btn btn-success rounded-pill px-4 mt-2">Cari Alumni</a>
                </div>
            @endforelse
        </div>

    </div>
</div>

{{-- CREATE POST MODAL --}}
<div class="modal fade" id="createPostModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Buat Postingan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('feed.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ auth()->user()->profile_picture_url }}" class="post-avatar">
                        <div>
                            <div class="fw-bold">{{ auth()->user()->name }}</div>
                            <select name="visibility" class="form-select form-select-sm border-0 bg-light rounded-pill px-3 mt-1" style="font-size: 0.75rem; width: auto;">
                                <option value="public">🌍 Publik</option>
                                <option value="friends">👥 Alumni Saja</option>
                            </select>
                        </div>
                    </div>
                    <textarea name="content" class="form-control border-0 bg-transparent p-0 fs-5 mb-3" rows="5" placeholder="Apa yang sedang terjadi?" required style="box-shadow: none;"></textarea>
                    
                    <div id="image-preview-container" class="position-relative d-none mb-3">
                        <img id="image-preview" src="#" class="w-100 rounded-3 shadow-sm">
                        <button type="button" class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-2" onclick="removeImage()">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-muted small">Tambahkan ke postingan</span>
                        <div class="d-flex gap-2">
                            <label class="btn btn-light rounded-circle p-2 cursor-pointer mb-0">
                                <i class="bi bi-image text-success fs-5"></i>
                                <input type="file" name="image" id="post-image-input" class="d-none" accept="image/*">
                            </label>
                            <button type="button" class="btn btn-light rounded-circle p-2"><i class="bi bi-emoji-smile text-warning fs-5"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-success w-100 rounded-3 fw-bold py-2">POSTING</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CREATE NOTE MODAL --}}
<div class="modal fade" id="createNoteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Bagikan Catatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('stories.note') }}" method="POST">
                @csrf
                <div class="modal-body text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ auth()->user()->profile_picture_url }}" class="rounded-circle border border-2 border-white shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                        <div class="position-absolute top-0 start-50 translate-middle-x bg-white text-dark rounded-pill px-3 py-2 shadow border small fw-bold" style="margin-top: -15px; min-width: 120px;">
                            <input type="text" name="content" class="form-control form-control-sm border-0 bg-transparent text-center p-0" placeholder="Apa yang Anda pikirkan?" maxlength="60" required autofocus>
                        </div>
                    </div>
                    <p class="small text-muted">Teman-teman dapat melihat catatan Anda selama 24 jam.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">BAGIKAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CREATE STORY MODAL --}}
<div class="modal fade" id="createStoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Posting Story</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="story-preview-container" class="position-relative mb-3 bg-light rounded-4 d-flex align-items-center justify-content-center" style="min-height: 300px;">
                        <img id="story-preview" src="#" class="w-100 rounded-4 d-none">
                        <label for="story-input" class="btn btn-success rounded-pill px-4 py-2" id="story-label">
                            <i class="bi bi-camera-fill me-2"></i>Ambil Gambar
                        </label>
                        <input type="file" name="image" id="story-input" class="d-none" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Atau Bagikan Lagu Spotify</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-spotify text-success"></i></span>
                            <input type="url" name="spotify_url" id="spotify-input" class="form-control border-start-0 ps-0 shadow-none" placeholder="Paste link lagu Spotify di sini...">
                        </div>
                    </div>

                    <input type="text" name="caption" class="form-control border-0 bg-light rounded-3 py-2 mb-2" placeholder="Tambahkan keterangan (opsional)..." maxlength="100">
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-success w-100 rounded-3 fw-bold py-2">BAGIKAN CERITA</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- STORY VIEWER MODAL --}}
<div class="modal fade" id="storyViewerModal" tabindex="-1" style="background: #000; z-index: 3000;">
    <div class="modal-dialog modal-fullscreen m-0">
        <div class="modal-content border-0 bg-dark text-white rounded-0">
            <div class="modal-body p-0 position-relative d-flex align-items-center justify-content-center bg-black">
                {{-- Top Overlay --}}
                <div class="position-absolute top-0 start-0 w-100 p-3" style="z-index: 10; background: linear-gradient(rgba(0,0,0,0.6), transparent);">
                    <div class="progress mb-3" style="height: 2px; background: rgba(255,255,255,0.2);">
                        <div id="story-progress" class="progress-bar bg-white" style="width: 0%; transition: none;"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <img id="story-user-avatar" src="" class="rounded-circle border border-1 border-white shadow-sm" style="width: 36px; height: 36px; object-fit: cover;">
                            <div>
                                <div id="story-user-name" class="fw-bold small" style="text-shadow: 0 1px 2px rgba(0,0,0,0.5);"></div>
                                <div id="story-time" class="opacity-75" style="font-size: 0.65rem;"></div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <img id="story-display-img" src="" class="img-fluid" style="max-height: 100vh; width: auto; object-fit: contain;">
                
                <div id="story-spotify-container" class="w-100 px-4 d-none position-absolute top-50 start-50 translate-middle" style="max-width: 400px; z-index: 15;">
                    <iframe id="story-spotify-iframe" src="" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy" class="rounded-4 shadow-lg"></iframe>
                </div>

                {{-- Bottom Overlay --}}
                <div class="position-absolute bottom-0 start-0 w-100 p-4 text-center" style="z-index: 10; background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                    <p id="story-display-caption" class="mb-3 fw-bold px-3" style="text-shadow: 0 1px 4px rgba(0,0,0,0.8); font-size: 1.1rem;"></p>
                    
                    {{-- Share Actions --}}
                    <div class="d-flex justify-content-center gap-3">
                        <button class="btn btn-dark btn-sm rounded-circle border border-secondary shadow" style="width: 40px; height: 40px;" onclick="shareStory('facebook')">
                            <i class="bi bi-facebook text-primary"></i>
                        </button>
                        <button class="btn btn-dark btn-sm rounded-circle border border-secondary shadow" style="width: 40px; height: 40px;" onclick="shareStory('whatsapp')">
                            <i class="bi bi-whatsapp text-success"></i>
                        </button>
                        <button class="btn btn-dark btn-sm rounded-circle border border-secondary shadow" style="width: 40px; height: 40px;" onclick="shareStory('native')">
                            <i class="bi bi-share-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Story Preview
    const storyInp = document.getElementById('story-input');
    const storyPre = document.getElementById('story-preview');
    const storyLab = document.getElementById('story-label');

    storyInp.onchange = evt => {
        const [file] = storyInp.files;
        if (file) {
            storyPre.src = URL.createObjectURL(file);
            storyPre.classList.remove('d-none');
            storyLab.classList.add('d-none');
        }
    }

    // Story Viewer Logic
    function viewStory(userId) {
        if (window.storyInterval) clearInterval(window.storyInterval);
        
        const modalEl = document.getElementById('storyViewerModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        
        // Reset UI immediately to avoid flicker from previous story
        document.getElementById('story-display-img').src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'; // Blank
        document.getElementById('story-progress').style.width = '0%';
        
        fetch(`/api/stories/active`)
            .then(r => r.json())
            .then(data => {
                const userStories = data[userId];
                if (!userStories || userStories.length === 0) return;
                
                const story = userStories[0];
                modalEl.dataset.currentStoryId = story.id;
                
                document.getElementById('story-user-avatar').src = story.user.profile_picture_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(story.user.name);
                document.getElementById('story-user-name').innerText = story.user.name;
                document.getElementById('story-time').innerText = story.created_at_human || 'Baru saja';
                
                const imgDisplay = document.getElementById('story-display-img');
                const spotifyContainer = document.getElementById('story-spotify-container');
                const spotifyIframe = document.getElementById('story-spotify-iframe');
                
                if (story.type === 'spotify' || story.spotify_url) {
                    imgDisplay.classList.add('d-none');
                    spotifyContainer.classList.remove('d-none');
                    spotifyIframe.src = story.spotify_url;
                } else {
                    spotifyContainer.classList.add('d-none');
                    imgDisplay.classList.remove('d-none');
                    imgDisplay.src = story.image_url;
                }

                document.getElementById('story-display-caption').innerText = story.caption || '';
                
                modal.show();

                // Progress bar animation
                const progressBar = document.getElementById('story-progress');
                let progress = 0;
                
                window.storyInterval = setInterval(() => {
                    progress += 1;
                    progressBar.style.width = progress + '%';
                    if (progress >= 100) {
                        clearInterval(window.storyInterval);
                        modal.hide();
                    }
                }, 50);

                modalEl.addEventListener('hidden.bs.modal', () => {
                    clearInterval(window.storyInterval);
                }, { once: true });
            });
    }

    // Share Story Logic
    function shareStory(platform) {
        const storyId = document.getElementById('storyViewerModal').dataset.currentStoryId;
        if (!storyId) return;

        const shareUrl = `${window.location.origin}/stories/${storyId}`;
        const title = 'Lihat Story Alumni ini!';

        if (platform === 'native' && navigator.share) {
            navigator.share({
                title: title,
                url: shareUrl
            }).catch(console.error);
        } else if (platform === 'facebook') {
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`, '_blank');
        } else if (platform === 'whatsapp') {
            window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(title + ' ' + shareUrl)}`, '_blank');
        } else {
            // Fallback copy to clipboard
            navigator.clipboard.writeText(shareUrl).then(() => {
                alert('Tautan disalin ke clipboard! Anda bisa membagikannya ke Instagram atau TikTok.');
            });
        }
    }

    // Quick Actions
    function openConfession() {
        const modal = new bootstrap.Modal(document.getElementById('createPostModal'));
        document.querySelector('#createPostModal .modal-title').innerText = '🎭 Anonymous Confession';
        document.querySelector('#createPostModal select[name="visibility"]').value = 'public';
        document.querySelector('#createPostModal select[name="visibility"]').disabled = true;
        
        // Add hidden input for anonymity
        let anonInput = document.getElementById('is_anonymous_hidden');
        if (!anonInput) {
            anonInput = document.createElement('input');
            anonInput.type = 'hidden';
            anonInput.name = 'is_anonymous';
            anonInput.id = 'is_anonymous_hidden';
            document.querySelector('#createPostModal form').appendChild(anonInput);
        }
        anonInput.value = '1';
        
        modal.show();
    }

    function openHelpRequest() {
        const modal = new bootstrap.Modal(document.getElementById('createPostModal'));
        document.querySelector('#createPostModal .modal-title').innerText = '📢 One Tap Help';
        
        let typeInput = document.getElementById('type_hidden');
        if (!typeInput) {
            typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = 'type';
            typeInput.id = 'type_hidden';
            document.querySelector('#createPostModal form').appendChild(typeInput);
        }
        typeInput.value = 'help_request';
        
        modal.show();
    }

    // Like logic (Real API)
    function toggleLike(btn, postId) {
        const icon = btn.querySelector('i');
        const count = btn.querySelector('.count');
        
        fetch(`/nostalgia/${postId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'liked') {
                icon.classList.replace('bi-heart', 'bi-heart-fill');
                btn.classList.add('active');
            } else {
                icon.classList.replace('bi-heart-fill', 'bi-heart');
                btn.classList.remove('active');
            }
            count.innerText = data.likes_count;
            
            // Haptic feedback if available
            if (window.navigator.vibrate) window.navigator.vibrate(10);
        });
    }

    function showComments(postId) {
        // Simple redirect to nostalgia for now or a modal can be added later
        window.location.href = `/nostalgia#post-${postId}`;
    }
</script>
@endpush
