@extends('layouts.admin')

@section('admin-content')
    <div class="mb-4">
        <h2 class="section-title">Konfigurasi & Branding Situs</h2>
        <p class="text-muted">Atur nama organisasi, identitas sekolah, dan deskripsi program di sini.</p>
    </div>

    @if(session('success')) 
        <div class="alert alert-success border-0 shadow-sm rounded-pill px-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div> 
    @endif

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                @csrf
                @method('PUT')

                @foreach($settings as $group => $items)
                    <div id="group-{{ $group }}" class="mb-5 p-4 bg-white border border-light rounded-4 shadow-sm scroll-margin-top">
                        <h4 class="fw-bold mb-4 text-dark d-flex align-items-center">
                            @if($group == 'general') <span class="bg-primary text-white p-2 rounded-3 me-3"><i class="bi bi-gear-fill"></i></span>Identitas & Branding
                            @elseif($group == 'profile') <span class="bg-dark text-white p-2 rounded-3 me-3"><i class="bi bi-eye-fill"></i></span>Profil & Visi Misi Organisasi
                            @elseif($group == 'hero') <span class="bg-warning text-dark p-2 rounded-3 me-3"><i class="bi bi-image"></i></span>Banner Utama (Hero)
                            @elseif($group == 'contact') <span class="bg-success text-white p-2 rounded-3 me-3"><i class="bi bi-telephone-fill"></i></span>Informasi Kontak & Sekretariat
                            @elseif($group == 'chairman') <span class="bg-info text-white p-2 rounded-3 me-3"><i class="bi bi-person-workspace"></i></span>Sambutan Ketua Umum
                            @elseif($group == 'event_chairman') <span class="bg-danger text-white p-2 rounded-3 me-3"><i class="bi bi-megaphone-fill"></i></span>Sambutan Ketua Panitia
                            @elseif($group == 'secretary') <span class="bg-success text-white p-2 rounded-3 me-3"><i class="bi bi-person-fill-check"></i></span>Sambutan Sekretaris Panitia
                            @elseif($group == 'ai') <span class="bg-dark text-white p-2 rounded-3 me-3"><i class="bi bi-robot"></i></span>Integrasi & API AI
                            @else <span class="bg-secondary text-white p-2 rounded-3 me-3"><i class="bi bi-grid-fill"></i></span>Konfigurasi Lanjutan @endif
                        </h4>
                        <hr class="mb-4 opacity-10">
                    
                    @foreach($items as $item)
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark">{{ $item->label }}</label>
                            @php
                                $key = (string)$item->key;
                                $isLongText = (strpos($key, 'message') !== false) || 
                                              (strpos($key, 'speech') !== false) || 
                                              (strpos($key, 'description') !== false) || 
                                              (strpos($key, 'program') !== false) || 
                                              (strpos($key, 'vision') !== false) || 
                                              (strpos($key, 'mission') !== false) || 
                                              (strpos($key, 'running_text') !== false) || 
                                              ($key == 'hero_title') || 
                                              ($key == 'contact_address');
                                
                                $isImage = (strpos($key, 'photo') !== false) || 
                                           (strpos($key, 'logo') !== false) || 
                                           (strpos($key, 'background') !== false);

                                $isBackground = strpos($key, 'background') !== false;
                            @endphp

                            @if($isLongText)
                                <textarea name="{{ $item->key }}" class="form-control shadow-sm" rows="4" style="border-radius: 10px;">{{ $item->value }}</textarea>
                            @elseif($item->key == 'launch_date')
                                @php $launchTs = !empty($item->value) ? strtotime($item->value) : false; @endphp
                                <input type="datetime-local" name="{{ $item->key }}" class="form-control shadow-sm" value="{{ $launchTs ? date('Y-m-d\TH:i', $launchTs) : '' }}" style="border-radius: 10px;">
                            @elseif($item->key == 'coming_soon_mode')
                                <div class="form-check form-switch p-3 bg-light rounded-3 shadow-sm border border-light-subtle d-inline-block">
                                    <input type="hidden" name="{{ $item->key }}" value="off">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="switch-{{ $item->key }}" name="{{ $item->key }}" value="on" {{ $item->value == 'on' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="switch-{{ $item->key }}">{{ $item->value == 'on' ? 'AKTIF' : 'NON-AKTIF' }}</label>
                                </div>
                                <script>
                                    document.getElementById('switch-{{ $item->key }}').addEventListener('change', function() {
                                        this.parentElement.querySelector('label').innerText = this.checked ? 'AKTIF' : 'NON-AKTIF';
                                    });
                                </script>
                            @elseif($isImage)
                                {{-- ===== LIVE PHOTO PREVIEW BLOCK ===== --}}
                                <div class="image-upload-box rounded-4 border overflow-hidden" 
                                     style="border-color: #dee2e6 !important; background: #f8f9fa;"
                                     data-key="{{ $item->key }}"
                                     data-bg="{{ $isBackground ? '1' : '0' }}">

                                    {{-- Preview Area --}}
                                    <div class="preview-area p-3">
                                        <div class="d-flex align-items-stretch gap-3">

                                            {{-- Gambar Sekarang --}}
                                            <div class="preview-current text-center" style="min-width: {{ $isBackground ? '180px' : '100px' }}; max-width: {{ $isBackground ? '180px' : '100px' }};">
                                                <div class="x-small fw-bold text-muted text-uppercase mb-1 letter-spacing-1">SAAT INI</div>
                                                <div class="preview-img-wrap position-relative rounded-3 overflow-hidden shadow-sm border border-2 border-white" 
                                                     style="height: 90px; background: #e9ecef;">
                                                    @if($item->value)
                                                        <img src="{{ $item->value }}" 
                                                             alt="Foto saat ini"
                                                             class="w-100 h-100"
                                                             style="object-fit: cover;"
                                                             onerror="this.parentElement.innerHTML='<div class=\'d-flex align-items-center justify-content-center h-100 text-muted x-small\'>Gagal muat</div>'">
                                                    @else
                                                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                                            <i class="bi bi-image fs-4 opacity-30"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Panah --}}
                                            <div class="d-flex align-items-center justify-content-center text-muted px-1" style="font-size: 1.2rem;">
                                                <i class="bi bi-arrow-right"></i>
                                            </div>

                                            {{-- Preview Baru (muncul saat file dipilih) --}}
                                            <div class="preview-new text-center flex-grow-1">
                                                <div class="x-small fw-bold text-primary text-uppercase mb-1">PRATINJAU BARU</div>
                                                <div class="new-preview-wrap position-relative rounded-3 overflow-hidden shadow-sm border border-2"
                                                     style="height: 90px; background: #e9ecef; border-color: #dee2e6 !important;"
                                                     id="new-preview-wrap-{{ $item->key }}">
                                                    <div class="empty-state d-flex flex-column align-items-center justify-content-center h-100 text-muted" id="empty-{{ $item->key }}">
                                                        <i class="bi bi-cloud-arrow-up fs-4 opacity-30"></i>
                                                        <div class="x-small mt-1 opacity-50">Pilih file untuk preview</div>
                                                    </div>
                                                    <img id="img-preview-{{ $item->key }}" 
                                                         src="" 
                                                         alt="Preview baru" 
                                                         class="w-100 h-100 d-none" 
                                                         style="object-fit: cover;">
                                                </div>
                                                {{-- Info file --}}
                                                <div id="file-info-{{ $item->key }}" class="mt-1 d-none">
                                                    <span class="badge bg-success rounded-pill x-small px-2 py-1" id="file-size-{{ $item->key }}"></span>
                                                    <span class="badge bg-info rounded-pill x-small px-2 py-1 ms-1" id="file-dim-{{ $item->key }}"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Upload Input Area --}}
                                    <div class="p-3 pt-0">
                                        <div class="upload-drop-area rounded-3 border-2 border-dashed p-3 text-center position-relative"
                                             style="border: 2px dashed #ced4da; cursor: pointer; transition: all 0.2s;"
                                             id="drop-{{ $item->key }}"
                                             onclick="document.getElementById('file-{{ $item->key }}').click()">
                                            <i class="bi bi-upload text-primary me-2"></i>
                                            <span class="small fw-bold text-primary">Klik atau seret foto ke sini</span>
                                            <div class="x-small text-muted mt-1">
                                                @if($isBackground)
                                                    JPG, PNG, WebP — Maks. 10MB · Disarankan 1920×800 px
                                                @elseif(strpos($key, 'logo') !== false)
                                                    PNG, SVG, JPG — Maks. 5MB · Disarankan persegi (mis. 200×200 px)
                                                @else
                                                    JPG, PNG — Maks. 5MB · Disarankan rasio portrait
                                                @endif
                                            </div>
                                            <input type="file" 
                                                   id="file-{{ $item->key }}" 
                                                   name="{{ $item->key }}" 
                                                   accept="image/*"
                                                   class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                                   style="cursor: pointer;"
                                                   data-key="{{ $item->key }}"
                                                   data-bg="{{ $isBackground ? '1' : '0' }}">
                                        </div>

                                        {{-- Status bar: nama file yang dipilih + tombol batal --}}
                                        <div id="selected-bar-{{ $item->key }}" class="d-none mt-2 d-flex align-items-center gap-2 p-2 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25">
                                            <i class="bi bi-file-earmark-image-fill text-success"></i>
                                            <span class="small fw-bold text-success flex-grow-1 text-truncate" id="selected-name-{{ $item->key }}"></span>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill py-0 px-2 x-small fw-bold" 
                                                    onclick="clearPreview('{{ $item->key }}')">
                                                <i class="bi bi-x-lg me-1"></i>Batalkan
                                            </button>
                                        </div>

                                        {{-- URL saat ini --}}
                                        <div class="mt-2 x-small text-muted d-flex align-items-center gap-1 flex-wrap">
                                            <i class="bi bi-link-45deg"></i>
                                            <span>URL aktif:</span>
                                            <code class="bg-white px-1 rounded text-truncate" style="max-width: 280px;" title="{{ $item->value }}">{{ $item->value ?: '(belum ada)' }}</code>
                                        </div>
                                    </div>
                                </div>
                                {{-- ===== END LIVE PHOTO PREVIEW BLOCK ===== --}}
                            @else
                                @if(strpos($item->key, 'api_key') !== false)
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;"><i class="bi bi-key-fill text-muted"></i></span>
                                        <input type="password" name="{{ $item->key }}" class="form-control border-start-0 shadow-sm" value="{{ $item->value }}" style="border-radius: 0 10px 10px 0;" placeholder="Masukkan kunci rahasia...">
                                    </div>
                                @else
                                    <input type="text" name="{{ $item->key }}" class="form-control shadow-sm" value="{{ $item->value }}" style="border-radius: 10px;">
                                @endif
                            @endif
                            <div class="form-text small opacity-50">Kunci: <code>{{ $item->key }}</code></div>
                        </div>
                    @endforeach
                    </div>
                @endforeach

                <div class="mt-4">
                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill shadow-lg fw-bold overflow-hidden position-relative border-0" style="background: linear-gradient(135deg, #1e293b, #334155);">
                        <span class="position-relative z-1"><i class="bi bi-cloud-upload-fill me-2"></i>SIMPAN & UNGGAH PERUBAHAN</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 bg-dark text-white sticky-top" style="border-radius: 15px; top: 20px;">
                <h6 class="fw-bold mb-3" style="color: #ffcc00;"><i class="bi bi-lightbulb-fill me-2"></i>PANDUAN CMS</h6>
                <p class="small opacity-75">Perubahan yang Anda buat di sini akan langsung berdampak pada seluruh halaman website:</p>
                <ul class="small opacity-75 list-unstyled">
                    <li class="mb-2"><i class="bi bi-check2-circle me-2 text-warning"></i>Nama Organisasi muncul di Navbar dan Footer.</li>
                    <li class="mb-2"><i class="bi bi-check2-circle me-2 text-warning"></i>Nama Sekolah digunakan di halaman pendaftaran.</li>
                    <li class="mb-2"><i class="bi bi-check2-circle me-2 text-warning"></i>Hero section menyambut pengunjung di halaman utama.</li>
                </ul>
                <hr class="opacity-25">
                <div class="small opacity-75">
                    <div class="fw-bold mb-2 text-warning"><i class="bi bi-image me-1"></i>Tips Upload Foto:</div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1"><i class="bi bi-dot me-1"></i>Pratinjau muncul otomatis saat Anda memilih file.</li>
                        <li class="mb-1"><i class="bi bi-dot me-1"></i>Klik <strong>Batalkan</strong> untuk membatalkan pilihan foto.</li>
                        <li class="mb-1"><i class="bi bi-dot me-1"></i>Foto tidak berubah jika tidak ada file baru dipilih.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .x-small { font-size: 0.72rem; }
    .letter-spacing-1 { letter-spacing: 0.05em; }
    
    .upload-drop-area:hover,
    .upload-drop-area.drag-over {
        border-color: #0d6efd !important;
        background: rgba(13, 110, 253, 0.04) !important;
    }
    .upload-drop-area.drag-over {
        transform: scale(1.01);
    }

    .image-upload-box {
        transition: box-shadow 0.2s;
    }
    .image-upload-box:focus-within {
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12) !important;
        border-color: #0d6efd !important;
    }

    .new-preview-wrap.has-image {
        border-color: #198754 !important;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15);
    }

    .preview-current .preview-img-wrap {
        transition: opacity 0.2s;
    }
