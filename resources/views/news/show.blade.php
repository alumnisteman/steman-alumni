@extends('layouts.app')
@section('meta')
    <meta property="og:title" content="{{ $item->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($item->content), 160) }}">
    <meta property="og:image" content="{{ $item->thumbnail ? (Str::startsWith($item->thumbnail, 'http') ? $item->thumbnail : asset($item->thumbnail)) : asset('/images/hero_iluni.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
@endsection

@section('styles')
<style>
    :root {
        --news-primary: #0f172a;
        --news-accent: #059669;
        --news-text: #334155;
        --news-bg: #fff;
    }
    .news-detail-wrapper {
        background-color: var(--news-bg);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    .news-header h1 {
        line-height: 1.1;
        letter-spacing: -1.5px;
        color: var(--news-primary);
        font-weight: 900;
    }
    .news-content {
        color: var(--news-text);
        font-size: 1.15rem;
        line-height: 1.8;
    }
    .news-content p {
        margin-bottom: 2rem;
    }
    .news-content ul, .news-content ol {
        margin-bottom: 2rem;
        padding-left: 1.5rem;
    }
    .news-content li {
        margin-bottom: 0.75rem;
    }
    /* Official Statement / Press Release Style */
    .press-release-header {
        border-bottom: 4px solid var(--news-primary);
        padding-bottom: 20px;
        margin-bottom: 40px;
    }
    .press-release-label {
        display: inline-block;
        background: var(--news-primary);
        color: #fff;
        padding: 5px 15px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.75rem;
        margin-bottom: 15px;
    }
    /* Premium First Paragraph */
    .news-content > p:first-of-type::first-letter {
        float: left;
        font-size: 4.5rem;
        line-height: 1;
        padding-top: 4px;
        padding-right: 12px;
        padding-left: 3px;
        font-weight: 900;
        color: var(--news-primary);
    }
    .news-meta-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 30px 0;
    }
    .sidebar-news-card {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 15px;
        margin-bottom: 15px;
        display: flex;
        gap: 15px;
        text-decoration: none;
    }
    .sidebar-news-card:last-child {
        border-bottom: 0;
    }
    .sidebar-news-card:hover {
        transform: translateX(5px);
    }
    .sidebar-news-thumb {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 12px;
        flex-shrink: 0;
    }
    .dark .news-detail-wrapper {
        background-color: #0f172a;
        --news-text: #cbd5e1;
        --news-primary: #f1f5f9;
        --news-bg: #0f172a;
    }
    .dark .news-meta-divider {
        background: #1e293b;
    }
</style>
@endsection

