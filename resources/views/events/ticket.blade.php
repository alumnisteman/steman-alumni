@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="mb-4">
                <a href="{{ route('alumni.dashboard') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>

            <!-- Ticket Card -->
            <div class="bg-white shadow-lg rounded-5 overflow-hidden position-relative">
                <!-- Perforated Line Decoration -->
                <div class="position-absolute start-0 end-0 d-flex justify-content-between px-3" style="top: 70%;">
                    <div class="bg-light rounded-circle" style="width: 30px; height: 30px; margin-left: -15px;"></div>
                    <div class="border-top border-2 border-dashed flex-grow-1 align-self-center mx-2 opacity-25"></div>
                    <div class="bg-light rounded-circle" style="width: 30px; height: 30px; margin-right: -15px;"></div>
                </div>

                <!-- Ticket Header -->
                <div class="bg-dark text-white p-5 text-center">
                    <h6 class="fw-black text-uppercase tracking-widest opacity-50 mb-3" style="font-size: 0.7rem;">OFFICIAL EVENT TICKET</h6>
                    <h3 class="fw-black text-uppercase mb-0">{{ $registration->program->title }}</h3>
                </div>

                <!-- Ticket Body -->
                <div class="p-5 pt-4">
                    <div class="row g-4 mb-5">
                        <div class="col-6">
                            <p class="small text-muted text-uppercase mb-1" style="font-size: 0.6rem; letter-spacing: 1px;">Nama Peserta</p>
                            <h6 class="fw-bold mb-0">{{ $registration->user->name }}</h6>
                        </div>
                        <div class="col-6">
                            <p class="small text-muted text-uppercase mb-1" style="font-size: 0.6rem; letter-spacing: 1px;">Status</p>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ strtoupper($registration->status) }}</span>
                        </div>
                        <div class="col-6">
                            <p class="small text-muted text-uppercase mb-1" style="font-size: 0.6rem; letter-spacing: 1px;">Tanggal Acara</p>
                            <h6 class="fw-bold mb-0">{{ $registration->program->event_date?->translatedFormat('d M Y') ?? '-' }}</h6>
                        </div>
                        <div class="col-6">
                            <p class="small text-muted text-uppercase mb-1" style="font-size: 0.6rem; letter-spacing: 1px;">Lokasi</p>
                            <h6 class="fw-bold mb-0 text-truncate">{{ $registration->program->event_location ?? '-' }}</h6>
                        </div>
                    </div>

                    <!-- QR Code Section -->
                    <div class="text-center mt-5 pt-3">
                        <div class="bg-light p-3 d-inline-block rounded-4 shadow-inner mb-3">
                            {!! QrCode::size(180)->generate($registration->ticket_code) !!}
                        </div>
                        <h5 class="fw-black font-monospace mb-1">{{ $registration->ticket_code }}</h5>
                        <p class="small text-muted">Tunjukkan QR Code ini kepada panitia di lokasi acara untuk proses check-in.</p>
                    </div>
                </div>

                <!-- Ticket Footer -->
                <div class="bg-light p-4 text-center">
                    <p class="small text-muted mb-0" style="font-size: 0.6rem; letter-spacing: 2px;">POWERED BY STEMAN PORTAL V5.0</p>
                </div>
            </div>

            <div class="text-center mt-4 d-grid">
                <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-5 fw-bold">
                    <i class="bi bi-printer me-2"></i> Cetak / Simpan Tiket
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .bg-white.shadow-lg { visibility: visible; position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; border: 1px solid #eee; }
    .bg-white.shadow-lg * { visibility: visible; }
    .btn, a { display: none !important; }
}
</style>
@endsection
