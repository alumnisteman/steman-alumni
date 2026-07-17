@extends('layouts.app')

@push('styles')
<style>
/* ── Dashboard Laporan Keuangan ────────────────────────── */
.report-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f172a 100%);
    color: #fff;
    padding: 3.5rem 0 2rem;
    position: relative;
    overflow: hidden;
}
.report-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%236366f1' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.report-hero .badge-type {
    background: rgba(99,102,241,0.2);
    color: #a5b4fc;
    border: 1px solid rgba(99,102,241,0.3);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 0.4rem 1rem;
    border-radius: 50px;
}
.stat-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    padding: 1.5rem;
    border: 1px solid rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
    height: 100%;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.stat-card .icon-box {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}
.stat-card .label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748b;
    margin-bottom: 0.25rem;
}
.stat-card .value {
    font-size: 1.55rem;
    font-weight: 900;
    line-height: 1.1;
    color: #0f172a;
}
.stat-card .value.text-income  { color: #059669; }
.stat-card .value.text-expense { color: #dc2626; }
.stat-card .value.text-balance { color: #2563eb; }

/* Donut Chart */
.donut-wrap { position: relative; width: 200px; height: 200px; flex-shrink: 0; }
.donut-wrap canvas { border-radius: 50%; }
.donut-center {
    position: absolute; inset: 0;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    pointer-events: none;
}
.donut-center .val { font-size: 1rem; font-weight: 900; color: #0f172a; }
.donut-center .sub { font-size: 0.65rem; color: #64748b; font-weight: 600; text-transform: uppercase; }

.dist-legend-item { display: flex; align-items: center; gap: 0.6rem; padding: 0.45rem 0; border-bottom: 1px solid #f1f5f9; }
.dist-legend-item:last-child { border-bottom: none; }
.dist-legend-dot { width: 12px; height: 12px; border-radius: 3px; flex-shrink: 0; }
.dist-bar { height: 6px; border-radius: 50px; flex: 1; background: #f1f5f9; overflow: hidden; }
.dist-bar-fill { height: 100%; border-radius: 50px; }

/* Donor table */
.donor-row { transition: background 0.15s; }
.donor-row:hover { background: #f8fafc; }
.avatar-sm {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    color: #fff; font-weight: 700; font-size: 0.8rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* Status badge */
.status-verified {
    background: linear-gradient(135deg, #059669, #10b981);
    color: #fff; border-radius: 50px; padding: 0.4rem 1.2rem;
    font-size: 0.78rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: 0.4rem;
}

/* Dark mode */
[data-bs-theme="dark"] .stat-card { background: #1e293b; border-color: rgba(255,255,255,0.06); }
[data-bs-theme="dark"] .stat-card .value { color: #f1f5f9; }
[data-bs-theme="dark"] .stat-card .label { color: #94a3b8; }
[data-bs-theme="dark"] .dist-legend-item { border-color: rgba(255,255,255,0.06); }
[data-bs-theme="dark"] .dist-bar { background: #334155; }
[data-bs-theme="dark"] .donut-center .val { color: #f1f5f9; }
[data-bs-theme="dark"] .donor-row:hover { background: rgba(255,255,255,0.03); }
</style>
@endpush

@section('content')

{{-- ── Hero ─────────────────────────────────────────── --}}
<div class="report-hero">
    <div class="container position-relative">
        <div class="d-flex flex-wrap align-items-start gap-3 mb-3">
            <a href="{{ route('donations.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 opacity-75">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            @auth
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.campaigns.report.edit', $campaign->id) }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold">
                    <i class="bi bi-pencil-square me-1"></i> Edit Laporan
                </a>
                @endif
            @endauth
        </div>

        <span class="badge-type mb-3 d-inline-block">
            {{ $campaign->type === 'foundation' ? '💰 Dana Yayasan' : '🎉 Dana Reuni' }}
        </span>
        <h1 class="fw-black fs-2 mb-2">{{ $campaign->title }}</h1>
        <p class="opacity-75 mb-3" style="max-width: 640px;">{{ $campaign->description }}</p>

        @if($campaign->report_status)
        <div class="status-verified">
            <i class="bi bi-patch-check-fill"></i>
            {{ $campaign->report_status }}
            @if($campaign->report_verified_at)
                · {{ $campaign->report_verified_at->translatedFormat('d F Y') }}
            @endif
        </div>
        @endif
    </div>
</div>

{{-- ── KPI Cards ─────────────────────────────────────── --}}
<div class="bg-light py-4">
    <div class="container">
        <div class="row g-3">

            {{-- Penerimaan --}}
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="icon-box bg-success bg-opacity-10 text-success mb-3">💰</div>
                    <div class="label">Total Penerimaan</div>
                    <div class="value text-income" style="font-size:1.1rem;">Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Pengeluaran --}}
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger mb-3">💸</div>
                    <div class="label">Total Pengeluaran</div>
                    <div class="value text-expense" style="font-size:1.1rem;">Rp {{ number_format($campaign->total_expense ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Saldo --}}
            @php $saldo = ($campaign->current_amount ?? 0) - ($campaign->total_expense ?? 0); @endphp
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3">💼</div>
                    <div class="label">Saldo Akhir</div>
                    <div class="value text-balance" style="font-size:1.1rem;">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Donatur --}}
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="icon-box bg-indigo bg-opacity-10 mb-3" style="background:rgba(99,102,241,0.1);color:#6366f1;">👥</div>
                    <div class="label">Donatur</div>
                    <div class="value" style="color:#6366f1;">{{ $donorCount }} Alumni</div>
                </div>
            </div>

            {{-- Sponsor --}}
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="icon-box mb-3" style="background:rgba(234,88,12,0.1);color:#ea580c;">🤝</div>
                    <div class="label">Sponsor</div>
                    <div class="value" style="font-size:1.3rem;color:#ea580c;">{{ $campaign->sponsor_count ?? 0 }} Mitra</div>
                </div>
            </div>

            {{-- Total Transaksi --}}
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card">
                    <div class="icon-box mb-3" style="background:rgba(8,145,178,0.1);color:#0891b2;">🧾</div>
                    <div class="label">Total Transaksi</div>
                    <div class="value" style="font-size:1.3rem;color:#0891b2;">{{ $transactionCount }} Transaksi</div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── Main Content ──────────────────────────────────── --}}
<div class="container py-5">
    <div class="row g-5">

        {{-- Distribusi Pengeluaran --}}
        @if($campaign->expense_distribution && count($campaign->expense_distribution) > 0)
        <div class="col-lg-7">
            <h4 class="fw-black mb-4">📊 Distribusi Pengeluaran</h4>
            <div class="bg-white rounded-4 shadow-sm border p-4 d-flex flex-wrap gap-4 align-items-center">
                {{-- Donut --}}
                <div class="donut-wrap">
                    <canvas id="donutChart" width="200" height="200"></canvas>
                    <div class="donut-center">
                        <div class="val">Rp {{ number_format(($campaign->total_expense ?? 0) / 1e6, 1) }}jt</div>
                        <div class="sub">Total</div>
                    </div>
                </div>
                {{-- Legend --}}
                <div class="flex-grow-1" style="min-width:180px;">
                    @foreach($campaign->expense_distribution as $item)
                    <div class="dist-legend-item">
                        <div class="dist-legend-dot" style="background:{{ $item['color'] ?? '#6366f1' }};"></div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold">{{ $item['label'] }}</span>
                                <span class="small fw-bold" style="color:{{ $item['color'] ?? '#6366f1' }}">{{ $item['percentage'] }}%</span>
                            </div>
                            <div class="dist-bar">
                                <div class="dist-bar-fill" style="width:{{ $item['percentage'] }}%; background:{{ $item['color'] ?? '#6366f1' }};"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Aksi & Status --}}
        <div class="{{ ($campaign->expense_distribution && count($campaign->expense_distribution) > 0) ? 'col-lg-5' : 'col-lg-12' }}">
            <h4 class="fw-black mb-4">📋 Dokumen & Status</h4>
            <div class="d-flex flex-column gap-3">

                @if($campaign->lpj_pdf_path)
                <a href="{{ asset('storage/' . $campaign->lpj_pdf_path) }}" target="_blank"
                   class="btn btn-dark rounded-4 py-3 d-flex align-items-center gap-3 text-start shadow-sm">
                    <div class="icon-box bg-white bg-opacity-10 rounded-3 p-2 text-white fs-5">📄</div>
                    <div>
                        <div class="fw-bold">Unduh LPJ Lengkap</div>
                        <div class="small opacity-75">Laporan Pertanggungjawaban resmi (PDF)</div>
                    </div>
                    <i class="bi bi-download ms-auto"></i>
                </a>
                @endif

                @if($campaign->finance_detail_pdf_path)
                <a href="{{ asset('storage/' . $campaign->finance_detail_pdf_path) }}" target="_blank"
                   class="btn btn-outline-dark rounded-4 py-3 d-flex align-items-center gap-3 text-start shadow-sm">
                    <div class="icon-box rounded-3 p-2 fs-5" style="background:rgba(0,0,0,0.05);">📑</div>
                    <div>
                        <div class="fw-bold">Unduh Rincian Keuangan</div>
                        <div class="small opacity-75">Breakdown lengkap pemasukan & pengeluaran (PDF)</div>
                    </div>
                    <i class="bi bi-download ms-auto"></i>
                </a>
                @endif

                {{-- Progress Bar --}}
                <div class="bg-white rounded-4 shadow-sm border p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold text-muted">Progress Dana</span>
                        <span class="fw-bold text-primary">{{ number_format($campaign->progress, 1) }}%</span>
                    </div>
                    <div class="progress rounded-pill" style="height:10px;">
                        <div class="progress-bar bg-primary" style="width:{{ min($campaign->progress, 100) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="small text-muted">Terkumpul: <b>Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</b></span>
                        <span class="small text-muted">Target: <b>Rp {{ number_format($campaign->goal_amount, 0, ',', '.') }}</b></span>
                    </div>
                </div>

                @if($campaign->report_status)
                <div class="bg-success bg-opacity-10 border border-success border-opacity-25 rounded-4 p-4 d-flex align-items-center gap-3">
                    <i class="bi bi-patch-check-fill text-success fs-3"></i>
                    <div>
                        <div class="fw-bold text-success">Status Laporan</div>
                        <div class="small text-muted mt-1">{{ $campaign->report_status }}</div>
                        @if($campaign->report_verified_at)
                        <div class="small text-muted">📅 {{ $campaign->report_verified_at->translatedFormat('d F Y') }}</div>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>

    {{-- ── Daftar Donatur ──────────────────────────────── --}}
    @if($campaign->show_donor_list && $donations->count() > 0)
    <div class="mt-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-black mb-0">📥 Daftar Donatur</h4>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 small fw-bold">
                {{ $donations->count() }} Donatur
            </span>
        </div>
        <div class="bg-white rounded-4 shadow-sm border overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 fw-bold small text-uppercase text-muted py-3">#</th>
                            <th class="fw-bold small text-uppercase text-muted py-3">Donatur</th>
                            <th class="fw-bold small text-uppercase text-muted py-3 text-end pe-4">Jumlah Donasi</th>
                            <th class="fw-bold small text-uppercase text-muted py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($donations as $i => $d)
                        <tr class="donor-row">
                            <td class="ps-4 text-muted small">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm">
                                        @if($d->is_anonymous)
                                            <i class="bi bi-incognito"></i>
                                        @elseif(!$d->is_anonymous && $d->user->profile_picture ?? null)
                                            <img src="{{ $d->user->profile_picture_url }}" class="rounded-circle w-100 h-100" style="object-fit:cover;">
                                        @else
                                            {{ strtoupper(substr($d->is_anonymous ? 'A' : $d->user->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold small">{{ $d->is_anonymous ? 'Alumni Anonim' : $d->user->name }}</div>
                                        @if(!$d->is_anonymous && $d->user->graduation_year ?? null)
                                        <div class="small text-muted">Angkatan {{ $d->user->graduation_year }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <span class="fw-bold text-success">Rp {{ number_format($d->amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="small text-muted">{{ $d->created_at->translatedFormat('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="ps-4 fw-bold py-3">Total</td>
                            <td class="text-end pe-4 fw-black text-success">Rp {{ number_format($donations->sum('amount'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Dokumentasi Kegiatan ───────────────────────── --}}
    @if($campaign->documentation_images && count($campaign->documentation_images) > 0)
    <div class="mt-5">
        <h4 class="fw-black mb-4">📸 Dokumentasi Kegiatan</h4>
        <div class="row g-3">
            @foreach($campaign->documentation_images as $img)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ asset('storage/' . $img) }}" target="_blank" class="d-block rounded-4 overflow-hidden shadow-sm" style="aspect-ratio:4/3;">
                    <img src="{{ asset('storage/' . $img) }}" class="w-100 h-100" style="object-fit:cover; transition:transform 0.3s;"
                         onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── CTA Donasi ─────────────────────────────────── --}}
    @if($campaign->status === 'active')
    <div class="mt-5 p-5 rounded-5 text-center shadow-sm" style="background: linear-gradient(135deg, #0f172a, #1e3a8a);">
        <h3 class="fw-black text-white mb-2">Ikut Berkontribusi</h3>
        <p class="text-white opacity-75 mb-4">Setiap donasi Anda dicatat secara transparan dan dapat diaudit oleh seluruh alumni.</p>
        @auth
        <a href="{{ route('donations.donate', $campaign->id) }}" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold shadow">
            <i class="bi bi-heart-fill me-2"></i> Donasi Sekarang
        </a>
        @else
        <a href="{{ route('login') }}" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold shadow">
            <i class="bi bi-box-arrow-in-right me-2"></i> Login untuk Donasi
        </a>
        @endauth
    </div>
    @endif

</div>
@endsection

@push('scripts')
@if($campaign->expense_distribution && count($campaign->expense_distribution) > 0)
<script>
(function() {
    const data = @json($campaign->expense_distribution);
    const canvas = document.getElementById('donutChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const cx = 100, cy = 100, r = 80, inner = 52;
    const total = data.reduce((s, d) => s + parseFloat(d.percentage), 0);
    let angle = -Math.PI / 2;

    data.forEach(item => {
        const slice = (parseFloat(item.percentage) / total) * 2 * Math.PI;
        ctx.beginPath();
        ctx.moveTo(cx, cy);
        ctx.arc(cx, cy, r, angle, angle + slice);
        ctx.closePath();
        ctx.fillStyle = item.color || '#6366f1';
        ctx.fill();
        angle += slice;
    });

    // Donut hole
    ctx.beginPath();
    ctx.arc(cx, cy, inner, 0, 2 * Math.PI);
    // Detect dark mode
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    ctx.fillStyle = isDark ? '#1e293b' : '#ffffff';
    ctx.fill();
})();
</script>
@endif
@endpush
