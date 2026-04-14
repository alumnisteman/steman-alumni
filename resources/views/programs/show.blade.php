@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('programs.index') }}" class="text-decoration-none text-muted">Program</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">{{ $program->title }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden mb-5" style="border-radius: 25px;">
                @if($program->image)
                    <img src="{{ $program->image }}" class="img-fluid w-100" style="max-height: 500px; object-fit: cover;">
                @endif
                
                <div class="card-body p-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-warning p-3 rounded-4 me-4 shadow-sm">
                            <i class="bi {{ $program->icon }} fs-1 text-dark"></i>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-2 small fw-bold">PROGRAM UNGGULAN</span>
                            <h1 class="fw-bold mb-0">{{ $program->title }}</h1>
                        </div>
                    </div>
                    
                    <div class="content-text fs-5 text-muted leading-relaxed">
                        {!! nl2br(e($program->content)) !!}
                    </div>

                    <div class="mt-5 p-4 bg-light rounded-4 border-start border-warning border-5">
                        <h5 class="fw-bold mb-3"><i class="bi bi-info-circle-fill me-2"></i>PORTAL PENDAFTARAN ALUMNI</h5>
                        
                        @guest
                            <p class="mb-3">Silakan login sebagai Alumni untuk melakukan pendaftaran secara online melalui portal ini.</p>
                            <div class="d-flex flex-wrap gap-3">
                                <a href="{{ route('login') }}" class="btn btn-warning px-5 py-3 rounded-pill fw-bold shadow-sm">
                                    LOGIN UNTUK MENDAFTAR <i class="bi bi-box-arrow-in-right ms-2"></i>
                                </a>
                                <a href="mailto:{{ setting('contact_email', 'alumnisteman@gmail.com') }}" class="btn btn-dark px-4 py-3 rounded-pill fw-bold">
                                    HUBUNGI KAMI
                                </a>
                            </div>
                        @else
                            @php
                                $registration = Auth::user()->programRegistrations()->where('program_id', $program->id)->first();
                            @endphp

                            @if($registration)
                                <div class="alert alert-{{ $registration->status === 'approved' ? 'success' : ($registration->status === 'rejected' ? 'danger' : 'info') }} rounded-4 p-4 border-0">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi {{ $registration->status === 'approved' ? 'bi-check-circle-fill' : ($registration->status === 'rejected' ? 'bi-x-circle-fill' : 'bi-clock-history') }} fs-4 me-3"></i>
                                        <h6 class="fw-bold mb-0">Status Pendaftaran: {{ strtoupper($registration->status) }}</h6>
                                    </div>
                                    <p class="mb-0 text-dark opacity-75">
                                        Pendaftaran Anda diterima pada {{ $registration->created_at->format('d M Y') }}.
                                        @if($registration->admin_notes)
                                            <hr>
                                            <strong>Catatan Admin:</strong><br>
                                            {{ $registration->admin_notes }}
                                        @endif
                                    </p>
                                </div>
                            @else
                                <form action="{{ route('programs.register', $program->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded-4 shadow-sm border mt-3">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Nomor WhatsApp/Telepon</label>
                                            <input type="text" name="phone_number" class="form-control rounded-3" value="{{ old('phone_number', Auth::user()->phone_number) }}" placeholder="Contoh: 081234567890" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Lampiran (Opsional, PDF/JPG)</label>
                                            <input type="file" name="attachment" class="form-control rounded-3">
                                            <small class="text-muted">CV / Transkrip dlm bentuk satu file (Max 5MB)</small>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Motivasi & Alasan Mengikuti</label>
                                            <textarea name="motivation" class="form-control rounded-3" rows="4" placeholder="Jelaskan mengapa Anda tertarik mengikuti program ini..." required>{{ old('motivation') }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-warning w-100 py-3 rounded-pill fw-bold shadow-sm">
                                                KIRIM PENDAFTARAN SEKARANG <i class="bi bi-send-fill ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                    <h5 class="fw-bold mb-4">Program Lainnya</h5>
                    @foreach(App\Models\Program::where('status', 'active')->where('id', '!=', $program->id)->take(3)->get() as $p)
                        <a href="{{ route('programs.show', $p->slug) }}" class="text-decoration-none text-dark d-flex mb-4 group h-100">
                            <div class="bg-light p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi {{ $p->icon }} fs-4 text-muted"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ $p->title }}</h6>
                                <p class="small text-muted mb-0">{{ Str::limit($p->description, 40) }}</p>
                            </div>
                        </a>
                    @endforeach
                    <a href="{{ route('programs.index') }}" class="btn btn-outline-warning w-100 fw-bold rounded-pill">LIHAT SEMUA PROGRAM</a>
                </div>

                <div class="card border-0 bg-warning p-4 shadow-sm" style="border-radius: 20px;">
                    <h5 class="fw-bold mb-3 text-dark">Gabung Komunitas</h5>
                    <p class="small text-dark opacity-75">Jadilah bagian dari jaringan alumni terbesar. Dapatkan info eksklusif mengenai karir dan beasiswa.</p>
                    <a href="/register" class="btn btn-dark w-100 fw-bold rounded-pill">DAFTAR SEKARANG</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .content-text p { margin-bottom: 1.5rem; }
</style>
@endsection
