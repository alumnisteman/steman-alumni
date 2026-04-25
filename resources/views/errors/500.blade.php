@extends('layouts.app')

@section('title', 'Sistem Sedang Memulihkan Diri')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center">
        <div class="mb-4">
            <i class="bi bi-cpu text-warning" style="font-size: 6rem; opacity: 0.8;"></i>
        </div>
        <h1 class="display-5 fw-bold text-dark mb-3">Sistem Sedang Sibuk</h1>
        <p class="lead text-muted mb-4">
            Mohon maaf, radar sistem kami sedang melakukan kalibrasi dan perbaikan otomatis (Self-Healing). <br>
            Fitur yang Anda tuju sedang dalam proses pemulihan.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ url('/') }}" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                <i class="bi bi-house-door me-2"></i> Kembali ke Beranda
            </a>
            <button onclick="window.location.reload()" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                <i class="bi bi-arrow-clockwise me-2"></i> Coba Lagi
            </button>
        </div>
        <div class="mt-5 text-muted small">
            <i class="bi bi-shield-check text-success me-1"></i> Auto-Guard System Active &bull; Steman Alumni Portal
        </div>
    </div>
</div>
@if(isset($exception_message))
    <!-- DEBUG INFO: {{ $exception_message }} -->
@endif
@endsection
