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
<div class="row">
    <!-- Desktop Banner Section -->
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold small text-uppercase text-muted">Upload Desktop Banner</label>
        
        <div class="preview-card bg-light rounded-4 p-3 mb-3 border">
            <div class="text-muted small mb-3 d-flex justify-content-between align-items-center">
                <span>Preview Desktop:</span>
                <span class="badge bg-white text-dark border">Target: <span id="desc-desktop">300x250</span> px</span>
            </div>
            
            <!-- Precision Preview Container -->
            <div class="banner-preview-wrapper mb-3 position-relative overflow-hidden rounded-3 shadow-sm bg-secondary bg-opacity-10" 
                 id="preview-desktop-container"
                 style="aspect-ratio: 300/250; width: 100%; max-width: 400px; margin: auto;">
                <div id="preview-desktop-img" class="w-100 h-100" 
                     style="background-image: url('{{ $ad->image_desktop ?? '' }}'); background-size: cover; background-position: {{ $ad->desktop_offset_x ?? 50 }}% {{ $ad->desktop_offset_y ?? 50 }}%; background-repeat: no-repeat; transform: scale({{ $ad->desktop_zoom ?? 1.0 }});">
                </div>
                @if(!isset($ad) || !$ad->image_desktop)
                    <div class="position-absolute top-50 start-50 translate-middle text-muted opacity-50 text-center">
                        <i class="bi bi-image fs-1 d-block mb-2"></i>
                        <span class="small">Belum ada gambar</span>
                    </div>
                @endif
            </div>

            <!-- Controls -->
            <div class="precision-controls mt-3 p-2 bg-white rounded-3 shadow-sm">
                <p class="small fw-bold text-center mb-2 text-primary">Geser slider untuk atur posisi</p>
                
                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <label class="text-muted">POS HORIZONTAL</label>
                        <span class="fw-bold" id="val-desktop-x">{{ $ad->desktop_offset_x ?? 50 }}%</span>
                    </div>
                    <input type="range" name="desktop_offset_x" class="form-range slider-pos" min="0" max="100" value="{{ $ad->desktop_offset_x ?? 50 }}" data-target="preview-desktop-img" data-axis="x">
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <label class="text-muted">POS VERTIKAL</label>
                        <span class="fw-bold" id="val-desktop-y">{{ $ad->desktop_offset_y ?? 50 }}%</span>
                    </div>
                    <input type="range" name="desktop_offset_y" class="form-range slider-pos" min="0" max="100" value="{{ $ad->desktop_offset_y ?? 50 }}" data-target="preview-desktop-img" data-axis="y">
                </div>

                <div class="mb-0">
                    <div class="d-flex justify-content-between small mb-1">
                        <label class="text-muted">ZOOM</label>
                        <span class="fw-bold" id="val-desktop-zoom">{{ $ad->desktop_zoom ?? 1.0 }}x</span>
                    </div>
                    <input type="range" name="desktop_zoom" class="form-range slider-zoom" min="1" max="3" step="0.1" value="{{ $ad->desktop_zoom ?? 1.0 }}" data-target="preview-desktop-img">
                </div>
            </div>
        </div>

        <input type="file" name="image_desktop" id="input-desktop" class="form-control border-0 bg-light px-4 py-3" accept="image/*" {{ isset($ad) ? '' : 'required' }}>
    </div>
    
    <!-- Mobile Banner Section -->
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold small text-uppercase text-muted">Upload Mobile Banner (Opsional)</label>
        
        <div class="preview-card bg-light rounded-4 p-3 mb-3 border">
            <div class="text-muted small mb-3 d-flex justify-content-between align-items-center">
                <span>Preview Mobile:</span>
                <span class="badge bg-white text-dark border">Target: <span id="desc-mobile">300x300</span> px</span>
            </div>
            
            <!-- Precision Preview Container -->
            <div class="banner-preview-wrapper mb-3 position-relative overflow-hidden rounded-3 shadow-sm bg-secondary bg-opacity-10" 
                 id="preview-mobile-container"
                 style="aspect-ratio: 300/300; width: 100%; max-width: 300px; margin: auto;">
                <div id="preview-mobile-img" class="w-100 h-100" 
                     style="background-image: url('{{ $ad->image_mobile ?? '' }}'); background-size: cover; background-position: {{ $ad->mobile_offset_x ?? 50 }}% {{ $ad->mobile_offset_y ?? 50 }}%; background-repeat: no-repeat; transform: scale({{ $ad->mobile_zoom ?? 1.0 }});">
                </div>
                @if(!isset($ad) || !$ad->image_mobile)
                    <div class="position-absolute top-50 start-50 translate-middle text-muted opacity-50 text-center" id="mobile-placeholder">
                        <i class="bi bi-phone fs-1 d-block mb-2"></i>
                        <span class="small">Kosongkan untuk auto-generate</span>
                    </div>
                @endif
            </div>

            <!-- Controls -->
            <div class="precision-controls mt-3 p-2 bg-white rounded-3 shadow-sm">
                <p class="small fw-bold text-center mb-2 text-primary">Geser slider untuk atur posisi</p>
                
                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <label class="text-muted">POS HORIZONTAL</label>
                        <span class="fw-bold" id="val-mobile-x">{{ $ad->mobile_offset_x ?? 50 }}%</span>
                    </div>
                    <input type="range" name="mobile_offset_x" class="form-range slider-pos" min="0" max="100" value="{{ $ad->mobile_offset_x ?? 50 }}" data-target="preview-mobile-img" data-axis="x">
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <label class="text-muted">POS VERTIKAL</label>
                        <span class="fw-bold" id="val-mobile-y">{{ $ad->mobile_offset_y ?? 50 }}%</span>
                    </div>
                    <input type="range" name="mobile_offset_y" class="form-range slider-pos" min="0" max="100" value="{{ $ad->mobile_offset_y ?? 50 }}" data-target="preview-mobile-img" data-axis="y">
                </div>

                <div class="mb-0">
                    <div class="d-flex justify-content-between small mb-1">
                        <label class="text-muted">ZOOM</label>
                        <span class="fw-bold" id="val-mobile-zoom">{{ $ad->mobile_zoom ?? 1.0 }}x</span>
                    </div>
                    <input type="range" name="mobile_zoom" class="form-range slider-zoom" min="1" max="3" step="0.1" value="{{ $ad->mobile_zoom ?? 1.0 }}" data-target="preview-mobile-img">
                </div>
            </div>
        </div>

        <input type="file" name="image_mobile" id="input-mobile" class="form-control border-0 bg-light px-4 py-3" accept="image/*">
    </div>
