@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="section-title d-inline-block">Galeri Alumni</h2>
        <p class="text-muted mt-2">Kumpulan momen dan video kegiatan alumni {{ setting('school_name', 'SMKN 2 Ternate') }}.</p>
    </div>

    <!-- Tab Navigation -->
    <div class="d-flex justify-content-center mb-5">
        <div class="btn-group p-1 bg-white dark:bg-slate-800 rounded-pill shadow-sm glass-2">
            <a href="/gallery?type=photo" class="btn rounded-pill px-4 {{ $type == 'photo' ? 'btn-dark shadow' : 'btn-link text-decoration-none text-muted' }}">
                <i class="bi bi-images me-2"></i>Foto
            </a>
            <a href="/gallery?type=video" class="btn rounded-pill px-4 {{ $type == 'video' ? 'btn-dark shadow' : 'btn-link text-decoration-none text-muted' }}">
                <i class="bi bi-play-circle me-2"></i>Video
            </a>
        </div>
    </div>

    <div class="{{ $type == 'photo' ? 'masonry-grid' : 'row g-4' }}">
        @forelse($media as $item)
            <div class="{{ $type == 'photo' ? 'masonry-item' : 'col-md-6 col-lg-4' }}">
                <div class="card glass-card border-0 glow-hover h-100 {{ $item->type == 'tiktok' ? 'card-tiktok' : '' }}">
                    @if($item->type == 'photo')
                        <a href="{{ $item->file_path }}" target="_blank" class="d-block overflow-hidden">
                            <img src="{{ $item->file_path }}" class="card-img-top transition-all" alt="{{ $item->title }}" style="width: 100%; height: auto; display: block;">
                        </a>
                    @elseif($item->type == 'tiktok')
                        <div class="tiktok-wrapper bg-dark position-relative overflow-hidden" style="height: 450px;">
                            <blockquote class="tiktok-embed" cite="{{ $item->tiktok_url }}" 
                                data-video-id="{{ $item->extractTiktokId() }}" 
                                style="width:100%; height:100%; margin:0; border:none;">
                                <section class="text-white p-4 h-100 d-flex flex-column justify-content-center align-items-center">
                                    <div class="spinner-border text-light mb-3" role="status"></div>
                                    <a target="_blank" href="{{ $item->tiktok_url }}" class="btn btn-outline-light btn-sm rounded-pill mt-3">
                                        <i class="bi bi-tiktok me-2"></i>Tonton di TikTok
                                    </a>
                                </section>
                            </blockquote>
                        </div>
                    @else
                        @if($item->youtube_url)
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ $item->youtube_url }}" title="YouTube video" allowfullscreen></iframe>
                            </div>
                        @else
                            <div class="ratio ratio-16x9 bg-dark d-flex align-items-center justify-content-center">
                                <video controls class="w-100 h-100" style="object-fit: contain;">
                                    <source src="{{ $item->file_path }}" type="video/mp4">
                                </video>
                            </div>
                        @endif
                    @endif
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="fw-bold mb-0 text-truncate" title="{{ $item->title }}">{{ $item->title }}</h6>
                            @if($item->type == 'tiktok')
                                <span class="badge bg-danger rounded-pill" style="font-size: 0.6rem;">TIKTOK</span>
                            @endif
                        </div>
                        @if($item->description && $item->description !== $item->title)
                            <p class="text-muted mb-0" style="font-size: 0.75rem;">{{ Str::limit($item->description, 80) }}</p>
                        @endif
                    </div>
                </div>
            </div>

        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-folder2-open display-1 text-light"></i>
                <p class="lead mt-3">Belum ada {{ $type == 'photo' ? 'foto' : 'video' }} kegiatan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $media->appends(['type' => $type])->links() }}
    </div>
</div>
<script async src="https://www.tiktok.com/embed.js"></script>
@endsection
