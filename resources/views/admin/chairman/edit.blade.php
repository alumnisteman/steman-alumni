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
                    <div class="card-header bg-warning bg-opacity-10 py-3 border-0">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-person-badge-fill me-2"></i>KETUA UMUM ILUNI
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="chairman_name" class="form-control" value="{{ setting('chairman_name') }}" id="c_name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Jabatan / Periode</label>
                            <input type="text" name="chairman_period" class="form-control" value="{{ setting('chairman_period') }}" id="c_period">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Pesan Sambutan</label>
                            <textarea name="chairman_message" class="form-control" rows="4" id="c_message">{{ setting('chairman_message') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Foto Ketua</label>
                            <input type="file" name="chairman_photo" class="form-control" id="c_photo">
                        </div>

                        <!-- Mini Preview -->
                        <div class="p-3 bg-light rounded-4 mt-3">
                            <div class="row align-items-center">
                                <div class="col-4">
                                    <img src="{{ setting('chairman_photo', 'https://ui-avatars.com/api/?name=Ketua+Umum&background=ffcc00&color=000&size=400') }}" 
                                         class="img-fluid rounded-3 shadow-sm border border-2 border-white" id="c_preview_photo">
                                </div>
                                <div class="col-8">
                                    <h6 class="fw-bold mb-0" id="c_preview_name">{{ setting('chairman_name', 'Nama Ketua') }}</h6>
                                    <p class="small text-muted mb-0" id="c_preview_period">{{ setting('chairman_period', 'Jabatan / Periode') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. KETUA PANITIA ACARA -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                    <div class="card-header bg-primary bg-opacity-10 py-3 border-0">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-person-fill-gear me-2"></i>KETUA PANITIA ACARA
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="event_chairman_name" class="form-control" value="{{ setting('event_chairman_name') }}" id="e_name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tema Acara / Periode</label>
                            <input type="text" name="event_chairman_period" class="form-control" value="{{ setting('event_chairman_period') }}" id="e_period">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Pesan Panitia</label>
                            <textarea name="event_chairman_message" class="form-control" rows="4" id="e_message">{{ setting('event_chairman_message') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Foto Ketua Panitia</label>
                            <input type="file" name="event_chairman_photo" class="form-control" id="e_photo">
                        </div>

                        <!-- Mini Preview -->
                        <div class="p-3 bg-light rounded-4 mt-3">
                            <div class="row align-items-center">
                                <div class="col-4">
                                    <img src="{{ setting('event_chairman_photo', 'https://ui-avatars.com/api/?name=Ketua+Panitia&background=007bff&color=fff&size=400') }}" 
                                         class="img-fluid rounded-3 shadow-sm border border-2 border-white" id="e_preview_photo">
                                </div>
                                <div class="col-8">
                                    <h6 class="fw-bold mb-0" id="e_preview_name">{{ setting('event_chairman_name', 'Nama Ketua Panitia') }}</h6>
                                    <p class="small text-muted mb-0" id="e_preview_period">{{ setting('event_chairman_period', 'Tema Acara / Periode') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill shadow-sm fw-bold">
                <i class="bi bi-save me-2"></i>SIMPAN SEMUA PERUBAHAN
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
    });
</script>
@endsection
