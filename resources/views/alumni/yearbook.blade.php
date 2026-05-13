@extends('layouts.app')

@section('content')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
/* ===== YEARBOOK PAGE CORE ===== */
.yearbook-page {
    min-height: 100vh;
    background: radial-gradient(ellipse at 20% 10%, #1a0533 0%, #0d1117 50%, #001a0d 100%);
    padding: 2rem 0 6rem;
    overflow-x: hidden;
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
    background: linear-gradient(135deg, #a855f7, #6366f1);
    color: #fff;
    font-size: 0.72rem;
    letter-spacing: 3px;
    text-transform: uppercase;
    padding: 6px 18px;
    border-radius: 50px;
    margin-bottom: 1rem;
}
.yb-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.2rem, 6vw, 4rem);
    font-weight: 700;
    color: #fff;
    line-height: 1.15;
    margin-bottom: 0.5rem;
}
.yb-hero h1 span {
    background: linear-gradient(135deg, #a855f7, #38bdf8);
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
    border-radius: 50px;
    border: 1.5px solid rgba(168,85,247,0.35);
    background: rgba(168,85,247,0.08);
    color: #c4b5fd;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    text-decoration: none;
}
.year-btn:hover, .year-btn.active {
    background: linear-gradient(135deg, #a855f7, #6366f1);
    border-color: transparent;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(168,85,247,0.35);
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

/* Cover styling */
.page-cover {
    background: linear-gradient(160deg, #1e1b4b 0%, #312e81 40%, #1a0533 100%) !important;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 40px 30px;
    position: relative;
    overflow: hidden;
    border-radius: 4px;
}
.page-cover::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.07'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.page-cover-emblem {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a855f7, #38bdf8);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2.2rem;
    box-shadow: 0 0 40px rgba(168,85,247,0.5);
    position: relative;
    z-index: 1;
}
.page-cover-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
    position: relative;
    z-index: 1;
    text-shadow: 0 2px 20px rgba(0,0,0,0.5);
}
.page-cover-subtitle {
    color: #c4b5fd;
    font-size: 0.9rem;
    margin-top: 0.75rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    position: relative;
    z-index: 1;
}
.page-cover-year {
    margin-top: 2rem;
    font-size: 3.5rem;
    font-weight: 900;
    font-family: 'Playfair Display', serif;
    color: rgba(255,255,255,0.12);
    line-height: 1;
    position: relative;
    z-index: 1;
}

/* Inner pages */
.page-inner {
    background: #faf8f5;
    padding: 24px 20px;
    height: 100%;
    overflow: hidden;
}
.page-inner.dark-page {
    background: #1e1b4b;
}
.dark .page-inner {
    background: #0f172a;
}
.page-header {
    border-bottom: 2px solid #e8e0f0;
    padding-bottom: 10px;
    margin-bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.dark .page-header {
    border-color: #334155;
}
.page-header-title {
    font-family: 'Playfair Display', serif;
    font-size: 0.95rem;
    color: #7c3aed;
    font-style: italic;
}
.page-number {
    font-size: 0.72rem;
    color: #94a3b8;
}

/* Alumni Card on page */
.alumni-card-yb {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 10px 6px;
    border-radius: 12px;
    transition: background 0.2s;
}
.alumni-card-yb:hover {
    background: rgba(168,85,247,0.06);
}
.alumni-avatar-yb {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e9d5ff;
    margin-bottom: 8px;
    flex-shrink: 0;
}
.dark .alumni-avatar-yb { border-color: #4c1d95; }
.alumni-name-yb {
    font-weight: 700;
    font-size: 0.78rem;
    color: #1e293b;
    line-height: 1.2;
    margin-bottom: 2px;
    word-break: break-word;
}
.dark .alumni-name-yb { color: #e2e8f0; }
.alumni-major-yb {
    font-size: 0.68rem;
    color: #7c3aed;
    font-weight: 500;
}
.alumni-job-yb {
    font-size: 0.65rem;
    color: #64748b;
    margin-top: 2px;
}
.dark .alumni-job-yb { color: #94a3b8; }
.alumni-quote-yb {
    font-size: 0.63rem;
    color: #94a3b8;
    font-style: italic;
    margin-top: 4px;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
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
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #a855f7, #6366f1);
    color: #fff;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.25s;
    box-shadow: 0 4px 20px rgba(168,85,247,0.4);
}
.book-nav-btn:hover { transform: scale(1.1); box-shadow: 0 8px 30px rgba(168,85,247,0.55); }
.book-nav-btn:disabled { opacity: 0.3; cursor: not-allowed; transform: none; }
.page-info-text {
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
    background: #fff;
    position: relative;
}
.story-author {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px dashed #e2e8f0;
}
.story-author-img {
    width: 40px; height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.story-author-info {
    display: flex;
    flex-direction: column;
}
.story-author-name {
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e293b;
}
.story-date {
    font-size: 0.65rem;
    color: #94a3b8;
}
.story-content {
    font-size: 0.82rem;
    line-height: 1.6;
    color: #334155;
    flex-grow: 1;
    overflow-y: auto;
}
.story-image {
    width: 100%;
    max-height: 180px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 12px;
    border: 1px solid #e2e8f0;
}
.story-quote-mark {
    font-family: 'Playfair Display', serif;
    font-size: 3rem;
    color: rgba(124,58,237,0.1);
    position: absolute;
    top: 70px;
    right: 20px;
    line-height: 1;
    z-index: 0;
}

/* ===== DIVIDER PAGE ===== */
.divider-page {
    background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 40px;
    height: 100%;
    color: white;
}
.divider-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 10px;
}
.divider-subtitle {
    font-size: 0.85rem;
    color: #ddd6fe;
    font-style: italic;
}

/* ===== MODALS (GLASSMORPHISM) ===== */
.glass-modal {
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.glass-modal .modal-content {
    background: rgba(30, 41, 59, 0.85);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    color: #f8fafc;
    border-radius: 16px;
}
.glass-modal .modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.glass-modal .modal-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}
.glass-modal .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}
.modal-avatar-lg {
    width: 120px; height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #a855f7;
    box-shadow: 0 0 20px rgba(168,85,247,0.4);
    margin-top: -60px;
    background: #1e293b;
}

/* ===== EMPTY STATE ===== */
.empty-yearbook {
    text-align: center;
    padding: 5rem 2rem;
    color: #64748b;
}
.empty-yearbook i { font-size: 4rem; opacity: 0.3; }

/* ===== DARK MODE ===== */
.dark .page-inner { background: #0f172a; }
.dark .page-header { border-color: #1e293b; }
.dark .alumni-name-yb { color: #f1f5f9; }

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
const PER_PAGE = 6;

function buildCoverPage(type) {
    const div = document.createElement('div');
    div.className = 'page-cover';
    if (type === 'front') {
        div.innerHTML = `
            <div class="page-cover-emblem">📚</div>
            <div class="page-cover-title">Buku Kenangan<br>${escapeHtml(schoolName)}</div>
            <div class="page-cover-subtitle">Angkatan ${escapeHtml(activeYear)}</div>
            <div class="page-cover-year">${escapeHtml(activeYear)}</div>
        `;
    } else {
        div.innerHTML = `
            <div class="page-cover-emblem" style="background: linear-gradient(135deg,#10b981,#3b82f6);">🎓</div>
            <div class="page-cover-title" style="font-size:1.3rem;">Semoga Sukses Selalu</div>
            <div class="page-cover-subtitle" style="margin-top:1rem;">STEMAN Alumni &bull; ${escapeHtml(activeYear)}</div>
            <div style="color:rgba(255,255,255,0.3); font-size:0.75rem; margin-top:3rem; position:relative;z-index:1;">alumni-steman.my.id</div>
        `;
    }
    return div;
}

function buildAlumniPage(chunk, pageNum, totalPages) {
    const div = document.createElement('div');
    div.className = 'page-inner';
    div.innerHTML = `
        <div class="page-header">
            <span class="page-header-title">Angkatan ${escapeHtml(activeYear)}</span>
            <span class="page-number">hal. ${pageNum} / ${totalPages}</span>
        </div>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px;">
            ${chunk.map(a => `
            <div class="alumni-card-yb" onclick="openAlumniModal(${a.id})" style="cursor:pointer;" title="Klik untuk lihat profil">
                <img src="${escapeHtml(avatarUrl(a))}" alt="${escapeHtml(a.name)}"
                     class="alumni-avatar-yb"
                     onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(a.name)}&background=7c3aed&color=fff&size=200&bold=true'">
                <div class="alumni-name-yb">${escapeHtml(a.name)}</div>
                <div class="alumni-major-yb">${escapeHtml(a.major || '-')}</div>
                ${a.current_job ? `<div class="alumni-job-yb">${escapeHtml(a.current_job)}${a.company_university ? ' @ '+escapeHtml(a.company_university) : ''}</div>` : ''}
                ${a.bio ? `<div class="alumni-quote-yb">"${escapeHtml(a.bio)}"</div>` : ''}
            </div>`).join('')}
        </div>
    `;
    return div;
}

function buildDividerPage(title, subtitle) {
    const div = document.createElement('div');
    div.className = 'divider-page';
    div.innerHTML = `
        <div class="divider-title">${escapeHtml(title)}</div>
        <div class="divider-subtitle">"${escapeHtml(subtitle)}"</div>
        <div style="font-size:2rem; margin-top:20px; opacity:0.5;">✨</div>
    `;
    return div;
}

function buildStoryPage(post, pageNum, totalPages) {
    const div = document.createElement('div');
    div.className = 'story-page';
    
    // Format date roughly
    const dateObj = new Date(post.created_at);
    const dateStr = dateObj.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    
    let imgHtml = '';
    if (post.image_url) {
        imgHtml = `<img src="${escapeHtml(post.image_url)}" class="story-image" alt="Story Image">`;
    }

    div.innerHTML = `
        <div class="page-header" style="border-bottom:none; margin-bottom:0;">
            <span class="page-header-title">Cerita Kenangan</span>
            <span class="page-number">hal. ${pageNum} / ${totalPages}</span>
        </div>
        <div class="story-quote-mark">"</div>
        <div class="story-author">
            <img src="${escapeHtml(avatarUrl(post.user))}" alt="${escapeHtml(post.user.name)}" class="story-author-img">
            <div class="story-author-info">
                <span class="story-author-name">${escapeHtml(post.user.name)}</span>
                <span class="story-date">${escapeHtml(dateStr)}</span>
            </div>
        </div>
        <div class="story-content" style="position:relative; z-index:1; cursor:pointer;" onclick="openStoryModal(${post.id})" title="Klik untuk membaca selengkapnya">
            ${imgHtml}
            <div style="white-space: pre-wrap; display:-webkit-box; -webkit-line-clamp:8; -webkit-box-orient:vertical; overflow:hidden;">${escapeHtml(post.content)}</div>
            <div class="text-primary mt-2 small fw-bold">Baca selengkapnya &rarr;</div>
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
    leftEl.style.cssText = `width:50%;height:100%;overflow:hidden;border-right:3px solid rgba(124,58,237,0.4);`;

    // Right page display
    const rightEl = document.createElement('div');
    rightEl.style.cssText = `width:50%;height:100%;overflow:hidden;`;

    // Spine glow
    const spineEl = document.createElement('div');
    spineEl.style.cssText = `
        position:absolute;left:50%;top:0;bottom:0;
        width:6px;transform:translateX(-50%);
        background:linear-gradient(180deg,#a855f7,#6366f1,#a855f7);
        box-shadow:0 0 20px rgba(168,85,247,0.8);
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
