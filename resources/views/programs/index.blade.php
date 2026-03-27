@extends('layouts.app')

@section('content')
<div class="bg-dark text-white py-5 mb-5" style="background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1517245318773-5025d5123c73?auto=format&fit=crop&q=80&w=2070') center/cover;">
    <div class="container py-5 text-center">
        <h1 class="display-4 fw-bold mb-3">PROGRAM KAMI</h1>
        <p class="lead opacity-75 mx-auto" style="max-width: 700px;">Wadah kolaborasi dan pengembangan bagi seluruh keluarga besar Alumni Pusat Keunggulan {{ setting('school_name', 'SMKN 2 Ternate') }}.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        @foreach($programs as $program)
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm transition-hover" style="border-radius: 20px;">
                @if($program->image)
                    <img src="{{ $program->image }}" class="card-img-top" style="height: 200px; object-fit: cover; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                @else
                    <div class="bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 200px; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                        <i class="bi {{ $program->icon }} display-4 text-warning"></i>
                    </div>
                @endif
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-dark text-warning p-2 rounded-3 me-3">
                            <i class="bi {{ $program->icon }} fs-4"></i>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $program->title }}</h4>
                    </div>
                    <p class="text-muted small mb-4">{{ $program->description }}</p>
                    <a href="{{ route('programs.show', $program->slug) }}" class="btn btn-warning w-100 py-2 fw-bold text-dark rounded-pill">
                        LIHAT SELENGKAPNYA <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .transition-hover {
        transition: all 0.3s ease;
    }
    .transition-hover:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection
