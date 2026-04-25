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

                <div class="job-content mb-5">
                    <h5 class="fw-bold mb-3">Deskripsi Pekerjaan</h5>
                    <div class="text-muted" style="white-space: pre-line;">
                        {{ $job->content ?? $job->description }}
                    </div>
                </div>

                <!-- Content Ad Slot -->
                <div class="my-5">
                    <x-ad-slot position="content" aspectRatio="1280/200" mobileAspectRatio="400/150" />
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 sticky-top" style="top: 2rem;">
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
                        <button class="btn btn-success btn-lg rounded-pill fw-bold py-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#applyModal">
                            ONE-CLICK APPLY <i class="bi bi-send-fill ms-2"></i>
                        </button>
                        <p class="small text-muted text-center mt-2 px-3">
                            Resume Anda akan dikirim secara otomatis berdasarkan Profil Alumni Anda.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Sidebar Ad Slot -->
            <x-ad-slot position="sidebar" aspectRatio="1/1" />
        </div>
    </div>
</div>

<!-- Apply Modal -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold" id="applyModalLabel">Kirim Lamaran (One-Click Apply)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('jobs.apply', $job->slug) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 rounded-3 mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i> Profil alumni Anda (termasuk kontak dan jurusan) akan otomatis dikirim sebagai Resume. Pastikan profil Anda sudah lengkap!
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pesan Pengantar (Cover Letter) <span class="text-muted fw-normal">- Opsional</span></label>
                        <textarea name="cover_letter" class="form-control rounded-3" rows="4" placeholder="Jelaskan secara singkat mengapa Anda cocok untuk posisi ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-between">
                    <a href="{{ route('public.profile') }}" class="btn btn-outline-secondary rounded-pill px-4">Update Profil Dulu</a>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">Kirim Lamaran Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
