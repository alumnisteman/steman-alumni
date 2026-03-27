@extends('layouts.app')

@section('content')
<div class="bg-primary text-white py-5 mb-5 shadow-sm">
    <div class="container py-4 text-center">
        <h1 class="fw-bold mb-3 display-4">LOWONGAN KERJA</h1>
        <p class="lead opacity-75">Temukan peluang karir terbaik dari mitra dan alumni {{ setting('school_name', 'SMKN 2 Ternate') }}</p>
    </div>
</div>

<div class="container py-5">
    <!-- Search & Tabs UI -->
    <div class="row mb-4 justify-content-center text-center">
        <div class="col-md-8">
            <form action="/jobs" method="GET" class="mb-4">
                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                    <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-0 py-3" placeholder="Nama perusahaan, posisi, atau lokasi..." value="{{ request('search') }}">
                    <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
                    <button class="btn btn-primary px-4 fw-bold" type="submit">Cari</button>
                </div>
            </form>

            <div class="d-inline-flex bg-white p-1 rounded-pill shadow-sm mb-4 border border-light">
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'all']) }}" class="btn {{ request('tab', 'all') === 'all' ? 'btn-primary' : 'btn-light border-0' }} rounded-pill px-4 py-2 fw-bold transition-all">
                    SEMUA LOWONGAN
                </a>
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'recommended']) }}" class="btn {{ request('tab') === 'recommended' ? 'btn-primary' : 'btn-light border-0' }} rounded-pill px-4 py-2 fw-bold transition-all position-relative">
                    REKOMENDASI JURUSAN
                    @if($matchCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white">
                            {{ $matchCount }}
                        </span>
                    @endif
                </a>
            </div>
            
            @if(request('tab') === 'recommended')
                <div class="alert alert-info border-0 rounded-4 shadow-sm py-3 px-4 text-start">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-robot fs-2 text-primary me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-1">AI Recommendation Active</h6>
                            <p class="small mb-0 opacity-75">Menampilkan lowongan yang relevan dengan keahlian Anda sebagai alumni <strong>{{ auth()->user()->jurusan }}</strong>.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        @forelse($jobs as $job)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden shadow-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            @if($job->image)
                                <div class="me-3">
                                    <img src="{{ $job->image }}" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $job->company }}">
                                </div>
                            @else
                                <div class="me-3 bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-briefcase text-primary"></i>
                                </div>
                            @endif
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">{{ $job->title }}</h5>
                                <p class="text-primary small mb-0 fw-bold">{{ $job->company }}</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2 me-1 small">
                                <i class="bi bi-geo-alt me-1"></i> {{ $job->location ?? 'N/A' }}
                            </span>
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2 small me-1">
                                <i class="bi bi-clock me-1"></i> {{ $job->type }}
                            </span>
                            @if(auth()->check() && auth()->user()->jurusan && (Str::contains(strtolower($job->description), strtolower(auth()->user()->jurusan)) || Str::contains(strtolower($job->title), strtolower(auth()->user()->jurusan))))
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 small mt-2 d-inline-block">
                                    <i class="bi bi-check-circle-fill me-1"></i> Major Match: {{ auth()->user()->jurusan }}
                                </span>
                            @endif
                        </div>
                        <p class="text-muted small mb-4" style="height: 4.5em; overflow: hidden;">
                            {{ $job->description }}
                        </p>
                        <div class="d-grid">
                            @if($job->external_link)
                                <a href="{{ $job->external_link }}" target="_blank" class="btn btn-outline-primary rounded-pill fw-bold">
                                    Apply on External Site <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            @else
                                <a href="{{ route('jobs.show', $job->slug) }}" class="btn btn-primary rounded-pill fw-bold">
                                    Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-briefcase display-1 text-light"></i>
                <p class="lead mt-3 text-muted">Belum ada lowongan kerja tersedia saat ini.</p>
            </div>
        @endforelse
    </div>
    
    <div class="d-flex justify-content-center mt-5">
        {{ $jobs->links() }}
    </div>
</div>
@endsection
