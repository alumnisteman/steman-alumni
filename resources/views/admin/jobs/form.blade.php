@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('admin.jobs.index') }}" class="text-decoration-none text-muted small mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h2 class="section-title">{{ isset($job) ? 'Edit Lowongan' : 'Tambah Lowongan Baru' }}</h2>
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
                    <label class="form-label fw-bold">Konten Detail (Opsional)</label>
                    <textarea name="content" class="form-control" rows="6">{{ old('content', $job->content ?? '') }}</textarea>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill">Simpan Lowongan</button>
            </div>
        </form>
    </div>
</div>
@endsection