@section('content')
<div class="news-detail-wrapper py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted small">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="/news" class="text-decoration-none text-muted small">Berita</a></li>
                        <li class="breadcrumb-item active small text-muted" aria-current="page">{{ Str::limit($item->title, 20) }}</li>
                    </ol>
                </nav>

                <div class="news-header mb-4">
                    @php
                        $isPressRelease = Str::contains(strtoupper($item->title), 'SIARAN PERS');
                    @endphp

                    @if($isPressRelease)
                        <div class="press-release-header">
                            <div class="press-release-label">Official Statement</div>
                            <h1 class="display-4">{{ $item->title }}</h1>
                        </div>
                    @else
                        <div class="badge bg-success rounded-pill mb-3 px-3 py-2 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">{{ $item->category }}</div>
                        <h1 class="display-4 mb-4">{{ $item->title }}</h1>
                    @endif
                    
                    <div class="news-meta d-flex align-items-center text-muted small">
                        <div class="d-flex align-items-center me-4">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2 overflow-hidden" style="width: 35px; height: 35px;">
                                <i class="bi bi-person-circle fs-4 text-secondary"></i>
                            </div>
                            <span>Oleh <strong>Media Center ILUNI</strong></span>
                        </div>
                        <div class="me-4"><i class="bi bi-calendar3 me-2"></i> {{ $item->created_at->translatedFormat('l, d F Y') }}</div>
                        <div><i class="bi bi-eye me-2"></i> {{ number_format(rand(120, 450)) }} Views</div>
                    </div>
                    <div class="news-meta-divider"></div>
                </div>

                @if($item->thumbnail)
                    <div class="mb-5">
                        <img src="{{ Str::startsWith($item->thumbnail, 'http') ? $item->thumbnail : asset($item->thumbnail) }}" class="img-fluid w-100 rounded-4 shadow-lg" alt="{{ $item->title }}" style="max-height: 500px; object-fit: cover;">
                        @if($item->caption)
                            <p class="text-muted small mt-3 text-center opacity-75"><em>Foto: {{ $item->caption }}</em></p>
                        @endif
                    </div>
                @endif

                <div class="news-content mb-5">
                    @php
                        $rawContent = $item->content;
                        // Normalize newlines
                        $rawContent = str_replace(["\r\n", "\r"], "\n", $rawContent);
                        
                        // Heuristic to check if it's already HTML (if it has common block tags)
                        $hasHtml = preg_match('/<(p|div|br|ul|li|blockquote|strong)/i', $rawContent);
                        
                        if (!$hasHtml) {
                            // Robust paragraph & list detection
                            $paragraphs = explode("\n\n", trim($rawContent));
                            $formatted = '';
                            
                            foreach ($paragraphs as $p) {
                                $p = trim($p);
                                if (empty($p)) continue;
                                
                                // Detect if paragraph is a list
                                if (preg_match('/^[-*•]/m', $p)) {
                                    $lines = explode("\n", $p);
                                    $formatted .= '<ul class="mb-4">';
                                    foreach ($lines as $line) {
                                        $cleanLine = preg_replace('/^[-*•]\s*/', '', trim($line));
                                        if (!empty($cleanLine)) {
                                            $formatted .= '<li>' . $cleanLine . '</li>';
                                        }
                                    }
                                    $formatted .= '</ul>';
                                } else {
                                    $formatted .= '<p>' . nl2br($p) . '</p>';
                                }
                            }
                            $finalContent = $formatted;
                        } else {
                            $finalContent = $rawContent;
                        }

                        // Special handling for sign-off (Only if NOT already semantically wrapped)
                        if (!Str::contains($finalContent, ['class="mt-5', 'border-top'])) {
                            $signOffKeywords = ["Hormat kami,", "Hormat Kami,", "Ttd,", "Salam,"];
                            foreach ($signOffKeywords as $keyword) {
                                if (Str::contains($finalContent, $keyword)) {
                                    // Split at keyword and wrap in a separate paragraph with styling
                                    $parts = explode($keyword, $finalContent, 2);
                                    $finalContent = $parts[0] . '</p><div class="mt-5 pt-4 border-top"><p class="fw-bold text-dark mb-1">' . $keyword . '</p><p class="fw-bold text-dark">' . $parts[1] . '</p></div>';
                                    break;
                                }
                            }
                        }
                    @endphp
                    {!! $finalContent !!}
                </div>

                <div class="share-area p-4 bg-light rounded-4 mb-5">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <div class="mb-3 mb-md-0">
                            <span class="small fw-black text-muted text-uppercase me-3" style="letter-spacing: 1px;">Bagikan:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle me-2"><i class="bi bi-facebook"></i></a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($item->title) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-circle me-2"><i class="bi bi-twitter-x"></i></a>
                            <a href="https://wa.me/?text={{ urlencode($item->title . ' - ' . url()->current()) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-circle"><i class="bi bi-whatsapp"></i></a>
                        </div>
                        <a href="/news" class="btn btn-dark btn-sm rounded-pill px-4">Kembali ke Berita</a>
                    </div>
                </div>

                <div class="my-5">
                    <x-ad-slot position="content" aspectRatio="1280/200" mobileAspectRatio="400/150" />
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px; z-index: 1;">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <h6 class="fw-bold mb-4 text-uppercase small text-muted border-start border-primary border-4 ps-3" style="letter-spacing: 1px;">Berita Terkait</h6>
                        @forelse($related as $r)
                            <a href="{{ route('news.show', $r->slug) }}" class="sidebar-news-card">
                                @if($r->thumbnail)
                                    <img src="{{ asset($r->thumbnail) }}" class="sidebar-news-thumb" alt="{{ $r->title }}">
                                @endif
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark small lh-base">{{ Str::limit($r->title, 60) }}</h6>
                                    <span class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-clock me-1"></i> {{ $r->created_at->diffForHumans() }}</span>
                                </div>
                            </a>
                        @empty
                            <p class="text-muted small text-center py-3">Belum ada berita terkait.</p>
                        @endforelse
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-dark text-white overflow-hidden position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;">
                        <div class="position-relative z-1">
                            <h6 class="fw-bold mb-2 text-warning">Portal Alumni</h6>
                            <p class="small mb-3 opacity-75">Update data Anda dan jalin kolaborasi antar alumni STEMAN.</p>
                            <a href="/register" class="btn btn-warning btn-sm rounded-pill fw-bold px-4">Daftar / Login</a>
                        </div>
                        <i class="bi bi-shield-check position-absolute bottom-0 end-0 opacity-25" style="font-size: 6rem; transform: translate(20%, 20%);"></i>
                    </div>
                    
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h6 class="fw-bold mb-3 text-uppercase small text-muted border-start border-success border-4 ps-3">Sponsor Utama</h6>
                        <x-ad-slot position="sidebar" aspectRatio="1/1" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let trackStart = Date.now();
    window.addEventListener("beforeunload", () => {
        let duration = Math.floor((Date.now() - trackStart) / 1000);
        if (duration > 2) {
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
