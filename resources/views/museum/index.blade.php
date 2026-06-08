@extends('layouts.app')

@section('content')
<style>
:root {
    --museum-gold: #d4a017;
    --museum-dark: #1a0f00;
    --museum-sepia: #8b7355;
}

.museum-hero {
    background: linear-gradient(135deg, #1a0f00 0%, #2d1a00 40%, #1a0a00 100%);
    position: relative;
    overflow: hidden;
    padding: 80px 0 60px;
}
.museum-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d4a017' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.museum-hero .badge-era {
    background: rgba(212,160,23,0.2);
    border: 1px solid rgba(212,160,23,0.4);
    color: #d4a017;
    font-size: 0.7rem;
    letter-spacing: 3px;
}

/* Museum Card */
.museum-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid rgba(0,0,0,0.07);
    cursor: pointer;
    position: relative;
}
.museum-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}
.museum-card .card-img-wrap {
    height: 220px;
    overflow: hidden;
    background: #f5f0e8;
    position: relative;
}
.museum-card .card-img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    filter: sepia(20%);
    transition: filter 0.3s, transform 0.3s;
}
.museum-card:hover .card-img-wrap img {
    filter: sepia(0%);
    transform: scale(1.05);
}
.museum-card .era-badge {
    position: absolute;
    top: 12px; left: 12px;
    background: rgba(0,0,0,0.7);
    color: #d4a017;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    letter-spacing: 1px;
}
.museum-card .category-badge {
    position: absolute;
    top: 12px; right: 12px;
    background: rgba(255,255,255,0.9);
    font-size: 1rem;
    width: 32px; height: 32px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.museum-card .card-body { padding: 1rem 1.2rem; }
.museum-card .like-btn {
    border: none; background: transparent;
    color: #94a3b8; font-size: 0.85rem;
    display: flex; align-items: center; gap: 5px;
    transition: color 0.2s;
    cursor: pointer;
}
.museum-card .like-btn.liked { color: #e11d48; }
.museum-card .like-btn:hover { color: #e11d48; }

/* Category Filter Pills */
.filter-pill {
    border: 1.5px solid #e2e8f0;
    background: transparent;
    padding: 6px 16px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    color: #64748b;
}
.filter-pill:hover, .filter-pill.active {
    background: #1a0f00;
    border-color: #1a0f00;
    color: #d4a017;
}

/* Stats bar */
.museum-stat {
    text-align: center;
    padding: 1.5rem;
    border-right: 1px solid rgba(212,160,23,0.2);
}
.museum-stat:last-child { border-right: none; }
.museum-stat .stat-num {
    font-size: 2rem;
    font-weight: 900;
    color: #d4a017;
}

/* Upload CTA */
.upload-cta {
    background: linear-gradient(135deg, #d4a017 0%, #b8860b 100%);
    border-radius: 20px;
    color: #1a0f00;
    padding: 2rem;
}

/* No image placeholder */
.no-img-placeholder {
    width: 100%; height: 220px;
    background: linear-gradient(135deg, #f5f0e8, #e8dcc8);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    font-size: 3rem;
    color: #8b7355;
}

/* Dark mode */
.dark .museum-card {
    background: #1e293b;
    border-color: rgba(255,255,255,0.08);
}
.dark .filter-pill { color: #94a3b8; border-color: rgba(255,255,255,0.1); }
.dark .filter-pill:hover, .dark .filter-pill.active {
    background: #d4a017; color: #1a0f00; border-color: #d4a017;
}

/* Tombol Edit/Hapus untuk arsip milik sendiri */
.museum-card.museum-card-owned .card-img-wrap { position: relative; }
.owner-actions {
    position: absolute;
    top: 8px; left: 8px;
    display: flex;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.2s ease;
    z-index: 10;
}
.museum-card.museum-card-owned:hover .owner-actions { opacity: 1; }
.owner-actions .btn { box-shadow: 0 2px 8px rgba(0,0,0,0.4); }
</style>

{{-- HERO --}}
<section class="museum-hero">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge badge-era mb-3 px-3 py-2 rounded-pill">🏛️ DIGITAL MUSEUM STEMAN</span>
                <h1 class="display-5 fw-black text-white mb-3">
                    Arsip Sejarah<br>
                    <span style="color: #d4a017;">SMKN 2 Ternate</span>
                </h1>
                <p class="text-white opacity-75 fs-6 mb-4">
                    Jaga memori. Lestarikan sejarah. Temukan kembali foto, ijazah, mesin bengkel, seragam, 
                    dan guru-guru legendaris yang membentuk kita.
                </p>
                <a href="#museum-grid" class="btn btn-warning fw-bold rounded-pill px-4 me-3">
                    <i class="bi bi-search me-2"></i>Jelajahi Arsip
                </a>
                @auth
                <button class="btn btn-outline-warning rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="bi bi-upload me-2"></i>Sumbang Arsip
                </button>
                @endauth
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <div style="font-size: 8rem; filter: drop-shadow(0 0 30px rgba(212,160,23,0.4)); animation: float 3s ease-in-out infinite;">🏛️</div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row mt-5">
            <div class="col-12">
                <div class="rounded-4 d-flex" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(212,160,23,0.2);">
                    <div class="museum-stat flex-fill">
                        <div class="stat-num">{{ number_format($stats['total']) }}</div>
                        <div class="text-white opacity-50 small">Total Arsip</div>
                    </div>
                    <div class="museum-stat flex-fill">
                        <div class="stat-num">{{ number_format($stats['total_likes']) }}</div>
                        <div class="text-white opacity-50 small">Total Apresiasi ❤️</div>
                    </div>
                    <div class="museum-stat flex-fill">
                        <div class="stat-num">{{ count($categories) }}</div>
                        <div class="text-white opacity-50 small">Kategori Arsip</div>
                    </div>
                    <div class="museum-stat flex-fill">
                        <div class="stat-num">{{ $eras->count() }}</div>
                        <div class="text-white opacity-50 small">Era / Dekade</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FILTER & GRID --}}
<section id="museum-grid" class="py-5">
    <div class="container">

        {{-- Category Filter --}}
        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="{{ route('museum.index') }}"
               class="filter-pill {{ !$category ? 'active' : '' }}">
                🏛️ Semua
            </a>
            @foreach ($categories as $key => $cat)
            <a href="{{ route('museum.index', ['category' => $key, 'era' => $era]) }}"
               class="filter-pill {{ $category === $key ? 'active' : '' }}">
                {{ $cat['icon'] }} {{ $cat['label'] }}
            </a>
            @endforeach
        </div>

        {{-- Era Filter --}}
        @if ($eras->isNotEmpty())
        <div class="d-flex flex-wrap gap-2 mb-5 align-items-center">
            <span class="text-muted small fw-bold me-2">ERA:</span>
            <a href="{{ route('museum.index', ['category' => $category]) }}"
               class="filter-pill {{ !$era ? 'active' : '' }}" style="font-size: 0.75rem;">Semua Era</a>
            @foreach ($eras as $eraYear)
            <a href="{{ route('museum.index', ['category' => $category, 'era' => $eraYear]) }}"
               class="filter-pill {{ $era == $eraYear ? 'active' : '' }}" style="font-size: 0.75rem;">
               {{ $eraYear }}
            </a>
            @endforeach
        </div>
        @endif

        {{-- Grid --}}
        @if ($items->isEmpty())
        <div class="text-center py-5">
            <div style="font-size: 4rem;">📭</div>
            <h4 class="mt-3 text-muted">Belum ada arsip di kategori ini</h4>
            @auth
            <p class="text-muted">Jadilah yang pertama menyumbang! 🏛️</p>
            <button class="btn btn-warning rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-upload me-2"></i>Sumbang Arsip
            </button>
            @endauth
        </div>
        @else
        <div class="row g-4">
            @foreach ($items as $item)
            @php
                $isOwner = auth()->check() && (
                    $item->uploaded_by === auth()->id() ||
                    auth()->user()->hasRole(['admin','editor'])
                );
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div class="museum-card h-100 {{ $isOwner ? 'museum-card-owned' : '' }}" onclick="window.location='{{ route('museum.show', $item) }}'">
                    <div class="card-img-wrap">
                        @if ($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy">
                        @elseif ($item->youtube_embed_id)
                            <img src="https://img.youtube.com/vi/{{ $item->youtube_embed_id }}/mqdefault.jpg"
                                 alt="{{ $item->title }}" loading="lazy">
                        @else
                            <div class="no-img-placeholder">{{ $item->category_icon }}</div>
                        @endif

                        @if ($item->era_year)
                        <div class="era-badge">{{ $item->era_year }}</div>
                        @endif
                        <div class="category-badge" title="{{ $item->category_label }}">
                            {{ $item->category_icon }}
                        </div>

                        {{-- Overlay tombol edit/hapus untuk arsip milik sendiri --}}
                        @if($isOwner)
                        <div class="owner-actions" onclick="event.stopPropagation()">
                            <a href="{{ route('museum.edit', $item) }}"
                               class="btn btn-sm btn-warning rounded-pill px-2 py-1"
                               title="Edit Arsip" style="font-size: 0.7rem;">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-danger rounded-pill px-2 py-1"
                                    title="Hapus Arsip" style="font-size: 0.7rem;"
                                    onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->title) }}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1 text-truncate">{{ $item->title }}</h6>
                        @if ($item->donated_by)
                        <div class="text-muted" style="font-size: 0.72rem;">
                            <i class="bi bi-person-heart me-1"></i>{{ $item->donated_by }}
                        </div>
                        @endif
                        <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                            <span class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-eye me-1"></i>{{ number_format($item->views) }}
                            </span>
                            @auth
                            <button class="like-btn {{ auth()->user() && $item->isLikedBy(auth()->user()) ? 'liked' : '' }}"
                                    onclick="event.stopPropagation(); toggleLike({{ $item->id }}, this)"
                                    data-id="{{ $item->id }}">
                                <i class="bi bi-heart{{ auth()->user() && $item->isLikedBy(auth()->user()) ? '-fill' : '' }}"></i>
                                <span class="like-count">{{ number_format($item->likes) }}</span>
                            </button>
                            @else
                            <span class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-heart me-1"></i>{{ number_format($item->likes) }}
                            </span>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Modal hapus dari grid --}}
        @auth
        <div class="modal fade" id="gridDeleteModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-0" style="background: #b91c1c;">
                        <h5 class="modal-title fw-bold text-white"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Arsip</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div style="font-size: 4rem;">🗑️</div>
                        <h5 class="fw-bold mt-3">Hapus arsip ini?</h5>
                        <p class="text-muted mb-0" id="deleteModalDesc">
                            Arsip akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
                        </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center pb-4 gap-3">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <form id="deleteForm" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger fw-bold rounded-pill px-4">
                                <i class="bi bi-trash me-2"></i>Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endauth

        {{-- Pagination --}}
        <div class="mt-5 d-flex justify-content-center">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</section>

