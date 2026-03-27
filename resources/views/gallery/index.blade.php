@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="section-title d-inline-block">Galeri Alumni</h2>
        <p class="text-muted mt-2">Kumpulan momen dan video kegiatan alumni {{ setting('school_name', 'SMKN 2 Ternate') }}.</p>
    </div>

    <!-- Tab Navigation -->
    <div class="d-flex justify-content-center mb-5">
        <div class="btn-group p-1 bg-light rounded-pill shadow-sm">
            <a href="/gallery?type=photo" class="btn rounded-pill px-4 {{ $type == 'photo' ? 'btn-alumni_smkn2 shadow' : 'btn-light' }}">
                <i class="bi bi-images me-2"></i>Foto
            </a>
            <a href="/gallery?type=video" class="btn rounded-pill px-4 {{ $type == 'video' ? 'btn-alumni_smkn2 shadow' : 'btn-light' }}">
                <i class="bi bi-play-circle me-2"></i>Video
            </a>
        </div>
    </div>

    <div class="row g-4">
        @forelse($media as $item)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 15px;">
                    @if($item->type == 'photo')
                        <a href="{{ $item->file_path }}" target="_blank">
                            <img src="{{ $item->file_path }}" class="card-img-top" alt="{{ $item->title }}" style="height: 250px; object-fit: cover;">
                        </a>
                    @elseif($item->type == 'tiktok')
                        <div class="tiktok-container text-center bg-dark" style="min-height:400px; display:flex; align-items:center; justify-content:center; padding: 10px;">
                            <blockquote class="tiktok-embed" cite="{{ $item->tiktok_url }}" 
                                data-video-id="{{ $item->extractTiktokId() }}" 
                                style="max-width:100%; min-width:100%; margin:0 auto; border:none;">
                                <section>
                                    <a target="_blank" href="{{ $item->tiktok_url }}">
                                        <p>🎵 {{ $item->title }}</p>
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
                            <div class="ratio ratio-16x9">
                                <video controls><source src="{{ $item->file_path }}" type="video/mp4"></video>
                            </div>
                        @endif
                    @endif
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-2">{{ $item->title }}</h5>
                        <p class="text-muted small mb-0">{{ $item->description }}</p>
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
