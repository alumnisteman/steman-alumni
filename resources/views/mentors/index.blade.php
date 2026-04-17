@extends('layouts.app')

@section('content')
<div class="bg-primary text-white py-5 mb-5 shadow-sm overflow-hidden position-relative">
    <div class="container py-4 text-center position-relative z-1">
        <h1 class="fw-bold mb-3 display-4">PROGRAM MENTORING</h1>
        <p class="lead opacity-75">Bimbingan karir dari alumni berpengalaman untuk sesama alumni dan siswa.</p>
        <div class="mt-3">
            <span class="badge bg-white text-primary fs-6 rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-people-fill me-2"></i>{{ $mentors->count() }} Mentor Tersedia
            </span>
        </div>
    </div>
    <div class="position-absolute top-50 start-50 translate-middle opacity-10" style="font-size: 20rem;">
        <i class="bi bi-people"></i>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 justify-content-center">
        @forelse($mentors as $mentor)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative mentor-card">
                    {{-- Mentor Badge --}}
                    <div class="position-absolute top-0 end-0 m-3 z-1">
                        <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">
                            <i class="bi bi-patch-check-fill me-1"></i> Mentor
                        </span>
                    </div>

                    <div class="card-body p-4 text-center">
                        <img src="{{ $mentor->profile_picture_url }}" 
                             class="rounded-circle border border-3 border-primary mb-3 shadow" width="110" height="110" style="object-fit: cover;">
                        
                        <h5 class="fw-bold mb-1">{{ $mentor->name }}</h5>
                        <p class="text-primary small mb-1 fw-bold">{{ $mentor->major ?? 'Alumni' }} · Angkatan {{ $mentor->graduation_year }}</p>
                        
                        @if($mentor->current_job)
                        <p class="text-muted small mb-3">
                            <i class="bi bi-briefcase me-1"></i>{{ $mentor->current_job }}
                        </p>
                        @endif
                        
                        <div class="mb-3">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 small fw-bold">
                                <i class="bi bi-award me-1"></i> {{ $mentor->mentor_expertise ?? 'General Mentor' }}
                            </span>
                        </div>
                        
                        <p class="text-muted small mb-4" style="height: 4.5em; overflow: hidden;">
                            {{ $mentor->mentor_bio ?? $mentor->bio ?? 'Siap membimbing alumni dan siswa.' }}
                        </p>
                        
                        <div class="d-grid gap-2 mb-2">
                            @if($mentor->phone_number)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mentor->phone_number) }}" target="_blank" class="btn btn-success rounded-pill fw-bold">
                                <i class="bi bi-whatsapp me-2"></i> Hubungi WhatsApp
                            </a>
                            @endif
                            <a href="{{ route('alumni.show', $mentor->id) }}" class="btn btn-outline-primary rounded-pill fw-bold small">
                                <i class="bi bi-person-lines-fill me-1"></i> Lihat Profil Lengkap
                            </a>
                        </div>
                    </div>

                    {{-- Points Badge --}}
                    <div class="card-footer bg-light border-0 text-center py-2">
                        <small class="text-muted fw-bold">
                            <i class="bi bi-star-fill text-warning me-1"></i> {{ $mentor->points }} Poin Kontribusi
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-people display-1 text-primary opacity-25"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Belum Ada Mentor Terdaftar</h4>
                <p class="lead text-muted mb-4">Jadilah mentor pertama dan bantu sesama alumni berkembang!</p>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm">
                    <i class="bi bi-person-plus me-2"></i> Daftar Jadi Mentor
                </a>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
    .mentor-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .mentor-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.12) !important;
    }
</style>
@endpush
@endsection
