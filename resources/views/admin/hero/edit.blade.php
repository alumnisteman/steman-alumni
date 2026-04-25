@extends('layouts.admin')

@section('admin-content')
    <div class="mb-4 d-flex justify-content-between align-items-center">

        <div>
            <h2 class="section-title mb-1">Editor Banner Utama (Hero)</h2>
            <p class="text-muted">Sesuaikan teks penyambutan dan gambar latar belakang beranda Anda.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    @if(session('success')) 
        <div class="alert alert-success border-0 shadow-sm rounded-pill px-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div> 
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4">Form Pengaturan</h5>
                <form action="{{ route('admin.hero.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark">JUDUL UTAMA (HEADER)</label>
                        <textarea name="hero_title" class="form-control" rows="3" required>{{ $hero_title }}</textarea>
                        <div class="form-text small">Gunakan baris baru untuk membagi teks.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark">SUB-JUDUL (SUBTITLE)</label>
                        <input type="text" name="hero_subtitle" class="form-control" value="{{ $hero_subtitle }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark">GAMBAR LATAR BELAKANG</label>
                        <input type="file" name="hero_background" class="form-control mb-2">
                        <div class="form-text small text-warning"><i class="bi bi-exclamation-triangle-fill me-1"></i>Ukuran maksimal 2MB. Gunakan gambar horizontal berkualitas tinggi.</div>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 py-3 rounded-pill shadow-sm fw-bold border-0 mt-3">
                        <i class="bi bi-save me-2"></i>SIMPAN PERUBAHAN
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm overflow-hidden h-100" style="border-radius: 20px;">
                <div class="bg-dark p-3 text-white d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-eye me-2"></i>PREVIEW TAMPILAN</h6>
                    <span class="badge bg-warning text-dark px-3 rounded-pill">LIVE PREVIEW</span>
                </div>
                <div class="hero-preview-container d-flex align-items-center justify-content-center text-center p-5" 
                     style="height: 400px; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ $hero_background }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                    <div class="text-white">
                        <div class="badge-yellow mb-3" style="background: #ffcc00; color: #000; font-weight: 800; padding: 5px 15px; display: inline-block; text-transform: uppercase; font-size: 0.7rem;">Official Portal</div>
                        <h2 class="fw-bold text-uppercase mb-2" style="font-size: 1.8rem; letter-spacing: -1px; line-height: 1.2;">{!! nl2br(e($hero_title)) !!}</h2>
                        <p class="small fw-bold opacity-75">{{ $hero_subtitle }}</p>
                        <div class="mt-4 gap-2 d-flex justify-content-center">
                            <button class="btn btn-warning btn-sm rounded-0 border-0 fw-bold px-3">DAFTAR</button>
                            <button class="btn btn-outline-light btn-sm rounded-0 fw-bold px-3">ALUMNI</button>
                        </div>
                    </div>
                </div>
                <div class="card-body bg-light">
                    <p class="small text-muted mb-0"><i class="bi bi-info-circle me-1"></i>Ini adalah simulasi tampilan di halaman utama. Gambar mungkin sedikit berbeda tergantung ukuran layar.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus {
        border-color: #ffcc00;
        box-shadow: 0 0 0 0.25rem rgba(255, 204, 0, 0.25);
    }
</style>
@endsection

