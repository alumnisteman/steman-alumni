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
        
        <!-- Current Image (if exists) -->
        @if(isset($ad) && $ad->image_desktop)
            <div class="mb-3 p-3 border rounded-3 bg-light shadow-sm text-center">
                <span class="badge bg-secondary mb-2">Gambar Saat Ini</span>
                <img src="{{ $ad->image_desktop }}" class="img-fluid rounded border" style="max-height: 150px;">
                <div class="small text-muted mt-2">Pilih file baru di bawah ini untuk mengganti.</div>
            </div>
        @endif

        <input type="file" name="image_desktop" id="input-desktop" class="form-control border-0 bg-light px-4 py-3 mb-3" accept="image/*" {{ isset($ad) ? '' : 'required' }}>
        
        <!-- Cropper Container -->
        <div id="cropper-desktop-container" style="display: none;" class="border rounded-4 p-3 bg-white shadow-sm">
            <div class="text-muted small mb-2 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary"><i class="bi bi-crop me-1"></i> Area Potong (Geser / Scroll)</span>
                <span class="badge bg-dark">Target: <span id="desc-desktop">300x250</span> px</span>
            </div>
            <div style="max-height: 400px; overflow: hidden; background-color: #f8f9fa;" class="rounded">
                <img id="image-desktop-crop" src="" style="display: block; max-width: 100%;">
            </div>
        </div>
    </div>
    
    <!-- Mobile Banner Section -->
    <div class="col-md-6 mb-4">
        <label class="form-label fw-bold small text-uppercase text-muted">Upload Mobile Banner (Opsional)</label>
        
        <!-- Current Image (if exists) -->
        @if(isset($ad) && $ad->image_mobile)
            <div class="mb-3 p-3 border rounded-3 bg-light shadow-sm text-center">
                <span class="badge bg-secondary mb-2">Gambar Mobile Saat Ini</span>
                <img src="{{ $ad->image_mobile }}" class="img-fluid rounded border" style="max-height: 150px;">
                <div class="small text-muted mt-2">Pilih file baru di bawah ini untuk mengganti.</div>
            </div>
        @endif

        <input type="file" name="image_mobile" id="input-mobile" class="form-control border-0 bg-light px-4 py-3 mb-3" accept="image/*">
        
        <!-- Cropper Container -->
        <div id="cropper-mobile-container" style="display: none;" class="border rounded-4 p-3 bg-white shadow-sm">
            <div class="text-muted small mb-2 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary"><i class="bi bi-crop me-1"></i> Area Potong Mobile</span>
                <span class="badge bg-dark">Target: <span id="desc-mobile">300x300</span> px</span>
            </div>
            <div style="max-height: 400px; overflow: hidden; background-color: #f8f9fa;" class="rounded">
                <img id="image-mobile-crop" src="" style="display: block; max-width: 100%;">
            </div>
        </div>
    </div>
</div>

<!-- Include Cropper.js from CDN -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const posSelect = document.querySelector('select[name="position"]');
        let desktopCropper = null;
        let mobileCropper = null;

        const getDims = (val) => {
            let dw = 300, dh = 250, mw = 300, mh = 300;
            if(val === 'header') { dw = 1200; dh = 300; mw = 600; mh = 300; } 
            else if(val === 'sidebar') { dw = 300; dh = 250; mw = 300; mh = 300; } 
            else if(val === 'content') { dw = 728; dh = 90; mw = 320; mh = 100; } 
            else if(val === 'footer') { dw = 1200; dh = 150; mw = 600; mh = 150; }
            else if(val === 'popup') { dw = 600; dh = 800; mw = 350; mh = 500; }
            return {dw, dh, mw, mh};
        };

        const updateDimsUI = () => {
            if(!posSelect) return;
            const dims = getDims(posSelect.value);
            const descD = document.getElementById('desc-desktop');
            const descM = document.getElementById('desc-mobile');
            if(descD) descD.innerText = `${dims.dw}x${dims.dh}`;
            if(descM) descM.innerText = `${dims.mw}x${dims.mh}`;
            
            if(desktopCropper) desktopCropper.setAspectRatio(dims.dw / dims.dh);
            if(mobileCropper) mobileCropper.setAspectRatio(dims.mw / dims.mh);
        };

        if(posSelect) {
            posSelect.addEventListener('change', updateDimsUI);
            updateDimsUI(); // init
        }

        const setupCropper = (inputId, imgId, containerId, isMobile) => {
            const input = document.getElementById(inputId);
            const img = document.getElementById(imgId);
            const container = document.getElementById(containerId);

            if(input) {
                input.addEventListener('change', function(e) {
                    if (this.files && this.files[0]) {
                        // Display the container
                        container.style.display = 'block';
                        
                        const reader = new FileReader();
                        reader.onload = function(evt) {
                            img.src = evt.target.result;
                            
                            // Destroy old cropper if exists
                            if(isMobile && mobileCropper) mobileCropper.destroy();
                            if(!isMobile && desktopCropper) desktopCropper.destroy();

                            const dims = getDims(posSelect.value);
                            const ratio = isMobile ? (dims.mw / dims.mh) : (dims.dw / dims.dh);

                            const cropper = new Cropper(img, {
                                aspectRatio: ratio,
                                viewMode: 1, // Restrict the crop box to not exceed the size of the canvas
                                dragMode: 'move', // Allow moving the image itself
                                autoCropArea: 1,
                                restore: false,
                                guides: true,
                                center: true,
                                highlight: false,
                                cropBoxMovable: true,
                                cropBoxResizable: true,
                                toggleDragModeOnDblclick: false,
                            });

                            if(isMobile) mobileCropper = cropper;
                            else desktopCropper = cropper;
                        }
                        reader.readAsDataURL(this.files[0]);
                    } else {
                        container.style.display = 'none';
                        if(isMobile && mobileCropper) mobileCropper.destroy();
                        if(!isMobile && desktopCropper) desktopCropper.destroy();
                    }
                });
            }
        };

        setupCropper('input-desktop', 'image-desktop-crop', 'cropper-desktop-container', false);
        setupCropper('input-mobile', 'image-mobile-crop', 'cropper-mobile-container', true);

        // Intercept Form Submit
        const form = document.querySelector('form');
        if(form) {
            form.addEventListener('submit', async function(e) {
                // Only intercept if we have active croppers
                if(!desktopCropper && !mobileCropper) return;
                
                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                if(submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> MEMPROSES POTONGAN...';
                }

                const processCropper = (cropper, inputElement, filename) => {
                    return new Promise((resolve) => {
                        if (!cropper) {
                            resolve();
                            return;
                        }
                        // Get cropped canvas
                        cropper.getCroppedCanvas({
                            imageSmoothingEnabled: true,
                            imageSmoothingQuality: 'high',
                        }).toBlob((blob) => {
                            if (blob) {
                                // Fallback to DataTransfer to modify the input files
                                const file = new File([blob], filename, { type: "image/jpeg", lastModified: new Date().getTime() });
                                const container = new DataTransfer();
                                container.items.add(file);
                                inputElement.files = container.files;
                            }
                            resolve();
                        }, 'image/jpeg', 0.90);
                    });
                };

                await processCropper(desktopCropper, document.getElementById('input-desktop'), 'desktop_crop.jpg');
                await processCropper(mobileCropper, document.getElementById('input-mobile'), 'mobile_crop.jpg');

                // Bypass this event listener and submit natively
                form.submit();
            });
        }
    });
</script>
