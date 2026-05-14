@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-lg-7">
            <h1 class="fw-black text-uppercase tracking-wider mb-2">💰 STEMAN ALUMNI FUND</h1>
            <p class="text-muted lead">Sistem penggalangan dana abadi yang transparan untuk beasiswa, bantuan sosial, dan pengembangan almamater.</p>
            <div class="mt-3">
                <a href="{{ route('donations.audit') }}" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-bold">
                    <i class="bi bi-shield-lock me-2"></i> LIHAT AUDIT PUBLIK
                </a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row g-2">
                <div class="col-6">
                    <div class="bg-white p-3 shadow-sm rounded-4 border-start border-4 border-primary h-100">
                        <p class="extra-small text-muted mb-1 font-weight-bold text-uppercase">💰 Dana Yayasan</p>
                        <h4 class="fw-black text-primary mb-0">Rp {{ number_format($totalFoundation, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-white p-3 shadow-sm rounded-4 border-start border-4 border-warning h-100">
                        <p class="extra-small text-muted mb-1 font-weight-bold text-uppercase">🎉 Dana Reuni</p>
                        <h4 class="fw-black text-warning mb-0">Rp {{ number_format($totalEvent, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dana Yayasan Section -->
    <div class="mb-5">
        <div class="d-flex align-items-center gap-3 mb-4">
            <h3 class="fw-black text-uppercase mb-0">💰 Dana Yayasan</h3>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 small">Sosial & Beasiswa</span>
        </div>
        <div class="row g-4">
            @forelse($foundationCampaigns as $campaign)
                @include('donations.partials.card', ['campaign' => $campaign, 'color' => 'primary'])
            @empty
                <div class="col-12 text-center py-4">
                    <p class="text-muted small italic">Belum ada program yayasan aktif.</p>
                </div>
            @endforelse
        </div>
    </div>

    <hr class="my-5 opacity-10">

    <!-- Dana Reuni Section -->
    <div class="mb-5">
        <div class="d-flex align-items-center gap-3 mb-4">
            <h3 class="fw-black text-uppercase mb-0">🎉 Dana Reuni</h3>
            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 small">Event & Kebersamaan</span>
        </div>
        <div class="row g-4">
            @forelse($eventCampaigns as $campaign)
                @include('donations.partials.card', ['campaign' => $campaign, 'color' => 'warning'])
            @empty
                <div class="col-12 text-center py-4">
                    <p class="text-muted small italic">Belum ada dana reuni aktif.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Real-time Donation Feed -->
    @if(count($recentDonations) > 0)
    <div class="mt-5">
        <h5 class="fw-black text-uppercase mb-4"><i class="bi bi-lightning-fill text-warning me-2"></i> Real-time Donation Feed</h5>
        <div class="row g-3">
            @foreach($recentDonations as $rd)
            <div class="col-md-6 col-lg-4">
                <div class="bg-white p-3 rounded-4 shadow-sm border-start border-4 border-primary d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="small mb-0 fw-bold">{{ $rd->is_anonymous ? 'Alumni Anonim' : $rd->user->name }}</p>
                        <p class="small text-muted mb-0">Donasi <b>Rp {{ number_format($rd->amount, 0, ',', '.') }}</b></p>
                    </div>
                    <span class="small text-muted opacity-50">{{ $rd->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Transparency Section -->
    <div class="mt-5 p-5 bg-dark text-white rounded-5 shadow-lg position-relative overflow-hidden">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h2 class="fw-black mb-3">#TransparansiTanpaBatas</h2>
                <p class="opacity-75 mb-4">Setiap rupiah yang Anda donasikan dicatat secara digital dan dapat diaudit oleh seluruh alumni. Kami menjamin dana Anda sampai ke tangan yang berhak.</p>
                <div class="d-flex gap-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-success fs-3"></i>
                        <span class="small fw-bold">Verifikasi Admin</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-eye text-info fs-3"></i>
                        <span class="small fw-bold">Audit Publik</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <i class="bi bi-file-earmark-bar-graph display-1 opacity-25"></i>
            </div>
        </div>
    </div>
</div>
@endsection
