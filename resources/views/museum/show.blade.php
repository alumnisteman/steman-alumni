@extends('layouts.app')

@section('content')
<style>
:root {
    --museum-gold: #d4a017;
    --museum-dark: #150b00;
    --museum-sepia: #8b7355;
}

.museum-detail-page {
    background: radial-gradient(circle at 50% 50%, #201405 0%, #0d0700 100%);
    color: #f5f0e8;
    min-height: calc(100vh - 60px);
    padding: 3rem 0;
}

.museum-back-btn {
    color: var(--museum-gold);
    text-decoration: none;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: transform 0.2s, color 0.2s;
}
.museum-back-btn:hover {
    color: #ffc83b;
    transform: translateX(-4px);
}

.museum-frame-container {
    background: #000;
    border: 12px solid #2d1a04;
    border-image: linear-gradient(to bottom, #d4a017, #593e1a) 10;
    box-shadow: 0 25px 60px rgba(0,0,0,0.8);
    position: relative;
    border-radius: 4px;
    overflow: hidden;
}

.museum-img-display {
    width: 100%;
    max-height: 550px;
    object-fit: contain;
    filter: sepia(10%);
    display: block;
    margin: 0 auto;
}

.museum-sidebar-info {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(212, 160, 23, 0.25);
    border-radius: 20px;
    padding: 2rem;
    backdrop-filter: blur(10px);
}

.museum-meta-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: var(--museum-sepia);
    font-weight: 700;
    margin-bottom: 2px;
}
.museum-meta-val {
    font-size: 1.05rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 1.5rem;
}

.museum-like-btn {
    background: rgba(225, 29, 72, 0.1);
    border: 1px solid rgba(225, 29, 72, 0.3);
    color: #f43f5e;
    font-weight: 700;
    border-radius: 30px;
    padding: 10px 24px;
    transition: all 0.2s;
}
.museum-like-btn:hover {
    background: #e11d48;
    color: #fff;
    transform: scale(1.05);
}
.museum-like-btn.liked {
    background: #e11d48;
    color: #fff;
    border-color: #e11d48;
}

.museum-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s;
    text-decoration: none;
    color: #fff;
    display: block;
}
.museum-card:hover {
    transform: translateY(-5px);
    border-color: var(--museum-gold);
    box-shadow: 0 10px 25px rgba(212,160,23,0.15);
    color: #fff;
}
.museum-card .card-img-wrap {
    height: 160px;
    overflow: hidden;
    position: relative;
    background: #000;
}
.museum-card .card-img-wrap img {
    width: 100%; height: 100%; object-fit: cover;
}
.museum-card .card-body { padding: 1rem; }

/* Dark mode overrides (mostly already custom colored page) */
.dark .museum-sidebar-info {
    background: rgba(0, 0, 0, 0.3);
}
</style>

