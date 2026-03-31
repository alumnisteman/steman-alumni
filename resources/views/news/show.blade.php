@extends('layouts.app')
@section('meta')
    <meta property="og:title" content="{{ $item->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($item->content), 160) }}">
    <meta property="og:image" content="{{ $item->thumbnail ? (Str::startsWith($item->thumbnail, 'http') ? $item->thumbnail : asset($item->thumbnail)) : asset('/assets/images/hero_iluni.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
@endsection
@section('content')
<div class="news-detail-wrapper py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
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

                <img src="{{ $item->thumbnail }}" class="img-fluid w-100 rounded-0 mb-5 shadow-sm" alt="{{ $item->title }}">

                <div class="news-content fs-5 lh-lg mb-5">
                    {!! $item->content !!}
                </div>

                <hr class="my-5 opacity-25">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <a href="/news" class="btn btn-outline-dark rounded-0 px-4">KEMBALI KE DAFTAR BERITA</a>
                    <div class="share-buttons mt-4 mt-md-0">
                        <span class="small fw-bold text-muted me-3">BAGIKAN:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="text-dark me-3" title="Bagikan ke Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($item->title) }}" target="_blank" class="text-dark me-3" title="Bagikan ke X"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://wa.me/?text={{ urlencode($item->title . ' - ' . url()->current()) }}" target="_blank" class="text-dark" title="Bagikan ke WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
