@extends('layouts.app')

@section('content')
<style>
:root {
    --museum-gold: #d4a017;
    --museum-dark: #1a0f00;
}
.museum-create-hero {
    background: linear-gradient(135deg, #1a0f00 0%, #2d1a00 40%, #1a0a00 100%);
    padding: 60px 0 40px;
    position: relative;
    overflow: hidden;
}
.museum-create-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d4a017' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.museum-form-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    padding: 2rem;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
}
.submit-btn {
    background: linear-gradient(135deg, #d4a017, #c17f24);
    color: #fff;
    border: none;
    border-radius: 30px;
    padding: 11px 32px;
    font-weight: 700;
    font-size: 0.95rem;
    transition: all 0.2s;
}
.submit-btn:hover { transform: scale(1.02); box-shadow: 0 6px 20px rgba(212,160,23,0.4); color: #fff; }
.category-card {
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
}
.category-card:hover, .category-card.selected {
    border-color: var(--museum-gold);
    background: #fff8e1;
}
.dark .museum-form-card { background: #1e293b; border-color: rgba(255,255,255,0.08); }
</style>

<section class="museum-create-hero">
    <div class="container position-relative text-white">
        <a href="{{ route('museum.index') }}" class="btn btn-sm rounded-pill mb-3"
           style="background:rgba(212,160,23,0.2); border:1px solid rgba(212,160,23,0.4); color:#d4a017;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Museum
        </a>
        <div class="d-flex align-items-center gap-3 mb-2">
            <span style="font-size:2.5rem;">🏛️</span>
            <div>
                <h1 class="fw-black mb-0" style="color:#d4a017;">Donasikan Arsip</h1>
                <p class="opacity-75 mb-0">Bagikan kenangan berharga untuk generasi mendatang</p>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            @if (session('success'))
            <div class="alert alert-success rounded-4 mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger rounded-4 mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="museum-form-card">
                <h5 class="fw-bold mb-1">📤 Form Donasi Arsip</h5>
                <p class="text-muted small mb-4">Arsip yang dikirimkan akan ditinjau oleh admin sebelum ditampilkan publik.</p>

                <form action="{{ route('museum.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Kategori --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Kategori Arsip <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            @foreach ($categories as $key => $cat)
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="category-card d-block {{ old('category') === $key ? 'selected' : '' }}"
                                       for="cat_{{ $key }}">
                                    <div style="font-size:1.8rem;">{{ $cat['icon'] }}</div>
                                    <div class="small fw-semibold mt-1">{{ $cat['label'] }}</div>
                                    <input type="radio" name="category" id="cat_{{ $key }}" value="{{ $key }}"
                                           class="d-none" {{ old('category') === $key ? 'checked' : '' }} required>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('category')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Judul --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="title">Judul Arsip <span class="text-danger">*</span></label>
                        <input type="text" id="title" name="title"
                               class="form-control rounded-3 @error('title') is-invalid @enderror"
                               value="{{ old('title') }}"
                               placeholder="Contoh: Foto Perpisahan Angkatan 2005" required maxlength="200">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="description">Deskripsi (opsional)</label>
                        <textarea id="description" name="description" rows="3"
                                  class="form-control rounded-3 @error('description') is-invalid @enderror"
                                  placeholder="Ceritakan latar belakang arsip ini..." maxlength="2000">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Tahun Era --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="era_year">Tahun / Era (opsional)</label>
                            <input type="number" id="era_year" name="era_year"
                                   class="form-control rounded-3 @error('era_year') is-invalid @enderror"
                                   value="{{ old('era_year') }}"
                                   placeholder="Contoh: 1998" min="1950" max="{{ date('Y') }}">
                            @error('era_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="donated_by">Nama Donatur (opsional)</label>
                            <input type="text" id="donated_by" name="donated_by"
                                   class="form-control rounded-3 @error('donated_by') is-invalid @enderror"
                                   value="{{ old('donated_by') }}"
                                   placeholder="Nama Anda atau 'Anonim'" maxlength="100">
                            @error('donated_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Foto --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="image">Foto Arsip (opsional, maks 3 MB)</label>
                        <input type="file" id="image" name="image" accept="image/*"
                               class="form-control rounded-3 @error('image') is-invalid @enderror"
                               onchange="previewImage(this)">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="imagePreviewWrap" class="mt-2 d-none">
                            <img id="imagePreview" src="" alt="Preview" class="rounded-3"
                                 style="max-height:200px; max-width:100%; object-fit:cover;">
                        </div>
                    </div>

                    {{-- URL Video --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="video_url">URL Video (opsional)</label>
                        <input type="url" id="video_url" name="video_url"
                               class="form-control rounded-3 @error('video_url') is-invalid @enderror"
                               value="{{ old('video_url') }}"
                               placeholder="https://youtube.com/watch?v=...">
                        @error('video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('museum.index') }}" class="btn btn-light rounded-pill px-4">
                            Batal
                        </a>
                        <button type="submit" class="submit-btn">
                            <i class="bi bi-send-fill me-2"></i>Kirim Arsip
                        </button>
                    </div>
                </form>
            </div>

            <div class="alert alert-warning rounded-4 mt-4 d-flex align-items-start gap-3">
                <span style="font-size:1.5rem; flex-shrink:0;">⏳</span>
                <div>
                    <strong>Proses Peninjauan</strong><br>
                    <span class="small">Arsip Anda akan ditinjau oleh admin sebelum ditampilkan. Terima kasih telah menjaga warisan Steman!</span>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
// Highlight category on click
document.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.category-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
    });
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreviewWrap').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