</style>
@endpush

@push('scripts')
<script>
function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
}

function handleFileSelect(input) {
    const key = input.dataset.key;
    const isBackground = input.dataset.bg === '1';
    const file = input.files[0];

    if (!file) {
        clearPreview(key);
        return;
    }

    // Validasi tipe file
    if (!file.type.startsWith('image/')) {
        alert('File harus berupa gambar (JPG, PNG, WebP, SVG, dll).');
        input.value = '';
        return;
    }

    // Validasi ukuran file (maks 10MB)
    const maxSize = isBackground ? 10 * 1024 * 1024 : 5 * 1024 * 1024;
    if (file.size > maxSize) {
        alert('Ukuran file terlalu besar. Maks ' + (isBackground ? '10MB' : '5MB') + '.');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const imgEl       = document.getElementById('img-preview-' + key);
        const emptyEl     = document.getElementById('empty-' + key);
        const wrapEl      = document.getElementById('new-preview-wrap-' + key);
        const fileInfoEl  = document.getElementById('file-info-' + key);
        const fileSizeEl  = document.getElementById('file-size-' + key);
        const fileDimEl   = document.getElementById('file-dim-' + key);
        const barEl       = document.getElementById('selected-bar-' + key);
        const nameEl      = document.getElementById('selected-name-' + key);

        imgEl.src = e.target.result;
        imgEl.classList.remove('d-none');
        emptyEl.classList.add('d-none');
        wrapEl.classList.add('has-image');

        // Tampilkan info ukuran
        fileSizeEl.textContent = formatBytes(file.size);

        // Dapatkan dimensi gambar
        const tempImg = new Image();
        tempImg.onload = function() {
            fileDimEl.textContent = this.naturalWidth + '×' + this.naturalHeight + ' px';
        };
        tempImg.src = e.target.result;

        fileInfoEl.classList.remove('d-none');

        // Tampilkan nama file di status bar
        nameEl.textContent = file.name;
        barEl.classList.remove('d-none');

        // Highlight drop area 
        const dropEl = document.getElementById('drop-' + key);
        if (dropEl) {
            dropEl.style.borderColor = '#198754';
            dropEl.style.background  = 'rgba(25, 135, 84, 0.05)';
        }
    };
    reader.readAsDataURL(file);
}