{{-- UPLOAD MODAL --}}
@auth
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">🏛️ Sumbang Arsip ke Museum Digital</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('museum.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info rounded-3 small">
                        <i class="bi bi-info-circle me-2"></i>
                        Arsip yang dikirim akan direview admin sebelum ditampilkan. Gambar maks 3MB.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Judul Arsip <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="contoh: Bengkel Mesin Tahun 1995" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category" class="form-select rounded-3" required>
                                @foreach ($categories as $key => $cat)
                                <option value="{{ $key }}">{{ $cat['icon'] }} {{ $cat['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Foto Arsip</label>
                            <input type="file" name="image" class="form-control rounded-3" accept="image/*">
                            <div class="form-text">JPG/PNG, maks 3MB. Auto-convert ke WebP.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Link Video YouTube (opsional)</label>
                            <input type="url" name="video_url" class="form-control rounded-3" placeholder="https://youtube.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tahun Era</label>
                            <input type="number" name="era_year" class="form-control rounded-3"
                                   placeholder="1990" min="1950" max="{{ date('Y') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Disumbang / Sumber</label>
                            <input type="text" name="donated_by" class="form-control rounded-3" placeholder="Nama alumni / donatur">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control rounded-3" rows="3"
                                      placeholder="Ceritakan tentang arsip ini..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold rounded-pill px-4">
                        <i class="bi bi-upload me-2"></i>Kirim Arsip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

@push('scripts')
<script>
function confirmDelete(id, title) {
    document.getElementById('deleteModalDesc').innerHTML =
        'Arsip <strong>"' + title + '"</strong> akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.';
    document.getElementById('deleteForm').action = '/museum/' + id;
    var modal = new bootstrap.Modal(document.getElementById('gridDeleteModal'));
    modal.show();
}

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
        const count = btn.querySelector('.like-count');
        if (data.liked) {
            btn.classList.add('liked');
            icon.className = 'bi bi-heart-fill';
        } else {
            btn.classList.remove('liked');
            icon.className = 'bi bi-heart';
        }
        count.textContent = data.total.toLocaleString('id-ID');
    });
}
</script>
<style>
@keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-15px)} }
</style>
@endpush
@endsection
