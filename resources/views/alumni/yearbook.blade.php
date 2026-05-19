@extends('layouts.app')

@section('content')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
/* ===== YEARBOOK PAGE CORE ===== */
.yearbook-page {
    min-height: 100vh;
    background: radial-gradient(circle at 50% 10%, rgba(255, 159, 28, 0.08) 0%, rgba(0, 180, 216, 0.05) 40%, #080b11 100%);
    background-color: #080b11;
    padding: 2.5rem 0 6rem;
    overflow-x: hidden;
    color: #e2e8f0;
    font-family: 'Inter', sans-serif;
}

/* ===== HERO HEADER ===== */
.yb-hero {
    text-align: center;
    padding: 3rem 1rem 2rem;
    opacity: 0;
    transform: translateY(-40px);
}
.yb-hero .badge-pill {
    display: inline-block;
    background: #ff9f1c;
    color: #000;
    font-family: 'Courier New', Courier, monospace;
    font-weight: 700;
    font-size: 0.72rem;
    letter-spacing: 3px;
    text-transform: uppercase;
    padding: 6px 18px;
    border-radius: 4px;
    margin-bottom: 1rem;
    box-shadow: 0 4px 15px rgba(255, 159, 28, 0.3);
}
.yb-hero h1 {
    font-family: 'Inter', sans-serif;
    font-size: clamp(2.2rem, 6vw, 4rem);
    font-weight: 900;
    color: #fff;
    line-height: 1.15;
    margin-bottom: 0.5rem;
    letter-spacing: -1px;
    text-transform: uppercase;
}
.yb-hero h1 span {
    background: linear-gradient(135deg, #00b4d8, #0077b6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.yb-hero p {
    color: #94a3b8;
    font-size: 1.05rem;
    max-width: 520px;
    margin: 0 auto 2rem;
}

/* ===== YEAR FILTER BAR ===== */
.year-filter-bar {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 2.5rem;
    opacity: 0;
    transform: translateY(20px);
}
.year-btn {
    padding: 8px 20px;
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.05);
    color: #94a3b8;
    font-size: 0.85rem;
    font-weight: 600;
    font-family: 'Courier New', Courier, monospace;
    cursor: pointer;
    transition: all 0.25s ease;
    text-decoration: none;
}
.year-btn:hover, .year-btn.active {
    background: #ff9f1c;
    border-color: transparent;
    color: #000;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(255, 159, 28, 0.25);
}

/* ===== BOOK WRAPPER ===== */
.book-scene {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 0 1rem 3rem;
    opacity: 0;
    transform: scale(0.92);
}

/* ===== PAGE FLIP BOOK STYLES ===== */
.stf__parent {
    cursor: pointer;
}

/* Cinematic Cover styling */
.page-cover {
    background: #11141c !important;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 40px 30px;
    position: relative;
    overflow: hidden;
    border-radius: 4px;
    box-sizing: border-box;
    height: 100%;
    box-shadow: inset 0 0 60px rgba(0, 0, 0, 0.8) !important;
}

/* Film Strip Edges on Cover */
.cover-film-edge {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 24px;
    background: #000;
    display: flex;
    flex-direction: column;
    justify-content: space-evenly;
    align-items: center;
    padding: 10px 0;
    border-right: 1px solid #222;
}
.cover-film-edge.right {
    left: auto;
    right: 0;
    border-right: none;
    border-left: 1px solid #222;
}
.sprocket {
    width: 10px;
    height: 14px;
    background: #2a2e38;
    border-radius: 2px;
    box-shadow: inset 0 0 4px rgba(0,0,0,0.8);
}

.cover-content {
    position: relative;
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}

.page-cover-emblem {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: #000;
    border: 2px solid #ff9f1c;
    box-shadow: 0 0 25px rgba(255, 159, 28, 0.4), inset 0 0 10px rgba(255, 159, 28, 0.4);
    color: #ff9f1c;
    font-size: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}
.page-cover-title {
    font-family: 'Inter', sans-serif;
    font-size: 1.8rem;
    font-weight: 900;
    color: #fff;
    letter-spacing: 4px;
    line-height: 1.2;
    text-transform: uppercase;
}
.page-cover-school {
    font-family: 'Courier New', Courier, monospace;
    font-weight: bold;
    color: #00b4d8;
    font-size: 1.1rem;
    margin: 0.5rem 0 1.2rem 0;
    background: rgba(0, 180, 216, 0.1);
    padding: 4px 12px;
    border-radius: 4px;
}
.page-cover-divider {
    width: 60px;
    height: 3px;
    background: #334155;
    margin: 1rem auto;
}
.page-cover-subtitle {
    color: #64748b;
    font-size: 0.8rem;
    font-family: 'Courier New', Courier, monospace;
    letter-spacing: 3px;
    text-transform: uppercase;
}
.page-cover-year {
    margin-top: 1.5rem;
    font-size: 3.5rem;
    font-weight: 900;
    font-family: 'Inter', sans-serif;
    color: #fff;
    line-height: 1;
    text-shadow: 0 0 20px rgba(255,255,255,0.2);
}

/* Inner pages - Film Negative/Darkroom feel */
.page-inner {
    background: #181b22;
    padding: 24px;
    height: 100%;
    overflow: hidden;
    box-sizing: border-box;
    box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.5);
    border-left: 1px solid #2a2e38;
    border-right: 1px solid #2a2e38;
}
.page-header {
    border-bottom: 1px dashed #334155;
    padding-bottom: 8px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.page-header-title {
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.9rem;
    color: #ff9f1c;
    font-weight: 700;
    letter-spacing: 1px;
}
.page-number {
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.8rem;
    color: #64748b;
}

/* Cinematic Film Strip Frame for Alumni */
.alumni-card-yb {
    display: flex;
    flex-direction: column;
    padding: 0;
    background: #000;
    border: 1px solid #333;
    border-radius: 4px;
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}
.alumni-card-yb:hover {
    transform: scale(1.05);
    box-shadow: 0 0 25px rgba(255, 159, 28, 0.3);
    border-color: #ff9f1c;
    z-index: 5;
}
.film-sprocket-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 8px;
    background: #000;
}
.film-sprocket-row .sprocket-mini {
    width: 6px;
    height: 8px;
    background: #2a2e38;
    border-radius: 1px;
}
.alumni-avatar-yb {
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    filter: grayscale(20%) sepia(30%) contrast(1.2);
    border-top: 2px solid #000;
    border-bottom: 2px solid #000;
}
.alumni-card-yb:hover .alumni-avatar-yb {
    filter: grayscale(0%) sepia(0%) contrast(1.1);
}
.alumni-info-box {
    padding: 8px;
    background: #000;
}
.alumni-name-yb {
    font-family: 'Courier New', Courier, monospace;
    font-weight: 700;
    font-size: 0.75rem;
    color: #fff;
    line-height: 1.2;
    margin-bottom: 4px;
    word-break: break-word;
    text-transform: uppercase;
}
.alumni-major-yb {
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.65rem;
    color: #00b4d8;
    font-weight: 600;
}
.alumni-job-yb {
    font-family: 'Inter', sans-serif;
    font-size: 0.65rem;
    color: #94a3b8;
    margin-top: 4px;
}
.frame-number {
    position: absolute;
    bottom: 8px;
    right: 8px;
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.6rem;
    color: #ff9f1c;
    font-weight: bold;
}

