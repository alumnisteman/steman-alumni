@extends('layouts.app')

@push('styles')
<style>
/* ── KPI Banner ─────────────────────────────────── */
.kpi-banner {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #0369a1 100%);
    padding: 2.8rem 0;
    position: relative;
    overflow: hidden;
}
.kpi-banner::after {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='20' cy='20' r='3'/%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}

.kpi-badge {
    display: inline-block;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    color: #bfdbfe;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    border-radius: 50px;
    padding: 0.3rem 1rem;
    margin-bottom: 1rem;
}

.kpi-box {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    transition: background .2s, transform .2s;
}
.kpi-box:hover { background: rgba(255,255,255,0.2); transform: translateY(-2px); }
.kpi-box .lbl {
    font-size: 0.62rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; color: rgba(255,255,255,0.6); margin-bottom: 0.35rem;
}
.kpi-box .val {
    font-size: 1.45rem; font-weight: 900; color: #ffffff; line-height: 1.1;
}
.kpi-box .sub { font-size: 0.68rem; color: rgba(255,255,255,0.45); margin-top: 0.2rem; }

/* ── Divider ────────────────────────────────────── */
.banner-foot {
    background: linear-gradient(to bottom right, #1d4ed8 49%, #f8fafc 50%);
    height: 52px; display: block; width: 100%;
}

/* ── Body ────────────────────────────────────────── */
.page-body { background: #f8fafc; padding: 2rem 0 4rem; }

.section-heading {
    font-size: 1.2rem; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 0.04em;
}
.section-chip {
    display: inline-flex; align-items: center; gap: 0.35rem;
    background: #e2e8f0; border-radius: 50px; color: #475569;
    padding: 0.28rem 0.8rem; font-size: 0.68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.06em;
}

/* ── Feed ─────────────────────────────────────────── */
.feed-card {
    background: #ffffff;
    border-left: 3px solid #3b82f6;
    border-radius: 0.75rem;
    box-shadow: 0 1px 6px rgba(0,0,0,0.06);
    padding: 0.8rem 1rem;
    display: flex; align-items: center; gap: 0.75rem;
    transition: transform .15s;
}
.feed-card:hover { transform: translateX(3px); }

/* ── Transparency Banner ───────────────────────────── */
.promo-banner {
    background: linear-gradient(135deg, #0f172a, #1e3a8a);
    border-radius: 1.5rem;
    padding: 3rem 2.5rem;
    margin-top: 3rem;
}
</style>
@endpush

@section('content')

{{-- ══ KPI Banner ══════════════════════════════════════ --}}
<div class="kpi-banner">
    <div class="container position-relative" style="z-index:1;">
        <div class="row align-items-center mb-4">
            <div class="col-md-7">
                <span class="kpi-badge">💰 Transparansi Dana Alumni</span>
                <h1 class="fw-black text-white mb-1" style="font-size:clamp(1.6rem,3.5vw,2.6rem);">
                    STEMAN ALUMNI FUND
                </h1>
                <p class="mb-3" style="color:rgba(255,255,255,0.65);font-size:0.9rem;max-width:480px;">
                    Sistem penggalangan dana transparan — setiap rupiah tercatat, terverifikasi, dan dapat diaudit seluruh alumni.
                </p>
                <a href="{{ route('donations.audit') }}"
                   class="btn btn-sm rounded-pill fw-bold px-4"
                   style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);">
                    <i class="bi bi-shield-lock me-2"></i> Lihat Audit Publik
                </a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="kpi-box">
                    <div class="lbl">💰 Dana Yayasan</div>
                    <div class="val">Rp {{ number_format($totalFoundation, 0, ',', '.') }}</div>
                    <div class="sub">Total terverifikasi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-box">
                    <div class="lbl">🎉 Dana Reuni</div>
                    <div class="val">Rp {{ number_format($totalEvent, 0, ',', '.') }}</div>
                    <div class="sub">Total terverifikasi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-box">
                    <div class="lbl">👥 Total Donatur</div>
                    <div class="val">{{ number_format($totalDonors) }} Alumni</div>
                    <div class="sub">Donatur unik terverifikasi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-box">
                    <div class="lbl">🧾 Transaksi</div>
                    <div class="val">{{ number_format($totalTransactions) }}</div>
                    <div class="sub">Total donasi masuk</div>
                </div>
            </div>
        </div>
    </div>
</div>
<span class="banner-foot"></span>

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
                             style="width:36px;height:36px;font-size:0.9rem;">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="small fw-bold text-dark text-truncate">
                                {{ $rd->is_anonymous ? 'Alumni Anonim' : $rd->user->name }}
                            </div>
                            <div class="small text-muted">
                                Donasi <b class="text-success">Rp {{ number_format($rd->amount, 0, ',', '.') }}</b>
                                @if($rd->campaign)· <span>{{ Str::limit($rd->campaign->title, 22) }}</span>@endif
                            </div>
                        </div>
                        <span class="small text-muted flex-shrink-0" style="font-size:0.65rem;">
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
                <h2 class="section-heading mb-0">💰 Dana Yayasan</h2>
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
                <h2 class="section-heading mb-0">🎉 Dana Reuni</h2>
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
        <div class="promo-banner">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="fw-black text-white mb-3">#TransparansiTanpaBatas</h2>
                    <p class="mb-4" style="color:rgba(255,255,255,0.6);">
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
                    <i class="bi bi-file-earmark-bar-graph text-white" style="font-size:6rem;opacity:0.1;"></i>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
