@foreach($posts as $post)
    @if(!$post || (!$post->is_anonymous && !$post->user)) @continue @endif
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden post-card" data-post-id="{{ $post->id }}">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $post->is_anonymous ? 'https://ui-avatars.com/api/?name=A&background=random' : $post->user->profile_picture_url }}" 
                         class="rounded-circle border border-2 border-white shadow-sm" 
                         style="width: 45px; height: 45px; object-fit: cover;">
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $post->is_anonymous ? 'Anonymous Alumni' : $post->user->name }}</h6>
                        <div class="text-muted" style="font-size: 0.7rem;">
                            {{ $post->created_at->diffForHumans() }} 
                            @if($post->type)
                                <span class="badge bg-light text-dark ms-1 border">{{ ucfirst($post->type) }}</span>
                            @endif
                            @if(($post->likes_count ?? 0) >= 10 || ($post->comments_count ?? 0) >= 5)
                                <span class="badge bg-danger ms-1 animate__animated animate__pulse animate__infinite"><i class="bi bi-fire"></i> LAGI RAME</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-flag me-2"></i> Laporkan</a></li>
                        @if(auth()->id() === $post->user_id || auth()->user()?->role === 'admin')
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i> Hapus</a></li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="post-content mb-3" style="font-size: 0.95rem; line-height: 1.6;">
                {!! nl2br(e($post->content)) !!}
            </div>

            @if($post->image_url)
                <div class="post-image mb-3 mx-n3">
                    <img src="{{ $post->image_url }}" class="w-100 img-fluid" style="max-height: 500px; object-fit: cover;" loading="lazy">
                </div>
            @endif

            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                <div class="d-flex gap-4">
                    <button class="btn btn-link text-decoration-none p-0 d-flex align-items-center gap-1 {{ $post->isLikedBy(auth()->user()) ? 'text-danger' : 'text-dark' }}" 
                            onclick="toggleLike({{ $post->id }})" id="like-btn-{{ $post->id }}">
                        <i class="bi {{ $post->isLikedBy(auth()->user()) ? 'bi-heart-fill' : 'bi-heart' }} fs-5"></i>
                        <span class="small fw-bold">{{ $post->likes_count ?? 0 }}</span>
                    </button>
                    <button class="btn btn-link text-decoration-none p-0 d-flex align-items-center gap-1 text-dark" 
                            onclick="focusComment({{ $post->id }})">
                        <i class="bi bi-chat fs-5"></i>
                        <span class="small fw-bold">{{ $post->comments_count ?? 0 }}</span>
                    </button>
                </div>
                <button class="btn btn-link text-decoration-none p-0 text-dark" onclick="sharePost({{ $post->id }})">
                    <i class="bi bi-share fs-5"></i>
                </button>
            </div>
        </div>
    </div>
@endforeach
