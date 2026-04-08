@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h2 class="section-title">Manajemen Sambutan</h2>
        <p class="text-muted">Kelola sambutan dari Ketua Iluni dan Ketua Panitia Acara.</p>
    </div>

    @if(session('success')) 
        <div class="alert alert-success border-0 shadow-sm rounded-pill px-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div> 
    @endif

    <form action="/admin/chairman/update" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- 1. KETUA UMUM ILUNI -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                    <div class="card-header bg-warning bg-opacity-10 py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-person-badge-fill me-2"></i>KETUA UMUM
                        </h5>
                        <span class="badge bg-warning text-dark rounded-pill shadow-sm">Utama</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap Ketua Umum</label>
                            <input type="text" name="chairman_name" class="form-control" value="{{ setting('chairman_name') }}" id="c_name" placeholder="Contoh: H. Ahmad Yusuf, S.T.">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Jabatan / Periode</label>
                            <input type="text" name="chairman_period" class="form-control" value="{{ setting('chairman_period') }}" id="c_period" placeholder="Contoh: Periode 2024 - 2028">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Sambutan Singkat (Beranda)</label>
                            <textarea name="chairman_message" class="form-control" rows="3" id="c_message" placeholder="Sambutan yang muncul di halaman depan...">{{ setting('chairman_message') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-primary">Pesan Khusus Untuk Alumni</label>
                            <textarea name="alumni_message" class="form-control border-primary border-opacity-25" rows="4" id="a_message" placeholder="Pesan inspiratif untuk seluruh alumni di halaman profil...">{{ setting('alumni_message') }}</textarea>
                            <div class="form-text small">Pesan ini akan muncul secara eksklusif di halaman Profil Organisasi.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Upload Foto Ketua Umum</label>
                            <input type="file" name="chairman_photo" class="form-control" id="c_photo">
                        </div>

                        <!-- Mini Preview -->
                                    <label for="c_photo_input" class="d-block cursor-pointer">
                                        <div class="position-relative group">
                                            <img src="{{ setting('chairman_photo', 'https://ui-avatars.com/api/?name=Ketua+Umum&background=ffcc00&color=000&size=400') }}" 
                                                 onerror="this.src='https://ui-avatars.com/api/?name=Ketua+Umum&background=ffcc00&color=000&size=400'"
                                                 class="img-fluid rounded-3 shadow-sm border border-2 border-white transition-all hover-opacity" id="c_preview_photo" style="height: 120px; width: 120px; object-fit: cover;">
                                            <div class="position-absolute inset-0 d-flex align-items-center justify-content-center bg-dark bg-opacity-25 rounded-3 opacity-0 group-hover-opacity-100 transition-all">
                                                <i class="bi bi-camera-fill text-white fs-4"></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-8">
                                    <h6 class="fw-bold mb-1" id="c_preview_name">{{ setting('chairman_name', 'Nama Ketua') }}</h6>
                                    <p class="small text-muted mb-2" id="c_preview_period">{{ setting('chairman_period', 'Jabatan / Periode') }}</p>
                                    
                                    <div class="mb-2 p-2 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-25">
                                        <label class="form-label small fw-bold text-dark mb-1">UNGGAH FOTO KETUA UMUM:</label>
                                        <input type="file" name="chairman_photo" class="form-control form-control-sm border-warning border-opacity-50 shadow-sm" id="c_photo_input" style="border-radius: 8px;">
                                    </div>
                                    <div class="form-text x-small text-muted">Format: JPG, PNG, WEBP (Maks 5MB)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. KETUA PANITIA ACARA -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                    <div class="card-header bg-primary bg-opacity-10 py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-person-fill-gear me-2"></i>KETUA PANITIA
                        </h5>
                        <span class="badge bg-primary text-white rounded-pill shadow-sm">Event</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Ketua Panitia</label>
                            <input type="text" name="event_chairman_name" class="form-control" value="{{ setting('event_chairman_name') }}" id="e_name" placeholder="Nama penanggung jawab acara">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tema / Keterangan Panitia</label>
                            <input type="text" name="event_chairman_period" class="form-control" value="{{ setting('event_chairman_period') }}" id="e_period" placeholder="Contoh: Reuni Akbar 2026">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Sambutan Ketua Panitia</label>
                            <textarea name="event_chairman_message" class="form-control" rows="8" id="e_message" placeholder="Teks lengkap sambutan ketua panitia...">{{ setting('event_chairman_message') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Upload Foto Ketua Panitia</label>
                            <input type="file" name="event_chairman_photo" class="form-control" id="e_photo">
                        </div>

                        <!-- Mini Preview -->
                        <div class="p-3 bg-light rounded-4 mt-3">
                            <div class="row align-items-center">
                                <div class="col-4">
                                    <label for="e_photo_input" class="d-block cursor-pointer">
                                        <div class="position-relative group">
                                            <img src="{{ setting('event_chairman_photo', 'https://ui-avatars.com/api/?name=Ketua+Panitia&background=007bff&color=fff&size=400') }}" 
                                                 onerror="this.src='https://ui-avatars.com/api/?name=Ketua+Panitia&background=007bff&color=fff&size=400'"
                                                 class="img-fluid rounded-3 shadow-sm border border-2 border-white transition-all hover-opacity" id="e_preview_photo" style="height: 120px; width: 120px; object-fit: cover;">
                                            <div class="position-absolute inset-0 d-flex align-items-center justify-content-center bg-dark bg-opacity-25 rounded-3 opacity-0 group-hover-opacity-100 transition-all">
                                                <i class="bi bi-camera-fill text-white fs-4"></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-8">
                                    <h6 class="fw-bold mb-1" id="e_preview_name">{{ setting('event_chairman_name', 'Nama Ketua Panitia') }}</h6>
                                    <p class="small text-muted mb-2" id="e_preview_period">{{ setting('event_chairman_period', 'Tema Acara / Periode') }}</p>
                                    
                                    <div class="mb-2 p-2 bg-primary bg-opacity-10 rounded-3 border border-primary border-opacity-25">
                                        <label class="form-label small fw-bold text-dark mb-1">UNGGAH FOTO KETUA PANITIA:</label>
                                        <input type="file" name="event_chairman_photo" class="form-control form-control-sm border-primary border-opacity-50 shadow-sm" id="e_photo_input" style="border-radius: 8px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <!-- 3. SEKRETARIS PANITIA ACARA -->
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-success bg-opacity-10 py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-person-fill-check me-2"></i>SEKRETARIS PANITIA
                        </h5>
                        <span class="badge bg-success text-white rounded-pill shadow-sm">Event</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Nama Sekretaris Panitia</label>
                                    <input type="text" name="secretary_name" class="form-control" value="{{ setting('secretary_name') }}" id="s_name" placeholder="Nama sekretaris pelaksana">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Keterangan / Jabatan</label>
                                    <input type="text" name="secretary_period" class="form-control" value="{{ setting('secretary_period') }}" id="s_period" placeholder="Contoh: Sekretaris Panitia Reuni 2026">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Sambutan Sekretaris</label>
                                    <textarea name="secretary_message" class="form-control" rows="5" id="s_message" placeholder="Teks sambutan sekretaris...">{{ setting('secretary_message') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Upload Foto Sekretaris</label>
                                    <div class="p-3 bg-light rounded-4 text-center">
                                        <label for="s_photo" class="d-block cursor-pointer">
                                            <img src="{{ setting('secretary_photo', 'https://ui-avatars.com/api/?name=Sekretaris&background=28a745&color=fff&size=400') }}" 
                                                 onerror="this.src='https://ui-avatars.com/api/?name=Sekretaris&background=28a745&color=fff&size=400'"
                                                 class="img-fluid rounded-4 shadow-sm border border-4 border-white mb-3" id="s_preview_photo" 
                                                 style="height: 200px; width: 100%; object-fit: cover; object-position: top;">
                                            <div class="btn btn-outline-success btn-sm w-100 rounded-pill">
                                                <i class="bi bi-camera me-1"></i> Ganti Foto
                                            </div>
                                        </label>
                                        <input type="file" name="secretary_photo" class="d-none" id="s_photo">
                                        <div class="mt-3">
                                            <h6 class="fw-bold mb-1" id="s_preview_name">{{ setting('secretary_name', 'Nama Sekretaris') }}</h6>
                                            <p class="small text-muted mb-0" id="s_preview_period">{{ setting('secretary_period', 'Jabatan Sekretaris') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-5 pb-5">
            <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill shadow-lg fw-bold overflow-hidden position-relative border-0" style="background: linear-gradient(135deg, #1e293b, #334155);">
                <span class="position-relative z-1">
                    <i class="bi bi-cloud-upload-fill me-2 text-warning"></i>
                    SIMPAN PERUBAHAN & UNGGAH FOTO
                </span>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const setupPreview = (inputPrefix, previewPrefix) => {
            const nameInput = document.getElementById(`${inputPrefix}_name`);
            const periodInput = document.getElementById(`${inputPrefix}_period`);
            const photoInput = document.getElementById(`${inputPrefix}_photo`);
            
            const namePreview = document.getElementById(`${previewPrefix}_preview_name`);
            const periodPreview = document.getElementById(`${previewPrefix}_preview_period`);
            const photoPreview = document.getElementById(`${previewPrefix}_preview_photo`);

            if (nameInput) nameInput.addEventListener('input', (e) => namePreview.textContent = e.target.value || 'Nama');
            if (periodInput) periodInput.addEventListener('input', (e) => periodPreview.textContent = e.target.value || 'Jabatan');
            
            if (photoInput) {
                photoInput.addEventListener('change', function(e) {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = (ex) => photoPreview.setAttribute('src', ex.target.result);
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
        };

        setupPreview('c', 'c');
        setupPreview('e', 'e');
        setupPreview('s', 's');
    });
</script>
@endsection
