@extends('layouts.app')
@section('meta')
    <meta property="og:title" content="{{ $item->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($item->content), 160) }}">
    <meta property="og:image" content="{{ $item->thumbnail ? (Str::startsWith($item->thumbnail, 'http') ? $item->thumbnail : asset($item->thumbnail)) : asset('/images/hero_iluni.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
@endsection
@section('content')
<div class="news-detail-wrapper py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/" class="text-dark">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="/news" class="text-dark">Berita</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($item->title, 30) }}</li>
                    </ol>
                </nav>

                <div class="news-header mb-5">
                    <div class="badge bg-primary rounded-0 mb-3 px-3 py-2 text-uppercase fw-bold" style="font-size: 0.7rem;">{{ $item->category }}</div>
                    <h1 class="display-5 fw-black mb-3">{{ $item->title }}</h1>
                    <div class="news-meta d-flex align-items-center text-muted small">
                        <div class="me-4"><i class="bi bi-clock me-1"></i> {{ $item->created_at->translatedFormat('d F Y') }}</div>
                        <div class="me-4"><i class="bi bi-person me-1"></i> Admin</div>
                    </div>
                </div>

                @if($item->thumbnail)
                    <img src="{{ Str::startsWith($item->thumbnail, 'http') ? $item->thumbnail : asset($item->thumbnail) }}" class="img-fluid w-100 rounded-0 mb-5 shadow-sm" alt="{{ $item->title }}">
                @endif

                <div class="news-content fs-5 lh-lg mb-4">
                    {!! $item->content !!}
                </div>

                <!-- Content Ad Slot -->
                <div class="my-5">
                    <x-ad-slot position="content" aspectRatio="1280/200" mobileAspectRatio="400/150" />
                </div>

                <hr class="my-5 opacity-25">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
                    <a href="/news" class="btn btn-outline-dark rounded-0 px-4">KEMBALI KE DAFTAR BERITA</a>
                    <div class="share-buttons mt-4 mt-md-0">
                        <span class="small fw-bold text-muted me-3">BAGIKAN:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="text-dark me-3" title="Bagikan ke Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($item->title) }}" target="_blank" class="text-dark me-3" title="Bagikan ke X"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://wa.me/?text={{ urlencode($item->title . ' - ' . url()->current()) }}" target="_blank" class="text-dark" title="Bagikan ke WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px; z-index: 1;">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <h6 class="fw-bold mb-3 text-uppercase small text-muted">Informasi Sponsor</h6>
                        <x-ad-slot position="sidebar" aspectRatio="1/1" />
                    </div>
                    
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h6 class="fw-bold mb-3 text-uppercase small text-muted">Berita Populer</h6>
                        <!-- Placeholder for related/popular news if needed -->
                        <p class="text-muted small">Cek berita menarik lainnya di direktori kami.</p>
                        <a href="/news" class="btn btn-link text-primary p-0 text-decoration-none fw-bold small">Cek Semua <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // AI Feed Behavior Tracking
    let trackStart = Date.now();

    window.addEventListener("beforeunload", () => {
        let duration = Math.floor((Date.now() - trackStart) / 1000);

        if (duration > 2) { // Only track if they stayed more than 2 seconds
            let data = new FormData();
            data.append('type', 'view');
            data.append('content_id', '{{ $item->id }}');
            data.append('content_type', 'news');
            data.append('keyword', '{{ strtolower($item->category) }}');
            data.append('duration', duration);
            data.append('_token', '{{ csrf_token() }}');

            navigator.sendBeacon('{{ route("api.track") }}', data);
        }
    });
</script>
@endpush
