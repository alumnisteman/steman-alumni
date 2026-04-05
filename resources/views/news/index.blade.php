@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row mb-5 justify-content-center">
        <div class="col-md-6">
            <form action="/news" method="GET">
                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                    <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-0 py-3" placeholder="Cari berita atau artikel..." value="{{ request('search') }}">
                    <button class="btn btn-warning px-4 fw-bold" type="submit">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center mb-5">
        <h2 class="section-heading d-inline-block">NEWS & UPDATES</h2>
        <p class="text-muted mt-2">Kumpulan berita, pengumuman, dan cerita inspiratif dari {{ setting('site_name', 'IKATAN ALUMNI SMKN 2') }}.</p>
    </div>

    <div class="row g-4">
        @forelse($news as $item)
            <div class="col-md-4">
                <div class="news-card card h-100">
                    @if($item->thumbnail)
                        <img src="{{ $item->thumbnail }}" class="card-img-top" alt="{{ $item->title }}" loading="lazy">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="height: 200px;">
                            <i class="bi bi-image display-4"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="date-tag mb-2">{{ $item->created_at->format('d M Y') }}</div>
                        <h5 class="fw-bold mb-3"><a href="/news/{{ $item->slug }}" class="text-dark text-decoration-none">{{ Str::limit($item->title, 100) }}</a></h5>
                        <p class="text-muted small mb-0">{{ Str::limit(strip_tags($item->content), 100) }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-journal-x display-1 text-light"></i>
                <p class="lead mt-3">Belum ada berita yang diterbitkan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $news->links() }}
    </div>
</div>
@endsection
