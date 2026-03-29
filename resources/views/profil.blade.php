@extends('layouts.app')

@section('content')
<div class="py-5 bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden shadow-sm shadow-hover">
                    <div class="card-header bg-dark text-white p-5 text-center border-0" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
                        <div class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold">PROFIL ORGANISASI</div>
                        <h1 class="fw-black mb-2 text-white">{{ setting('site_name', 'IKATAN ALUMNI SMKN 2') }}</h1>
                        <p class="lead opacity-75 mb-0">{{ setting('site_tagline', 'Wadah Jaringan Terkuat Alumni STEMAN') }}</p>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-5 align-items-center mb-5">
                            <div class="col-md-5 text-center">
                                <div class="position-relative d-inline-block">
                                    <img src="{{ setting('chairman_photo', 'https://ui-avatars.com/api/?name=Ketua+Umum&background=ffcc00&color=000&size=400') }}" 
                                         class="img-fluid rounded-4 shadow-lg border border-5 border-white" 
                                         style="max-height: 400px; width:100%; object-fit: cover;" 
                                         alt="Ketua Umum">
                                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n3">
                                        <span class="badge bg-warning text-dark px-4 py-2 rounded-pill shadow-sm fw-bold">KETUA UMUM</span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <h4 class="fw-bold mb-1 text-dark">{{ setting('chairman_name', 'Nama Ketua Umum') }}</h4>
                                    <p class="text-muted small mb-0">{{ setting('chairman_period', 'Periode 2024 - 2028') }}</p>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="text-primary fw-bold text-uppercase mb-2">Sambutan Ketua Umum</h6>
                                        <h2 class="fw-bold mb-0 font-heading">Pesan Untuk Alumni</h2>
                                    </div>
                                    @auth
                                        @if(auth()->user()->role == 'admin')
                                            <a href="{{ route('admin.chairman.edit') }}" class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm fw-bold border-0">
                                                <i class="bi bi-pencil-square me-1"></i> EDIT
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                                <div class="lead text-muted lh-lg" style="font-size: 1.1rem; font-style: italic;">
                                    "{!! nl2br(e(setting('alumni_message', 'Selamat datang di portal resmi Ikatan Alumni SMKN 2 Ternate. Wadah silaturahmi, kolaborasi, dan kontribusi nyata lulusan untuk almamater dan bangsa.'))) !!}"
                                </div>
                            </div>
                        </div>

                        <hr class="my-5 opacity-10">

                        <div class="row g-5">
                            <div class="col-md-6">
                                <div class="p-4 bg-white rounded-4 h-100 border border-light">
                                    <h4 class="fw-bold mb-4 d-flex align-items-center">
                                        <span class="bg-primary text-white p-2 rounded-3 me-3"><i class="bi bi-building"></i></span>
                                        Visi Organisasi
                                    </h4>
                                    <p class="text-muted lh-lg">
                                        Menjadi wadah alumni yang unggul, profesional, dan kontributif dalam mempererat kekeluargaan serta memajukan almamater {{ setting('school_name', 'SMKN 2 Ternate') }}.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 bg-white rounded-4 h-100 border border-light">
                                    <h4 class="fw-bold mb-4 d-flex align-items-center">
                                        <span class="bg-primary text-white p-2 rounded-3 me-3"><i class="bi bi-bullseye"></i></span>
                                        Misi Utama
                                    </h4>
                                    <ul class="list-unstyled text-muted lh-lg">
                                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> Mengelola database alumni yang terintegrasi.</li>
                                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> Menjembatani peluang karir dan kewirausahaan.</li>
                                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> Melaksanakan program bimbingan untuk adik kelas.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .font-heading { font-family: 'Inter', sans-serif; letter-spacing: -0.5px; }
    .shadow-hover { transition: transform 0.4s ease, box-shadow 0.4s ease; }
    .shadow-hover:hover { transform: translateY(-5px); }
</style>
@endsection
