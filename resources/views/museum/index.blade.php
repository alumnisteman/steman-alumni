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

{{-- TIMELINE & SEJARAH (STEMAN HISTORY) --}}
<section class="py-5" style="background: linear-gradient(180deg, #fcf9f5 0%, #f3ece0 100%);">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <span class="badge rounded-pill mb-2 px-3 py-2" style="background: rgba(212,160,23,0.1); color: #b8860b; border: 1px solid rgba(212,160,23,0.3); font-weight: bold; letter-spacing: 2px;">JEJAK LANGKAH</span>
                <h2 class="fw-black display-6" style="color: var(--museum-dark); font-weight: 800;">Sejarah & Warisan Kolaboratif STEMAN</h2>
                <p class="text-muted max-w-2xl mx-auto">Merajut kembali benang merah perjalanan sekolah dan kontribusi nyata antar angkatan secara transparan.</p>
            </div>
        </div>

        <div class="row g-4">
            {{-- Kiri: Arsip Inti & Kolaborasi LPJ --}}
            <div class="col-lg-7">
                <div class="card border-0 rounded-4 shadow-sm h-100" style="background: white; border: 1px solid rgba(212,160,23,0.1) !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                            <div class="rounded-3 p-2 me-3" style="background: rgba(212,160,23,0.1); color: var(--museum-gold);">
                                <i class="bi bi-journal-bookmark-fill fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-0" style="color: var(--museum-dark);">Data & Fakta Sejarah</h4>
                        </div>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 rounded-4" style="background: #faf6f0; border-left: 4px solid var(--museum-gold);">
                                    <h6 class="fw-bold text-uppercase mb-2 text-muted" style="font-size: 0.75rem;"><i class="bi bi-building me-2"></i>1. Tahun Berdiri</h6>
                                    <p class="mb-0 fs-6"><strong>1965</strong> – Berdiri sebagai <strong>STM Nasional Ternate</strong> (Swasta).</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded-4" style="background: #faf6f0; border-left: 4px solid var(--museum-gold);">
                                    <h6 class="fw-bold text-uppercase mb-2 text-muted" style="font-size: 0.75rem;"><i class="bi bi-person-badge me-2"></i>2. Kepala Sekolah</h6>
                                    <p class="mb-0 fs-6"><strong>Mustafa Muhammad, S.Pd., MM.</strong> <span class="badge bg-success rounded-pill" style="font-size: 0.65rem;">Aktif</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size: 0.75rem;"><i class="bi bi-tools me-2"></i>3. Bidang Keahlian Saat Ini</h6>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-2" style="font-size: 0.75rem;">Teknik Konstruksi & Perumahan</span>
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-2" style="font-size: 0.75rem;">Desain Pemodelan & Info Bangunan</span>
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-2" style="font-size: 0.75rem;">Teknik Mesin</span>
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-2" style="font-size: 0.75rem;">Teknik Otomotif</span>
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-2" style="font-size: 0.75rem;">Teknik Pengelasan & Fabrikasi Logam</span>
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-2" style="font-size: 0.75rem;">Teknik Elektronika</span>
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-2" style="font-size: 0.75rem;">Teknik Ketenagalistrikan</span>
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-2" style="font-size: 0.75rem;">Teknik Geospasial</span>
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-2" style="font-size: 0.75rem;">Teknik Jaringan Komputer & Telekomunikasi</span>
                            </div>
                        </div>

                        {{-- KEPALA SEKOLAH GALLERY --}}
                        <div class="mt-5 border-top pt-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="fw-bold mb-0" style="color: var(--museum-dark);"><i class="bi bi-person-badge-fill me-2" style="color: var(--museum-gold);"></i> Kepala Sekolah dari Masa ke Masa</h5>
                                @auth
                                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'editor')
                                        <button class="btn btn-sm btn-warning rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addPrincipalModal">
                                            <i class="bi bi-plus-lg me-1"></i>Tambah
                                        </button>
                                    @endif
                                @endauth
                            </div>

                            @if($principals->isEmpty())
                                <div class="text-center py-4 bg-light rounded-4 border border-dashed">
                                    <div class="fs-2">👤</div>
                                    <p class="text-muted mb-0 small">Belum ada foto kepala sekolah yang diunggah. Bantu kami melengkapi arsip ini!</p>
                                </div>
                            @else
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($principals as $p)
                                        <div class="text-center position-relative principal-card" style="width: 110px;">
                                            @auth
                                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'editor')
                                                    <div class="position-absolute top-0 end-0 d-flex gap-1" style="z-index: 2; transform: translate(4px, -4px);">
                                                        <button class="btn btn-sm btn-light border rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width:24px;height:24px;"
                                                                data-bs-toggle="modal" data-bs-target="#editPrincipalModal{{ $p->id }}" title="Edit">
                                                            <i class="bi bi-pencil-fill" style="font-size: 0.6rem;"></i>
                                                        </button>
                                                        <form action="{{ route('museum.principals.destroy', $p) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-danger rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width:24px;height:24px;" title="Hapus">
                                                                <i class="bi bi-trash-fill" style="font-size: 0.6rem;"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endauth
                                            <div class="rounded-4 overflow-hidden border mx-auto mb-2" style="width: 90px; height: 110px; background: #f5f0e8;">
                                                @if($p->photo_path)
                                                    <img src="{{ $p->photo_path }}" alt="{{ $p->name }}" style="width:100%;height:100%;object-fit:cover;">
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center h-100" style="font-size:2.5rem; color:#8b7355;">👤</div>
                                                @endif
                                            </div>
                                            <p class="mb-0 fw-bold" style="font-size: 0.72rem; line-height: 1.2; color: var(--museum-dark);">{{ $p->name }}</p>
                                            <p class="mb-0 text-muted" style="font-size: 0.65rem;">{{ $p->period }}</p>
                                            @if($p->status === 'active')
                                                <span class="badge bg-success rounded-pill mt-1" style="font-size: 0.55rem;">Aktif</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- DYNAMIC LPJ COLLABORATION DATA --}}
                        <div class="mt-5 border-top pt-4">
                            <h5 class="fw-bold mb-3" style="color: var(--museum-dark);"><i class="bi bi-gift-fill me-2 text-danger"></i> Legacy & Kolaborasi Nyata (Data LPJ)</h5>
                            <p class="text-muted small">Program kerja bakti sosial, renovasi sarana, dan legacy alumni yang terverifikasi secara transparan.</p>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="p-3 rounded-4 text-center border" style="background: rgba(25, 135, 84, 0.03); border-color: rgba(25, 135, 84, 0.1) !important;">
                                        <span class="d-block text-muted small mb-1">Legacy Terlaksana</span>
                                        <strong class="fs-4 text-success">{{ $stats['lpj_count'] ?? 0 }} Program</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-4 text-center border" style="background: rgba(212,160,23,0.03); border-color: rgba(212,160,23,0.1) !important;">
                                        <span class="d-block text-muted small mb-1">Total Nilai Kontribusi</span>
                                        <strong class="fs-4 text-dark">Rp {{ number_format($stats['lpj_expense'] ?? 0, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>

                            @if(!empty($stats['lpj_list']))
                                <div class="list-group rounded-4 border-0">
                                    @foreach($stats['lpj_list'] as $lpj)
                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 mb-2 rounded-3 border bg-light">
                                            <div>
                                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: var(--museum-dark);">{{ $lpj['title'] }}</h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar-event me-1"></i>Tahun {{ $lpj['verified_at'] ?? '-' }} 
                                                    <span class="mx-2">•</span> 
                                                    <i class="bi bi-wallet2 me-1"></i>Rp {{ number_format($lpj['total_expense'], 0, ',', '.') }}
                                                </small>
                                            </div>
                                            @if($lpj['pdf_url'])
                                                <a href="{{ $lpj['pdf_url'] }}" target="_blank" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold">
                                                    <i class="bi bi-file-pdf me-1"></i>LPJ
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 bg-light rounded-4 border border-dashed">
                                    <div class="fs-2">📋</div>
                                    <p class="text-muted mb-0 small">Belum ada dokumen LPJ yang diunggah atau diverifikasi.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kanan: Timeline Museum --}}
            <div class="col-lg-5">
                <div class="card border-0 rounded-4 shadow-sm h-100" style="background: linear-gradient(135deg, #1a0f00 0%, #2d1a00 100%); color: white;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-5 border-bottom pb-3 border-secondary">
                            <div class="rounded-3 p-2 me-3" style="background: rgba(212,160,23,0.1); color: var(--museum-gold);">
                                <i class="bi bi-signpost-split-fill fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-0" style="color: var(--museum-gold);">Timeline STEMAN</h4>
                        </div>
                        
                        <div class="timeline-container position-relative ps-4" style="border-left: 2px dashed rgba(212,160,23,0.3);">
                            
                            <div class="timeline-item mb-4 position-relative">
                                <div class="timeline-dot position-absolute" style="left: -29px; top: 3px; width: 14px; height: 14px; border-radius: 50%; background: var(--museum-gold); box-shadow: 0 0 10px rgba(212,160,23,0.5);"></div>
                                <h5 class="fw-bold text-white mb-1">1965</h5>
                                <p class="text-white-50 small mb-0">Lahirnya STM Nasional Ternate (Swasta)</p>
                            </div>

                            <div class="timeline-item mb-4 position-relative">
                                <div class="timeline-dot position-absolute" style="left: -29px; top: 3px; width: 14px; height: 14px; border-radius: 50%; background: var(--museum-gold);"></div>
                                <h5 class="fw-bold text-white mb-1">1980-an</h5>
                                <p class="text-white-50 small mb-0">Pembangunan kampus legendaris Dufa-Dufa (STM 80)</p>
                            </div>

                            <div class="timeline-item mb-4 position-relative">
                                <div class="timeline-dot position-absolute" style="left: -29px; top: 3px; width: 14px; height: 14px; border-radius: 50%; background: var(--museum-gold);"></div>
                                <h5 class="fw-bold text-white mb-1">1997</h5>
                                <p class="text-white-50 small mb-0">Berganti nama resmi menjadi <strong>SMKN 2 Ternate</strong></p>
                            </div>

                            <div class="timeline-item mb-4 position-relative">
                                <div class="timeline-dot position-absolute" style="left: -29px; top: 3px; width: 14px; height: 14px; border-radius: 50%; background: var(--museum-gold);"></div>
                                <h5 class="fw-bold text-white mb-1">2000-an</h5>
                                <p class="text-white-50 small mb-0">Penambahan program keahlian baru menyesuaikan perkembangan zaman</p>
                            </div>

                            <div class="timeline-item position-relative">
                                <div class="timeline-dot position-absolute" style="left: -29px; top: 3px; width: 14px; height: 14px; border-radius: 50%; background: #198754; box-shadow: 0 0 15px rgba(25,135,84,0.8);"></div>
                                <h5 class="fw-bold text-white mb-1" style="color: #198754 !important;">2026</h5>
                                <p class="text-white-50 small mb-2">Reuni Akbar STEMAN dengan panggung megah, videotron LED, serta peluncuran website alumni-steman.my.id.</p>
                                
                                <div class="p-3 mt-4 rounded-3 border border-secondary" style="background: rgba(255,255,255,0.03);">
                                    <p class="mb-2 fs-6 fw-bold" style="color: var(--museum-gold);"><i class="bi bi-lightbulb-fill me-1"></i> Lengkapi Galeri Sejarah Kita!</p>
                                    <ul class="text-white-50 small mb-0 ps-3">
                                        <li>Foto Kepala Sekolah & Guru legendaris</li>
                                        <li>Daftar pengurus OSIS lintas angkatan</li>
                                        <li>Foto bangunan sekolah tiap dekade</li>
                                        <li>Dokumentasi Reuni Akbar terdahulu</li>
                                    </ul>
                                </div>
                            </div>

                        </div>
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
            <div class="col-6 col-md-4 col-lg-3">
                <div class="museum-card h-100" onclick="window.location='{{ route('museum.show', $item) }}'">
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

{{-- ADD PRINCIPAL MODAL --}}
@auth
@if(auth()->user()->role === 'admin' || auth()->user()->role === 'editor')
<div class="modal fade" id="addPrincipalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">👤 Tambah Kepala Sekolah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('museum.principals.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Mustafa Muhammad, S.Pd., MM." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Periode Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="period" class="form-control rounded-3" placeholder="Contoh: 2018 - Sekarang" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="former">Terdahulu</option>
                                <option value="active">Aktif (Saat Ini)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Foto</label>
                            <input type="file" name="photo" class="form-control rounded-3" accept="image/*">
                            <div class="form-text">JPG/PNG, maks 2MB.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Urutan Tampil <span class="text-danger">*</span></label>
                            <input type="number" name="sort_order" class="form-control rounded-3" value="0" min="0" required>
                            <div class="form-text">Semakin kecil = tampil lebih dulu.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold rounded-pill px-4">
                        <i class="bi bi-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT PRINCIPAL MODALS (one per principal) --}}
@foreach($principals as $p)
<div class="modal fade" id="editPrincipalModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">✏️ Edit Kepala Sekolah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('museum.principals.update', $p) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-3" value="{{ $p->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Periode Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="period" class="form-control rounded-3" value="{{ $p->period }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="former" {{ $p->status === 'former' ? 'selected' : '' }}>Terdahulu</option>
                                <option value="active" {{ $p->status === 'active' ? 'selected' : '' }}>Aktif (Saat Ini)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ganti Foto</label>
                            <input type="file" name="photo" class="form-control rounded-3" accept="image/*">
                            @if($p->photo_path)
                                <div class="form-text text-success"><i class="bi bi-check-circle me-1"></i>Foto saat ini sudah ada. Kosongkan jika tidak ingin mengganti.</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Urutan Tampil <span class="text-danger">*</span></label>
                            <input type="number" name="sort_order" class="form-control rounded-3" value="{{ $p->sort_order }}" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold rounded-pill px-4">
                        <i class="bi bi-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif
@endauth

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
