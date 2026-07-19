@extends('layouts.app')

@push('styles')
<style>
/* ── KPI Strip ─────────────────────────────────── */
.kpi-strip {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 2.2rem 0 1.5rem;
}
.fund-title {
    font-size: clamp(1.6rem, 3.5vw, 2.4rem);
    font-weight: 900;
    color: #0f172a;
    letter-spacing: -0.02em;
    text-transform: uppercase;
}
.fund-sub { color: #64748b; font-size: 0.88rem; max-width: 460px; }
.audit-btn {
    display: inline-flex; align-items: center; gap: 0.45rem;
    background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;
    border-radius: 50px; padding: 0.4rem 1.1rem; font-size: 0.75rem;
    font-weight: 700; text-decoration: none; transition: background .15s;
}
.audit-btn:hover { background: #e2e8f0; color: #0f172a; }

/* KPI cards */
.kpi-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-top: 1.8rem; }
@media(max-width:768px){ .kpi-grid { grid-template-columns: repeat(2,1fr); } }

.kpi-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-top: 4px solid #3b82f6;
    border-radius: 0.85rem;
    padding: 1.1rem 1.3rem;
    box-shadow: 0 1px 6px rgba(0,0,0,0.05);
    transition: box-shadow .2s, transform .2s;
}
.kpi-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.1); transform: translateY(-2px); }
.kpi-card.blue  { border-top-color: #3b82f6; }
.kpi-card.amber { border-top-color: #f59e0b; }
.kpi-card.green { border-top-color: #10b981; }
.kpi-card.sky   { border-top-color: #0ea5e9; }

.kpi-lbl {
    font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; color: #94a3b8; margin-bottom: 0.3rem;
}
.kpi-val { font-size: 1.35rem; font-weight: 900; color: #0f172a; line-height: 1.1; }
.kpi-sub { font-size: 0.65rem; color: #94a3b8; margin-top: 0.15rem; }

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

/* ══ LPJ Reuni 2026 Section ══════════════════════ */
.lpj-header { border-left: 5px solid #f59e0b; padding-left: 1rem; }
.lpj-badge {
    background: linear-gradient(135deg,#1e3a8a,#0f172a);
    color:#f59e0b;font-size:.65rem;font-weight:800;letter-spacing:.12em;
    border-radius:50px;padding:.3rem .9rem;text-transform:uppercase;white-space:nowrap;
}
.lpj-title { font-size:clamp(1.1rem,2.5vw,1.5rem);font-weight:900;color:#0f172a; }
.lpj-sub { font-size:.78rem;color:#64748b; }
.lpj-kpi-card {
    border-radius:.85rem;padding:1rem 1.1rem;border:1.5px solid #e2e8f0;
    height:100%;box-shadow:0 1px 5px rgba(0,0,0,.05);
}
.lpj-kpi-card.green{background:#f0fdf4;border-top:4px solid #10b981;}
.lpj-kpi-card.red  {background:#fef2f2;border-top:4px solid #ef4444;}
.lpj-kpi-card.amber{background:#fffbeb;border-top:4px solid #f59e0b;}
.lpj-kpi-card.blue {background:#eff6ff;border-top:4px solid #3b82f6;}
.lkpi-lbl{font-size:.6rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#64748b;margin-bottom:.25rem;}
.lkpi-val{font-size:1.05rem;font-weight:900;color:#0f172a;line-height:1.2;}
.lkpi-sub{font-size:.6rem;color:#94a3b8;margin-top:.1rem;}
.lpj-card {
    background:#fff;border-radius:1rem;border:1.5px solid #e2e8f0;
    padding:1.3rem;box-shadow:0 1px 6px rgba(0,0,0,.04);height:100%;
}
.lpj-card-title {
    font-size:.65rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;
    color:#64748b;margin-bottom:1rem;padding-bottom:.5rem;border-bottom:1.5px solid #f1f5f9;
}
.lpj-dot{display:inline-block;width:10px;height:10px;border-radius:3px;flex-shrink:0;}
.lpj-row{display:flex;justify-content:space-between;align-items:center;padding:.4rem 0 .15rem;}
.lpj-row-left{display:flex;align-items:center;gap:.5rem;flex:1;min-width:0;}
.lpj-row-label{font-size:.78rem;font-weight:600;color:#334155;}
.lpj-count{color:#94a3b8;font-weight:400;}
.lpj-row-right{display:flex;align-items:center;gap:.75rem;flex-shrink:0;}
.lpj-amount{font-size:.78rem;font-weight:700;color:#0f172a;}
.lpj-pct{font-size:.68rem;font-weight:800;background:#f1f5f9;border-radius:50px;padding:.15rem .5rem;color:#475569;min-width:38px;text-align:center;}
.lpj-bar-wrap{background:#f1f5f9;height:5px;border-radius:50px;overflow:hidden;margin-bottom:.25rem;}
.lpj-bar{height:100%;border-radius:50px;}
.lpj-total-row{display:flex;justify-content:space-between;padding-top:.75rem;border-top:2px solid #e2e8f0;font-size:.88rem;}
.lpj-exp-row{margin-bottom:.5rem;}
.lpj-exp-label{font-size:.72rem;font-weight:500;color:#334155;}
.lpj-exp-amount{font-size:.72rem;font-weight:700;color:#0f172a;white-space:nowrap;flex-shrink:0;}
.lpj-stats-bar{background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:1rem;padding:1.5rem;}
.lpj-stat-val{font-size:1.8rem;font-weight:900;color:#f59e0b;line-height:1.1;}
.lpj-stat-lbl{font-size:.72rem;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.06em;margin-top:.2rem;}
.lpj-stat-sub{font-size:.62rem;color:rgba(255,255,255,.45);margin-top:.15rem;}
.lpj-contrib-box{background:#fff9f0;border:1.5px solid #fed7aa;border-radius:.85rem;padding:1.2rem;}
.lpj-contrib-icon{font-size:1.8rem;flex-shrink:0;}
@media(max-width:768px){.lpj-amount{font-size:.7rem;}.lpj-exp-amount{font-size:.68rem;}}

/* ── Tombol Lihat LPJ — proporsional & responsif ── */
.lpj-doc-row {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:.75rem;
    flex-wrap:wrap;
}
.lpj-doc-btn {
    font-size:.82rem;
    padding:.45rem 1.4rem;
    white-space:nowrap;
    flex-shrink:0;
}
@media(max-width:575.98px){
    /* Mobile: tombol full-width, posisi di bawah info file */
    .lpj-doc-row { flex-direction:column; align-items:stretch; }
    .lpj-doc-btn {
        width:100%;
        font-size:.88rem;
        padding:.6rem 1rem;
        text-align:center;
    }
    /* Modal header: kecilkan teks */
    #modalPdfViewer .modal-header h6,
    #modalPdfShow .modal-header h6 { font-size:.72rem; }
    /* Tombol nav: lebih besar di mobile (touch target ≥44px) */
    .pdf-nav-btn { padding:.55rem .9rem !important; font-size:.78rem !important; }
}

/* ══ Donor Cards ══════════════════════════════════ */
.donor-card {
    background:#fff;border-radius:.85rem;border:1.5px solid #e2e8f0;
    padding:1rem 1.1rem;border-left-width:4px!important;
    box-shadow:0 1px 5px rgba(0,0,0,.04);height:100%;
    transition:box-shadow .2s,transform .2s;
}
.donor-card:hover{box-shadow:0 6px 20px rgba(0,0,0,.1);transform:translateY(-2px);}
.donor-avatar{
    width:44px;height:44px;border-radius:12px;display:flex;
    align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;
}
.donor-name{font-size:.85rem;font-weight:800;color:#0f172a;line-height:1.2;}
.donor-role{font-size:.65rem;color:#94a3b8;font-weight:500;margin-top:.1rem;}
.donor-amount{font-size:.88rem;font-weight:900;line-height:1.2;}
.donor-ket{font-size:.6rem;color:#94a3b8;margin-top:.1rem;}

/* ══ In-Kind Items ════════════════════════════════ */
.inkind-item{
    background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:.85rem;
    padding:1rem 1.1rem;height:100%;
}
.inkind-header{display:flex;align-items:flex-start;gap:.75rem;margin-bottom:.6rem;}
.inkind-icon{font-size:1.5rem;flex-shrink:0;margin-top:.1rem;}
.inkind-name{font-size:.82rem;font-weight:800;color:#0f172a;}
.inkind-angk{font-size:.65rem;color:#64748b;font-weight:500;}
.inkind-list{margin:0;padding-left:1rem;}
.inkind-list li{font-size:.72rem;color:#475569;line-height:1.6;}

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
                <a href="{{ route('donations.audit') }}" class="audit-btn">
                    <i class="bi bi-shield-lock"></i> Lihat Audit Publik
                </a>
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

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- LPJ REUNI AKBAR KE-4 2026 — Laporan Keuangan Transparan  --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="lpj-section mb-5">

            {{-- Header LPJ --}}
            <div class="lpj-header mb-4">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="lpj-badge">📋 LPJ RESMI</div>
                    <div>
                        <h2 class="lpj-title mb-0">Laporan Keuangan Reuni Akbar Ke-4 Tahun 2026</h2>
                        <p class="lpj-sub mb-0">20–26 Juni 2026 · Ternate · STM Nasional – STM Negeri – SMK Negeri 2 Ternate</p>
                    </div>
                </div>
            </div>

            {{-- KPI Ringkasan --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="lpj-kpi-card green">
                        <div class="lkpi-lbl">💰 Total Pemasukan</div>
                        <div class="lkpi-val">Rp 230.345.250</div>
                        <div class="lkpi-sub">4 sumber pendapatan</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="lpj-kpi-card red">
                        <div class="lkpi-lbl">💸 Total Pengeluaran</div>
                        <div class="lkpi-val">Rp 230.341.930</div>
                        <div class="lkpi-sub">11 pos pengeluaran</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="lpj-kpi-card amber">
                        <div class="lkpi-lbl">🏦 Saldo Akhir</div>
                        <div class="lkpi-val">Rp 241.835</div>
                        <div class="lkpi-sub">Kas Rp 3.320 + Bank Rp 239.465</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="lpj-kpi-card blue">
                        <div class="lkpi-lbl">👥 Total Peserta</div>
                        <div class="lkpi-val">840 Orang</div>
                        <div class="lkpi-sub">25 dari 37 angkatan hadir</div>
                    </div>
                </div>
            </div>

            {{-- Pemasukan & Pengeluaran --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="lpj-card">
                        <h6 class="lpj-card-title">📈 Sumber Pemasukan</h6>
                        <div class="lpj-row">
                            <div class="lpj-row-left"><span class="lpj-dot" style="background:#f59e0b;"></span><span class="lpj-row-label">Donatur Alumni <span class="lpj-count">(11 donatur)</span></span></div>
                            <div class="lpj-row-right"><span class="lpj-amount">Rp 112.500.000</span><span class="lpj-pct">49%</span></div>
                        </div>
                        <div class="lpj-bar-wrap"><div class="lpj-bar" style="width:49%;background:#f59e0b;"></div></div>
                        <div class="lpj-row">
                            <div class="lpj-row-left"><span class="lpj-dot" style="background:#3b82f6;"></span><span class="lpj-row-label">Iuran Alumni/Angkatan <span class="lpj-count">(25 angkatan)</span></span></div>
                            <div class="lpj-row-right"><span class="lpj-amount">Rp 84.500.000</span><span class="lpj-pct">37%</span></div>
                        </div>
                        <div class="lpj-bar-wrap"><div class="lpj-bar" style="width:37%;background:#3b82f6;"></div></div>
                        <div class="lpj-row">
                            <div class="lpj-row-left"><span class="lpj-dot" style="background:#8b5cf6;"></span><span class="lpj-row-label">Keuntungan Kaos Reuni <span class="lpj-count">(680 pcs × Rp 150k)</span></span></div>
                            <div class="lpj-row-right"><span class="lpj-amount">Rp 28.745.250</span><span class="lpj-pct">12%</span></div>
                        </div>
                        <div class="lpj-bar-wrap"><div class="lpj-bar" style="width:12%;background:#8b5cf6;"></div></div>
                        <div class="lpj-row">
                            <div class="lpj-row-left"><span class="lpj-dot" style="background:#10b981;"></span><span class="lpj-row-label">Bazar Makanan <span class="lpj-count">(Panitia + Alumni '93)</span></span></div>
                            <div class="lpj-row-right"><span class="lpj-amount">Rp 4.600.000</span><span class="lpj-pct">2%</span></div>
                        </div>
                        <div class="lpj-bar-wrap"><div class="lpj-bar" style="width:2%;background:#10b981;"></div></div>
                        <div class="lpj-total-row mt-3">
                            <span class="fw-black">Total Pemasukan</span>
                            <span class="fw-black text-success">Rp 230.345.250</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="lpj-card">
                        <h6 class="lpj-card-title">📉 Rincian Pengeluaran</h6>
                        @php
                        $expenses = [
                            ['label'=>'Penyediaan Konsumsi Peserta','amount'=>54740000,'color'=>'#ef4444','pct'=>24],
                            ['label'=>'Sewa Peralatan Panggung & AV (2 hari)','amount'=>53000000,'color'=>'#f97316','pct'=>23],
                            ['label'=>'Cindera Mata Almamater / SMK Negeri 2','amount'=>25400000,'color'=>'#f59e0b','pct'=>11],
                            ['label'=>'Cetak dan Media Publikasi','amount'=>24446400,'color'=>'#eab308','pct'=>11],
                            ['label'=>'Alat/Bahan Dekorasi Venue','amount'=>20541000,'color'=>'#84cc16','pct'=>9],
                            ['label'=>'Sewa/Belanja Lain-lain','amount'=>15100000,'color'=>'#22c55e','pct'=>7],
                            ['label'=>'Bantuan Sembako 100 Paket','amount'=>12760000,'color'=>'#06b6d4','pct'=>6],
                            ['label'=>'Sewa Perlengkapan Pendukung Venue','amount'=>12305000,'color'=>'#3b82f6','pct'=>5],
                            ['label'=>'ATK & Operasional Kesekretariatan','amount'=>6105280,'color'=>'#8b5cf6','pct'=>3],
                            ['label'=>'Rapat LPJ & Pembubaran Panitia','amount'=>3330000,'color'=>'#ec4899','pct'=>1],
                            ['label'=>'Software & Pelengkapan IT','amount'=>2614050,'color'=>'#a855f7','pct'=>1],
                        ];
                        @endphp
                        @foreach($expenses as $exp)
                        <div class="lpj-exp-row">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="d-flex align-items-center gap-2">
                                    <span class="lpj-dot" style="background:{{ $exp['color'] }};"></span>
                                    <span class="lpj-exp-label">{{ $exp['label'] }}</span>
                                </span>
                                <span class="lpj-exp-amount">Rp {{ number_format($exp['amount'],0,',','.') }}</span>
                            </div>
                            <div class="lpj-bar-wrap"><div class="lpj-bar" style="width:{{ $exp['pct'] }}%;background:{{ $exp['color'] }};"></div></div>
                        </div>
                        @endforeach
                        <div class="lpj-total-row mt-3">
                            <span class="fw-black">Total Pengeluaran</span>
                            <span class="fw-black text-danger">Rp 230.341.930</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ DAFTAR DONATUR ALUMNI ═══════════════════════════════ --}}
            <div class="lpj-card mb-4">
                <h6 class="lpj-card-title">🏆 Donatur Alumni — Kontribusi Dana Tunai</h6>
                <p class="small text-muted mb-3">11 alumni yang berkontribusi dana tunai sebesar <strong>Rp 112.500.000</strong> (49% dari total pemasukan). Terima kasih atas kepercayaan dan kepedulian Bapak/Ibu.</p>
                @php
                $donatur = [
                    ['no'=>1,'nama'=>'Muhammad Sinen','jabatan'=>'Ketua FORSA Steman Ternate','jumlah'=>50000000,'keterangan'=>'Diserahkan Cash','rank'=>'platinum'],
                    ['no'=>2,'nama'=>'Ir. Namto Roba, SH','jabatan'=>'Alumni','jumlah'=>25000000,'keterangan'=>'Bukti Transfer 25','rank'=>'gold'],
                    ['no'=>3,'nama'=>'Gamal R. Kambey, ST., M.Si','jabatan'=>'Alumni','jumlah'=>10000000,'keterangan'=>'Bukti Transfer 26','rank'=>'silver'],
                    ['no'=>4,'nama'=>'Dr. Ir. H. Ibrahim Buka, ST., MT','jabatan'=>'Alumni','jumlah'=>10000000,'keterangan'=>'Bukti Transfer 27','rank'=>'silver'],
                    ['no'=>5,'nama'=>'Zuldan A. Kader','jabatan'=>'Ketua Pelaksana Panitia','jumlah'=>10000000,'keterangan'=>'Bukti Transfer 28','rank'=>'silver'],
                    ['no'=>6,'nama'=>'Andi Mohtar','jabatan'=>'Alumni','jumlah'=>2000000,'keterangan'=>'Bukti Transfer 29','rank'=>'bronze'],
                    ['no'=>7,'nama'=>'Hengky Oba','jabatan'=>'Alumni','jumlah'=>1500000,'keterangan'=>'Bukti Transfer 30','rank'=>'bronze'],
                    ['no'=>8,'nama'=>'Chalis Malikiddin','jabatan'=>'Alumni','jumlah'=>1000000,'keterangan'=>'Bukti Transfer 31','rank'=>'bronze'],
                    ['no'=>9,'nama'=>'Sofyan Maulasa','jabatan'=>'Alumni','jumlah'=>1000000,'keterangan'=>'Bukti Transfer 32','rank'=>'bronze'],
                    ['no'=>10,'nama'=>'Andri Noky','jabatan'=>'Alumni','jumlah'=>1000000,'keterangan'=>'Bukti Transfer 33','rank'=>'bronze'],
                    ['no'=>11,'nama'=>'H. Ardanan Salasa','jabatan'=>'Alumni','jumlah'=>1000000,'keterangan'=>'Diserahkan Cash','rank'=>'bronze'],
                ];
                $rankColor = ['platinum'=>'#7c3aed','gold'=>'#d97706','silver'=>'#64748b','bronze'=>'#b45309'];
                $rankBg = ['platinum'=>'#f5f3ff','gold'=>'#fffbeb','silver'=>'#f8fafc','bronze'=>'#fef3c7'];
                $rankIcon = ['platinum'=>'💎','gold'=>'🥇','silver'=>'🥈','bronze'=>'🥉'];
                @endphp
                <div class="row g-3">
                    @foreach($donatur as $d)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="donor-card" style="border-left:4px solid {{ $rankColor[$d['rank']] }};background:{{ $rankBg[$d['rank']] }};">
                            <div class="d-flex align-items-center gap-3">
                                <div class="donor-avatar" style="background:{{ $rankColor[$d['rank']] }};">
                                    {{ $rankIcon[$d['rank']] }}
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="donor-name">{{ $d['nama'] }}</div>
                                    <div class="donor-role">{{ $d['jabatan'] }}</div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <div class="donor-amount" style="color:{{ $rankColor[$d['rank']] }};">
                                        Rp {{ number_format($d['jumlah'],0,',','.') }}
                                    </div>
                                    <div class="donor-ket">{{ $d['keterangan'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="lpj-total-row mt-4">
                    <span class="fw-black">Total Dana Tunai Donatur</span>
                    <span class="fw-black" style="color:#7c3aed;">Rp 112.500.000</span>
                </div>
            </div>

            {{-- ══ DONATUR BARANG / IN-KIND ════════════════════════════ --}}
            <div class="lpj-card mb-4">
                <h6 class="lpj-card-title">🎁 Donatur Barang & Fasilitas (In-Kind)</h6>
                <p class="small text-muted mb-3">Selain dana tunai, beberapa alumni dan mitra memberikan dukungan nyata berupa barang dan fasilitas yang sangat membantu kelancaran acara.</p>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="inkind-item">
                            <div class="inkind-header">
                                <span class="inkind-icon">🌾</span>
                                <div>
                                    <div class="inkind-name">Ir. Namto Roba, SH</div>
                                    <div class="inkind-angk">Alumni Angkatan 1979</div>
                                </div>
                            </div>
                            <ul class="inkind-list"><li>100 sak beras (masing-masing 5 kg)</li></ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="inkind-item">
                            <div class="inkind-header">
                                <span class="inkind-icon">🏗️</span>
                                <div>
                                    <div class="inkind-name">Faizal M. Alkatiri</div>
                                    <div class="inkind-angk">Alumni Angkatan 1993 · Wakil Ketua Panitia</div>
                                </div>
                            </div>
                            <ul class="inkind-list">
                                <li>Kayu Balok 5/5, 1,5 m³ · Triplex 4mm, 8 lembar</li>
                                <li>Cat & Lampu outdoor 40 pcs · Cetak baliho nama angkatan</li>
                                <li>Perbaikan AC sekretariat · Kaos reuni & tournamen</li>
                                <li>Upah security + biaya operasional kegiatan</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="inkind-item">
                            <div class="inkind-header">
                                <span class="inkind-icon">📡</span>
                                <div>
                                    <div class="inkind-name">Akhmad Ibrahim</div>
                                    <div class="inkind-angk">Alumni Angkatan 1993 · Direktur PT. Bukit Sejati</div>
                                </div>
                            </div>
                            <ul class="inkind-list">
                                <li>Sponsor Live Streaming Talkshow "Inspirasi Alumni dan Prestasi"</li>
                                <li>Pro 1 FM 101,8 MHz · RRI Digital · YouTube RRI Ternate (3,5 jam)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="inkind-item">
                            <div class="inkind-header">
                                <span class="inkind-icon">🪑</span>
                                <div>
                                    <div class="inkind-name">Nuzul Umasangaji</div>
                                    <div class="inkind-angk">Alumni Angkatan 1994</div>
                                </div>
                            </div>
                            <ul class="inkind-list">
                                <li>Kursi + sarung 358 buah · Meja prasmanan 5 buah</li>
                                <li>Meja VIP 4 buah · Meja kecil 4 buah</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="inkind-item">
                            <div class="inkind-header">
                                <span class="inkind-icon">🖼️</span>
                                <div>
                                    <div class="inkind-name">Hambali</div>
                                    <div class="inkind-angk">Alumni Angkatan 1990</div>
                                </div>
                            </div>
                            <ul class="inkind-list"><li>Cetak baliho Lapangan Ngaralamo</li></ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik Acara --}}
            <div class="lpj-stats-bar mb-4">
                <div class="row g-3 text-center">
                    <div class="col-6 col-md-3">
                        <div class="lpj-stat"><div class="lpj-stat-val">840</div><div class="lpj-stat-lbl">Total Peserta</div><div class="lpj-stat-sub">760 alumni + 80 pendidik & guru</div></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="lpj-stat"><div class="lpj-stat-val">11</div><div class="lpj-stat-lbl">Donatur Tunai</div><div class="lpj-stat-sub">Total Rp 112.500.000</div></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="lpj-stat"><div class="lpj-stat-val">25/37</div><div class="lpj-stat-lbl">Angkatan Hadir</div><div class="lpj-stat-sub">67,5% tingkat partisipasi</div></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="lpj-stat"><div class="lpj-stat-val">99,9%</div><div class="lpj-stat-lbl">Efisiensi Anggaran</div><div class="lpj-stat-sub">Saldo akhir Rp 241.835</div></div>
                    </div>
                </div>
            </div>

            {{-- Kontribusi Almamater --}}
            <div class="lpj-contrib-box mb-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="lpj-contrib-icon">🏫</div>
                    <div>
                        <h6 class="fw-black text-dark mb-1">Kontribusi kepada Almamater SMK Negeri 2 Ternate — Rp 25.400.000</h6>
                        <p class="small text-muted mb-0">Panitia menyerahkan: 1 Podium, 1 Mimbar Juara, 2 Tiang Listrik, 6 Lampu PJU Solarsel, 4 unit Braket Lampu PJU, 2 buah Mic Wireless Podium, dan 1 Megaphone. Ditambah <strong>100 paket sembako (Rp 12.760.000)</strong> berisi gula, minyak goreng, kopi, dan teh yang dibagikan kepada anak yatim dan keluarga alumni kurang mampu.</p>
                    </div>
                </div>
            </div>

            {{-- Lihat LPJ (view only, no download) --}}
            <div class="lpj-doc-row p-3 rounded-3" style="background:#f8fafc;border:1.5px dashed #cbd5e1;">
                <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                    <div style="width:42px;height:42px;min-width:42px;background:#fee2e2;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">📄</div>
                    <div class="min-w-0">
                        <div class="fw-bold small text-dark">Laporan Pertanggungjawaban Lengkap (Revisi)</div>
                        <div class="small text-muted">Ternate, 1 Juli 2026 · Ketua Panitia: Zuldan A. Kader · 52 halaman</div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-dark rounded-pill lpj-doc-btn"
                        onclick="bukaLPJ('{{ route('pdf.view') }}?f=campaign-docs%2FLPJ_Reuni2026.pdf')">
                    <i class="bi bi-eye me-1"></i> Lihat PDF LPJ
                </button>
            </div>
        </div>
        {{-- ═══════════════════════════════════════════════════════════ --}}

        {{-- Modal Viewer PDF (view only) --}}
        <div class="modal fade" id="modalPdfViewer" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down">
                {{-- Flex column: header + iframe(flex:1) + footer selalu kelihatan --}}
                <div class="modal-content border-0 d-flex flex-column" style="background:#1e293b;height:88vh;max-height:88vh;">
                    {{-- Header --}}
                    <div class="modal-header border-0 px-3 py-2 flex-shrink-0">
                        <span class="text-white fw-bold" style="font-size:.8rem;">
                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i>LPJ Reuni 2026
                        </span>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <span class="badge bg-warning text-dark" style="font-size:.58rem;">
                                <i class="bi bi-lock-fill"></i> DILINDUNGI
                            </span>
                            <button type="button" class="btn-close btn-close-white ms-1" data-bs-dismiss="modal"></button>
                        </div>
                    </div>
                    {{-- PDF iframe + tombol nav floating di atas --}}
                    <div class="flex-grow-1 overflow-hidden" style="min-height:0;position:relative;">
                        <iframe id="pdfFrame" src="" style="width:100%;height:100%;border:0;display:block;"
                                referrerpolicy="no-referrer"></iframe>
                        {{-- Overlay z-index:10: blokir klik kanan pada PDF (iframe tidak bisa dicegah dari parent) --}}
                        <div id="pdfOverlay"
                             style="position:absolute;inset:0;z-index:10;cursor:default;"
                             oncontextmenu="return false;"></div>
                        {{-- Tombol nav floating: selalu kelihatan, tidak bergantung footer --}}
                        <div style="position:absolute;bottom:0;left:0;right:0;z-index:20;
                                    background:linear-gradient(transparent,rgba(15,23,42,.95) 45%);
                                    padding:1.8rem .75rem .75rem;">
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-light rounded-pill flex-grow-1 fw-semibold"
                                        onclick="pdfPrevPage()" id="btnPdfPrev" disabled
                                        style="min-height:42px;">
                                    <i class="bi bi-chevron-left"></i> Sebelumnya
                                </button>
                                <span class="text-white text-center flex-shrink-0"
                                      id="pdfPageInfo" style="font-size:.72rem;min-width:62px;">Hal 1 / 52</span>
                                <button class="btn btn-sm btn-warning rounded-pill flex-grow-1 fw-bold"
                                        onclick="pdfNextPage()" id="btnPdfNext"
                                        style="min-height:42px;">
                                    Lanjut <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

@push('scripts')
<script>
// ── Navigasi halaman PDF ─────────────────────────────────
let _pdfBaseUrl = '';
let _pdfPage    = 1;
const PDF_MAX   = 52; // jumlah halaman LPJ

function bukaLPJ(url) {
    _pdfBaseUrl = url;
    _pdfPage    = 1;
    _renderPdfFrame();
    var modal = new bootstrap.Modal(document.getElementById('modalPdfViewer'));
    modal.show();
}

function _renderPdfFrame() {
    var frame  = document.getElementById('pdfFrame');
    // &_p=N → URL unik tiap halaman, browser wajib reload
    // #toolbar=0&navpanes=0 → sembunyikan toolbar download Chrome
    var newSrc = _pdfBaseUrl + '&_p=' + _pdfPage + '#toolbar=0&navpanes=0&page=' + _pdfPage;
    frame.src  = newSrc;
    document.getElementById('pdfPageInfo').textContent = 'Hal ' + _pdfPage + ' / ' + PDF_MAX;
    document.getElementById('btnPdfPrev').disabled = (_pdfPage <= 1);
    document.getElementById('btnPdfNext').disabled = (_pdfPage >= PDF_MAX);
}

function pdfNextPage() {
    if (_pdfPage < PDF_MAX) { _pdfPage++; _renderPdfFrame(); }
}
function pdfPrevPage() {
    if (_pdfPage > 1) { _pdfPage--; _renderPdfFrame(); }
}

// Kosongkan src saat modal ditutup (hentikan loading)
document.getElementById('modalPdfViewer').addEventListener('hidden.bs.modal', function () {
    document.getElementById('pdfFrame').src = '';
    _pdfPage = 1;
});

// ── Proteksi: blokir klik kanan di seluruh halaman ───────
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    return false;
});

// ── Proteksi: blokir keyboard shortcut simpan/print ──────
document.addEventListener('keydown', function(e) {
    // Ctrl+S, Ctrl+P, Ctrl+U, Ctrl+Shift+I, F12
    if ((e.ctrlKey || e.metaKey) && ['s','p','u','a'].includes(e.key.toLowerCase())) {
        e.preventDefault(); return false;
    }
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && ['i','j'].includes(e.key.toLowerCase())) {
        e.preventDefault(); return false;
    }
    if (e.key === 'F12') { e.preventDefault(); return false; }
    if (e.key === 'PrintScreen') { e.preventDefault(); return false; }
});

// ── Proteksi: blokir drag & select teks ─────────────────
document.addEventListener('selectstart', function(e) {
    if (e.target.closest('#modalPdfViewer')) e.preventDefault();
});
document.addEventListener('dragstart', function(e) {
    e.preventDefault();
});
</script>
@endpush

@endsection
