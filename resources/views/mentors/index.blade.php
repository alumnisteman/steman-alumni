@extends('layouts.app')

@section('content')
<div class="bg-primary text-white py-5 mb-5 shadow-sm overflow-hidden position-relative">
    <div class="container py-4 text-center position-relative z-1">
        <h1 class="fw-bold mb-3 display-4">PROGRAM MENTORING</h1>
        <p class="lead opacity-75">Bimbingan karir dari alumni berpengalaman untuk sesama alumni dan siswa.</p>
    </div>
    <div class="position-absolute top-50 start-50 translate-middle opacity-10" style="font-size: 20rem;">
        <i class="bi bi-people"></i>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 justify-content-center">
        @forelse($mentors as $mentor)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative shadow-hover">
                    <div class="card-body p-4 text-center">
                        <img src="{{ $mentor->foto_profil ?? 'https://ui-avatars.com/api/?name='.urlencode($mentor->name) }}" 
                             class="rounded-circle border mb-3 shadow-sm" width="100" height="100" style="object-fit: cover;">
                        
                        <h5 class="fw-bold mb-1">{{ $mentor->name }}</h5>
                        <p class="text-primary small mb-3 fw-bold">{{ $mentor->jurusan ?? 'Alumni' }} · Angkatan {{ $mentor->tahun_lulus }}</p>
                        
                        <div class="mb-3">
                            <span class="badge bg-primary text-white rounded-pill px-3 py-2 small">
                                <i class="bi bi-award me-1"></i> {{ $mentor->mentor_expertise ?? 'General Mentor' }}
                            </span>
                        </div>
                        
                        <p class="text-muted small mb-4" style="height: 4.5em; overflow: hidden;">
                            {{ $mentor->mentor_bio ?? $mentor->bio }}
                        </p>
                        
                        <div class="d-grid mb-2">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mentor->nomor_telepon) }}" target="_blank" class="btn btn-success rounded-pill fw-bold">
                                <i class="bi bi-whatsapp me-2"></i> Hubungi WhatsApp
                            </a>
                        </div>
                        <a href="{{ route('profile.show', $mentor->id) }}" class="btn btn-link text-decoration-none small fw-bold">Lihat Profil Lengkap</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-people display-1 text-light"></i>
                <p class="lead mt-3 text-muted">Belum ada mentor terdaftar saat ini. Jadilah mentor pertama!</p>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary rounded-pill px-4 fw-bold mt-2">Daftar Jadi Mentor</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
