<div class="mb-4">
    <label class="form-label fw-bold small text-uppercase text-muted">Judul Iklan / Banner</label>
    <input type="text" name="title" class="form-control form-control-lg border-0 bg-light px-4" value="{{ old('title', $ad->title ?? '') }}" placeholder="Contoh: Promo Ramadhan Berkah" required>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold small text-uppercase text-muted">Posisi Tampil</label>
        <select name="position" class="form-select border-0 bg-light px-4 h-100" style="min-height: 50px;" required>
            <option value="sidebar" {{ (old('position', $ad->position ?? '') == 'sidebar') ? 'selected' : '' }}>Sidebar (Kotak)</option>
            <option value="header" {{ (old('position', $ad->position ?? '') == 'header') ? 'selected' : '' }}>Header (Banner Panjang)</option>
            <option value="footer" {{ (old('position', $ad->position ?? '') == 'footer') ? 'selected' : '' }}>Footer (Tipis)</option>
            <option value="content" {{ (old('position', $ad->position ?? '') == 'content') ? 'selected' : '' }}>Tengah Konten (Inline)</option>
        </select>
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold small text-uppercase text-muted">Status</label>
        <div class="form-check form-switch p-3 bg-light rounded-3 ms-0 d-flex align-items-center justify-content-between">
            <label class="form-check-label ms-2 fw-bold" for="is_active">Aktifkan Iklan</label>
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $ad->is_active ?? true) ? 'checked' : '' }}>
        </div>
    </div>
</div>

<div class="mb-4">
    <label class="form-label fw-bold small text-uppercase text-muted">Link Tujuan (Klik)</label>
    <div class="input-group">
        <span class="input-group-text border-0 bg-light"><i class="bi bi-link-45deg"></i></span>
        <input type="url" name="link" class="form-control border-0 bg-light px-3" value="{{ old('link', $ad->link ?? '') }}" placeholder="https://contoh.com/promo">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold small text-uppercase text-muted">Tanggal Mulai (Opsional)</label>
        <input type="date" name="start_date" class="form-control border-0 bg-light px-4" value="{{ old('start_date', isset($ad->start_date) ? $ad->start_date->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold small text-uppercase text-muted">Tanggal Berakhir (Opsional)</label>
        <input type="date" name="end_date" class="form-control border-0 bg-light px-4" value="{{ old('end_date', isset($ad->end_date) ? $ad->end_date->format('Y-m-d') : '') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold small text-uppercase text-muted">Upload Desktop Banner</label>
        @if(isset($ad) && $ad->image_desktop)
            <div class="mb-3">
                <div class="text-muted small mb-2">Preview Desktop:</div>
                <img src="{{ $ad->image_desktop }}" class="img-thumbnail shadow-sm w-100" style="max-height: 150px; object-fit: contain; border-radius: 8px;">
            </div>
        @endif
        <input type="file" name="image_desktop" class="form-control border-0 bg-light px-4 py-3" accept="image/*" {{ isset($ad) ? '' : 'required' }}>
        <div class="form-text small mt-2">
            Target: <span class="fw-bold" id="desc-desktop">300x250 px</span> (Akan di-crop otomatis)
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold small text-uppercase text-muted">Upload Mobile Banner (Opsional)</label>
        @if(isset($ad) && $ad->image_mobile)
            <div class="mb-3">
                <div class="text-muted small mb-2">Preview Mobile:</div>
                <img src="{{ $ad->image_mobile }}" class="img-thumbnail shadow-sm w-100" style="max-height: 150px; object-fit: contain; border-radius: 8px;">
            </div>
        @endif
        <input type="file" name="image_mobile" class="form-control border-0 bg-light px-4 py-3" accept="image/*">
        <div class="form-text small mt-2">
            Target: <span class="fw-bold" id="desc-mobile">300x300 px</span> (Kosongkan untuk auto-generate)
        </div>
    </div>
</div>

<div class="alert alert-info border-0 rounded-4 p-3 mb-4">
    <div class="d-flex align-items-center">
        <i class="bi bi-magic fs-4 me-3"></i>
        <div class="small">
            <strong>Sistem Otomasi Aktif:</strong> Apapun ukuran yang Anda upload, sistem akan otomatis melakukan <strong>Center Crop</strong> dan <strong>Optimasi WebP</strong> sesuai standar posisi yang dipilih.
        </div>
    </div>
</div>

<script>
    const posSelect = document.querySelector('select[name="position"]');
    const descDesktop = document.getElementById('desc-desktop');
    const descMobile = document.getElementById('desc-mobile');

    const updateDims = () => {
        const val = posSelect.value;
        if(val === 'header') {
            descDesktop.innerText = '1200 x 300 px';
            descMobile.innerText = '600 x 300 px';
        } else if(val === 'sidebar') {
            descDesktop.innerText = '300 x 250 px';
            descMobile.innerText = '300 x 300 px';
        } else if(val === 'content') {
            descDesktop.innerText = '728 x 90 px';
            descMobile.innerText = '320 x 100 px';
        } else if(val === 'footer') {
            descDesktop.innerText = '1200 x 150 px';
            descMobile.innerText = '600 x 150 px';
        }
    }

    posSelect.addEventListener('change', updateDims);
    updateDims();
</script>