/* ===== BOOK CONTROLS ===== */
.book-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 1.5rem;
    opacity: 0;
}
.book-nav-btn {
    width: 48px; height: 48px;
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.05);
    color: #fff;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.25s;
}
.book-nav-btn:hover { background: #ff9f1c; color: #000; border-color: transparent; box-shadow: 0 0 15px rgba(255, 159, 28, 0.4); }
.book-nav-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.page-info-text {
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.85rem;
    color: #94a3b8;
    min-width: 120px;
    text-align: center;
}

/* ===== STORY PAGES ===== */
.story-page {
    display: flex;
    flex-direction: column;
    padding: 24px;
    height: 100%;
    background: #11141c !important;
    position: relative;
    box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.6);
}
.story-author {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px dashed #334155;
}
.story-author-img {
    width: 40px; height: 40px;
    border-radius: 4px;
    object-fit: cover;
    border: 1px solid #475569;
}
.story-author-info {
    display: flex;
    flex-direction: column;
}
.story-author-name {
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.85rem;
    font-weight: 700;
    color: #e2e8f0;
}
.story-date {
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.65rem;
    color: #ff9f1c;
}
.story-content {
    font-family: 'Inter', sans-serif;
    font-size: 0.85rem;
    line-height: 1.6;
    color: #cbd5e1;
    flex-grow: 1;
    overflow-y: auto;
}
.story-image {
    width: 100%;
    max-height: 180px;
    object-fit: cover;
    border-radius: 4px;
    margin-bottom: 12px;
    border: 2px solid #334155;
    filter: grayscale(10%) contrast(1.1);
}
.story-quote-mark {
    font-family: 'Courier New', Courier, monospace;
    font-size: 4rem;
    color: rgba(255, 159, 28, 0.1);
    position: absolute;
    top: 60px;
    right: 20px;
    line-height: 1;
    z-index: 0;
}

