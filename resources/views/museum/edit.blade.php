@extends('layouts.app')

@section('content')
<style>
:root {
    --museum-gold: #d4a017;
    --museum-dark: #1a0f00;
}
.edit-hero {
    background: linear-gradient(135deg, #1a0f00 0%, #2d1a00 40%, #1a0a00 100%);
    padding: 50px 0 40px;
}
.edit-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    overflow: hidden;
}
.edit-card .card-header-bar {
    background: linear-gradient(135deg, #d4a017, #b8860b);
    padding: 1.5rem 2rem;
    color: #1a0f00;
}
.edit-card .card-body { padding: 2rem; }
.edit-label { font-weight: 700; font-size: 0.85rem; color: #374151; margin-bottom: 6px; }
.preview-img-wrap {
    width: 100%;
    height: 220px;
    border-radius: 12px;
    overflow: hidden;
    background: #f5f0e8;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px dashed #e2d4b8;
    position: relative;
}
.preview-img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    filter: sepia(15%);
}
.preview-img-wrap .no-img { font-size: 3rem; color: #8b7355; text-align: center; }
.status-badge-pending   { background: #fef9c3; color: #854d0e; border: 1px solid #fde68a; }
.status-badge-approved  { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.status-badge-rejected  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

.dark .edit-card { background: #1e293b; }
.dark .edit-label { color: #94a3b8; }
</style>

{{-- HERO --}}
<section class="edit-hero">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('museum.show', $museumItem) }}" class="btn btn-outline-warning btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
            <div>
                <span class="badge px-3 py-1 rounded-pill mb-2 status-badge-{{ $museumItem->status }}">
                    {{ $museumItem->status === 'approved' ? '✅ Tampil di Museum' : ($museumItem->status === 'pending' ? '⏳ Menunggu Review' : '❌ Ditolak') }}
                </span>
                <h2 class="fw-black text-white mb-0">✏️ Edit Arsip Museum</h2>
                <p class="text-warning opacity-75 mb-0 small">Perubahan akan direview ulang oleh admin sebelum tampil.</p>
            </div>
        </div>
    </div>
</section>

{{-- FORM --}}
<section class="py-5" style="background: #f8fafc;">
    <div class="container">

        @if(session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger rounded-4 border-0 mb-4">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('museum.update', $museumItem) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">

                {{-- Kolom Kiri: Preview & Gambar --}}
                <div class="col-lg-4">
                    <div class="edit-card">
                        <div class="card-header-bar">
                            <h6 class="fw-black mb-0"><i class="bi bi-image me-2"></i>Foto Arsip</h6>
                        </div>
                        <div class="card-body">
                            {{-- Preview Gambar --}}
                            <div class="preview-img-wrap mb-3" id="imgPreviewWrap">
                                @if($museumItem->image_url)
                                    <img src="{{ $museumItem->image_url }}" alt="{{ $museumItem->title }}" id="imgPreview">
                                @else
                                    <div class="no-img">
                                        <div>{{ $museumItem->category_icon }}</div>
                                        <div style="font-size: 0.75rem; color: #8b7355; margin-top: 8px;">Belum ada foto</div>
                                    </div>
                                @endif
                            </div>

                            {{-- Upload Gambar Baru --}}
                            <div class="edit-label">Ganti Foto (opsional)</div>
                            <input type="file" name="image" id="imageInput" class="form-control rounded-3 mb-2" accept="image/*">
                            <div class="form-text mb-3">JPG/PNG/WebP, maks 3MB. Auto-convert ke WebP.</div>

                            {{-- Hapus Gambar --}}
                            @if($museumItem->image_url)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeImg">
                                <label class="form-check-label text-danger small fw-semibold" for="removeImg">
                                    <i class="bi bi-trash me-1"></i>Hapus foto ini
                                </label>
                            </div>
                            @endif

                            {{-- Info Status --}}
                            <hr>
                            <div class="small text-muted">
                                <div class="mb-1"><i class="bi bi-person me-1"></i>Diunggah oleh: <strong>{{ $museumItem->uploader->name }}</strong></div>
                                <div class="mb-1"><i class="bi bi-calendar me-1"></i>Tanggal: {{ $museumItem->created_at->format('d M Y') }}</div>
                                <div><i class="bi bi-eye me-1"></i>Dilihat: {{ number_format($museumItem->views) }} kali</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Detail Arsip --}}
                <div class="col-lg-8">
                    <div class="edit-card">
                        <div class="card-header-bar">
                            <h6 class="fw-black mb-0"><i class="bi bi-pencil-square me-2"></i>Detail Arsip</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                {{-- Judul --}}
                                <div class="col-12">
                                    <div class="edit-label">Judul Arsip <span class="text-danger">*</span></div>
                                    <input type="text" name="title" class="form-control rounded-3 @error('title') is-invalid @enderror"
                                           value="{{ old('title', $museumItem->title) }}"
                                           placeholder="contoh: Bengkel Mesin Tahun 1995" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Kategori & Era --}}
                                <div class="col-md-6">
                                    <div class="edit-label">Kategori <span class="text-danger">*</span></div>
                                    <select name="category" class="form-select rounded-3 @error('category') is-invalid @enderror" required>
                                        @foreach($categories as $key => $cat)
                                        <option value="{{ $key }}" {{ old('category', $museumItem->category) === $key ? 'selected' : '' }}>
                                            {{ $cat['icon'] }} {{ $cat['label'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <div class="edit-label">Tahun Era</div>
                                    <input type="number" name="era_year" class="form-control rounded-3 @error('era_year') is-invalid @enderror"
                                           value="{{ old('era_year', $museumItem->era_year) }}"
                                           placeholder="1990" min="1950" max="{{ date('Y') }}">
                                    @error('era_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Donatur & Video --}}
                                <div class="col-md-6">
                                    <div class="edit-label">Disumbang / Sumber</div>
                                    <input type="text" name="donated_by" class="form-control rounded-3"
                                           value="{{ old('donated_by', $museumItem->donated_by) }}"
                                           placeholder="Nama alumni / donatur">
                                </div>

                                <div class="col-md-6">
                                    <div class="edit-label">Link Video YouTube (opsional)</div>
                                    <input type="url" name="video_url" class="form-control rounded-3 @error('video_url') is-invalid @enderror"
                                           value="{{ old('video_url', $museumItem->video_url) }}"
                                           placeholder="https://youtube.com/...">
                                    @error('video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Deskripsi --}}
                                <div class="col-12">
                                    <div class="edit-label">Deskripsi / Cerita Sejarah</div>
                                    <textarea name="description" class="form-control rounded-3 @error('description') is-invalid @enderror"
                                              rows="5" placeholder="Ceritakan tentang arsip ini, tahun berapa, siapa yang terlibat, apa maknanya...">{{ old('description', $museumItem->description) }}</textarea>
                                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">Maks 2000 karakter</div>
                                </div>

                                {{-- Info review --}}
                                @auth
                                @unless(auth()->user()->hasRole(['admin', 'editor']))
                                <div class="col-12">
                                    <div class="alert alert-info rounded-3 small border-0 mb-0" style="background: #eff6ff;">
                                        <i class="bi bi-info-circle me-2 text-primary"></i>
                                        Setelah diedit, arsip akan <strong>direview ulang</strong> oleh admin sebelum kembali tampil di museum.
                                    </div>
                                </div>
                                @endunless
                                @endauth

                            </div>
                        </div>

                        {{-- Footer Aksi --}}
                        <div class="d-flex align-items-center justify-content-between px-4 pb-4 gap-3 flex-wrap">
                            <a href="{{ route('museum.show', $museumItem) }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <div class="d-flex gap-2">
                                {{-- Tombol Hapus --}}
                                <button type="button" class="btn btn-outline-danger rounded-pill px-4"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi bi-trash me-2"></i>Hapus Arsip
                                </button>
                                {{-- Tombol Simpan --}}
                                <button type="submit" class="btn btn-warning fw-bold rounded-pill px-5">
                                    <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</section>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="font-size: 4rem;">🗑️</div>
                <h5 class="fw-bold mt-3">Hapus arsip ini?</h5>
                <p class="text-muted mb-0">
                    Arsip <strong>"{{ $museumItem->title }}"</strong> akan dihapus permanen beserta fotonya.
                    Tindakan ini <strong class="text-danger">tidak bisa dibatalkan</strong>.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4 gap-3">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Batal
                </button>
                <form action="{{ route('museum.destroy', $museumItem) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-bold rounded-pill px-4">
                        <i class="bi bi-trash me-2"></i>Ya, Hapus Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Preview gambar sebelum upload
document.getElementById('imageInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(ev) {
        const wrap = document.getElementById('imgPreviewWrap');
        wrap.innerHTML = `<img src="${ev.target.result}" id="imgPreview" style="width:100%;height:100%;object-fit:cover;filter:sepia(15%);">`;
    };
    reader.readAsDataURL(file);
});

// Jika checkbox hapus gambar dicentang, hilangkan preview
document.getElementById('removeImg')?.addEventListener('change', function() {
    const wrap = document.getElementById('imgPreviewWrap');
    if (this.checked) {
        wrap.innerHTML = '<div class="no-img"><div style="font-size:3rem;">🗑️</div><div style="font-size:0.75rem;color:#8b7355;margin-top:8px;">Foto akan dihapus</div></div>';
        document.getElementById('imageInput').value = '';
    } else {
        location.reload();
    }
});
</script>
@endpush

@endsection
