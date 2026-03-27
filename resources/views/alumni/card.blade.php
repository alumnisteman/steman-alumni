@extends('layouts.app')

@section('content')
<div class="min-h-screen py-12 px-4 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="card-3d-wrapper mx-auto" style="width: 400px; height: 260px;">
        <div class="card-3d-inner">
            <!-- FRONT SIDE -->
            <div class="card-front glass-effect shadow-2xl d-flex flex-column justify-content-between p-4" style="background: linear-gradient(135deg, #0f172a 0%, #3f37c9 100%); border: 1px solid rgba(255,255,255,0.1); color: white;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="fw-black mb-0 tracking-tighter">STEMAN ALUMNI</h4>
                        <span class="small opacity-75 fw-bold text-uppercase" style="font-size: 0.6rem; letter-spacing: 2px;">Official Digital ID Card</span>
                    </div>
                    <i class="bi bi-cpu fs-3 opacity-50"></i>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="profile-img-container">
                        @if($user->foto_profil)
                            <img src="{{ asset('storage/' . $user->foto_profil) }}" class="rounded-circle border border-2 border-white shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-white text-primary fw-bold d-flex align-items-center justify-content-center border border-2 border-white shadow-sm" style="width: 70px; height: 70px; font-size: 1.5rem;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="text-start">
                        <h5 class="fw-bold mb-0 text-white">{{ $user->name }}</h5>
                        <p class="small mb-0 opacity-75">{{ $user->jurusan }} • Lulus {{ $user->tahun_lulus }}</p>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-end">
                    <div class="text-start">
                        <span class="d-block opacity-50" style="font-size: 0.5rem; text-transform: uppercase;">Member ID</span>
                        <span class="fw-mono small">#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold" style="font-size: 0.6rem;">VERIFIED</div>
                </div>
            </div>

            <!-- BACK SIDE -->
            <div class="card-back glass-effect shadow-2xl d-flex flex-column align-items-center justify-content-center p-4 text-center" style="background: white; border: 1px solid rgba(0,0,0,0.1); color: #1e293b;">
                <div class="mb-2">
                    {!! $qrCode !!}
                </div>
                <p class="small fw-bold mb-1">SCAN TO VERIFY</p>
                <p class="text-muted" style="font-size: 0.6rem;">This card is a valid proof of alumni membership for SMK N 2 Ternate.</p>
                <div class="mt-2 w-100 border-top pt-2">
                    <span class="badge bg-primary text-white" style="font-size: 0.5rem;">GLOBAL NETWORK PLATFORM</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 text-center">
        <p class="text-muted small mb-4"><i class="bi bi-info-circle me-1"></i> Arahkan kursor atau tap kartu untuk melihat sisi belakang.</p>
        <div class="d-flex gap-2 justify-content-center">
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                <i class="bi bi-printer me-2"></i>Cetak Kartu
            </button>
            <a href="{{ route('alumni.dashboard') }}" class="btn btn-dark btn-sm rounded-pill px-4">
                Dashboard
            </a>
        </div>
    </div>
</div>

        <div class="mt-8 text-center">
            <a href="{{ route('alumni.dashboard') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-600 text-sm font-medium transition-colors">
                &lsaquo; Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
