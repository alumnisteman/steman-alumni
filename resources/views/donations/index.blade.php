@extends('layouts.app')

@push('styles')
<style>
/* ── KPI Strip ─────────────────────────────────── */
.kpi-strip {
    position: relative;
    background: url('/images/donations-hero.jpg') center center / cover no-repeat;
    border-bottom: none;
    padding: 3rem 0 2.2rem;
    overflow: hidden;
}
.kpi-strip::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(10,15,30,0.62) 0%, rgba(20,40,90,0.55) 100%);
    backdrop-filter: blur(1px);
    z-index: 0;
}
.kpi-strip .container { position: relative; z-index: 1; }

.fund-title {
    font-size: clamp(1.6rem, 3.5vw, 2.4rem);
    font-weight: 900;
    color: #ffffff;
    letter-spacing: -0.02em;
    text-transform: uppercase;
    text-shadow: 0 2px 12px rgba(0,0,0,0.4);
}
.fund-sub { color: rgba(255,255,255,0.72); font-size: 0.88rem; max-width: 460px; }
.audit-btn {
    display: inline-flex; align-items: center; gap: 0.45rem;
    background: rgba(255,255,255,0.12); color: #fff; border: 1px solid rgba(255,255,255,0.3);
    border-radius: 50px; padding: 0.4rem 1.1rem; font-size: 0.75rem;
    font-weight: 700; text-decoration: none; transition: background .15s;
}
.audit-btn:hover { background: rgba(255,255,255,0.22); color: #fff; }

/* KPI badge label di atas strip */
.kpi-strip .text-primary { color: #93c5fd !important; }

/* KPI cards */
.kpi-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-top: 1.8rem; }
@media(max-width:768px){ .kpi-grid { grid-template-columns: repeat(2,1fr); } }

.kpi-card {
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.22);
    border-top: 4px solid #3b82f6;
    border-radius: 0.85rem;
    padding: 1.1rem 1.3rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    transition: box-shadow .2s, transform .2s;
}
.kpi-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,0.35); transform: translateY(-2px); }
.kpi-card.blue  { border-top-color: #60a5fa; }
.kpi-card.amber { border-top-color: #fbbf24; }
.kpi-card.green { border-top-color: #34d399; }
.kpi-card.sky   { border-top-color: #38bdf8; }

.kpi-lbl {
    font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; color: rgba(255,255,255,0.55); margin-bottom: 0.3rem;
}
.kpi-val { font-size: 1.35rem; font-weight: 900; color: #ffffff; line-height: 1.1; text-shadow: 0 1px 6px rgba(0,0,0,0.3); }
.kpi-sub { font-size: 0.65rem; color: rgba(255,255,255,0.5); margin-top: 0.15rem; }

/* ── Body ─────────────────────────────────────── */
.page-body { background: #f8fafc; padding: 2rem 0 4rem; }

.section-label {
    font-size: 1.15rem; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 0.03em;
}
.section-chip {
    display: inline-flex; align-items: center; gap: 0.3rem;
    background: #e2e8f0; border-radius: 50px; color: #475569;
    padding: 0.25rem 0.75rem; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
}

/* ── Live Feed ─────────────────────────────────── */
.feed-card {
    background: #fff; border-left: 3px solid #3b82f6;
    border-radius: 0.75rem; box-shadow: 0 1px 5px rgba(0,0,0,0.06);
    padding: 0.75rem 1rem; display: flex; align-items: center; gap: 0.7rem;
    transition: transform .15s;
}
.feed-card:hover { transform: translateX(3px); }

/* ── Promo ─────────────────────────────────────── */
.promo-banner {
    background: linear-gradient(135deg, #0f172a, #1e3a8a);
    border-radius: 1.5rem; padding: 2.8rem 2.5rem; margin-top: 2.5rem;
}
</style>
@endpush

@section('content')

{{-- ══ KPI Strip (WHITE) ══════════════════════════════ --}}
<div class="kpi-strip">
    <div class="container">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <p class="text-xs fw-bold text-primary text-uppercase mb-1" style="font-size:.65rem;letter-spacing:.12em;">
                    💰 Transparansi Dana Alumni
                </p>
                <h1 class="fund-title mb-1">STEMAN ALUMNI FUND</h1>
                <p class="fund-sub mb-2">
                    Sistem penggalangan dana transparan — setiap rupiah tercatat,
                    terverifikasi, dan dapat diaudit seluruh alumni.
                </p>
            </div>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card blue">
                <div class="kpi-lbl">💰 Dana Yayasan</div>
                <div class="kpi-val text-primary">Rp {{ number_format($totalFoundation, 0, ',', '.') }}</div>
                <div class="kpi-sub">Total terhimpun</div>
            </div>
            <div class="kpi-card amber">
                <div class="kpi-lbl">🎉 Dana Reuni</div>
                <div class="kpi-val" style="color:#d97706;">Rp {{ number_format($totalEvent, 0, ',', '.') }}</div>
                <div class="kpi-sub">Total terhimpun</div>
            </div>
            <div class="kpi-card green">
                <div class="kpi-lbl">👥 Total Donatur</div>
                <div class="kpi-val" style="color:#059669;">{{ number_format($totalDonors) }} Alumni</div>
                <div class="kpi-sub">Donatur unik terverifikasi</div>
            </div>
            <div class="kpi-card sky">
                <div class="kpi-lbl">🧾 Transaksi</div>
                <div class="kpi-val" style="color:#0284c7;">{{ number_format($totalTransactions) }}</div>
                <div class="kpi-sub">Total donasi masuk</div>
            </div>
        </div>
    </div>
</div>

{{-- ══ Body ════════════════════════════════════════════ --}}
<div class="page-body">
    <div class="container">

        {{-- Live Feed --}}
        @if(count($recentDonations) > 0)
        <div class="mb-5">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="section-chip"><i class="bi bi-lightning-fill text-warning"></i> Live Feed Donasi</span>
            </div>
            <div class="row g-2">
                @foreach($recentDonations as $rd)
                <div class="col-md-6 col-lg-4">
                    <div class="feed-card">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:36px;height:36px;font-size:.85rem;">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="small fw-bold text-dark text-truncate">
                                {{ $rd->is_anonymous ? 'Alumni Anonim' : $rd->user->name }}
                            </div>
                            <div class="small text-muted">
                                Donasi <b class="text-success">Rp {{ number_format($rd->amount, 0, ',', '.') }}</b>
                                @if($rd->campaign) · <span>{{ Str::limit($rd->campaign->title, 22) }}</span>@endif
                            </div>
                        </div>
                        <span class="text-muted flex-shrink-0" style="font-size:.6rem;">
                            {{ $rd->created_at->diffForHumans(null, true) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Dana Yayasan --}}
        @if($foundationCampaigns->count() > 0)
        <div class="mb-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <h2 class="section-label mb-0">💰 Dana Yayasan</h2>
                <span class="section-chip">Sosial & Beasiswa</span>
            </div>
            <div class="row g-4">
                @foreach($foundationCampaigns as $campaign)
                    @include('donations.partials.card', ['campaign' => $campaign, 'color' => 'primary'])
                @endforeach
            </div>
        </div>
        @else
        <div class="mb-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <h2 class="section-label mb-0">💰 Dana Yayasan</h2>
                <span class="section-chip">Sosial & Beasiswa</span>
            </div>
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center" style="background:#f8fafc;">
                <i class="bi bi-bank display-4 text-primary opacity-25 d-block mb-3"></i>
                <p class="text-muted mb-1 fw-semibold">Dana Yayasan sedang dalam persiapan</p>
                <p class="text-muted small mb-0">Program beasiswa dan sosial akan segera dibuka. Pantau terus!</p>
            </div>
        </div>
        @endif

        {{-- Dana Reuni --}}
        @if($eventCampaigns->count() > 0)
        <div class="mb-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <h2 class="section-label mb-0">🎉 Dana Reuni</h2>
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

        {{-- Promo Transparansi --}}
        <div class="promo-banner">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="fw-black text-white mb-3">#TransparansiTanpaBatas</h2>
                    <p class="mb-4" style="color:rgba(255,255,255,.6);">
                        Setiap rupiah yang Anda donasikan dicatat secara digital dan dapat diaudit oleh seluruh alumni.
                        Kami menjamin dana Anda sampai ke tangan yang berhak.
                    </p>
                    <div class="d-flex flex-wrap gap-4">
                        <div class="d-flex align-items-center gap-2 text-white">
                            <i class="bi bi-shield-check text-success fs-5"></i>
                            <span class="small fw-bold">Verifikasi Admin</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white">
                            <i class="bi bi-eye text-info fs-5"></i>
                            <span class="small fw-bold">Audit Publik</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white">
                            <i class="bi bi-graph-up text-warning fs-5"></i>
                            <span class="small fw-bold">Laporan Real-time</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="bi bi-file-earmark-bar-graph text-white" style="font-size:6rem;opacity:.1;"></i>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
