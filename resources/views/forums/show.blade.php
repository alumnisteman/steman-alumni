@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <a href="{{ route('forums.index') }}" class="text-decoration-none text-muted fw-bold">
            <i class="bi bi-arrow-left me-2"></i> KEMBALI KE FORUM
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Thread Main -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ $forum->user?->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($forum->user?->name ?? 'User') }}" class="rounded-circle me-3" width="50" height="50">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark fs-5">{{ $forum->user?->name ?? 'User Terhapus' }}</h6>
                            <span class="text-muted small">{{ optional($forum->created_at)->format('d M Y - H:i') ?? '-' }}</span>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-4">{{ $forum->title }}</h2>
                    <div class="text-dark opacity-90 mb-5 fs-5 leading-relaxed" style="white-space: pre-line;">
                        {{ $forum->content }}
                    </div>
                    <hr class="opacity-10 mb-5">

                    <!-- Comments Section -->
                    <h5 class="fw-bold mb-4">Diskusi ({{ $forum->comments_count }})</h5>
                    
                    <div class="comments-list mb-5">
                        @foreach($forum->comments as $comment)
                            <div class="d-flex mb-4">
                                <img src="{{ $comment->user?->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user?->name ?? 'User') }}" class="rounded-circle me-3 shadow-sm" width="40" height="40">
                                <div class="bg-light p-3 rounded-4 w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="fw-bold mb-0 text-dark small">{{ $comment->user?->name ?? 'User Terhapus' }}</h6>
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ optional($comment->created_at)->diffForHumans() ?? '-' }}</span>
                                    </div>
                                    <div class="small text-dark opacity-80">
                                        {{ $comment->konten }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Post Comment -->
                    <form action="{{ route('forums.comments.store', $forum->id) }}" method="POST">
                        @csrf
                        <div class="bg-white p-3 rounded-4 border">
                            <textarea name="konten" class="form-control border-0 shadow-none mb-3" rows="3" placeholder="Tuliskan tanggapan Anda..." required></textarea>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Kirim Balasan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-primary text-white">
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i> Aturan Forum</h5>
                <ul class="small list-unstyled mb-0">
                    <li class="mb-2"><i class="bi bi-check2-circle me-2"></i> Gunakan bahasa yang sopan.</li>
                    <li class="mb-2"><i class="bi bi-check2-circle me-2"></i> Saling menghargai pendapat.</li>
                    <li class="mb-2"><i class="bi bi-check2-circle me-2"></i> Hindari spam atau promosi berlebih.</li>
                    <li><i class="bi bi-check2-circle me-2"></i> Berbagi solusi itu hebat!</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
