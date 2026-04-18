@extends('layouts.app')

@section('title', 'Alumni Feed - Jalin Silaturahmi')

@push('styles')
<style>
    .feed-container { max-width: 680px; margin: 0 auto; }
    .post-card {
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 16px;
        transition: transform 0.2s;
    }
    .post-card:hover { transform: translateY(-2px); }
    .post-avatar { width: 48px; height: 48px; object-fit: cover; border-radius: 12px; }
    .post-image { border-radius: 12px; max-height: 500px; object-fit: cover; width: 100%; }
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
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="feed-container">
        
        {{-- CREATE POST TRIGGER (Desktop/Tablet) --}}
        <div class="create-post-trigger d-none d-md-flex align-items-center gap-3 mb-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createPostModal">
            <img src="{{ auth()->user()->profile_picture_url }}" class="post-avatar">
            <div class="bg-light rounded-pill flex-grow-1 px-4 py-2 text-muted">
                Apa yang sedang Anda pikirkan, {{ auth()->user()->name }}?
            </div>
            <button class="btn btn-success rounded-circle p-2"><i class="bi bi-camera-fill"></i></button>
        </div>

        {{-- FEED LIST --}}
        <div id="feed-list">
            @forelse($posts as $post)
                <div class="card post-card mb-4 shadow-sm border-0 overflow-hidden">
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex gap-3 align-items-center">
                                <a href="{{ route('alumni.show', $post->user) }}">
                                    <img src="{{ $post->user->profile_picture_url }}" class="post-avatar shadow-sm">
                                </a>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $post->user->name }}</h6>
                                    <small class="text-muted">
                                        {{ $post->user->major }} '{{ $post->user->graduation_year }} &bull; {{ $post->created_at->diffForHumans() }}
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

@endsection

@push('scripts')
<script>
    // Image Preview logic
    const imgInp = document.getElementById('post-image-input');
    const imgPre = document.getElementById('image-preview');
    const preCon = document.getElementById('image-preview-container');

    imgInp.onchange = evt => {
        const [file] = imgInp.files;
        if (file) {
            imgPre.src = URL.createObjectURL(file);
            preCon.classList.remove('d-none');
        }
    }

    function removeImage() {
        imgInp.value = "";
        preCon.classList.add('d-none');
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
