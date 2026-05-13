@extends('layouts.admin')

@section('admin-content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('admin.jobs.index') }}" class="text-decoration-none text-muted small mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h2 class="section-title">{{ isset($job) ? 'Edit Lowongan' : 'Tambah Lowongan Baru' }}</h2>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%); border: 1px solid #e0e0e0 !important;">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                <i class="bi bi-robot text-primary fs-4"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0">AI Smart Importer</h5>
                <p class="small text-muted mb-0">Tempel URL dari LinkedIn, JobsDB, atau Loker.id untuk mengisi form secara otomatis.</p>
            </div>
        </div>
        <div class="input-group">
            <input type="url" id="ai-import-url" class="form-control border-primary border-opacity-25" placeholder="https://www.linkedin.com/jobs/view/...">
            <button type="button" id="btn-ai-import" class="btn btn-primary px-4 fw-bold">
                <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                AMBIL DATA AI
            </button>
        </div>
        <div id="ai-import-status" class="mt-2 small d-none"></div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="{{ isset($job) ? route('admin.jobs.update', $job) : route('admin.jobs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($job)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Judul Lowongan <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $job->title ?? '') }}" required placeholder="Contoh: Web Developer">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nama Perusahaan <span class="text-danger">*</span></label>
                    <input type="text" name="company" class="form-control" value="{{ old('company', $job->company ?? '') }}" required placeholder="Nama PT/Instansi">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Lokasi</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location', $job->location ?? '') }}" placeholder="Contoh: Ternate, Maluku Utara">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tipe Pekerjaan</label>
                    <select name="type" class="form-select">
                        <option value="Full-time" {{ old('type', $job->type ?? '') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="Part-time" {{ old('type', $job->type ?? '') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="Contract" {{ old('type', $job->type ?? '') == 'Contract' ? 'selected' : '' }}>Contract</option>
                        <option value="Internship" {{ old('type', $job->type ?? '') == 'Internship' ? 'selected' : '' }}>Internship</option>
                        <option value="Freelance" {{ old('type', $job->type ?? '') == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Link Eksternal (Opsional)</label>
                    <input type="url" name="external_link" class="form-control" value="{{ old('external_link', $job->external_link ?? '') }}" placeholder="https://id.jobstreet.com/job/...">
                    <small class="text-muted">Isi jika lowongan berasal dari Jobstreet, LinkedIn, dll.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ old('status', $job->status ?? 'active') == 'active' ? 'selected' : '' }}>Aktif (Active)</option>
                        <option value="closed" {{ old('status', $job->status ?? '') == 'closed' ? 'selected' : '' }}>Ditutup (Closed)</option>
                        <option value="draft" {{ old('status', $job->status ?? '') == 'draft' ? 'selected' : '' }}>Draf (Draft)</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Logo/Gambar (Opsional)</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Deskripsi Ringkas</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $job->description ?? '') }}</textarea>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">Konten Detail (Opsional)</label>
                        <button type="button" id="btn-ai-job-content" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-stars me-1"></i> AI Generate
                        </button>
                    </div>
                    <textarea name="content" id="job-content" class="form-control" rows="6">{{ old('content', $job->content ?? '') }}</textarea>
                    <div id="ai-job-status" class="mt-1 small text-muted d-none">AI sedang menyusun deskripsi...</div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill">Simpan Lowongan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Existing AI Importer Logic
    document.getElementById('btn-ai-import').addEventListener('click', function() {
        const urlField = document.getElementById('ai-import-url');
        const url = urlField.value;
        
        if (!url) {
            Swal.fire('Input Diperlukan', 'Silakan masukkan URL lowongan terlebih dahulu.', 'warning');
            return;
        }

        const btn = this;
        const spinner = btn.querySelector('.spinner-border');
        const status = document.getElementById('ai-import-status');

        btn.disabled = true;
        spinner.classList.remove('d-none');
        status.classList.remove('d-none');
        status.classList.remove('text-danger', 'text-success');
        status.classList.add('text-muted');
        status.textContent = 'Sedang memproses data dengan AI...';

        fetch("{{ route('admin.jobs.import-ai') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ url: url })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const data = result.data;
                document.querySelector('input[name="title"]').value = data.title || '';
                document.querySelector('input[name="company"]').value = data.company || '';
                document.querySelector('input[name="location"]').value = data.location || '';
                document.querySelector('select[name="type"]').value = data.type || 'Full-time';
                document.querySelector('input[name="external_link"]').value = data.external_link || url;
                document.querySelector('textarea[name="description"]').value = data.description || '';
                document.querySelector('textarea[name="content"]').value = data.content || '';
                
                status.classList.remove('text-muted');
                status.classList.add('text-success');
                status.textContent = 'Data berhasil ditarik otomatis!';
                Swal.fire('Success', 'Data lowongan berhasil ditarik otomatis!', 'success');
            } else {
                status.classList.remove('text-muted');
                status.classList.add('text-danger');
                status.textContent = 'Gagal: ' + (result.message || 'Terjadi kesalahan.');
            }
        })
        .catch(error => {
            console.error('AI Import Error:', error);
            status.classList.remove('text-muted');
            status.classList.add('text-danger');
            status.textContent = 'Gagal memproses data. Pastikan URL valid.';
        })
        .finally(() => {
            btn.disabled = false;
            spinner.classList.add('d-none');
        });
    });

    // New AI Content Generator Logic
    document.getElementById('btn-ai-job-content').addEventListener('click', function() {
        const title = document.querySelector('input[name="title"]').value;
        const company = document.querySelector('input[name="company"]').value;
        const status = document.getElementById('ai-job-status');
        const contentArea = document.getElementById('job-content');
        const btn = this;

        if (!title || !company) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Kurang Lengkap',
                text: 'Silakan isi Judul dan Perusahaan agar AI bisa menyusun deskripsi yang pas.',
                confirmButtonColor: '#6366f1'
            });
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Berpikir...';
        status.classList.remove('d-none');

        fetch("{{ route('admin.ai.generate-content') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ 
                type: 'job', 
                input: `Lowongan ${title} di ${company}` 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.content) {
                contentArea.value = data.content;
                Swal.fire({
                    icon: 'success',
                    title: 'Deskripsi Selesai!',
                    text: 'AI telah menyusun rincian pekerjaan profesional untuk Anda.',
                    confirmButtonColor: '#6366f1'
                });
            } else {
                throw new Error(data.error || 'Gagal menghasilkan konten');
            }
        })
        .catch(err => {
            Swal.fire('Error', err.message, 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-stars me-1"></i> AI Generate';
            status.classList.add('d-none');
        });
    });
</script>
@endpush