/* ===== DIVIDER PAGE ===== */
.divider-page {
    background: #000 !important;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 40px;
    height: 100%;
    color: #fff;
    position: relative;
    border-left: 2px dashed #333;
    border-right: 2px dashed #333;
}
.divider-title {
    font-family: 'Inter', sans-serif;
    font-size: 1.8rem;
    font-weight: 900;
    color: #fff;
    margin-bottom: 10px;
    letter-spacing: 2px;
    text-transform: uppercase;
}
.divider-subtitle {
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.85rem;
    color: #00b4d8;
    background: rgba(0, 180, 216, 0.1);
    padding: 4px 12px;
    border-radius: 4px;
}

/* ===== MODALS (GLASSMORPHISM) ===== */
.glass-modal {
    background: rgba(8, 11, 17, 0.8);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}
.glass-modal .modal-content {
    background: #11141c;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
    color: #e2e8f0;
    border-radius: 8px;
    border: 1px solid #334155;
}
.glass-modal .modal-header {
    border-bottom: 1px dashed #334155;
}
.glass-modal .modal-footer {
    border-top: 1px dashed #334155;
}
.glass-modal .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}
.modal-avatar-lg {
    width: 120px; height: 120px;
    border-radius: 4px;
    object-fit: cover;
    border: 4px solid #000;
    box-shadow: 0 0 0 2px #ff9f1c;
    margin-top: -60px;
    background: #000;
}

/* ===== EMPTY STATE ===== */
.empty-yearbook {
    text-align: center;
    padding: 5rem 2rem;
    color: #475569;
}
.empty-yearbook i { font-size: 4rem; opacity: 0.5; }

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .yb-hero h1 { font-size: 2rem; }
    .book-scene { padding: 0 0 3rem; }
}
</style>
@endpush

<div class="yearbook-page">
    <div class="container">

        {{-- HERO --}}
        <div class="yb-hero" id="yb-hero">
            <div class="badge-pill">📖 Digital Yearbook</div>
            <h1>Buku Kenangan <span>STEMAN</span></h1>
            <p>Kenangan indah masa sekolah, kini hadir dalam format digital yang abadi.</p>
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show text-center" style="max-width: 600px; margin: 0 auto 2rem;" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show text-center" style="max-width: 600px; margin: 0 auto 2rem;" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- YEAR FILTER & WRITE BUTTON --}}
        <div class="year-filter-bar" id="year-filter-bar">
            @forelse($years as $yr)
                <a href="{{ route('alumni.yearbook', ['year' => $yr]) }}"
                   class="year-btn {{ $yr == $activeYear ? 'active' : '' }}">
                    Angkatan {{ $yr }}
                </a>
            @empty
                <span class="text-muted small">Belum ada data angkatan.</span>
            @endforelse
            
            @auth
                @if(auth()->user()->graduation_year == $activeYear && in_array(auth()->user()->status, ['active', 'approved']))
                    <button class="year-btn" style="background: linear-gradient(135deg, #10b981, #3b82f6); color:white; border:none; margin-left: 1rem;" data-bs-toggle="modal" data-bs-target="#writeYearbookModal">
                        <i class="bi bi-pencil-square me-1"></i> Tulis Kenangan
                    </button>
                @endif
            @endauth
        </div>

        {{-- BOOK SCENE --}}
        <div class="book-scene" id="book-scene">
            @if($alumni->count() > 0)
                <div id="flipbook-container" style="position:relative; width:100%; max-width:900px; display:flex; flex-direction:column; align-items:center;"></div>
            @else
                <div class="empty-yearbook">
                    <i class="bi bi-book d-block mb-3"></i>
                    <h5>Belum ada alumni untuk Angkatan {{ $activeYear }}</h5>
                    <p class="small">Pilih tahun lain atau tunggu hingga data diperbarui.</p>
                </div>
            @endif
        </div>

        {{-- BOOK CONTROLS --}}
        @if($alumni->count() > 0)
        <div class="book-controls" id="book-controls">
            <button class="book-nav-btn" id="prev-btn" disabled title="Halaman Sebelumnya">
                <i class="bi bi-chevron-left"></i>
            </button>
            <span class="page-info-text" id="page-info">Halaman 1</span>
            <button class="book-nav-btn" id="next-btn" title="Halaman Berikutnya">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
        @endif

    </div>
