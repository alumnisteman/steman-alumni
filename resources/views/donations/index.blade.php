@extends('layouts.app')

@push('styles')
<style>
/* ── Hero Dashboard ──────────────────────────────── */
.fund-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #0f172a 100%);
    padding: 3.5rem 0 0;
    position: relative;
    overflow: hidden;
}
.fund-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%236366f1' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.fund-hero .tagline {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #a5b4fc;
    background: rgba(99,102,241,0.15);
    border: 1px solid rgba(99,102,241,0.3);
    border-radius: 50px;
    padding: 0.35rem 1rem;
    display: inline-block;
}

/* KPI Cards */
.kpi-card {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 1rem;
    padding: 1.4rem 1.6rem;
    backdrop-filter: blur(10px);
    transition: background 0.2s, transform 0.2s;
}
.kpi-card:hover { background: rgba(255,255,255,0.1); transform: translateY(-2px); }
.kpi-card .kpi-label {
    font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 0.4rem;
}
.kpi-card .kpi-value {
    font-size: 1.5rem; font-weight: 900; color: #fff; line-height: 1.1;
}
.kpi-card .kpi-sub { font-size: 0.7rem; color: rgba(255,255,255,0.4); margin-top: 0.25rem; }

/* Wave separator */
.wave-sep {
    display: block; width: 100%; height: 60px;
    background: linear-gradient(to bottom right, transparent 49%, #f8fafc 50%);
}

/* Campaign section */
.section-chip {
    display: inline-flex; align-items: center; gap: 0.4rem;
    background: #f1f5f9; border-radius: 50px;
    padding: 0.3rem 0.9rem; font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em; color: #475569;
}

/* Real-time feed */
.feed-item {
    background: #fff;
    border-radius: 0.75rem;
    box-shadow: 0 1px 8px rgba(0,0,0,0.06);
    padding: 0.85rem 1rem;
    display: flex; align-items: center; gap: 0.75rem;
    border-left: 3px solid #6366f1;
    transition: transform 0.15s;
}
.feed-item:hover { transform: translateX(3px); }

/* Dark mode */
[data-bs-theme="dark"] .wave-sep { background: linear-gradient(to bottom right, transparent 49%, #0f172a 50%); }
[data-bs-theme="dark"] .section-chip { background: rgba(255,255,255,0.06); color: #94a3b8; }
[data-bs-theme="dark"] .feed-item { background: #1e293b; border-color: #6366f1; }
</style>
@endpush

@section('content')

{{-- ── Hero dengan KPI Dashboard ─────────────────────── --}}
<div class="fund-hero">
    <div class="container position-relative">

        {{-- Header --}}
        <div class="text-center mb-5">
            <span class="tagline mb-3">💰 Transparansi Dana Alumni</span>
            <h1 class="fw-black text-white mt-3 mb-2" style="font-size:clamp(1.8rem,4vw,3rem);">
                STEMAN ALUMNI FUND
            </h1>
            <p class="text-white opacity-50 mb-4" style="max-width:520px;margin:0 auto;">
                Sistem penggalangan dana yang transparan — setiap rupiah tercatat, terverifikasi, dan dapat diaudit seluruh alumni.
            </p>
            <a href="{{ route('donations.audit') }}" class="btn btn-sm btn-outline-light rounded-pill px-4 opacity-75 fw-bold">
                <i class="bi bi-shield-lock me-2"></i> Lihat Audit Publik
            </a>
        </div>

        {{-- KPI Cards --}}
        <div class="row g-3 pb-5">
            <div class="col-6 col-md-3">
                <div class="kpi-card">
                    <div class="kpi-label">💰 Dana Yayasan</div>
                    <div class="kpi-value text-primary" style="color:#818cf8!important;">
                        Rp {{ number_format($totalFoundation, 0, ',', '.') }}
                    </div>
                    <div class="kpi-sub">Total terverifikasi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card">
                    <div class="kpi-label">🎉 Dana Reuni</div>
                    <div class="kpi-value" style="color:#fbbf24!important;">
                        Rp {{ number_format($totalEvent, 0, ',', '.') }}
                    </div>
                    <div class="kpi-sub">Total terverifikasi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card">
                    <div class="kpi-label">👥 Total Donatur</div>
                    <div class="kpi-value" style="color:#34d399!important;">
                        {{ number_format($totalDonors) }} Alumni
                    </div>
                    <div class="kpi-sub">Donatur unik terverifikasi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card">
                    <div class="kpi-label">🧾 Transaksi</div>
                    <div class="kpi-value" style="color:#38bdf8!important;">
                        {{ number_format($totalTransactions) }}
                    </div>
                    <div class="kpi-sub">Total donasi masuk</div>
                </div>
            </div>
        </div>

    </div>
    <span class="wave-sep"></span>
</div>

{{-- ── Konten Utama ───────────────────────────────────── --}}
<div style="background:#f8fafc;" class="py-5">

    @if(count($recentDonations) > 0)
    {{-- Real-time Feed --}}
    <div class="container mb-5">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="section-chip"><i class="bi bi-lightning-fill text-warning"></i> Live Feed Donasi</span>
        </div>
        <div class="row g-2">
            @foreach($recentDonations as $rd)
            <div class="col-md-6 col-lg-4">
                <div class="feed-item">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:38px;height:38px;font-size:1rem;">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="small fw-bold text-truncate">{{ $rd->is_anonymous ? 'Alumni Anonim' : $rd->user->name }}</div>
                        <div class="small text-muted">Donasi <b class="text-success">Rp {{ number_format($rd->amount, 0, ',', '.') }}</b>
                            · <span class="text-muted">{{ $rd->campaign->title ?? '' }}</span>
                        </div>
                    </div>
                    <span class="small text-muted opacity-50 flex-shrink-0">{{ $rd->created_at->diffForHumans(null, true) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="container">

        {{-- Dana Yayasan --}}
        @if($foundationCampaigns->count() > 0)
        <div class="mb-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <h3 class="fw-black mb-0">💰 Dana Yayasan</h3>
                <span class="section-chip">Sosial & Beasiswa</span>
            </div>
            <div class="row g-4">
                @foreach($foundationCampaigns as $campaign)
                    @include('donations.partials.card', ['campaign' => $campaign, 'color' => 'primary'])
                @endforeach
            </div>
        </div>
        @endif

        {{-- Dana Reuni --}}
        @if($eventCampaigns->count() > 0)
        <div class="mb-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <h3 class="fw-black mb-0">🎉 Dana Reuni</h3>
                <span class="section-chip">Event & Kebersamaan</span>
            </div>
            <div class="row g-4">
                @foreach($eventCampaigns as $campaign)
                    @include('donations.partials.card', ['campaign' => $campaign, 'color' => 'warning'])
                @endforeach
            </div>
        </div>
        @endif

        @if($foundationCampaigns->count() === 0 && $eventCampaigns->count() === 0)
        <div class="text-center py-5">
            <i class="bi bi-folder2-open display-3 text-muted opacity-25"></i>
            <p class="text-muted mt-3">Belum ada program aktif saat ini.</p>
        </div>
        @endif

        {{-- Transparansi Banner --}}
        <div class="rounded-5 shadow-sm overflow-hidden mt-2"
             style="background: linear-gradient(135deg, #0f172a, #1e3a8a); padding: 3rem 2.5rem;">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="fw-black text-white mb-3">#TransparansiTanpaBatas</h2>
                    <p class="text-white opacity-60 mb-4">
                        Setiap rupiah yang Anda donasikan dicatat secara digital dan dapat diaudit oleh seluruh alumni.
                        Kami menjamin dana Anda sampai ke tangan yang berhak.
                    </p>
                    <div class="d-flex flex-wrap gap-4">
                        <div class="d-flex align-items-center gap-2 text-white">
                            <i class="bi bi-shield-check text-success fs-4"></i>
                            <span class="small fw-bold">Verifikasi Admin</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white">
                            <i class="bi bi-eye text-info fs-4"></i>
                            <span class="small fw-bold">Audit Publik</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white">
                            <i class="bi bi-graph-up text-warning fs-4"></i>
                            <span class="small fw-bold">Laporan Real-time</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="bi bi-file-earmark-bar-graph text-white opacity-10" style="font-size:7rem;"></i>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
