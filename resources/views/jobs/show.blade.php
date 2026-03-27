@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <a href="{{ route('jobs.index') }}" class="text-decoration-none text-muted small mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Loker
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex align-items-center mb-4 pb-4 border-bottom">
                    @if($job->image)
                        <img src="{{ $job->image }}" class="rounded-4 me-4 shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="bg-primary bg-opacity-10 text-primary p-4 rounded-4 me-4 border border-primary border-opacity-10">
                            <i class="bi bi-briefcase fs-1"></i>
                        </div>
                    @endif
                    <div>
                        <h2 class="fw-bold mb-1">{{ $job->title }}</h2>
                        <h5 class="text-primary fw-bold">{{ $job->company }}</h5>
                        <div class="text-muted small mt-2">
                            <span class="me-3"><i class="bi bi-geo-alt me-1"></i> {{ $job->location }}</span>
                            <span><i class="bi bi-calendar3 me-1"></i> Diposting {{ $job->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <div class="job-content">
                    <h5 class="fw-bold mb-3">Deskripsi Pekerjaan</h5>
                    <div class="text-muted" style="white-space: pre-line;">
                        {{ $job->content ?? $job->description }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 2rem;">
                <h5 class="fw-bold mb-4">Informasi Lowongan</h5>
                <div class="mb-3">
                    <p class="small text-muted mb-1">Tipe Pekerjaan</p>
                    <p class="fw-bold mb-0 text-dark">{{ $job->type }}</p>
                </div>
                <div class="mb-4">
                    <p class="small text-muted mb-1">Lokasi</p>
                    <p class="fw-bold mb-0 text-dark">{{ $job->location }}</p>
                </div>
                
                <div class="d-grid gap-2">
                    @if($job->external_link)
                        <a href="{{ $job->external_link }}" target="_blank" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow-sm">
                            APPLY NOW <i class="bi bi-box-arrow-up-right ms-2"></i>
                        </a>
                        <p class="small text-muted text-center mt-2 px-3">
                            Anda akan diarahkan ke link pendaftaran di platform eksternal.
                        </p>
                    @else
                        <button class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow-sm disabled">
                            APPLY VIA EMAIL
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
