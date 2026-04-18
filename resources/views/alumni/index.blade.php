@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row mb-5 align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-black mb-1"> DIREKTORI ALUMNI</h2>
            <p class="text-muted">Temukan dan jalin komunikasi dengan jejaring alumni {{ setting('school_name', 'SMKN 2 Ternate') }} di seluruh dunia.</p>
        </div>
        <div class="col-lg-6">
            <form action="/alumni" method="GET" class="glass-effect p-3 rounded-4 shadow-sm">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0 shadow-none bg-transparent" placeholder="Cari berdasarkan nama, major, atau angkatan..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary px-4 rounded-3 h-100">Cari Alumni</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @forelse($alumni as $user)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift bg-white">
                    <div class="card-header border-0 bg-transparent py-4 text-center">
                        <div class="position-relative d-inline-block">
                            <div class="p-1 rounded-circle {{ $user->active_stories_count > 0 ? 'bg-gradient-story' : '' }}">
                                <img src="{{ $user->profile_picture_url }}" 
                                     class="rounded-circle border border-4 border-white shadow-sm" 
                                     style="width: 100px; height: 100px; object-fit: cover;" 
                                     alt="{{ $user->name }}">
                            </div>
                            @if($user->active_stories_count > 0)
                                <div class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-white" style="font-size: 0.6rem; z-index: 10;">LIVE</div>
                            @endif
                            @if($user->is_mentor)
                                <div class="position-absolute bottom-0 end-0 bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 30px; height: 30px;" title="Mentor Verified">
                                    <i class="bi bi-patch-check-fill"></i>
                                </div>
                            @endif
                        </div>
                        <h5 class="fw-bold mt-3 mb-1"><a href="/alumni/{{ $user->username ?? $user->id }}" class="text-dark text-decoration-none hover-text-primary">{{ $user->name }}</a></h5>
                        <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">{{ $user->major ?? 'Umum' }}</div>
                    </div>
                    <div class="card-body pt-0 px-4">
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-award me-2 text-primary"></i>
                                <span>Angkatan {{ $user->graduation_year ?? '-' }}</span>
                            </div>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-briefcase me-2 text-primary"></i>
                                <span class="text-truncate">{{ $user->current_job ?? 'Belum terisi' }}</span>
                            </div>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-geo-alt me-2 text-primary"></i>
                                <span class="text-truncate">{{ $user->address ?? 'Lokasi' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-0 bg-light bg-opacity-50 py-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="/alumni/{{ $user->username ?? $user->id }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill">Lihat Profil</a>
                            </div>
                            <div class="col-6">
                                <a href="mailto:{{ $user->email }}" class="btn btn-primary btn-sm w-100 rounded-pill">Kontak</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="glass-effect p-5 rounded-4 d-inline-block">
                    <i class="bi bi-person-x d-block display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada alumni yang sesuai dengan kriteria pencarian.</h5>
                    <a href="/alumni" class="btn btn-link">Tampilkan Semua</a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $alumni->links() }}
    </div>
</div>
@endsection