function clearPreview(key) {
    const inputEl     = document.getElementById('file-' + key);
    const imgEl       = document.getElementById('img-preview-' + key);
    const emptyEl     = document.getElementById('empty-' + key);
    const wrapEl      = document.getElementById('new-preview-wrap-' + key);
    const fileInfoEl  = document.getElementById('file-info-' + key);
    const barEl       = document.getElementById('selected-bar-' + key);
    const dropEl      = document.getElementById('drop-' + key);

    if (inputEl) inputEl.value = '';
    if (imgEl) { imgEl.src = ''; imgEl.classList.add('d-none'); }
    if (emptyEl) emptyEl.classList.remove('d-none');
    if (wrapEl) wrapEl.classList.remove('has-image');
    if (fileInfoEl) fileInfoEl.classList.add('d-none');
    if (barEl) barEl.classList.add('d-none');
    if (dropEl) {
        dropEl.style.borderColor = '';
        dropEl.style.background  = '';
    }
}

// Pasang event listener ke semua input file
document.querySelectorAll('input[type="file"][data-key]').forEach(function(input) {
    input.addEventListener('change', function() {
        handleFileSelect(this);
    });

    // Drag & drop support
    const dropArea = document.getElementById('drop-' + input.dataset.key);
    if (dropArea) {
        dropArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        dropArea.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });
        dropArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const fileInput = document.getElementById('file-' + input.dataset.key);
                // Inject file ke input via DataTransfer
                const dt = new DataTransfer();
                dt.items.add(files[0]);
                fileInput.files = dt.files;
                handleFileSelect(fileInput);
            }
        });
    }
});
</script>
@endpush
