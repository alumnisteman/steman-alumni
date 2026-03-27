@extends('layouts.app')
@section('meta')
    <meta property="og:title" content="{{ $item->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($item->content), 160) }}">
    <meta property="og:image" content="{{ $item->thumbnail ? (Str::startsWith($item->thumbnail, 'http') ? $item->thumbnail : asset($item->thumbnail)) : asset('/images/hero_iluni.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
@endsection

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/" class="text-dark">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="/news" class="text-dark">Berita</a></li>
                    <li class="breadcrumb-item active">{{ $item->title }}</li>
                </ol>
            </nav>

            <div class="mb-4">
                <span class="badge bg-warning text-dark px-3 rounded-pill mb-2">{{ $item->category }}</span>
                <h1 class="fw-bold display-5">{{ $item->title }}</h1>
                <div class="d-flex align-items-center text-muted small mt-3">
                    <div class="me-4"><i class="bi bi-calendar-event me-2"></i>{{ $item->created_at->format('d M Y') }}</div>
                    <div><i class="bi bi-person me-2"></i>Ditulis oleh: {{ $item->user->name }}</div>
                </div>
            </div>

            @if($item->thumbnail)
                <img src="{{ $item->thumbnail }}" class="img-fluid w-100 rounded-0 mb-5 shadow-sm" alt="{{ $item->title }}">
            @endif

            <div class="news-content lead text-dark opacity-90" style="line-height: 1.8;">
                {!! nl2br(e($item->content)) !!}
            </div>

            <hr class="my-5">
            <div class="d-flex justify-content-between">
                <a href="/news" class="btn btn-outline-dark rounded-0 px-4">KEMBALI KE DAFTAR BERITA</a>
                <div class="share-links">
                    <span class="small me-3 fw-bold">BAGIKAN:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="text-dark me-3" title="Bagikan ke Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($item->title) }}" target="_blank" class="text-dark me-3" title="Bagikan ke X"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://wa.me/?text={{ urlencode($item->title . ' - ' . url()->current()) }}" target="_blank" class="text-dark" title="Bagikan ke WhatsApp"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
