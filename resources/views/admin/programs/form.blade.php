@extends('layouts.admin')

@section('admin-content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg" style="border-radius: 20px;">
                <div class="card-body p-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-warning p-2 rounded-3 me-3">
                            <i class="bi bi-grid-fill text-dark fs-4"></i>
                        </div>
                        <h3 class="fw-bold mb-0">{{ isset($program) ? 'Edit' : 'Tambah' }} Program</h3>
                    </div>

                    <form action="{{ isset($program) ? route('admin.programs.update', $program) : route('admin.programs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($program)) @method('PUT') @endif

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Judul Program</label>
                            <input type="text" name="title" class="form-control form-control-lg border-0 bg-light px-4" value="{{ old('title', $program->title ?? '') }}" placeholder="Contoh: Beasiswa Alumni Berprestasi" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Bootstrap Icon</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="bi {{ old('icon', $program->icon ?? 'bi-box-seam') }}"></i></span>
                                    <input type="text" name="icon" class="form-control border-0 bg-light px-3" value="{{ old('icon', $program->icon ?? 'bi-box-seam') }}" placeholder="Contoh: bi-mortarboard" required>
                                </div>
                                <div class="form-text small">Cari di <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Status</label>
                                <select name="status" class="form-select border-0 bg-light px-4">
                                    <option value="published" {{ (old('status', $program->status ?? '') == 'published') ? 'selected' : '' }}>Diterbitkan (Published)</option>
                                    <option value="draft" {{ (old('status', $program->status ?? '') == 'draft') ? 'selected' : '' }}>Draf</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Deskripsi Singkat</label>
                            <textarea name="description" class="form-control border-0 bg-light px-4 py-3" style="height: 100px; border-radius: 15px;" required>{{ old('description', $program->description ?? '') }}</textarea>
                            <div class="form-text small">Teks ini akan muncul di daftar kartu program.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Konten Lengkap (HTML support)</label>
                            <textarea name="content" class="form-control border-0 bg-light px-4 py-3" style="height: 300px; border-radius: 15px;" placeholder="Tulis rincian program di sini..." required>{{ old('content', $program->content ?? '') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Link Pendaftaran (Opsional)</label>
                            <input type="url" name="registration_link" class="form-control border-0 bg-light px-4 py-3" value="{{ old('registration_link', $program->registration_link ?? '') }}" placeholder="https://forms.gle/..." style="border-radius: 12px;">
                            <div class="form-text small">Masukkan link Google Form atau link eksternal lainnya jika ada.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Gambar Utama (Optional)</label>
                            @if(isset($program) && $program->image)
                                <div class="mb-2">
                                    <img src="{{ $program->image }}" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control border-0 bg-light px-4 py-3" accept="image/*">
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold py-3 shadow-sm rounded-3 text-uppercase">
                                {{ isset($program) ? 'SIMPAN PERUBAHAN' : 'TAMBAH PROGRAM' }}
                            </button>
                            <a href="{{ route('admin.programs.index') }}" class="btn btn-link text-muted mt-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

