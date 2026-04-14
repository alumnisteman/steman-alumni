@extends('layouts.app')
@section('title', 'Menunggu Verifikasi - Alumni STEMAN')
@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="bi bi-hourglass-split text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Menunggu Verifikasi Admin</h2>
                    <p class="text-muted mb-4">
                        Halo <strong>{{ auth()->user()->name }}</strong>, akun Anda saat ini sedang dalam status <span class="badge bg-warning text-dark">Pending</span>.
                        Untuk menjaga keamanan platform dari akun palsu, Administrator kami sedang memverifikasi profil dan kesesuaian data kelulusan Anda.
                    </p>
                    <div class="alert alert-info border-0 shadow-sm text-start" style="border-radius: 15px;">
                        <i class="bi bi-info-circle-fill me-2"></i> <strong>Apa yang bisa Anda lakukan sekarang?</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>Memperbarui profil dan melengkapi info major/kelulusan.</li>
                            <li>Mengakses halaman dasbor utama terbatas.</li>
                            <li>Tunggu 1x24 jam untuk proses moderasi.</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4 gap-2 d-flex justify-content-center">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary px-4 fw-bold" style="border-radius: 10px;">
                            <i class="bi bi-person-lines-fill me-2"></i>Lengkapi Profil
                        </a>
                        <a href="{{ route('alumni.dashboard') }}" class="btn btn-outline-secondary px-4 fw-bold" style="border-radius: 10px;">
                            Ke Dasbor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
