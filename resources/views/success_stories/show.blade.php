@extends('layouts.app')

@section('content')
<div class="bg-light min-vh-100">
    <!-- Hero Header -->
    <div class="position-relative overflow-hidden py-5" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); min-height: 400px;">
        <div class="container position-relative z-index-1 mt-5">
            <div class="row align-items-center">
                <div class="col-lg-8 text-white">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/" class="text-white opacity-75 text-decoration-none">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-white opacity-75 text-decoration-none">Jejak Sukses</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ $successStory->name }}</li>
                        </ol>
                    </nav>
                    <h1 class="display-4 fw-bolder mb-3 animate__animated animate__fadeInUp">{{ $successStory->name }}</h1>
                    <div class="d-flex align-items-center gap-3 mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill shadow-sm">{{ $successStory->title }}</span>
                        <div class="vr opacity-50"></div>
                        <span class="opacity-90 fw-medium"><i class="bi bi-mortarboard-fill me-2"></i>{{ $successStory->major_year }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Abstract Decoration -->
        <div class="position-absolute end-0 bottom-0 opacity-10 d-none d-lg-block">
            <i class="bi bi-mortarboard-fill" style="font-size: 300px; transform: rotate(-15deg) translateY(50px);"></i>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container" style="margin-top: -80px; position: relative; z-index: 10;">
        <div class="row g-4">
            <!-- Left Sidebar (Photo) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden animate__animated animate__fadeInLeft">
                    <img src="{{ $successStory->image_path ? asset('storage/'.$successStory->image_path) : 'https://ui-avatars.com/api/?name='.urlencode($successStory->name).'&size=600&background=ffcc00&color=000' }}" 
                         class="img-fluid w-100" style="object-fit: cover; aspect-ratio: 1/1;" alt="{{ $successStory->name }}">
                    <div class="card-body p-4 bg-white">
                        <div class="p-3 rounded-4 bg-primary bg-opacity-5 border border-primary border-opacity-10">
                            <h6 class="fw-bold text-primary mb-2"><i class="bi bi-quote me-2"></i>Kutipan Favorit</h6>
                            <p class="text-muted italic mb-0" style="font-size: 0.95rem; line-height: 1.6;">"{{ $successStory->quote }}"</p>
                        </div>
                        
                        @if($successStory->user_id)
                        <hr class="my-4 opacity-10">
                        <a href="{{ route('alumni.show', $successStory->user_id) }}" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                            <i class="bi bi-person-circle me-2"></i>LIHAT PROFIL ALUMNI
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Content (Narrative) -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 animate__animated animate__fadeInRight" style="background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4">
                        <h3 class="fw-bold mb-0">Cerita Inspiratif</h3>
                        <div class="text-muted small">
                            <i class="bi bi-calendar3 me-2"></i>Dipublikasikan: {{ $successStory->created_at->format('d M Y') }}
                        </div>
                    </div>
                    
                    <div class="success-story-narrative" style="font-size: 1.15rem; line-height: 1.8; color: #334155;">
                        {!! nl2br(e($successStory->content)) !!}
                    </div>

                    <div class="mt-5 pt-4 border-top">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="text-muted">
                                <span class="me-3"><i class="bi bi-share me-2"></i>Bagikan Kisah Ini:</span>
                                <a href="#" class="text-primary me-2"><i class="bi bi-facebook fs-5"></i></a>
                                <a href="#" class="text-info me-2"><i class="bi bi-twitter fs-5"></i></a>
                                <a href="#" class="text-danger"><i class="bi bi-whatsapp fs-5"></i></a>
                            </div>
                            <a href="/" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer Spacer -->
    <div class="py-5"></div>
</div>

<style>
    .success-story-narrative p {
        margin-bottom: 2rem;
    }
    .z-index-1 { z-index: 1; }
    .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.5); }
</style>
@endsection