</div>

{{-- MODALS --}}

{{-- Modal Tulis Kenangan --}}
<div class="modal fade glass-modal" id="writeYearbookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('alumni.yearbook.message') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pen"></i> Tulis di Buku Kenangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <p class="text-muted small mb-3">Pesan Anda akan abadi di Buku Kenangan Angkatan {{ $activeYear }}.</p>
                    <div class="mb-3">
                        <textarea name="content" class="form-control bg-dark text-light border-secondary" rows="4" placeholder="Ceritakan kenangan tak terlupakan, pesan untuk teman-teman, atau pesan untuk sekolah..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Unggah Foto Kenangan (Opsional)</label>
                        <input type="file" name="image" class="form-control bg-dark text-light border-secondary" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: linear-gradient(135deg, #a855f7, #6366f1); border:none;">Simpan Kenangan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Detail Alumni --}}
<div class="modal fade glass-modal" id="alumniDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center pt-5 pb-4 px-3 mt-4">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="position-absolute top-0 start-50 translate-middle">
                <img src="" id="modalAlumniImg" class="modal-avatar-lg" alt="">
            </div>
            <h4 class="fw-bold mt-4 mb-1" id="modalAlumniName">Nama Alumni</h4>
            <div class="text-purple fw-semibold mb-3" id="modalAlumniMajor" style="color:#c4b5fd;">Jurusan</div>
            
            <div class="d-flex justify-content-center gap-3 mb-4 text-muted small">
                <div id="modalAlumniJobContainer" class="d-none">
                    <i class="bi bi-briefcase text-info"></i> <span id="modalAlumniJob"></span>
                </div>
                <div id="modalAlumniCompanyContainer" class="d-none">
                    <i class="bi bi-building text-warning"></i> <span id="modalAlumniCompany"></span>
                </div>
            </div>

            <div class="bg-dark rounded-4 p-3 text-start mx-auto" style="max-width: 90%; border:1px solid rgba(255,255,255,0.05);">
                <i class="bi bi-quote fs-3 text-secondary opacity-50"></i>
                <p class="fst-italic text-light mb-0" id="modalAlumniBio" style="font-size: 0.9rem; line-height: 1.6;"></p>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Kenangan --}}