</div>

<style>
    .banner-preview-wrapper {
        border: 2px dashed #dee2e6;
        transition: all 0.3s ease;
    }
    .form-range::-webkit-slider-thumb { background: #ffcc00; }
    .form-range::-moz-range-thumb { background: #ffcc00; }
    .form-range::-ms-thumb { background: #ffcc00; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const posSelect = document.querySelector('select[name="position"]');
        const descDesktop = document.getElementById('desc-desktop');
        const descMobile = document.getElementById('desc-mobile');
        const previewDesktopContainer = document.getElementById('preview-desktop-container');
        const previewMobileContainer = document.getElementById('preview-mobile-container');

        const updateDims = () => {
            const val = posSelect.value;
            let dw = 300, dh = 250, mw = 300, mh = 300;

            if(val === 'header') { dw = 1200; dh = 300; mw = 600; mh = 300; } 
            else if(val === 'sidebar') { dw = 300; dh = 250; mw = 300; mh = 300; } 
            else if(val === 'content') { dw = 728; dh = 90; mw = 320; mh = 100; } 
            else if(val === 'footer') { dw = 1200; dh = 150; mw = 600; mh = 150; }
            else if(val === 'popup') { dw = 600; dh = 800; mw = 350; mh = 500; }

            descDesktop.innerText = `${dw} x ${dh}`;
            descMobile.innerText = `${mw} x ${mh}`;
            
            previewDesktopContainer.style.aspectRatio = `${dw}/${dh}`;
            previewMobileContainer.style.aspectRatio = `${mw}/${mh}`;
        }

        // Live Image Preview when selecting file
        const setupFilePreview = (inputId, imgId, placeholderId) => {
            const input = document.getElementById(inputId);
            const img = document.getElementById(imgId);
            const placeholder = document.getElementById(placeholderId);

            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.style.backgroundImage = `url('${e.target.result}')`;
                        if (placeholder) placeholder.style.display = 'none';
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        };

        setupFilePreview('input-desktop', 'preview-desktop-img', null);
        setupFilePreview('input-mobile', 'preview-mobile-img', 'mobile-placeholder');

        // Sliders Logic
        const sliders = document.querySelectorAll('.form-range');
        sliders.forEach(slider => {
            slider.addEventListener('input', function() {
                const targetId = this.dataset.target;
                const targetImg = document.getElementById(targetId);
                const isZoom = this.classList.contains('slider-zoom');
                
                if (isZoom) {
                    const zoomVal = this.value;
                    targetImg.style.transform = `scale(${zoomVal})`;
                    document.getElementById(`val-${targetId.split('-')[1]}-zoom`).innerText = `${zoomVal}x`;
                } else {
                    const axis = this.dataset.axis;
                    const val = this.value;
                    const otherAxis = axis === 'x' ? 'y' : 'x';
                    const otherVal = document.querySelector(`input[name="${targetId.split('-')[1]}_offset_${otherAxis}"]`).value;
                    
                    if (axis === 'x') {
                        targetImg.style.backgroundPosition = `${val}% ${otherVal}%`;
                    } else {
                        targetImg.style.backgroundPosition = `${otherVal}% ${val}%`;
                    }
                    
                    document.getElementById(`val-${targetId.split('-')[1]}-${axis}`).innerText = `${val}%`;
                }
            });
        });

        posSelect.addEventListener('change', updateDims);
        updateDims();
    });
</script>
