@extends('layouts.admin')

@section('admin-content')
<div class="container py-4" style="max-width:860px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ \Illuminate\Support\Facades\URL::route('admin.merchandise.index') }}" class="btn btn-light rounded-pill px-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="fw-black mb-0">{{ isset($merchandise) ? 'Edit Merchandise' : 'Tambah Merchandise' }}</h2>
            <p class="text-muted mb-0 small">{{ isset($merchandise) ? 'Perbarui informasi & foto produk' : 'Tambahkan produk baru ke katalog' }}</p>
        </div>
    </div>

    @if(\Illuminate\Support\Facades\Session::has('success'))
        <div class="alert alert-success border-0 rounded-3 mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            {{ \Illuminate\Support\Facades\Session::get('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-3 mb-4">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ isset($merchandise)
            ? \Illuminate\Support\Facades\URL::route('admin.merchandise.update', $merchandise)
            : \Illuminate\Support\Facades\URL::route('admin.merchandise.store') }}"
          method="POST" enctype="multipart/form-data" id="merch-form">
        @csrf
        @if(isset($merchandise)) @method('PUT') @endif

        {{-- ── FOTO PRODUK ──────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-bold mb-1 text-muted">FOTO PRODUK</h6>
            <p class="text-muted small mb-3">Foto utama tampil di thumbnail katalog. Galeri tampil di halaman detail produk.</p>

            <div class="row g-4">
                {{-- Foto Utama --}}
                <div class="col-md-5">
                    <label class="form-label fw-semibold mb-2">Foto Utama</label>
                    <div id="main-photo-area" class="upload-zone rounded-4 border-2 border-dashed d-flex flex-column align-items-center justify-content-center text-center p-3"
                         style="min-height:220px; cursor:pointer; position:relative; background:#fafafa; border: 2px dashed #dee2e6; transition: border-color .2s, background .2s;">
                        @if(isset($merchandise) && $merchandise->image)
                            <img id="main-preview" src="{{ $merchandise->image }}" class="w-100 h-100 rounded-3" style="object-fit:cover; position:absolute; inset:0;">
                            <div id="main-overlay" class="position-absolute inset-0 rounded-4 d-flex flex-column align-items-center justify-content-center"
                                 style="background:rgba(0,0,0,.45); inset:0; opacity:0; transition:opacity .2s;">
                                <i class="bi bi-camera-fill text-white fs-2"></i>
                                <span class="text-white small mt-1">Ganti Foto</span>
                            </div>
                        @else
                            <div id="main-placeholder">
                                <i class="bi bi-image text-muted" style="font-size:3rem;"></i>
                                <p class="text-muted small mb-0 mt-2">Klik atau seret foto ke sini</p>
                                <p class="text-muted" style="font-size:11px;">JPG, PNG, WEBP — maks. 5 MB</p>
                            </div>
                            <img id="main-preview" src="" class="w-100 h-100 rounded-3 d-none" style="object-fit:cover; position:absolute; inset:0;">
                        @endif
                        <input type="file" name="image" id="main-input" accept="image/*" class="position-absolute w-100 h-100" style="opacity:0; cursor:pointer; top:0; left:0;">
                    </div>
                </div>

                {{-- Galeri Foto --}}
                <div class="col-md-7">
                    <label class="form-label fw-semibold mb-2">
                        Galeri Foto
                        <span class="text-muted fw-normal small ms-1">(tampil di halaman detail)</span>
                    </label>

                    {{-- Existing gallery --}}
                    @if(isset($merchandise) && !empty($merchandise->images))
                    <div class="row g-2 mb-3" id="existing-gallery">
                        @foreach($merchandise->images as $idx => $img)
                        <div class="col-4" id="gallery-item-{{ $idx }}">
                            <div class="position-relative rounded-3 overflow-hidden shadow-sm" style="aspect-ratio:1/1;">
                                <img src="{{ $img }}" class="w-100 h-100" style="object-fit:cover;">
                                <div class="position-absolute top-0 end-0 p-1 d-flex gap-1">
                                    {{-- Set as main --}}
                                    <form action="{{ \Illuminate\Support\Facades\URL::route('admin.merchandise.gallery.main', $merchandise) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="index" value="{{ $idx }}">
                                        <button type="submit" class="btn btn-warning btn-sm rounded-2 p-1 lh-1" title="Jadikan foto utama" style="width:26px;height:26px;">
                                            <i class="bi bi-star-fill" style="font-size:11px;"></i>
                                        </button>
                                    </form>
                                    {{-- Delete --}}
                                    <form action="{{ \Illuminate\Support\Facades\URL::route('admin.merchandise.gallery.delete', $merchandise) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus foto ini?')">
                                        @csrf
                                        <input type="hidden" name="index" value="{{ $idx }}">
                                        <button type="submit" class="btn btn-danger btn-sm rounded-2 p-1 lh-1" title="Hapus" style="width:26px;height:26px;">
                                            <i class="bi bi-trash3-fill" style="font-size:11px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Upload new gallery --}}
                    <div id="gallery-drop-zone" class="upload-zone rounded-4 border-2 border-dashed p-3 text-center"
                         style="min-height:110px; cursor:pointer; background:#fafafa; border: 2px dashed #dee2e6; transition: border-color .2s, background .2s; position:relative;">
                        <i class="bi bi-images text-muted fs-3 d-block mb-1"></i>
                        <p class="text-muted small mb-0">Klik atau seret beberapa foto sekaligus</p>
                        <p class="text-muted mb-0" style="font-size:11px;">Maks. 5 MB per foto — bisa pilih banyak sekaligus</p>
                        <input type="file" name="gallery[]" id="gallery-input" accept="image/*" multiple
                               class="position-absolute w-100 h-100" style="opacity:0; cursor:pointer; top:0; left:0;">
                    </div>

                    {{-- New uploads preview --}}
                    <div id="gallery-preview" class="row g-2 mt-2"></div>
                </div>
            </div>
        </div>

        {{-- ── INFORMASI PRODUK ─────────────────────────────────── --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-bold mb-3 text-muted">INFORMASI PRODUK</h6>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $merchandise->name ?? '') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                    <select name="category" class="form-select rounded-3" required>
                        <option value="">-- Pilih --</option>
                        @foreach($categories as $key => $cat)
                            <option value="{{ $key }}" {{ old('category', $merchandise->category ?? '') === $key ? 'selected' : '' }}>
                                {{ $cat['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="description" class="form-control rounded-3" rows="3">{{ old('description', $merchandise->description ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── HARGA & STOK ─────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-bold mb-3 text-muted">HARGA & STOK</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Harga Normal (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="price" class="form-control rounded-end-3" value="{{ old('price', $merchandise->price ?? '') }}" min="0" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Harga Alumni (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="price_member" class="form-control rounded-end-3" value="{{ old('price_member', $merchandise->price_member ?? '') }}" min="0">
                    </div>
                    <div class="form-text">Kosongkan jika tidak ada diskon</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Stok</label>
                    <input type="number" name="stock" class="form-control rounded-3" value="{{ old('stock', $merchandise->stock ?? 0) }}" min="0">
                    <div class="form-text">0 = tidak terbatas</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Minimal Order</label>
                    <input type="number" name="min_order" class="form-control rounded-3" value="{{ old('min_order', $merchandise->min_order ?? 1) }}" min="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">No. WhatsApp CS</label>
                    <input type="text" name="whatsapp_contact" class="form-control rounded-3" value="{{ old('whatsapp_contact', $merchandise->whatsapp_contact ?? '') }}" placeholder="628xxxxxxxxxx">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Urutan Tampil</label>
                    <input type="number" name="sort_order" class="form-control rounded-3" value="{{ old('sort_order', $merchandise->sort_order ?? 0) }}" min="0">
                </div>
            </div>
        </div>

        {{-- ── VARIAN ───────────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-bold mb-3 text-muted">VARIAN PRODUK</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Pilihan Ukuran</label>
                    <input type="text" name="sizes" class="form-control rounded-3"
                        value="{{ old('sizes', isset($merchandise) && $merchandise->sizes ? implode(', ', $merchandise->sizes) : '') }}"
                        placeholder="S, M, L, XL, XXL">
                    <div class="form-text">Pisahkan dengan koma</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Pilihan Warna</label>
                    <input type="text" name="colors" class="form-control rounded-3"
                        value="{{ old('colors', isset($merchandise) && $merchandise->colors ? implode(', ', $merchandise->colors) : '') }}"
                        placeholder="Hitam, Putih, Navy, Abu">
                    <div class="form-text">Pisahkan dengan koma</div>
                </div>
            </div>
        </div>

        {{-- ── PRE-ORDER ─────────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-bold mb-3 text-muted">PENGATURAN PRE-ORDER</h6>
            <div class="row g-3">
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_pre_order" id="is_pre_order" value="1"
                            {{ old('is_pre_order', $merchandise->is_pre_order ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_pre_order">Aktifkan Pre-Order</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Dibuka Mulai</label>
                    <input type="datetime-local" name="pre_order_open_at" class="form-control rounded-3"
                        value="{{ old('pre_order_open_at', isset($merchandise) && $merchandise->pre_order_open_at ? $merchandise->pre_order_open_at->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Ditutup Pada</label>
                    <input type="datetime-local" name="pre_order_close_at" class="form-control rounded-3"
                        value="{{ old('pre_order_close_at', isset($merchandise) && $merchandise->pre_order_close_at ? $merchandise->pre_order_close_at->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Estimasi Pengiriman</label>
                    <input type="datetime-local" name="estimated_delivery_at" class="form-control rounded-3"
                        value="{{ old('estimated_delivery_at', isset($merchandise) && $merchandise->estimated_delivery_at ? $merchandise->estimated_delivery_at->format('Y-m-d\TH:i') : '') }}">
                </div>
            </div>
        </div>

        {{-- ── STATUS ───────────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-bold mb-3 text-muted">STATUS</h6>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', $merchandise->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="is_active">Produk Aktif (tampil ke publik)</label>
            </div>
        </div>

        <div class="d-flex gap-3 justify-content-end pb-5">
            <a href="{{ \Illuminate\Support\Facades\URL::route('admin.merchandise.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
            <button type="submit" class="btn btn-warning fw-bold rounded-pill px-5">
                <i class="bi bi-check-lg me-2"></i>{{ isset($merchandise) ? 'Simpan Perubahan' : 'Tambah Produk' }}
            </button>
        </div>
    </form>
</div>

<style>
.upload-zone:hover, .upload-zone.drag-over {
    border-color: #ffc107 !important;
    background: #fffbea !important;
}
#main-photo-area:hover #main-overlay { opacity: 1 !important; }
.gallery-thumb-new { position:relative; aspect-ratio:1/1; border-radius:12px; overflow:hidden; }
.gallery-thumb-new img { width:100%; height:100%; object-fit:cover; }
.gallery-thumb-new .remove-btn { position:absolute; top:4px; right:4px; background:rgba(220,53,69,.9); color:#fff; border:none; border-radius:6px; width:22px; height:22px; font-size:11px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
</style>

<script>
(function () {
    // ── Main photo preview ──────────────────────────────────────
    const mainInput    = document.getElementById('main-input');
    const mainPreview  = document.getElementById('main-preview');
    const mainPlaceholder = document.getElementById('main-placeholder');
    const mainOverlay  = document.getElementById('main-overlay');

    mainInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            mainPreview.src = e.target.result;
            mainPreview.classList.remove('d-none');
            if (mainPlaceholder) mainPlaceholder.classList.add('d-none');
            if (mainOverlay) mainOverlay.style.cssText += '; opacity:0;';
        };
        reader.readAsDataURL(file);
    });

    // ── Gallery upload & preview ────────────────────────────────
    const galleryInput   = document.getElementById('gallery-input');
    const galleryPreview = document.getElementById('gallery-preview');
    const dropZone       = document.getElementById('gallery-drop-zone');
    let   extraFiles     = [];   // DataTransfer accumulator

    function renderGalleryPreviews(files) {
        Array.from(files).forEach((file, i) => {
            extraFiles.push(file);
            const reader = new FileReader();
            reader.onload = e => {
                const col = document.createElement('div');
                col.className = 'col-4';
                const idx = extraFiles.length - 1;
                col.innerHTML = `
                    <div class="gallery-thumb-new">
                        <img src="${e.target.result}" alt="">
                        <button type="button" class="remove-btn" data-idx="${idx}" title="Hapus">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>`;
                col.querySelector('.remove-btn').addEventListener('click', function () {
                    const fi = parseInt(this.dataset.idx);
                    extraFiles[fi] = null;  // mark as removed
                    syncFileInput();
                    col.remove();
                });
                galleryPreview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
        syncFileInput();
    }

    function syncFileInput() {
        const dt = new DataTransfer();
        extraFiles.filter(Boolean).forEach(f => dt.items.add(f));
        galleryInput.files = dt.files;
    }

    galleryInput.addEventListener('change', function () {
        renderGalleryPreviews(this.files);
    });

    // Drag & drop on gallery zone
    ['dragenter', 'dragover'].forEach(ev => {
        dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    });
    ['dragleave', 'drop'].forEach(ev => {
        dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.remove('drag-over'); });
    });
    dropZone.addEventListener('drop', e => {
        renderGalleryPreviews(e.dataTransfer.files);
    });

    // Drag & drop on main zone
    const mainZone = document.getElementById('main-photo-area');
    ['dragenter', 'dragover'].forEach(ev => {
        mainZone.addEventListener(ev, e => { e.preventDefault(); mainZone.classList.add('drag-over'); });
    });
    ['dragleave', 'drop'].forEach(ev => {
        mainZone.addEventListener(ev, e => { e.preventDefault(); mainZone.classList.remove('drag-over'); });
    });
    mainZone.addEventListener('drop', e => {
        if (e.dataTransfer.files[0]) {
            const dt = new DataTransfer();
            dt.items.add(e.dataTransfer.files[0]);
            mainInput.files = dt.files;
            mainInput.dispatchEvent(new Event('change'));
        }
    });
})();
</script>
@endsection