<div class="modal fade glass-modal" id="editYearbookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editYearbookForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Edit Kenangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="mb-3">
                        <textarea name="content" id="editYearbookContent" class="form-control bg-dark text-light border-secondary" rows="6" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: linear-gradient(135deg, #10b981, #3b82f6); border:none;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Detail Cerita --}}
<div class="modal fade glass-modal" id="storyDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div class="d-flex align-items-center gap-3">
                    <img src="" id="modalStoryAuthorImg" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                    <div>
                        <h6 class="mb-0 fw-bold" id="modalStoryAuthorName">Penulis</h6>
                        <small class="text-muted" id="modalStoryDate">Tanggal</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="" id="modalStoryImage" class="img-fluid rounded-3 mb-4 d-none" style="width:100%; max-height:400px; object-fit:cover;">
                <div id="modalStoryContent" style="white-space: pre-wrap; font-size: 1rem; line-height: 1.8;"></div>
            </div>
            <div class="modal-footer border-0 pt-0 d-none" id="modalStoryActions">
                <form id="deleteStoryForm" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kenangan ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4 btn-sm"><i class="bi bi-trash"></i> Hapus</button>
                </form>
                <button type="button" class="btn btn-info rounded-pill px-4 btn-sm" onclick="openEditStoryModal()"><i class="bi bi-pencil"></i> Edit</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://unpkg.com/page-flip@2.0.7/dist/js/page-flip.browser.js"></script>

<script>
// ===== DATA =====
const alumniData = @json($alumni);
const postsData  = @json($posts ?? []);
const activeYear  = "{{ $activeYear }}";
const schoolName  = "{{ setting('school_name','SMKN 2 Ternate') }}";
const authUserId  = {{ auth()->id() ?? 'null' }};

// ===== GSAP ENTRANCE ANIMATIONS =====
document.addEventListener('DOMContentLoaded', function () {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
    tl.to('#yb-hero',         { opacity: 1, y: 0, duration: 0.8 })
      .to('#year-filter-bar', { opacity: 1, y: 0, duration: 0.6 }, '-=0.4')
      .to('#book-scene',      { opacity: 1, scale: 1, duration: 0.7 }, '-=0.3')
      .to('#book-controls',   { opacity: 1, duration: 0.5 }, '-=0.2');
});

// ===== HELPERS =====
function avatarUrl(alumni) {
    if (alumni.profile_picture) return alumni.profile_picture;
    const initial = encodeURIComponent(alumni.name.charAt(0).toUpperCase());
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(alumni.name)}&background=7c3aed&color=fff&size=200&bold=true`;
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ===== BUILD PAGES =====
// 6 alumni per page grid (2 cols x 3 rows)
const PER_PAGE = 6function buildCoverPage(type) {
    const div = document.createElement('div');
    div.className = 'page-cover';
    
    // Film strip edge left
    const edgeLeft = document.createElement('div');
    edgeLeft.className = 'cover-film-edge';
    for(let i=0; i<15; i++) {
        const sprocket = document.createElement('div');
        sprocket.className = 'sprocket';
        edgeLeft.appendChild(sprocket);
    }
    
    // Film strip edge right
    const edgeRight = document.createElement('div');
    edgeRight.className = 'cover-film-edge right';
    for(let i=0; i<15; i++) {
        const sprocket = document.createElement('div');
        sprocket.className = 'sprocket';
        edgeRight.appendChild(sprocket);
    }

    div.appendChild(edgeLeft);
    div.appendChild(edgeRight);

    const contentDiv = document.createElement('div');
    contentDiv.className = 'cover-content';

    if (type === 'front') {
        contentDiv.innerHTML = `
            <div class="page-cover-subtitle" style="margin-bottom:2rem; color:#ff9f1c;">[ SAFETY FILM ]</div>
            <div class="page-cover-emblem"><i class="bi bi-camera-reels-fill"></i></div>
            <div class="page-cover-title">BUKU<br>KENANGAN</div>
            <div class="page-cover-school">${escapeHtml(schoolName)}</div>
            <div class="page-cover-divider"></div>
            <div class="page-cover-subtitle">ROLL ANGKATAN</div>
            <div class="page-cover-year">${escapeHtml(activeYear)}</div>
        `;
    } else {
        contentDiv.innerHTML = `
            <div class="page-cover-subtitle" style="margin-bottom:2rem; color:#00b4d8;">[ END OF ROLL ]</div>
            <div class="page-cover-emblem" style="border-color:#00b4d8; color:#00b4d8;"><i class="bi bi-film"></i></div>
            <div class="page-cover-title" style="font-size:1.4rem;">THE END</div>
            <div class="page-cover-school" style="font-size: 0.9rem; background:transparent;">SMK Negeri 2 Ternate</div>
            <div class="page-cover-divider"></div>
            <div class="page-cover-subtitle" style="font-size: 0.72rem;">STEMAN ALUMNI PORTAL</div>
            <div style="color:#334155; font-size:0.75rem; margin-top:2.5rem; font-family:'Courier New', monospace;">alumni-steman.my.id</div>
        `;
    }
    
    div.appendChild(contentDiv);
    return div;
}

function buildAlumniPage(chunk, pageNum, totalPages) {
    const div = document.createElement('div');
    div.className = 'page-inner';
    
    div.innerHTML = `
        <div class="page-header">
            <span class="page-header-title">ROLL ${escapeHtml(activeYear)}</span>
            <span class="page-number">FR. ${pageNum} / ${totalPages}</span>
        </div>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px 8px;">
            ${chunk.map((a, idx) => {
                const globalIndex = ((pageNum - 1) * PER_PAGE) + idx + 1;
                const frameStr = globalIndex.toString().padStart(2, '0');
                
                return `
                <div class="alumni-card-yb" onclick="openAlumniModal(${a.id})" style="cursor:pointer;" title="Klik untuk lihat profil">
                    <div class="film-sprocket-row">
                        <div class="sprocket-mini"></div><div class="sprocket-mini"></div><div class="sprocket-mini"></div><div class="sprocket-mini"></div><div class="sprocket-mini"></div>
                    </div>
                    <img src="${escapeHtml(avatarUrl(a))}" alt="${escapeHtml(a.name)}"
                         class="alumni-avatar-yb"
                         onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(a.name)}&background=000&color=fff&size=200&bold=true'">
                    <div class="film-sprocket-row" style="padding-bottom:2px;">
                        <div class="sprocket-mini"></div><div class="sprocket-mini"></div><div class="sprocket-mini"></div><div class="sprocket-mini"></div><div class="sprocket-mini"></div>
                    </div>
                    <div class="alumni-info-box">
                        <div class="alumni-name-yb">${escapeHtml(a.name)}</div>
                        <div class="alumni-major-yb">${escapeHtml(a.major || '-')}</div>
                        ${a.current_job ? `<div class="alumni-job-yb">${escapeHtml(a.current_job)}</div>` : ''}
                    </div>
                    <div class="frame-number">▶ ${frameStr}A</div>
                </div>`;
            }).join('')}
        </div>
    `;
    return div;
}

function buildDividerPage(title, subtitle) {
    const div = document.createElement('div');
    div.className = 'divider-page';
    div.innerHTML = `
        <div class="divider-title">${escapeHtml(title)}</div>
        <div class="divider-subtitle">[ ${escapeHtml(subtitle)} ]</div>
        <div style="font-size:2rem; margin-top:20px; color:#ff9f1c;"><i class="bi bi-camera-video-fill"></i></div>
    `;
    return div;
}

function buildStoryPage(post, pageNum, totalPages) {
    const div = document.createElement('div');
    div.className = 'story-page';
    
    const dateObj = new Date(post.created_at);
    const dateStr = dateObj.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' }).toUpperCase();
    
    let imgHtml = '';
    if (post.image_url) {
        imgHtml = `<img src="${escapeHtml(post.image_url)}" class="story-image" alt="Story Image">`;
    }

    div.innerHTML = `
        <div class="page-header" style="border-bottom:none; margin-bottom:0;">
            <span class="page-header-title">ARCHIVE LOG</span>
            <span class="page-number">FR. ${pageNum} / ${totalPages}</span>
        </div>
        <div class="story-quote-mark"><i class="bi bi-quote"></i></div>
        <div class="story-author">
            <img src="${escapeHtml(avatarUrl(post.user))}" alt="${escapeHtml(post.user.name)}" class="story-author-img">
            <div class="story-author-info">
                <span class="story-author-name">${escapeHtml(post.user.name)}</span>
                <span class="story-date">EXPOSED: ${escapeHtml(dateStr)}</span>
            </div>
        </div>
        <div class="story-content" style="position:relative; z-index:1; cursor:pointer;" onclick="openStoryModal(${post.id})" title="Klik untuk membaca selengkapnya">
            ${imgHtml}
            <div style="white-space: pre-wrap; display:-webkit-box; -webkit-line-clamp:5; -webkit-box-orient:vertical; overflow:hidden;">${escapeHtml(post.content)}</div>
            <div class="mt-2 small fw-bold" style="color: #00b4d8;">[ READ LOG ] &rarr;</div>
        </div>
    `;
    return div;
}

// ===== MODAL HANDLERS =====
function openAlumniModal(id) {
    const a = alumniData.find(x => x.id === id);
    if (!a) return;
    
    document.getElementById('modalAlumniImg').src = avatarUrl(a);
    document.getElementById('modalAlumniName').textContent = a.name;
    document.getElementById('modalAlumniMajor').textContent = a.major || 'Alumni STEMAN';
    
    if (a.current_job) {
        document.getElementById('modalAlumniJobContainer').classList.remove('d-none');
        document.getElementById('modalAlumniJob').textContent = a.current_job;
    } else {
        document.getElementById('modalAlumniJobContainer').classList.add('d-none');
    }
    
    if (a.company_university) {
        document.getElementById('modalAlumniCompanyContainer').classList.remove('d-none');
        document.getElementById('modalAlumniCompany').textContent = a.company_university;
    } else {
        document.getElementById('modalAlumniCompanyContainer').classList.add('d-none');
    }
    
    document.getElementById('modalAlumniBio').textContent = a.bio || "Belum ada bio/pesan kenangan dari alumni ini.";
    
    new bootstrap.Modal(document.getElementById('alumniDetailModal')).show();
}

let currentStoryId = null;

function openStoryModal(id) {
    const p = postsData.find(x => x.id === id);
    if (!p) return;
    
    currentStoryId = id;
    const dateObj = new Date(p.created_at);
    
    document.getElementById('modalStoryAuthorImg').src = avatarUrl(p.user);
    document.getElementById('modalStoryAuthorName').textContent = p.user.name;
    document.getElementById('modalStoryDate').textContent = dateObj.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('modalStoryContent').textContent = p.content;
    
    const imgEl = document.getElementById('modalStoryImage');
    if (p.image_url) {
        imgEl.src = p.image_url;
        imgEl.classList.remove('d-none');
    } else {
        imgEl.classList.add('d-none');
    }

    const actionsDiv = document.getElementById('modalStoryActions');
    if (authUserId && p.user_id === authUserId) {
        actionsDiv.classList.remove('d-none');
        document.getElementById('deleteStoryForm').action = `/alumni/yearbook/message/${id}`;
    } else {
        actionsDiv.classList.add('d-none');
    }
    
    new bootstrap.Modal(document.getElementById('storyDetailModal')).show();
}

function openEditStoryModal() {
    const p = postsData.find(x => x.id === currentStoryId);
    if (!p) return;

    // Sembunyikan modal detail
    const detailModal = bootstrap.Modal.getInstance(document.getElementById('storyDetailModal'));
    if (detailModal) detailModal.hide();

    // Isi form edit
    document.getElementById('editYearbookContent').value = p.content;
    document.getElementById('editYearbookForm').action = `/alumni/yearbook/message/${p.id}`;

    // Tampilkan modal edit
    new bootstrap.Modal(document.getElementById('editYearbookModal')).show();
}

// ===== CUSTOM FLIPBOOK (canvas-free, CSS-based) =====
if (alumniData.length > 0 || postsData.length > 0) {
    const container = document.getElementById('flipbook-container');

    // Split alumni into chunks
    const chunks = [];
    for (let i = 0; i < alumniData.length; i += PER_PAGE) {
        chunks.push(alumniData.slice(i, i + PER_PAGE));
    }
    const totalPages = chunks.length + 2; // +2 for covers

    // Build all pages
    const pages = [];
    pages.push(buildCoverPage('front'));
    
    // Alumni Pages
    chunks.forEach((chunk, idx) => {
        pages.push(buildAlumniPage(chunk, idx + 1, chunks.length));
    });
    
    // Story Pages
    if (postsData && postsData.length > 0) {
        // Need to ensure left/right spread logic holds. 
        // If the last alumni page is on the right (idx % 2 != 0), maybe add a blank page?
        // Actually our flipbook renderer just puts pages sequentially, so it doesn't matter too much,
        // but adding a divider is nice.
        pages.push(buildDividerPage('Cerita Kenangan', 'Jejak langkah yang tak terlupakan'));
        
        postsData.forEach((post, idx) => {
            pages.push(buildStoryPage(post, idx + 1, postsData.length));
        });
    }

    pages.push(buildCoverPage('back'));

    // Render book UI
    let currentPage = 0;
    const bookWidth  = Math.min(window.innerWidth - 32, 900);
    const pageWidth  = Math.floor(bookWidth / 2);
    const pageHeight = Math.floor(pageWidth * 1.38);

    const bookEl = document.createElement('div');
    bookEl.style.cssText = `
        display:flex;
        width:${bookWidth}px;
        height:${pageHeight}px;
        border-radius:8px;
        overflow:hidden;
        box-shadow:0 30px 80px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.05);
        position:relative;
    `;

    // Left page display
    const leftEl = document.createElement('div');
    leftEl.style.cssText = `width:50%;height:100%;overflow:hidden;border-right:1px solid #334155;`;

    // Right page display
    const rightEl = document.createElement('div');
    rightEl.style.cssText = `width:50%;height:100%;overflow:hidden;border-left:1px solid #000;`;

    // Spine glow (Cinematic Reel)
    const spineEl = document.createElement('div');
    spineEl.style.cssText = `
        position:absolute;left:50%;top:0;bottom:0;
        width:14px;transform:translateX(-50%);
        background:repeating-linear-gradient(180deg, #111, #111 8px, #000 8px, #000 16px);
        box-shadow:inset 0 0 10px rgba(0,0,0,0.9), 0 0 15px rgba(255, 159, 28, 0.15);
        border-left:1px dashed #334155;
        border-right:1px dashed #334155;
        z-index:10;
    `;

    bookEl.appendChild(leftEl);
    bookEl.appendChild(spineEl);
    bookEl.appendChild(rightEl);
    container.appendChild(bookEl);

    function renderPages(idx) {
        // Left = page[idx], Right = page[idx+1]
        leftEl.innerHTML  = '';
        rightEl.innerHTML = '';

        const lp = pages[idx]     ? pages[idx].cloneNode(true)     : null;
        const rp = pages[idx + 1] ? pages[idx + 1].cloneNode(true) : null;

        if (lp) { lp.style.cssText += 'width:100%;height:100%;overflow:hidden;box-sizing:border-box;'; leftEl.appendChild(lp); }
        if (rp) { rp.style.cssText += 'width:100%;height:100%;overflow:hidden;box-sizing:border-box;'; rightEl.appendChild(rp); }

        // Update controls
        const pageInfo = document.getElementById('page-info');
        const prevBtn  = document.getElementById('prev-btn');
        const nextBtn  = document.getElementById('next-btn');
        const spread   = Math.floor(idx / 2) + 1;
        const spreads  = Math.ceil(pages.length / 2);
        if (pageInfo) pageInfo.textContent = `Spread ${spread} / ${spreads}`;
        if (prevBtn)  prevBtn.disabled = (idx === 0);
        if (nextBtn)  nextBtn.disabled = (idx + 2 >= pages.length);
    }

    function flipTo(idx, dir) {
        const lp = leftEl.querySelector(':first-child');
        const rp = rightEl.querySelector(':first-child');
        const fromX = dir === 'next' ? 0 : 0;
        const toX   = dir === 'next' ? -pageWidth : pageWidth;

        gsap.to([lp, rp].filter(Boolean), {
            x: dir === 'next' ? [-pageWidth/2, pageWidth/2] : [pageWidth/2, -pageWidth/2],
            opacity: 0,
            duration: 0.3,
            ease: 'power2.in',
            onComplete: () => {
                currentPage = idx;
                renderPages(currentPage);
                gsap.fromTo(
                    [leftEl.querySelector(':first-child'), rightEl.querySelector(':first-child')].filter(Boolean),
                    { x: dir === 'next' ? pageWidth/3 : -pageWidth/3, opacity: 0 },
                    { x: 0, opacity: 1, duration: 0.35, ease: 'power2.out' }
                );
            }
        });
    }

    // Initial render
    renderPages(0);

    // Controls
    document.getElementById('prev-btn')?.addEventListener('click', () => {
        if (currentPage > 0) flipTo(Math.max(0, currentPage - 2), 'prev');
    });
    document.getElementById('next-btn')?.addEventListener('click', () => {
        if (currentPage + 2 < pages.length) flipTo(currentPage + 2, 'next');
    });

    // Keyboard navigation
    document.addEventListener('keydown', e => {
        if (e.key === 'ArrowRight' && currentPage + 2 < pages.length) flipTo(currentPage + 2, 'next');
        if (e.key === 'ArrowLeft'  && currentPage > 0) flipTo(Math.max(0, currentPage - 2), 'prev');
    });

    // Swipe support
    let touchStartX = 0;
    bookEl.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; });
    bookEl.addEventListener('touchend',   e => {
        const dx = e.changedTouches[0].clientX - touchStartX;
        if (dx < -50 && currentPage + 2 < pages.length) flipTo(currentPage + 2, 'next');
        if (dx >  50 && currentPage > 0)                flipTo(Math.max(0, currentPage - 2), 'prev');
    });
}
</script>
@endpush

@endsection
