@extends('layouts.app')

@push('styles')
<style>
/* ── Hero ─────────────────────────────────────────────── */
.report-hero {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 2.5rem 0 0;
}
.report-hero .type-badge {
    font-size: 0.62rem; font-weight: 700; letter-spacing: 0.12em;
    text-transform: uppercase; border-radius: 50px;
    padding: 0.3rem 0.9rem; display: inline-block;
}
.report-hero h1 { font-size: clamp(1.4rem,3vw,2rem); font-weight: 900; color: #0f172a; }
.report-hero p.desc { color: #475569; max-width: 700px; font-size: 0.88rem; line-height: 1.7; }

/* Hero KPI strip (dark bar) */
.hero-kpi-bar {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
    padding: 1.25rem 0;
    margin-top: 2rem;
}
.hkpi { border-right: 1px solid rgba(255,255,255,0.12); padding: 0.4rem 1.5rem; }
.hkpi:last-child { border-right: none; }
.hkpi .lbl { font-size: 0.58rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(255,255,255,0.45); }
.hkpi .val { font-size: 1.1rem; font-weight: 900; color: #fff; line-height: 1.2; }
.hkpi .sub { font-size: 0.62rem; color: rgba(255,255,255,0.4); }

/* ── KPI Cards ────────────────────────────────────────── */
.kpi-row { background: #f8fafc; padding: 2rem 0; }
.kcard {
    background: #fff; border-radius: 1rem;
    border: 1.5px solid #e2e8f0;
    padding: 1.2rem 1.4rem;
    box-shadow: 0 1px 6px rgba(0,0,0,0.05);
    transition: box-shadow .2s, transform .2s; height: 100%;
}
.kcard:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.1); transform: translateY(-2px); }
.kcard .lbl { font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; margin-bottom: 0.3rem; }
.kcard .val { font-size: 1.2rem; font-weight: 900; color: #0f172a; line-height: 1.15; }
.kcard .sub { font-size: 0.62rem; color: #94a3b8; margin-top: 0.15rem; }

/* ── Charts ───────────────────────────────────────────── */
.donut-wrap { position: relative; width: 180px; height: 180px; flex-shrink: 0; }
.donut-center {
    position: absolute; inset: 0;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    pointer-events: none;
}
.donut-center .dval { font-size: 0.9rem; font-weight: 900; color: #0f172a; }
.donut-center .dsub { font-size: 0.6rem; color: #64748b; font-weight: 600; text-transform: uppercase; }

/* Legend */
.leg-item { display: flex; align-items: center; gap: 0.55rem; padding: 0.4rem 0; border-bottom: 1px solid #f1f5f9; }
.leg-item:last-child { border-bottom: none; }
.leg-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
.leg-bar { height: 5px; border-radius: 50px; flex: 1; background: #f1f5f9; overflow: hidden; }
.leg-bar-fill { height: 100%; border-radius: 50px; }

/* ── Info Cards ───────────────────────────────────────── */
.info-card {
    background: #fff; border-radius: 1rem;
    border: 1.5px solid #e2e8f0; padding: 1.5rem;
    box-shadow: 0 1px 6px rgba(0,0,0,0.04);
}
.info-card h6 { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; margin-bottom: 1rem; }

/* ── Download Buttons ─────────────────────────────────── */
.dl-btn {
    display: flex; align-items: center; gap: 1rem;
    padding: 1rem 1.25rem; border-radius: 0.85rem;
    text-decoration: none; transition: transform .15s, box-shadow .15s;
    border: 1.5px solid #e2e8f0; background: #fff;
}
.dl-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
.dl-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }

/* ── Donor table ─────────────────────────────────────── */
.donor-tr { transition: background .12s; }
.donor-tr:hover { background: #f8fafc; }
.avatar-sm {
    width: 34px; height: 34px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    color: #fff; font-weight: 700; font-size: 0.78rem;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}

/* ── Status chip ─────────────────────────────────────── */
.status-chip {
    display: inline-flex; align-items: center; gap: 0.4rem;
    border-radius: 50px; padding: 0.35rem 1rem;
    font-size: 0.72rem; font-weight: 700;
}
.status-chip.verified { background: #d1fae5; color: #065f46; }
.status-chip.draft    { background: #fef3c7; color: #92400e; }
.status-chip.pending  { background: #dbeafe; color: #1e40af; }

/* Dark mode */
[data-bs-theme="dark"] .report-hero { background: #0f172a; border-color: rgba(255,255,255,0.08); }
[data-bs-theme="dark"] .report-hero h1 { color: #f1f5f9; }
[data-bs-theme="dark"] .kpi-row { background: #0f172a; }
[data-bs-theme="dark"] .kcard { background: #1e293b; border-color: rgba(255,255,255,0.08); }
[data-bs-theme="dark"] .kcard .val { color: #f1f5f9; }
[data-bs-theme="dark"] .info-card { background: #1e293b; border-color: rgba(255,255,255,0.08); }
[data-bs-theme="dark"] .dl-btn { background: #1e293b; border-color: rgba(255,255,255,0.08); }
[data-bs-theme="dark"] .leg-item { border-color: rgba(255,255,255,0.06); }
[data-bs-theme="dark"] .leg-bar { background: #334155; }
[data-bs-theme="dark"] .donut-center .dval { color: #f1f5f9; }
[data-bs-theme="dark"] .donor-tr:hover { background: rgba(255,255,255,0.03); }
</style>
@endpush

@section('content')

@php
    $dist  = $campaign->expense_distribution ?? [];
    $saldo = ($campaign->current_amount ?? 0) - ($campaign->total_expense ?? 0);
    $isReuni2026 = str_contains($campaign->slug ?? '', 'reuni-akbar-2026')
                || str_contains($campaign->slug ?? '', 'informasi-keuangan');

    // Sumber pemasukan Reuni Akbar 2026 (dari LPJ hal.11)
    $incomeSources = [
        ['label' => 'Donatur Potensial (11 orang)',   'percent' => 49, 'amount' => 112500000, 'color' => '#f59e0b'],
        ['label' => 'Iuran Alumni (25 Angkatan)',     'percent' => 37, 'amount' =>  84500000, 'color' => '#3b82f6'],
        ['label' => 'Keuntungan Penjualan Kaos',      'percent' => 12, 'amount' =>  28745250, 'color' => '#10b981'],
        ['label' => 'Bazar Makanan',                  'percent' =>  2, 'amount' =>   4600000, 'color' => '#8b5cf6'],
    ];

    // Info tambahan Reuni Akbar 2026
    $acara = [
        ['icon' => '🎯', 'label' => 'Tema', 'value' => '"Menjalin Silaturahmi, Merajut Kisah, dan Membangun Sinergi"'],
        ['icon' => '📅', 'label' => 'Tanggal', 'value' => '20–26 Juni 2026'],
        ['icon' => '📍', 'label' => 'Lokasi', 'value' => 'SMK Negeri 2 Ternate, Lapangan Ngaralamo, Landmark Kota Ternate'],
        ['icon' => '👥', 'label' => 'Peserta', 'value' => '840 orang (760 alumni + 80 tenaga pendidik & guru purnatugas)'],
        ['icon' => '🤝', 'label' => 'Donatur Potensial', 'value' => '11 donatur'],
        ['icon' => '🏫', 'label' => 'Angkatan Berpartisipasi', 'value' => '25 dari 37 angkatan (67,5%)'],
        ['icon' => '👕', 'label' => 'Penjualan Kaos', 'value' => '770 pcs dipesan, 680 pcs terjual ke alumni'],
        ['icon' => '🎁', 'label' => 'Kontribusi Almamater', 'value' => 'Tiang listrik, 6 lampu PJU solarsel, 2 podium, Mic Wireless, Megaphone'],
    ];
@endphp

{{-- ══ HERO ══════════════════════════════════════════════ --}}
<div class="report-hero">
    <div class="container">

        {{-- Nav --}}
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <a href="{{ route('donations.index') }}" class="btn btn-sm btn-light rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            @auth @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.campaigns.report.edit', $campaign->id) }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold">
                <i class="bi bi-pencil-square me-1"></i> Edit Laporan
            </a>
            @endif @endauth

            {{-- Status chip --}}
            @if($campaign->report_status === 'verified')
                <span class="status-chip verified ms-auto">
                    <i class="bi bi-patch-check-fill"></i> Terverifikasi
                    @if($campaign->report_verified_at)· {{ $campaign->report_verified_at->translatedFormat('d M Y') }}@endif
                </span>
            @elseif($campaign->report_status === 'draft')
                <span class="status-chip draft ms-auto"><i class="bi bi-pencil-fill"></i> Draft</span>
            @endif
        </div>

        {{-- Title --}}
        <span class="type-badge mb-2
            {{ $campaign->type === 'foundation' ? 'bg-primary bg-opacity-10 text-primary' : 'bg-warning bg-opacity-10 text-warning' }}">
            {{ $campaign->type === 'foundation' ? '💰 Dana Yayasan' : '🎉 Dana Reuni' }}
        </span>
        <h1 class="mb-2">{{ $campaign->title }}</h1>
        <p class="desc mb-0">{{ $campaign->description }}</p>

        {{-- KPI Bar --}}
        <div class="hero-kpi-bar mt-3">
            <div class="container">
                <div class="row g-0 text-center">
                    <div class="col-6 col-md-3 hkpi">
                        <div class="lbl">💰 Total Pemasukan</div>
                        <div class="val">Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</div>
                        <div class="sub">Dana terhimpun</div>
                    </div>
                    <div class="col-6 col-md-3 hkpi">
                        <div class="lbl">💸 Total Pengeluaran</div>
                        <div class="val">Rp {{ number_format($campaign->total_expense ?? 0, 0, ',', '.') }}</div>
                        <div class="sub">Realisasi</div>
                    </div>
                    <div class="col-6 col-md-3 hkpi">
                        <div class="lbl">💼 Saldo Akhir</div>
                        <div class="val" style="color:#34d399;">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
                        <div class="sub">Kas + Bank</div>
                    </div>
                    <div class="col-6 col-md-3 hkpi">
                        <div class="lbl">🤝 Donatur / Sponsor</div>
                        <div class="val">{{ $donorCount }} + {{ $campaign->sponsor_count ?? 0 }}</div>
                        <div class="sub">Donatur + Mitra</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ BODY ════════════════════════════════════════════════ --}}
<div class="kpi-row">
    <div class="container">

        {{-- ── Mini KPI Cards ──────────────────────────────── --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="kcard">
                    <div class="lbl">Progress Dana</div>
                    <div class="val text-primary">{{ number_format($campaign->progress, 1) }}%</div>
                    <div style="height:4px;background:#e2e8f0;border-radius:50px;margin-top:.5rem;">
                        <div style="height:4px;width:{{ min($campaign->progress,100) }}%;background:#3b82f6;border-radius:50px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="kcard">
                    <div class="lbl">Efisiensi Anggaran</div>
                    @php $eff = $campaign->current_amount > 0 ? (($campaign->total_expense ?? 0) / $campaign->current_amount) * 100 : 0; @endphp
                    <div class="val" style="color:#059669;">{{ number_format($eff, 1) }}%</div>
                    <div class="sub">Penggunaan dana</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="kcard">
                    <div class="lbl">Sponsor / Mitra</div>
                    <div class="val" style="color:#ea580c;">{{ $campaign->sponsor_count ?? 0 }}</div>
                    <div class="sub">Donatur potensial</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="kcard">
                    <div class="lbl">Transaksi Donasi</div>
                    <div class="val" style="color:#0891b2;">{{ $transactionCount }}</div>
                    <div class="sub">Total tercatat</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="kcard">
                    <div class="lbl">Target Dana</div>
                    <div class="val" style="color:#7c3aed;">Rp {{ number_format($campaign->goal_amount / 1e6, 0) }}jt</div>
                    <div class="sub">Rp {{ number_format($campaign->goal_amount, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="kcard">
                    <div class="lbl">Status Laporan</div>
                    <div class="val" style="font-size:0.95rem;color:{{ $campaign->report_status === 'verified' ? '#059669' : '#d97706' }};">
                        {{ $campaign->report_status === 'verified' ? '✅ Verified' : ($campaign->report_status === 'draft' ? '📝 Draft' : ($campaign->report_status ?? '—')) }}
                    </div>
                    @if($campaign->report_verified_at)
                    <div class="sub">{{ $campaign->report_verified_at->translatedFormat('d M Y') }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Distribusi Pengeluaran + Sumber Pemasukan ─── --}}
        <div class="row g-4 mb-4">

            {{-- Pengeluaran --}}
            @if(count($dist) > 0)
            <div class="col-lg-7">
                <div class="info-card h-100">
                    <h6>📊 Distribusi Pengeluaran</h6>
                    <div class="d-flex flex-wrap gap-4 align-items-center">
                        <div class="donut-wrap">
                            <canvas id="donutExpense" width="180" height="180"></canvas>
                            <div class="donut-center">
                                <div class="dval">Rp {{ number_format(($campaign->total_expense ?? 0) / 1e6, 0) }}jt</div>
                                <div class="dsub">Pengeluaran</div>
                            </div>
                        </div>
                        <div class="flex-grow-1" style="min-width:200px;">
                            @foreach($dist as $item)
                            <div class="leg-item">
                                <div class="leg-dot" style="background:{{ $item['color'] ?? '#6366f1' }};"></div>
                                <div class="flex-grow-1" style="min-width:0;">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small text-truncate" style="max-width:160px;">{{ $item['label'] }}</span>
                                        <span class="small fw-bold ms-2" style="white-space:nowrap;color:{{ $item['color'] ?? '#6366f1' }}">{{ $item['percent'] ?? $item['percentage'] ?? 0 }}%</span>
                                    </div>
                                    <div class="leg-bar">
                                        <div class="leg-bar-fill" style="width:{{ $item['percent'] ?? $item['percentage'] ?? 0 }}%;background:{{ $item['color'] ?? '#6366f1' }};"></div>
                                    </div>
                                    <div class="small text-muted mt-1">Rp {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Sumber Pemasukan (khusus Reuni Akbar 2026) --}}
            @if($isReuni2026)
            <div class="col-lg-5">
                <div class="info-card h-100">
                    <h6>💰 Sumber Pemasukan</h6>
                    <div class="d-flex flex-wrap gap-4 align-items-center">
                        <div class="donut-wrap" style="width:140px;height:140px;">
                            <canvas id="donutIncome" width="140" height="140"></canvas>
                            <div class="donut-center">
                                <div class="dval" style="font-size:.8rem;">Rp {{ number_format($campaign->current_amount / 1e6, 0) }}jt</div>
                                <div class="dsub">Pemasukan</div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            @foreach($incomeSources as $src)
                            <div class="leg-item">
                                <div class="leg-dot" style="background:{{ $src['color'] }};"></div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small" style="font-size:.72rem;">{{ $src['label'] }}</span>
                                        <span class="small fw-bold ms-1" style="color:{{ $src['color'] }}">{{ $src['percent'] }}%</span>
                                    </div>
                                    <div class="small text-muted">Rp {{ number_format($src['amount'], 0, ',', '.') }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- ── Info Acara + Dokumen ─────────────────────────── --}}
        <div class="row g-4 mb-4">

            {{-- Info Acara (Reuni 2026) --}}
            @if($isReuni2026)
            <div class="col-lg-8">
                <div class="info-card">
                    <h6>📋 Informasi Kegiatan — Reuni Akbar 2026</h6>
                    <div class="row g-2">
                        @foreach($acara as $a)
                        <div class="col-sm-6">
                            <div class="d-flex gap-2 p-2 rounded-3" style="background:#f8fafc;">
                                <span style="font-size:1.1rem;line-height:1.4;">{{ $a['icon'] }}</span>
                                <div>
                                    <div style="font-size:.6rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;">{{ $a['label'] }}</div>
                                    <div class="small fw-semibold text-dark" style="font-size:.78rem;">{{ $a['value'] }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Panitia Inti --}}
                    <div class="mt-3 pt-3 border-top">
                        <div style="font-size:.6rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;margin-bottom:.6rem;">Panitia Inti</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach([
                                ['name'=>'Zuldan A. Kader','role'=>'Ketua Pelaksana'],
                                ['name'=>'Faizal Alkatiri','role'=>'Wakil Ketua / Pemateri'],
                                ['name'=>'Walid Syukur','role'=>'Sekretaris / Moderator'],
                                ['name'=>'Sitna Hamid','role'=>'Bendahara / Pemateri'],
                                ['name'=>'Friyanti','role'=>'Wakil Bendahara'],
                            ] as $p)
                            <div class="d-flex align-items-center gap-2 px-2 py-1 rounded-3" style="background:#e0e7ff;">
                                <div class="avatar-sm" style="width:26px;height:26px;font-size:.65rem;background:linear-gradient(135deg,#4f46e5,#7c3aed);">{{ strtoupper(substr($p['name'],0,1)) }}</div>
                                <div>
                                    <div style="font-size:.72rem;font-weight:700;color:#1e1b4b;">{{ $p['name'] }}</div>
                                    <div style="font-size:.6rem;color:#4338ca;">{{ $p['role'] }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Dokumen & Status --}}
            <div class="{{ $isReuni2026 ? 'col-lg-4' : 'col-lg-6' }}">
                <div class="info-card h-100">
                    <h6>📋 Dokumen LPJ</h6>
                    <div class="d-flex flex-column gap-3">

                        @if($campaign->lpj_pdf_path)
                        <button type="button" class="dl-btn text-dark border-0 bg-white text-start"
                                onclick="openPdfViewer('{{ route('donations.view.lpj', $campaign->slug) }}', 'LPJ Lengkap')">
                            <div class="dl-icon" style="background:#fee2e2;color:#dc2626;">📄</div>
                            <div>
                                <div class="small fw-bold">Lihat LPJ Lengkap</div>
                                <div style="font-size:.65rem;color:#94a3b8;">Laporan Pertanggungjawaban (PDF)</div>
                            </div>
                            <i class="bi bi-eye ms-auto text-muted"></i>
                        </button>
                        @else
                        <div class="dl-btn text-muted" style="cursor:default;opacity:.55;">
                            <div class="dl-icon" style="background:#f1f5f9;color:#94a3b8;">📄</div>
                            <div>
                                <div class="small fw-bold">LPJ Lengkap</div>
                                <div style="font-size:.65rem;">Belum tersedia</div>
                            </div>
                        </div>
                        @endif

                        @if($campaign->finance_detail_pdf_path)
                        <button type="button" class="dl-btn text-dark border-0 bg-white text-start"
                                onclick="openPdfViewer('{{ route('donations.view.finance', $campaign->slug) }}', 'Rincian Keuangan')">
                            <div class="dl-icon" style="background:#dbeafe;color:#2563eb;">📑</div>
                            <div>
                                <div class="small fw-bold">Lihat Rincian Keuangan</div>
                                <div style="font-size:.65rem;color:#94a3b8;">Detail pemasukan & pengeluaran (PDF)</div>
                            </div>
                            <i class="bi bi-eye ms-auto text-muted"></i>
                        </button>
                        @endif

                        {{-- Ringkasan rekapitulasi (Reuni 2026) --}}
                        @if($isReuni2026)
                        <div style="background:#f8fafc;border-radius:.75rem;padding:1rem;">
                            <div style="font-size:.6rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;margin-bottom:.6rem;">Rekapitulasi Akhir</div>
                            <div class="d-flex justify-content-between small border-bottom pb-1 mb-1">
                                <span class="text-muted">Total Pemasukan</span>
                                <span class="fw-bold text-success">Rp 230.345.250</span>
                            </div>
                            <div class="d-flex justify-content-between small border-bottom pb-1 mb-1">
                                <span class="text-muted">Total Pengeluaran</span>
                                <span class="fw-bold text-danger">Rp 230.341.930</span>
                            </div>
                            <div class="d-flex justify-content-between small border-bottom pb-1 mb-1">
                                <span class="text-muted">Sisa Kas</span>
                                <span class="fw-bold text-primary">Rp 3.320</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Sisa Bank</span>
                                <span class="fw-bold text-primary">Rp 239.465</span>
                            </div>
                        </div>
                        @endif

                        @if($campaign->report_status)
                        <div class="d-flex align-items-center gap-2 p-3 rounded-3
                            {{ $campaign->report_status === 'verified' ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10' }}">
                            <i class="bi {{ $campaign->report_status === 'verified' ? 'bi-patch-check-fill text-success' : 'bi-clock-fill text-warning' }} fs-4"></i>
                            <div>
                                <div class="small fw-bold {{ $campaign->report_status === 'verified' ? 'text-success' : 'text-warning' }}">
                                    {{ $campaign->report_status === 'verified' ? 'Laporan Terverifikasi' : 'Status: '.ucfirst($campaign->report_status) }}
                                </div>
                                @if($campaign->report_verified_at)
                                <div style="font-size:.65rem;color:#64748b;">{{ $campaign->report_verified_at->translatedFormat('d F Y') }}</div>
                                @endif
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- ── Daftar Donatur ───────────────────────────────── --}}
        @if($campaign->show_donor_list && $donations->count() > 0)
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-black mb-0">📥 Daftar Donatur Terverifikasi</h5>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 small fw-bold">
                    {{ $donations->count() }} Donatur
                </span>
            </div>
            <div class="info-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="ps-4 py-3" style="font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#94a3b8;">#</th>
                                <th class="py-3" style="font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#94a3b8;">Donatur</th>
                                <th class="py-3 text-end pe-4" style="font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#94a3b8;">Jumlah</th>
                                <th class="py-3" style="font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#94a3b8;">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donations as $i => $d)
                            <tr class="donor-tr">
                                <td class="ps-4 text-muted small">{{ $i + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm">
                                            {{ $d->is_anonymous ? '?' : strtoupper(substr($d->user->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="small fw-bold">{{ $d->is_anonymous ? 'Alumni Anonim' : $d->user->name }}</div>
                                            @if(!$d->is_anonymous && ($d->user->graduation_year ?? null))
                                            <div class="small text-muted">Angkatan {{ $d->user->graduation_year }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="fw-bold text-success small">Rp {{ number_format($d->amount, 0, ',', '.') }}</span>
                                </td>
                                <td class="small text-muted">{{ $d->created_at->translatedFormat('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background:#f8fafc;">
                            <tr>
                                <td colspan="2" class="ps-4 fw-bold py-3 small">Total</td>
                                <td class="text-end pe-4 fw-black text-success small">Rp {{ number_format($donations->sum('amount'), 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- ── Dokumentasi Foto ─────────────────────────────── --}}
        @if($campaign->documentation_images && count($campaign->documentation_images) > 0)
        <div class="mb-4">
            <h5 class="fw-black mb-3">📸 Dokumentasi Kegiatan</h5>
            <div class="row g-3">
                @foreach($campaign->documentation_images as $img)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ asset('storage/' . $img) }}" target="_blank"
                       class="d-block rounded-4 overflow-hidden shadow-sm" style="aspect-ratio:4/3;">
                        <img src="{{ asset('storage/' . $img) }}" class="w-100 h-100" style="object-fit:cover;transition:transform .3s;"
                             onmouseover="this.style.transform='scale(1.06)'" onmouseout="this.style.transform='scale(1)'">
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── CTA Donasi ───────────────────────────────────── --}}
        @if($campaign->status === 'active')
        <div class="p-5 rounded-4 text-center" style="background:linear-gradient(135deg,#0f172a,#1e3a8a);">
            <h3 class="fw-black text-white mb-2">Ikut Berkontribusi</h3>
            <p class="mb-4" style="color:rgba(255,255,255,.65);">Setiap donasi Anda dicatat secara transparan dan dapat diaudit oleh seluruh alumni.</p>
            @auth
            <a href="{{ route('donations.donate', $campaign->id) }}" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold">
                <i class="bi bi-heart-fill me-2"></i> Donasi Sekarang
            </a>
            @else
            <a href="{{ route('login') }}" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold">
                <i class="bi bi-box-arrow-in-right me-2"></i> Login untuk Donasi
            </a>
            @endauth
        </div>
        @endif

    </div>
</div>

{{-- ══ PDF VIEWER MODAL ════════════════════════════════════ --}}
<style>
/* Mobile-first PDF modal */
#pdfViewerModal .modal-dialog {
    margin: 0;
    max-width: 100%;
    height: 100%;
}
@media (min-width: 768px) {
    #pdfViewerModal .modal-dialog {
        margin: 1.5rem auto;
        max-width: 900px;
        height: auto;
    }
}
#pdfViewerModal .modal-content {
    background: #1e293b;
    border: none;
    border-radius: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
}
@media (min-width: 768px) {
    #pdfViewerModal .modal-content {
        border-radius: 1rem;
        height: auto;
    }
}
#pdfCanvasContainer {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: .75rem;
    background: #334155;
    -webkit-overflow-scrolling: touch;
    user-select: none;
    -webkit-user-select: none;
}
#pdfCanvasContainer canvas {
    display: block;
    margin: 0 auto;
    border-radius: .5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,.4);
    max-width: 100%;
    touch-action: pan-y;
}
.pdf-nav-btn {
    width: 44px; height: 44px;
    border-radius: 50%;
    border: 1.5px solid rgba(255,255,255,.3);
    background: rgba(255,255,255,.08);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background .15s;
    -webkit-tap-highlight-color: transparent;
}
.pdf-nav-btn:active { background: rgba(255,255,255,.2); }
.pdf-nav-btn:disabled { opacity: .3; cursor: default; }
#pdfProgressBar {
    height: 3px;
    background: #3b82f6;
    width: 0%;
    transition: width .2s;
}
</style>

<div class="modal fade" id="pdfViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Header --}}
            <div class="flex-shrink-0 px-4 py-3 d-flex align-items-center gap-3" style="background:#0f172a;">
                <i class="bi bi-file-earmark-text text-danger flex-shrink-0"></i>
                <span class="text-white fw-bold small flex-grow-1 text-truncate" id="pdfViewerTitle">Dokumen</span>
                <button type="button" class="btn-close btn-close-white flex-shrink-0" data-bs-dismiss="modal" style="font-size:.75rem;"></button>
            </div>
            {{-- Progress bar --}}
            <div style="background:#0f172a;padding:0 0 2px;"><div id="pdfProgressBar"></div></div>

            {{-- Loading overlay --}}
            <div id="pdfLoading" class="d-flex flex-column align-items-center justify-content-center gap-2 py-5"
                 style="background:#334155;min-height:50vh;">
                <div class="spinner-border text-primary" style="width:2rem;height:2rem;" role="status"></div>
                <div class="text-white-50 small" id="pdfLoadingText">Menyiapkan halaman 1…</div>
            </div>

            {{-- Canvas --}}
            <div id="pdfCanvasContainer" style="display:none;min-height:50vh;"></div>

            {{-- Footer nav --}}
            <div class="flex-shrink-0 px-4 py-2 d-flex align-items-center gap-2" style="background:#0f172a;">
                <i class="bi bi-shield-lock-fill text-success me-1" style="font-size:.7rem;"></i>
                <span class="text-white-50 flex-grow-1" style="font-size:.65rem;">Hanya untuk dilihat</span>
                <button class="pdf-nav-btn" id="pdfPrevPage" disabled>
                    <i class="bi bi-chevron-left"></i>
                </button>
                <span class="text-white fw-bold" id="pdfPageInfo" style="font-size:.8rem;min-width:56px;text-align:center;">—</span>
                <button class="pdf-nav-btn" id="pdfNextPage" disabled>
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function drawDonut(canvasId, data, cx, cy, r, inner) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const total = data.reduce((s, d) => s + parseFloat(d.percent ?? d.percentage ?? 0), 0);
    let angle = -Math.PI / 2;
    const gap = 0.015;

    data.forEach(item => {
        const pct = parseFloat(item.percent ?? item.percentage ?? 0);
        const slice = (pct / total) * 2 * Math.PI - gap;
        ctx.beginPath();
        ctx.moveTo(cx, cy);
        ctx.arc(cx, cy, r, angle + gap/2, angle + gap/2 + slice);
        ctx.closePath();
        ctx.fillStyle = item.color || '#6366f1';
        ctx.fill();
        angle += slice + gap;
    });

    // Donut hole
    ctx.beginPath();
    ctx.arc(cx, cy, inner, 0, 2 * Math.PI);
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    ctx.fillStyle = isDark ? '#1e293b' : '#ffffff';
    ctx.fill();
}

@if(count($dist) > 0)
drawDonut('donutExpense', @json($dist), 90, 90, 78, 50);
@endif

@if($isReuni2026)
drawDonut('donutIncome', @json($incomeSources), 70, 70, 62, 40);
@endif
</script>

{{-- PDF.js: load async, tidak block render halaman --}}
<script>
(function(){
    const s = document.createElement('script');
    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
    s.crossOrigin = 'anonymous';
    document.head.appendChild(s);
})();
</script>
<script>
/* ── PDF Viewer ───────────────────────────────────────────── */
let _pdf       = null;   // pdfDocument
let _page      = 1;
let _total     = 0;
let _rendering = false;
const _cache   = new Map(); // pageNum → ImageBitmap (fast repaint)

// Elemen UI (cached setelah DOM ready)
let elModal, elTitle, elLoading, elLoadText, elProgress,
    elContainer, elInfo, elPrev, elNext;

document.addEventListener('DOMContentLoaded', () => {
    elModal     = document.getElementById('pdfViewerModal');
    elTitle     = document.getElementById('pdfViewerTitle');
    elLoading   = document.getElementById('pdfLoading');
    elLoadText  = document.getElementById('pdfLoadingText');
    elProgress  = document.getElementById('pdfProgressBar');
    elContainer = document.getElementById('pdfCanvasContainer');
    elInfo      = document.getElementById('pdfPageInfo');
    elPrev      = document.getElementById('pdfPrevPage');
    elNext      = document.getElementById('pdfNextPage');

    elPrev.addEventListener('click', () => goPage(_page - 1));
    elNext.addEventListener('click', () => goPage(_page + 1));

    // Swipe kiri/kanan untuk navigasi halaman di mobile
    let _tx = 0;
    elContainer.addEventListener('touchstart', e => { _tx = e.touches[0].clientX; }, { passive: true });
    elContainer.addEventListener('touchend',   e => {
        const dx = e.changedTouches[0].clientX - _tx;
        if (Math.abs(dx) > 50) dx < 0 ? goPage(_page + 1) : goPage(_page - 1);
    }, { passive: true });

    // Block klik kanan & drag
    elContainer.addEventListener('contextmenu', e => e.preventDefault());
    elContainer.addEventListener('dragstart',   e => e.preventDefault());

    // Block shortcut keyboard saat modal terbuka
    document.addEventListener('keydown', e => {
        if (!elModal?.classList.contains('show')) return;
        if ((e.ctrlKey || e.metaKey) && ['s','p','u'].includes(e.key.toLowerCase())) {
            e.preventDefault(); e.stopPropagation();
        }
        if (e.key === 'F12') e.preventDefault();
        if (e.key === 'ArrowRight') goPage(_page + 1);
        if (e.key === 'ArrowLeft')  goPage(_page - 1);
    });

    // Reset saat modal ditutup
    elModal?.addEventListener('hidden.bs.modal', () => {
        _pdf = null; _page = 1; _total = 0; _cache.clear();
        elContainer.innerHTML = '';
        elContainer.style.display = 'none';
        elProgress.style.width = '0%';
    });
});

/* Buka viewer ------------------------------------------------ */
async function openPdfViewer(url, title) {
    if (typeof pdfjsLib === 'undefined') {
        // PDF.js belum selesai load, tunggu sebentar
        await new Promise(r => setTimeout(r, 800));
    }
    if (typeof pdfjsLib === 'undefined') {
        alert('PDF.js gagal dimuat, coba refresh halaman.');
        return;
    }

    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // Reset state
    _pdf = null; _page = 1; _total = 0; _rendering = false; _cache.clear();

    elTitle.textContent   = title;
    elLoading.style.display  = 'flex';
    elContainer.style.display = 'none';
    elContainer.innerHTML = '';
    elInfo.textContent    = '—';
    elPrev.disabled = elNext.disabled = true;
    elProgress.style.width = '0%';

    new bootstrap.Modal(elModal, { backdrop: true }).show();

    try {
        const task = pdfjsLib.getDocument({
            url,
            withCredentials: true,
            rangeChunkSize: 32768,  // 32 KB chunks — render hal.1 segera
            disableStream: false,
            disableAutoFetch: false,
        });

        // Progress bar saat download
        task.onProgress = ({ loaded, total }) => {
            if (total > 0) elProgress.style.width = Math.round(loaded / total * 80) + '%';
        };

        _pdf   = await task.promise;
        _total = _pdf.numPages;

        elProgress.style.width = '90%';
        elLoadText.textContent  = 'Merender halaman 1…';

        await renderPage(1);

        elProgress.style.width = '100%';
        setTimeout(() => { elProgress.style.width = '0%'; }, 400);

        // Pre-render hal.2 di background supaya navigasi instan
        if (_total > 1) prerenderPage(2);

    } catch (err) {
        console.error('PDF load error:', err);
        elLoading.innerHTML =
            '<i class="bi bi-exclamation-triangle text-warning fs-3 mb-2"></i>' +
            '<div class="text-white-50 small">Gagal memuat dokumen.</div>';
    }
}

/* Render satu halaman ---------------------------------------- */
async function renderPage(num) {
    if (!_pdf || _rendering || num < 1 || num > _total) return;
    _rendering = true;
    elPrev.disabled = elNext.disabled = true;
    elInfo.textContent = num + ' / ' + _total;

    try {
        // Tampilkan dari cache jika ada
        if (_cache.has(num)) {
            showBitmap(num, _cache.get(num));
            elLoading.style.display  = 'none';
            elContainer.style.display = 'block';
            elContainer.scrollTop    = 0;
            _page = num;
            updateNav();
            _rendering = false;
            // Pre-render tetangga
            if (num + 1 <= _total && !_cache.has(num + 1)) prerenderPage(num + 1);
            if (num - 1 >= 1    && !_cache.has(num - 1)) prerenderPage(num - 1);
            return;
        }

        const pdfPage = await _pdf.getPage(num);
        const dpr     = Math.min(window.devicePixelRatio || 1, 2); // max 2x retina
        const containerW = elContainer.clientWidth || window.innerWidth;
        const baseVp     = pdfPage.getViewport({ scale: 1 });
        const scale      = Math.min(containerW / baseVp.width, 1.8) * dpr;
        const viewport   = pdfPage.getViewport({ scale });

        const canvas    = document.createElement('canvas');
        canvas.width    = viewport.width;
        canvas.height   = viewport.height;
        canvas.style.width  = (viewport.width  / dpr) + 'px';
        canvas.style.height = (viewport.height / dpr) + 'px';
        canvas.addEventListener('contextmenu', e => e.preventDefault());

        await pdfPage.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;

        // Simpan ke cache sebagai ImageBitmap (efisien memori)
        if (typeof createImageBitmap !== 'undefined') {
            createImageBitmap(canvas).then(bmp => _cache.set(num, bmp)).catch(() => {});
        }

        elContainer.innerHTML = '';
        elContainer.appendChild(canvas);
        elContainer.scrollTop    = 0;
        elLoading.style.display  = 'none';
        elContainer.style.display = 'block';
        _page = num;
        updateNav();

        // Pre-render halaman berikutnya di background
        if (num + 1 <= _total && !_cache.has(num + 1)) prerenderPage(num + 1);

    } catch (err) {
        console.error('Render error:', err);
    } finally {
        _rendering = false;
    }
}

/* Pre-render halaman ke cache (background, tidak tampil) ----- */
async function prerenderPage(num) {
    if (!_pdf || _cache.has(num) || num < 1 || num > _total) return;
    try {
        const pdfPage   = await _pdf.getPage(num);
        const dpr       = Math.min(window.devicePixelRatio || 1, 2);
        const containerW = elContainer.clientWidth || window.innerWidth;
        const baseVp    = pdfPage.getViewport({ scale: 1 });
        const scale     = Math.min(containerW / baseVp.width, 1.8) * dpr;
        const viewport  = pdfPage.getViewport({ scale });
        const offscreen = document.createElement('canvas');
        offscreen.width  = viewport.width;
        offscreen.height = viewport.height;
        await pdfPage.render({ canvasContext: offscreen.getContext('2d'), viewport }).promise;
        if (typeof createImageBitmap !== 'undefined') {
            const bmp = await createImageBitmap(offscreen);
            _cache.set(num, bmp);
        }
    } catch (_) {}
}

/* Tampilkan ImageBitmap yang sudah di-cache ke canvas -------- */
function showBitmap(num, bmp) {
    const dpr       = Math.min(window.devicePixelRatio || 1, 2);
    const canvas    = document.createElement('canvas');
    canvas.width    = bmp.width;
    canvas.height   = bmp.height;
    canvas.style.width  = (bmp.width  / dpr) + 'px';
    canvas.style.height = (bmp.height / dpr) + 'px';
    canvas.addEventListener('contextmenu', e => e.preventDefault());
    canvas.getContext('2d').drawImage(bmp, 0, 0);
    elContainer.innerHTML = '';
    elContainer.appendChild(canvas);
}

/* Navigasi --------------------------------------------------- */
function goPage(num) {
    if (num < 1 || num > _total || _rendering) return;
    renderPage(num);
}

function updateNav() {
    elPrev.disabled = (_page <= 1);
    elNext.disabled = (_page >= _total);
}
</script>
@endpush