<div class="museum-detail-page">
    <div class="container">
        
        <!-- Back Link -->
        <div class="mb-4">
            <a href="{{ route('museum.index') }}" class="museum-back-btn">
                <i class="bi bi-arrow-left"></i> KEMBALI KE MUSEUM DIGITAL
            </a>
        </div>

        <div class="row g-5">
            
            <!-- Main Content Area: Frame -->
            <div class="col-lg-8">
                <div class="museum-frame-container mb-4">
                    @if ($museumItem->youtube_embed_id)
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/{{ $museumItem->youtube_embed_id }}?autoplay=0" 
                                    title="{{ $museumItem->title }}" 
                                    allowfullscreen></iframe>
                        </div>
                    @elseif ($museumItem->image_url)
                        <img src="{{ $museumItem->image_url }}" alt="{{ $museumItem->title }}" class="museum-img-display">
                    @else
                        <div class="text-center py-5 bg-dark">
                            <div style="font-size: 7rem;">{{ $museumItem->category_icon }}</div>
                            <p class="text-muted mt-3">Tidak ada pratinjau media untuk arsip ini.</p>
                        </div>
                    @endif
                </div>

                <h1 class="fw-black mb-3" style="color: var(--museum-gold);">{{ $museumItem->title }}</h1>
                
                <div class="p-3 rounded-4 mb-4" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                    <h6 class="fw-bold mb-2 text-white">Deskripsi / Cerita Sejarah:</h6>
                    <p class="mb-0 opacity-80" style="line-height: 1.7; white-space: pre-line;">
                        {{ $museumItem->description ?: 'Belum ada deskripsi untuk arsip ini. Kontribusikan deskripsi jika Anda memiliki informasi lebih lanjut!' }}
                    </p>
                </div>
            </div>

            <!-- Sidebar Info Panel -->
            <div class="col-lg-4">
                <div class="museum-sidebar-info shadow-lg">
                    <h5 class="fw-bold mb-4 border-bottom pb-2" style="color: var(--museum-gold);">
                        <i class="bi bi-info-circle me-2"></i>Detail Arsip
                    </h5>

                    <div class="museum-meta-label">Kategori</div>
                    <div class="museum-meta-val">
                        <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill">
                            {{ $museumItem->category_icon }} {{ $museumItem->category_label }}
                        </span>
                    </div>

                    <div class="museum-meta-label">Era / Dekade</div>
                    <div class="museum-meta-val">
                        <i class="bi bi-calendar-event me-2 text-warning"></i>{{ $museumItem->era_year ?: 'Tahun tidak diketahui' }}
                    </div>

                    <div class="museum-meta-label">Sumber / Disumbang Oleh</div>
                    <div class="museum-meta-val text-warning fw-bold">
                        <i class="bi bi-person-heart me-2"></i>{{ $museumItem->donated_by ?: 'Alumni Steman' }}
                    </div>

                    <div class="museum-meta-label">Tanggal Ditambahkan</div>
                    <div class="museum-meta-val">
                        <i class="bi bi-clock me-2"></i>{{ $museumItem->created_at->format('d M Y H:i') }}
                    </div>

                    <div class="museum-meta-label">Diunggah Oleh</div>
                    <div class="museum-meta-val">
                        <i class="bi bi-person me-2"></i>{{ $museumItem->uploader->name }}
                    </div>

                    <div class="museum-meta-label">Statistik Kunjungan</div>
                    <div class="museum-meta-val">
                        <i class="bi bi-eye me-2"></i>{{ number_format($museumItem->views) }} kali dilihat
                    </div>

                    <!-- Appreciate Button -->
                    <div class="d-flex align-items-center gap-3 mt-4 pt-3 border-top border-secondary">
                        @auth
                        <button class="btn museum-like-btn {{ $isLiked ? 'liked' : '' }}" onclick="toggleLike({{ $museumItem->id }}, this)">
                            <i class="bi bi-heart{{ $isLiked ? '-fill' : '' }} me-2"></i>APRESIASI
                        </button>
                        @else
                        <button class="btn museum-like-btn" disabled>
                            <i class="bi bi-heart me-2"></i>APRESIASI
                        </button>
                        @endauth
                        <div>
                            <h4 class="mb-0 fw-black text-white" id="likeCounter">{{ number_format($museumItem->likes) }}</h4>
                            <span class="text-muted small">Mencintai ini</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Related Section -->
        @if ($related->isNotEmpty())
        <div class="mt-5 pt-5 border-top border-secondary">
            <h4 class="fw-bold mb-4" style="color: var(--museum-gold);"><i class="bi bi-layers me-2"></i>Arsip Terkait</h4>
            <div class="row g-4">
                @foreach ($related as $rel)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('museum.show', $rel) }}" class="museum-card h-100">
                        <div class="card-img-wrap">
                            @if ($rel->image_url)
                                <img src="{{ $rel->image_url }}" alt="{{ $rel->title }}" loading="lazy">
                            @elseif ($rel->youtube_embed_id)
                                <img src="https://img.youtube.com/vi/{{ $rel->youtube_embed_id }}/mqdefault.jpg" alt="{{ $rel->title }}" loading="lazy">
                            @else
                                <div class="text-center py-5 bg-dark h-100 d-flex align-items-center justify-content-center" style="font-size: 3rem;">
                                    {{ $rel->category_icon }}
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold mb-1 text-truncate">{{ $rel->title }}</h6>
                            <span class="text-muted small">{{ $rel->category_label }}</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
function toggleLike(id, btn) {
    fetch(`/museum/${id}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        const icon = btn.querySelector('i');
        const counter = document.getElementById('likeCounter');
        if (data.liked) {
            btn.classList.add('liked');
            icon.className = 'bi bi-heart-fill me-2';
        } else {
            btn.classList.remove('liked');
            icon.className = 'bi bi-heart me-2';
        }
        counter.textContent = data.total.toLocaleString('id-ID');
    });
}
</script>
@endpush
@endsection
