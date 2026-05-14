@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row mb-5 justify-content-center">
        <div class="col-md-6">
            <form action="{{ route('news.index') }}" method="GET">
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
        <p class="text-muted mt-2">Kumpulan berita, pengumuman, dan cerita inspiratif dari {{ setting('site_name', 'Forum Silaturahmi Alumni Steman Ternate') }}.</p>
    </div>

    <!-- 🔥 TRENDING NOW SECTION -->
    @if(isset($trending) && $trending->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center flex-wrap gap-2 p-3 bg-dark rounded-4 shadow-sm" style="border: 1px solid rgba(255,255,255,0.1);">
                <span class="text-warning fw-bold small me-2"><i class="bi bi-fire"></i> TRENDING:</span>
                @foreach($trending as $t)
                    <a href="{{ route('news.index', ['search' => $t]) }}" class="badge bg-secondary bg-opacity-25 text-light text-decoration-none border border-secondary border-opacity-25 rounded-pill px-3 py-2" style="transition: 0.3s;">
                        #{{ $t }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse($news as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="news-card card h-100">
                            @if($item->thumbnail)
                                <img src="{{ Str::startsWith($item->thumbnail, 'http') ? $item->thumbnail : asset($item->thumbnail) }}" class="card-img-top" alt="{{ $item->title }}" loading="lazy">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="height: 200px;">
                                    <i class="bi bi-image display-4"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <div class="date-tag mb-2">{{ $item->created_at->format('d M Y') }}</div>
                                <h5 class="fw-bold mb-3"><a href="{{ route('news.show', $item->slug) }}" class="text-dark text-decoration-none">{{ Str::limit($item->title, 100) }}</a></h5>
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
        </div>
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px; z-index: 1;">
                
                <!-- AUTO VIRAL FEED AGGREGATOR -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3 text-uppercase small text-muted border-bottom pb-2">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i> TOP HEADLINES
                    </h5>
                    
                    <div class="d-flex flex-column gap-3">
                        @if(isset($aggregatedNews) && $aggregatedNews->count() > 0)
                            @foreach($aggregatedNews->take(5) as $n)
                            <div class="bg-dark p-3 rounded-4" style="border: 1px solid rgba(255,255,255,0.1);">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-primary bg-opacity-25 text-primary" style="font-size: 0.65rem;">
                                        <i class="bi bi-newspaper"></i> {{ $n['source'] }}
                                    </span>
                                    <small class="text-muted" style="font-size: 0.65rem;">
                                        {{ \Carbon\Carbon::parse($n['published_at'])->diffForHumans() }}
                                    </small>
                                </div>
                                <h6 class="fw-bold text-white mb-2" style="font-size: 0.9rem; line-height: 1.4;">
                                    {{ $n['title'] }}
                                </h6>
                                <p class="text-muted small mb-0" style="font-size: 0.75rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ Str::limit($n['desc'], 80) }}
                                </p>
                                <a href="{{ $n['url'] }}" target="_blank" class="text-info text-decoration-none small mt-2 d-inline-block fw-bold" style="font-size: 0.75rem;">
                                    Baca Selengkapnya <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-3 small">Belum ada headline terkini.</div>
                        @endif
                    </div>
                </div>

                <h6 class="fw-bold mb-3 text-uppercase small text-muted mt-4 border-bottom pb-2">Sponsor</h6>
                <x-ad-slot position="sidebar" aspectRatio="1/1" />
            </div>
        </div>
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $news->links() }}
    </div>
</div>
@endsection
