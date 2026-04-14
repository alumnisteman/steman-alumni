@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="py-5 bg-dark text-white text-center position-relative overflow-hidden">
    <div class="container py-5 position-relative z-1">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb justify-content-center text-uppercase small fw-bold">
                <li class="breadcrumb-item"><a href="/" class="text-white opacity-75 text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">Kontak Kami</li>
            </ol>
        </nav>
        <h1 class="display-4 fw-black mb-3">KONTAK KAMI</h1>
        <p class="lead opacity-75">Saran dan kritik Anda sangat berharga bagi kemajuan Alumni {{ setting('school_name', 'SMKN 2 Ternate') }}</p>
    </div>
    <!-- Decorative background -->
    <div class="position-absolute top-50 start-50 translate-middle opacity-10" style="font-size: 20rem; pointer-events: none;">
        <i class="bi bi-envelope-at"></i>
    </div>
</div>

<div class="container py-5 mt-n5 position-relative z-2">
    <div class="row g-4">
        <!-- Sidebar / Contact Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg p-4 mb-4" style="border-radius: 20px;">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 me-3">
                        <i class="bi bi-building fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">SEKRETARIAT</h6>
                        <small class="text-muted text-uppercase fw-bold">address Lengkap</small>
                    </div>
                </div>
                <p class="text-dark small mb-0">{{ setting('contact_address', 'Jl. Ki Hajar Dewantoro, Ternate') }}</p>
            </div>

            <div class="card border-0 shadow-lg p-4 mb-4" style="border-radius: 20px;">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-4 me-3">
                        <i class="bi bi-envelope-at fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">EMAIL RESMI</h6>
                        <small class="text-muted text-uppercase fw-bold">Korespondensi</small>
                    </div>
                </div>
                <a href="mailto:{{ setting('contact_email', 'sekretariat@alumni_smkn2.id') }}" class="text-dark fw-bold text-decoration-none small d-block mb-1">{{ setting('contact_email', 'sekretariat@alumni_smkn2.id') }}</a>
            </div>

            <div class="card border-0 shadow-lg p-4" style="border-radius: 20px;">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-4 me-3">
                        <i class="bi bi-telephone fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">KONTAK INFO</h6>
                        <small class="text-muted text-uppercase fw-bold">Telepon / WA</small>
                    </div>
                </div>
                <a href="tel:{{ setting('contact_phone', '+62-123-4567-890') }}" class="text-dark fw-bold text-decoration-none small d-block mb-1">{{ setting('contact_phone', '+62-123-4567-890') }}</a>
            </div>
            
            @auth
                @if(auth()->user()->canAccessAdminPanel())
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary w-100 py-3 rounded-pill shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#editKontakModal">
                            <i class="bi bi-pencil-square me-2"></i>EDIT KONTAK
                        </button>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Main Content / Form -->
        <div class="col-lg-8">
            @if(session('message_sent'))
                <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4 py-4 px-4 d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-3 me-3 text-success"></i>
                    <div>
                        <div class="fw-bold">Pesan Terkirim!</div>
                        <div class="small">{{ session('message_sent') }}</div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-4 py-3 px-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    @foreach($errors->all() as $error)
                        <div class="small">{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="card border-0 shadow-lg p-5" style="border-radius: 20px;">
                <h3 class="fw-black mb-4">Kirim Pesan</h3>
                <p class="text-muted mb-5">Besar harapan kami untuk mendapatkan kritik dan saran membangun dalam rangka meningkatkan kualitas penyajian informasi. Jangan sungkan untuk menghubungi kami.</p>
                
                <form action="/kontak/pesan" method="POST" class="row g-4">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">NAMA LENGKAP <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg bg-light border-0 px-4" placeholder="Masukkan nama Anda..." value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">EMAIL <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-lg bg-light border-0 px-4" placeholder="Email aktif..." value="{{ old('email') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">SUBJEK <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control form-control-lg bg-light border-0 px-4" placeholder="Tujuan pesan..." value="{{ old('subject') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">PESAN <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control form-control-lg bg-light border-0 px-4 py-3" rows="5" placeholder="Tuliskan pesan Anda di sini... (minimal 10 karakter)" required>{{ old('message') }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-send-fill me-2"></i>KIRIM PESAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@auth
    @if(auth()->user()->canAccessAdminPanel())
    <!-- Modal Edit Kontak -->
    <div class="modal fade" id="editKontakModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-black">EDIT INFORMASI KONTAK</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/admin/settings" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase opacity-50">Sekretariat / address</label>
                            <textarea name="contact_address" class="form-control border-0 bg-light p-3" rows="3" style="border-radius: 15px;">{{ setting('contact_address') }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase opacity-50">Email Resmi</label>
                            <input type="email" name="contact_email" class="form-control border-0 bg-light p-3" style="border-radius: 15px;" value="{{ setting('contact_email') }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-uppercase opacity-50">Nomor Telepon / WA</label>
                            <input type="text" name="contact_phone" class="form-control border-0 bg-light p-3" style="border-radius: 15px;" value="{{ setting('contact_phone', '+62-123-4567-890') }}">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">SIMPAN PERUBAHAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endauth

<style>
    .mt-n5 { margin-top: -8rem !important; }
    .fw-black { font-weight: 900; }
    .z-1 { z-index: 1; }
    .z-2 { z-index: 2; }
    .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.5); }
</style>
@endsection
