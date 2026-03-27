@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold display-5">FORUM ALUMNI</h1>
            <p class="text-muted">Ruang diskusi, tanya jawab, dan berbagi informasi antar alumni.</p>
        </div>
        <button class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newForumModal">
            <i class="bi bi-plus-circle me-2"></i> Mulai Diskusi
        </button>
    </div>

    <div class="row g-4">
        @forelse($forums as $forum)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-all shadow-hover p-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $forum->user->foto_profil ?? 'https://ui-avatars.com/api/?name='.urlencode($forum->user->name) }}" class="rounded-circle me-3" width="40" height="40">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $forum->user->name }}</h6>
                                <span class="text-muted small">{{ $forum->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-3">{{ $forum->judul_diskusi }}</h5>
                        <p class="text-muted small mb-4 text-truncate-2">
                            {{ Str::limit($forum->deskripsi_masalah, 150) }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                <i class="bi bi-chat-left-text me-2"></i> {{ $forum->jumlah_komentar }} Komentar
                            </span>
                            <a href="{{ route('forums.show', $forum->id) }}" class="btn btn-outline-primary rounded-pill fw-bold btn-sm px-4">
                                Ikut Diskusi <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-chat-dots display-1 text-light"></i>
                <p class="lead mt-3 text-muted">Belum ada diskusi di forum ini. Jadilah yang pertama memulai!</p>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-5">
        {{ $forums->links() }}
    </div>
</div>

<!-- Modal New Forum -->
<div class="modal fade" id="newForumModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Mulai Diskusi Baru</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('forums.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small uppercase tracking-wider text-muted">Judul Diskusi</label>
                        <input type="text" name="judul_diskusi" class="form-control rounded-3 py-2" placeholder="Apa yang ingin Anda bahas?" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small uppercase tracking-wider text-muted">Deskripsi / Pertanyaan</label>
                        <textarea name="deskripsi_masalah" class="form-control rounded-3" rows="5" placeholder="Berikan detail diskusi Anda..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Kirim Diskusi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
