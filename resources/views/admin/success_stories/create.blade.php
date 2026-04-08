@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.success-stories.index') }}" class="btn btn-outline-dark rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">Tambah Kisah Sukses</h2>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                <form action="{{ route('admin.success-stories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Nama Alumni & Gelar</label>
                            <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso, S.Kom." required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Posisi / Jabatan Sekarang</label>
                            <input type="text" name="title" class="form-control rounded-3 @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Contoh: Software Engineer at Google" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Jurusan & Angkatan</label>
                            <input type="text" name="major_year" class="form-control rounded-3 @error('major_year') is-invalid @enderror" value="{{ old('major_year') }}" placeholder="Contoh: TKJ '2015" required>
                            @error('major_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">Urutan Tampil</label>
                            <input type="number" name="order" class="form-control rounded-3" value="{{ old('order', 0) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase">Kutipan Singkat (Muncul di Landing Page)</label>
                            <textarea name="quote" class="form-control rounded-3 @error('quote') is-invalid @enderror" rows="2" placeholder="Tuliskan pesan singkat yang akan muncul di kartu halaman depan..." required>{{ old('quote') }}</textarea>
                            @error('quote') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase">Cerita Lengkap (Detail Jejak Sukses)</label>
                            <textarea name="content" class="form-control rounded-3 @error('content') is-invalid @enderror" rows="8" placeholder="Tuliskan cerita lengkap perjuangan dan kesuksesan alumni di sini..." required>{{ old('content') }}</textarea>
                            <small class="text-muted">Gunakan baris baru untuk memisahkan paragraf. Ini akan muncul saat pengguna mengklik kartu.</small>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-uppercase">Link Akun Alumni (Opsional)</label>
                            <select name="user_id" class="form-select rounded-3">
                                <option value="">-- Hubungkan dengan User Alumni (Jika ada) --</option>
                                @foreach($alumni as $a)
                                    <option value="{{ $a->id }}" {{ old('user_id') == $a->id ? 'selected' : '' }}>{{ $a->name }} ({{ $a->jurusan }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Jika dihubungkan, klik pada kartu akan mengarah ke profil alumni tersebut.</small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-uppercase">Foto Profil</label>
                            <input type="file" name="image_path" class="form-control rounded-3 @error('image_path') is-invalid @enderror">
                            <small class="text-muted">Disarankan rasio 1:1 (Square) agar tampilan optimal.</small>
                            @error('image_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1" id="publishSwitch" checked>
                                <label class="form-check-label fw-bold" for="publishSwitch">Publikasikan Langsung</label>
                            </div>
                        </div>

                        <div class="col-12 mt-5">
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow">
                                <i class="bi bi-save me-2"></i>SIMPAN KISAH SUKSES
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
