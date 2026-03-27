@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="bg-warning p-5 text-center">
                    <img src="{{ $user->foto_profil ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=000&color=fff&size=200' }}" 
                         class="rounded-circle border border-4 border-white shadow-sm mb-3" width="180" height="180" style="object-fit: cover;">
                    <h2 class="fw-bold mb-1">{{ $user->name }}</h2>
                    <p class="lead opacity-75 mb-2 text-uppercase small fw-bold">{{ $user->jurusan }} | Angkatan {{ $user->tahun_lulus }}</p>
                    <div class="d-inline-flex align-items-center bg-dark text-white px-3 py-1 rounded-pill small shadow-sm">
                        <i class="bi bi-shield-check me-2" style="color: #ffcc00;"></i>
                        <span class="fw-bold" style="font-size: 0.75rem;">Verified Member</span>
                        <span class="ms-2 opacity-50" style="font-size: 0.7rem;">ILUNI-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
                
                <div class="card-body p-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Informasi Pekerjaan</h6>
                            <div class="mb-4">
                                <label class="text-muted d-block small">Pekerjaan Sekarang</label>
                                <span class="fw-bold text-dark">{{ $user->pekerjaan_sekarang ?? 'Belum ditentukan' }}</span>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted d-block small">Instansi / Perusahaan</label>
                                <span class="fw-bold text-dark text-uppercase small">{{ $user->perusahaan_universitas ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Kontak</h6>
                            <div class="mb-4">
                                <label class="text-muted d-block small">Alamat</label>
                                <span class="text-dark">{{ $user->alamat ?? '-' }}</span>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted d-block small">Email</label>
                                <span class="text-dark">{{ $user->email }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Tentang Saya</h6>
                        <p class="text-dark" style="white-space: pre-line;">{{ $user->bio ?? 'Belum ada informasi tambahan.' }}</p>
                    </div>

                    <div class="text-center mt-5 pt-4 border-top">
                        <a href="/alumni" class="btn btn-outline-dark rounded-pill px-5">Kembali ke Daftar Alumni</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
