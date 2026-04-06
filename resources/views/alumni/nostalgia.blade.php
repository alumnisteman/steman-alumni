@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar / Filter -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-funnel-fill me-2"></i>Filter Angkatan</h5>
                    <form action="{{ route('nostalgia.index') }}" method="GET">
                        <select name="angkatan" class="form-select border-0 bg-light rounded-3 mb-3" onchange="this.form.submit()">
                            <option value="">Semua Angkatan</option>
                            @foreach($angkatanList as $thn)
                                <option value="{{ $thn }}" {{ request('angkatan') == $thn ? 'selected' : '' }}>Angkatan {{ $thn }}</option>
                            @endforeach
                        </select>
                    </form>
                    <hr class="opacity-25">
                    <div class="small text-muted">
                        <p><i class="bi bi-info-circle me-1"></i> Bagikan kenangan indah masa sekolah Anda di sini.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Feed -->
        <div class="col-lg-6">
            <!-- Create Post Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Bagikan Cerita Nostalgia</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="{{ route('nostalgia.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <textarea name="content" class="form-control border-0 bg-light rounded-4 p-3" rows="3" placeholder="Apa kenangan yang ingin Anda bagikan hari ini?" required></textarea>
                        </div>
                        
                        <div id="image-preview-container" class="mb-3 d-none">
                            <img id="image-preview" src="#" alt="Preview" class="img-fluid rounded-4 shadow-sm">
                            <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute" style="top: 10px; right: 25px;" onclick="removeImage()">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted mb-1">Tipe Kenangan</label>
                                <select name="type" class="form-select border-0 bg-light rounded-3">
                                    <option value="memory">Kenangan Umum</option>
                                    <option value="story">Cerita Lucu</option>
                                    <option value="event">Throwback Event</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted mb-1">Tag Teman Seangkatan</label>
                                <div class="position-relative">
                                    <input type="text" id="alumni-search" class="form-control border-0 bg-light rounded-3" placeholder="Cari nama teman...">
                                    <div id="search-results" class="position-absolute w-100 shadow-lg rounded-3 bg-white d-none" style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                                        <!-- AJAX Results -->
                                    </div>
                                </div>
                                <div id="selected-tags" class="mt-2 d-flex flex-wrap gap-1">
                                    <!-- Selected Tags -->
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <label class="btn btn-light rounded-pill px-3 mb-0" style="cursor: pointer;">
                                <i class="bi bi-image text-primary me-1"></i> Foto Lama
                                <input type="file" name="image" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </label>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Posting</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Posts List -->
            @forelse($posts as $post)
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden post-card" id="post-{{ $post->id }}">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center">
                        <img src="{{ $post->user->foto_profil ? $post->user->foto_profil : 'https://ui-avatars.com/api/?name='.urlencode($post->user->name) }}" class="rounded-circle me-3" width="45" height="45" style="object-fit: cover;">
                        <div>
                            <h6 class="fw-bold mb-0">{{ $post->user->name }}</h6>
                            <small class="text-muted">
                                {{ $post->user->tahun_lulus ? 'Angkatan ' . $post->user->tahun_lulus : 'Alumni' }} 
                                &bull; {{ $post->created_at->diffForHumans() }}
                                @if($post->type == 'story')
                                    <span class="badge bg-info-subtle text-info rounded-pill ms-1">Cerita Lucu</span>
                                @elseif($post->type == 'event')
                                    <span class="badge bg-warning-subtle text-warning rounded-pill ms-1">Throwback</span>
                                @endif
                            </small>
                        </div>
                    </div>
                    @if(Auth::id() == $post->user_id || in_array(Auth::user()->role, ['admin', 'editor']))
                    <div class="dropdown">
                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                            <li>
                                <form action="{{ route('nostalgia.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Hapus postingan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger small"><i class="bi bi-trash me-2"></i>Hapus Postingan</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>

                <div class="card-body px-4 py-0">
                    <p class="mb-3" style="white-space: pre-line;">{{ $post->content }}</p>
                    
                    @if($post->taggedUsers->isNotEmpty())
                    <div class="mb-3 small">
                        <span class="text-muted"><i class="bi bi-people-fill me-1"></i> Bersama: </span>
                        @foreach($post->taggedUsers as $user)
                            <a href="{{ route('alumni.show', $user->id) }}" class="text-decoration-none fw-bold text-primary">@ {{ $user->name }}</a>{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </div>
                    @endif
                </div>

                @if($post->image_url)
                <div class="post-image-container px-4 mb-3">
                    <img src="{{ $post->image_url }}" class="img-fluid rounded-4 w-100 shadow-sm" alt="Memory Photo" style="max-height: 500px; object-fit: contain; background: #f8f9fa;">
                </div>
                @endif

                <div class="card-footer bg-white border-0 px-4 pb-4">
                    <div class="d-flex align-items-center mb-3">
                        <button class="btn btn-like btn-{{ $post->isLikedBy(Auth::user()) ? 'primary' : 'light' }} rounded-pill px-3 me-3" onclick="toggleLike({{ $post->id }}, this)">
                            <i class="bi bi-heart{{ $post->isLikedBy(Auth::user()) ? '-fill' : '' }} me-1"></i>
                            <span class="like-count">{{ $post->likes_count }}</span> Like
                        </button>
                        <button class="btn btn-light rounded-pill px-3" onclick="document.getElementById('comment-form-{{ $post->id }}').classList.toggle('d-none')">
                            <i class="bi bi-chat-left-text me-1"></i>
                            {{ $post->comments_count }} Komentar
                        </button>
                    </div>

                    <!-- Comment List -->
                    <div class="comments-section mb-3">
                        @foreach($post->comments->take(3) as $comment)
                        <div class="d-flex mb-2">
                            <img src="{{ $comment->user->foto_profil ? $comment->user->foto_profil : 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name) }}" class="rounded-circle me-2" width="28" height="28">
                            <div class="bg-light rounded-3 px-3 py-2 flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold small">{{ $comment->user->name }}</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="small mb-0">{{ $comment->content }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Comment Form -->
                    <form action="{{ route('nostalgia.comment.store', $post->id) }}" method="POST" id="comment-form-{{ $post->id }}" class="d-none">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="content" class="form-control border-0 bg-light rounded-start-pill px-3" placeholder="Tulis komentar..." required>
                            <button class="btn btn-primary rounded-end-pill px-3" type="submit"><i class="bi bi-send"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-camera fs-1 text-muted opacity-25"></i>
                <p class="text-muted mt-3">Belum ada postingan nostalgia. Jadilah yang pertama!</p>
            </div>
            @endforelse

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        </div>

        <!-- Sidebar Right -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <img src="{{ Auth::user()->foto_profil ? Auth::user()->foto_profil : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" class="rounded-circle mb-3 border border-3 border-primary p-1" width="80" height="80">
                    <h6 class="fw-bold mb-0">{{ Auth::user()->name }}</h6>
                    <p class="text-muted small mb-3">Angkatan {{ Auth::user()->tahun_lulus }}</p>
                    <div class="d-flex justify-content-around">
                        <div>
                            <div class="fw-bold">{{ Auth::user()->points }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">POIN</div>
                        </div>
                        <div class="border-start"></div>
                        <div>
                            <div class="fw-bold">{{ Post::where('user_id', Auth::id())->count() }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">POST</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .post-card {
        transition: transform 0.2s;
    }
    .btn-like {
        transition: all 0.2s ease-in-out;
    }
    .btn-like:hover {
        transform: scale(1.05);
    }
    .dark .card { background-color: #1e293b; color: white; }
    .dark .card-header, .dark .card-footer { background-color: #1e293b; }
    .dark .bg-light { background-color: #334155 !important; color: white; }
    .dark .form-control { color: white; }
    .dark .form-control::placeholder { color: #94a3b8; }
</style>
@endpush

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview-container').classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage() {
        document.querySelector('input[name="image"]').value = '';
        document.getElementById('image-preview-container').classList.add('d-none');
    }

    async function toggleLike(postId, btn) {
        try {
            const response = await fetch(`/nostalgia/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const data = await response.json();
            
            const heartIcon = btn.querySelector('i');
            const likeCount = btn.querySelector('.like-count');
            
            if (data.status === 'liked') {
                btn.classList.replace('btn-light', 'btn-primary');
                heartIcon.classList.replace('bi-heart', 'bi-heart-fill');
            } else {
                btn.classList.replace('btn-primary', 'btn-light');
                heartIcon.classList.replace('bi-heart-fill', 'bi-heart');
            }
            likeCount.innerText = data.likes_count;
        } catch (error) {
            console.error('Error liking post:', error);
        }
    }

    // Tagging logic
    const searchInput = document.getElementById('alumni-search');
    const resultsDiv = document.getElementById('search-results');
    const selectedTagsDiv = document.getElementById('selected-tags');
    let selectedAlumni = [];

    searchInput.addEventListener('input', async function() {
        const q = this.value;
        if (q.length < 2) {
            resultsDiv.classList.add('d-none');
            return;
        }

        const response = await fetch(`/api/alumni/search?q=${q}`);
        const alumni = await response.json();

        if (alumni.length > 0) {
            resultsDiv.innerHTML = '';
            alumni.forEach(person => {
                if (selectedAlumni.some(a => a.id === person.id)) return;
                
                const item = document.createElement('div');
                item.className = 'p-2 border-bottom small hover-bg-light cursor-pointer';
                item.innerHTML = `<strong>${person.name}</strong> <span class="text-muted">(Agt ${person.tahun_lulus})</span>`;
                item.onclick = () => selectAlumni(person);
                resultsDiv.appendChild(item);
            });
            resultsDiv.classList.remove('d-none');
        } else {
            resultsDiv.classList.add('d-none');
        }
    });

    function selectAlumni(person) {
        selectedAlumni.push(person);
        renderTags();
        searchInput.value = '';
        resultsDiv.classList.add('d-none');
    }

    function removeAlumni(id) {
        selectedAlumni = selectedAlumni.filter(a => a.id !== id);
        renderTags();
    }

    function renderTags() {
        selectedTagsDiv.innerHTML = '';
        selectedAlumni.forEach(person => {
            const badge = document.createElement('span');
            badge.className = 'badge bg-primary rounded-pill px-2 py-1 small';
            badge.innerHTML = `@${person.name} <i class="bi bi-x ms-1 cursor-pointer" onclick="removeAlumni(${person.id})"></i>`;
            
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'tags[]';
            hiddenInput.value = person.id;
            
            badge.appendChild(hiddenInput);
            selectedTagsDiv.appendChild(badge);
        });
    }

    // Close results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.classList.add('d-none');
        }
    });
</script>
<style>
    .hover-bg-light:hover { background-color: #f8f9fa; }
    .cursor-pointer { cursor: pointer; }
    .dark .hover-bg-light:hover { background-color: #334155; }
</style>
@endpush
@endsection
