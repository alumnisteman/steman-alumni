@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black text-dark mb-1 text-uppercase">DASHBOARD {{ auth()->user()->role }} (VERSION 4.2)</h2>
            <p class="text-muted">Selamat datang kembali, {{ auth()->user()->name }}. Anda masuk sebagai <strong>{{ ucfirst(auth()->user()->role) }}</strong> Portal Steman.</p>
            <p class="small text-danger fw-bold"><i class="bi bi-cpu-fill me-1"></i> SERVER TIME: {{ now()->format('H:i:s') }} | <i class="bi bi-shield-check me-1"></i> STATUS: SECURE</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill border d-none d-md-inline-block">
                <i class="bi bi-clock-fill text-primary me-2"></i> {{ date('l, d M Y') }}
            </span>
            <a href="javascript:void(0)" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" 
               onclick="Swal.fire({
                   title: 'Keluar dari Sistem?',
                   text: 'Anda perlu login kembali untuk mengakses dashboard.',
                   icon: 'question',
                   showCancelButton: true,
                   confirmButtonColor: '#ef4444',
                   cancelButtonColor: '#64748b',
                   confirmButtonText: 'Ya, Logout',
                   cancelButtonText: 'Batal'
               }).then((result) => { if (result.isConfirmed) window.location.href = '/logout'; })">
                <i class="bi bi-box-arrow-right me-1"></i> LOGOUT
            </a>
        </div>
    </div>

    <div class="admin-dashboard-content">
        <!-- Launch Control Center (New V4.2 Feature) -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-5 overflow-hidden {{ $launchInfo['mode'] === 'on' ? 'bg-primary bg-opacity-10' : 'bg-white' }}">
                    <div class="card-body p-4 p-lg-5">
                        <div class="row align-items-center">
                            <div class="col-lg-1">
                                <div class="bg-{{ $launchInfo['mode'] === 'on' ? 'primary' : 'success' }} text-white p-3 rounded-circle text-center shadow-sm">
                                    <i class="bi bi-{{ $launchInfo['mode'] === 'on' ? 'hourglass-split' : 'rocket-takeoff-fill' }} fs-2"></i>
                                </div>
                            </div>
                            <div class="col-lg-7 ps-lg-4">
                                <h4 class="fw-black mb-1 text-dark">LAUNCH CONTROL CENTER</h4>
                                <p class="text-muted mb-0">Status: <span class="badge bg-{{ $launchInfo['mode'] === 'on' ? 'warning text-dark' : 'success' }} px-3 py-2 rounded-pill">{{ $launchInfo['mode'] === 'on' ? 'COMING SOON ACTIVE' : 'PORTAL IS LIVE' }}</span></p>
                                @if($launchInfo['mode'] === 'on')
                                    <div class="mt-3 small text-dark fw-bold">
                                        <i class="bi bi-calendar-event me-2"></i> Target Launch: {{ date('d M Y, H:i', strtotime($launchInfo['date'])) }}
                                        <span class="ms-3 text-primary"><i class="bi bi-stopwatch me-1"></i> {{ \Carbon\Carbon::parse($launchInfo['date'])->diffForHumans() }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                                <form action="{{ route('admin.settings.update') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="coming_soon_mode" value="{{ $launchInfo['mode'] === 'on' ? 'off' : 'on' }}">
                                    <button type="submit" class="btn btn-{{ $launchInfo['mode'] === 'on' ? 'success' : 'outline-danger' }} btn-lg rounded-pill px-5 fw-bold shadow-lg">
                                        <i class="bi bi-{{ $launchInfo['mode'] === 'on' ? 'play-fill' : 'pause-fill' }} me-2"></i>
                                        {{ $launchInfo['mode'] === 'on' ? 'LAUNCH PORTAL NOW' : 'ACTIVATE COMING SOON' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.settings.index') }}#group-launch" class="btn btn-light border btn-lg rounded-circle shadow-sm ms-2" title="Settings">
                                    <i class="bi bi-gear-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- STAT CARDS                                                  --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- TOP ANALYTICS & STATS                                      --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="row g-4 mb-5 gsap-reveal">
            {{-- Main Chart: Alumni Growth --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-5 bg-white p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-black text-dark mb-1">PERTUMBUHAN ALUMNI</h5>
                            <p class="text-muted small mb-0">Statistik pendaftaran berdasarkan tahun lulus</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-pill px-3 dropdown-toggle shadow-none" type="button" data-bs-toggle="dropdown">
                                Last 10 Years
                            </button>
                        </div>
                    </div>
                    <div class="skeleton-loader rounded-4 mb-4" style="height: 300px; display: block;" id="growthChartLoader"></div>
                    <div style="height: 300px; display: none;" id="growthChartContainer">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Distribution Chart: Majors --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-5 bg-dark text-white p-4 h-100">
                    <h5 class="fw-black mb-1 text-primary">SEBARAN JURUSAN</h5>
                    <p class="text-white-50 small mb-4">Proporsi alumni per kompetensi keahlian</p>
                    <div class="skeleton-loader rounded-circle mx-auto" style="width: 200px; height: 200px; display: block;" id="majorChartLoader"></div>
                    <div style="height: 250px; display: none;" id="majorChartContainer">
                        <canvas id="majorChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center small mb-1">
                            <span class="opacity-75">Terserap Kerja</span>
                            <span class="fw-bold">{{ $employedPercentage }}%</span>
                        </div>
                        <div class="progress rounded-pill bg-white bg-opacity-10" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $employedPercentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-5 gsap-reveal">
            {{-- Quick Stats Row --}}
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 border-start border-primary border-4">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Total Alumni</div>
                    <h3 class="fw-black text-dark mb-0">{{ number_format($totalAlumni) }}</h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 border-start border-success border-4">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Online</div>
                    <h3 class="fw-black text-success mb-0">{{ number_format($onlineUsers) }}</h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 border-start border-warning border-4">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Pending</div>
                    <h3 class="fw-black text-warning mb-0">{{ number_format($pendingAlumni) }}</h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 border-start border-info border-4">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Pekerjaan</div>
                    <h3 class="fw-black text-info mb-0">{{ $totalJobs }}</h3>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- LIVE PULSE & ACTIVITY FEED                                 --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="row g-4 mb-5 gsap-reveal">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-5 bg-white h-100 overflow-hidden">
                    <div class="card-header bg-white py-4 px-4 d-flex justify-content-between align-items-center border-0">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-activity text-primary me-2"></i>AKTIVITAS TERBARU</h5>
                            <p class="text-muted small mb-0">Pantauan log sistem dalam 24 jam terakhir</p>
                        </div>
                        <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 border border-primary border-opacity-10">
                            <span class="pulse-dot me-1"></span> LIVE PULSE
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-feed p-4" style="max-height: 400px; overflow-y: auto;">
                            @forelse($recentActivities as $log)
                            <div class="d-flex gap-3 mb-4 activity-item">
                                <div class="flex-shrink-0">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="bi bi-{{ strpos($log->action, 'delete') !== false ? 'trash text-danger' : (strpos($log->action, 'update') !== false ? 'pencil text-warning' : 'plus-lg text-success') }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="fw-bold mb-0 small text-dark">{{ $log->user->name ?? 'System' }}</h6>
                                        <span class="text-muted x-small">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-muted small mb-1">{{ $log->description }}</p>
                                    <span class="badge bg-light text-muted fw-normal" style="font-size: 0.65rem;">{{ strtoupper($log->action) }} • IP: {{ $log->ip_address ?? '127.0.0.1' }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <p class="text-muted">Belum ada aktivitas tercatat hari ini.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Elite System Guard & Health Pulse --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-5 bg-dark text-white p-4 h-100 overflow-hidden position-relative" style="background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);">
                    <div class="position-absolute top-0 end-0 p-5 mt-n4 me-n4 opacity-10">
                        <i class="bi bi-shield-lock-fill display-1"></i>
                    </div>
                    
                    <h5 class="fw-black mb-1 position-relative z-1 text-primary">COMMAND CENTER</h5>
                    <p class="text-white-50 small mb-4">Autonomous System Guard Status</p>
                    
                    <div class="p-3 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-10 mb-4 text-center">
                        <div id="dash-guard-status" class="fw-black fs-4 mb-1">
                            <span class="spinner-border spinner-border-sm me-2"></span> CHECKING...
                        </div>
                        <div class="x-small opacity-50 text-uppercase tracking-widest" id="dash-guard-label">SCANNING SYSTEMS</div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2 x-small">
                            <span class="opacity-75">INTEGRITY SCORE</span>
                            <span class="fw-bold text-info" id="dash-integrity-val">0%</span>
                        </div>
                        <div class="progress bg-white bg-opacity-10 rounded-pill" style="height: 6px;">
                            <div id="dash-integrity-bar" class="progress-bar bg-info progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="mt-auto position-relative z-1">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.guard.dashboard') }}" class="btn btn-primary rounded-pill fw-bold py-3 shadow-lg">
                                <i class="bi bi-shield-check me-2"></i>OPEN SYSTEM GUARD
                            </a>
                            <div class="text-center mt-2">
                                <span class="x-small opacity-50"><i class="bi bi-robot me-1"></i> AI AGENT IS ACTIVE</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafana Live Monitoring --}}
        <div class="row mb-5 gsap-reveal">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-5 bg-dark overflow-hidden">
                    <div class="card-header bg-dark py-3 px-4 d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-25">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-graph-up-arrow text-info fs-4"></i>
                            <h5 class="fw-bold mb-0 text-white">LIVE SERVER METRICS</h5>
                        </div>
                        <a href="/grafana/" target="_blank" class="btn btn-outline-info btn-sm rounded-pill px-3">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Full Monitor
                        </a>
                    </div>
                    <div class="card-body p-0" style="height: 400px; background: #111217;">
                        <iframe src="/grafana/d/steman-overview/steman-alumni-system-monitoring?orgId=1&refresh=10s&kiosk=tv" width="100%" height="100%" frameborder="0" style="border: none;"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Insights Block -->
        <div class="row mb-5 gsap-reveal">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-5 bg-light p-4 p-lg-5">
                    <div class="row align-items-center">
                        <div class="col-lg-1 d-none d-lg-block">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-center">
                                <i class="bi bi-robot text-primary fs-1"></i>
                            </div>
                        </div>
                        <div class="col-lg-11 ps-lg-4">
                            <h4 class="fw-black mb-3 text-dark"><i class="bi bi-stars text-primary me-2"></i>GEMINI AI INTELLIGENCE</h4>
                            <div class="row g-3">
                                @foreach($aiInsights as $insight)
                                    @if(!empty(trim($insight)))
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start p-3 rounded-4 bg-white border shadow-sm h-100">
                                            <i class="bi bi-lightbulb-fill text-warning me-3 fs-5"></i>
                                            <div class="small leading-relaxed text-dark">{{ ltrim($insight, " -•*") }}</div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // GSAP Entry Animation
    gsap.to(".gsap-reveal", {
        opacity: 1,
        y: 0,
        duration: 0.8,
        stagger: 0.2,
        ease: "power2.out"
    });

    // 1. Alumni Growth Chart (Line)
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    const growthData = {
        labels: {!! json_encode($alumniByYear->pluck('graduation_year')) !!},
        datasets: [{
            label: 'Jumlah Alumni',
            data: {!! json_encode($alumniByYear->pluck('total')) !!},
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#6366f1'
        }]
    };
    new Chart(growthCtx, {
        type: 'line',
        data: growthData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Major Distribution Chart (Doughnut)
    const majorCtx = document.getElementById('majorChart').getContext('2d');
    const majorData = {
        labels: {!! json_encode($alumniByMajor->pluck('major')) !!},
        datasets: [{
            data: {!! json_encode($alumniByMajor->pluck('total')) !!},
            backgroundColor: [
                '#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#4cc9f0', '#4895ef'
            ],
            borderWidth: 0
        }]
    };
    new Chart(majorCtx, {
        type: 'doughnut',
        data: majorData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#fff', font: { size: 10 }, usePointStyle: true }
                }
            }
        }
    // Hide skeletons and show charts
    document.getElementById('growthChartLoader').style.display = 'none';
    document.getElementById('growthChartContainer').style.display = 'block';
    document.getElementById('majorChartLoader').style.display = 'none';
    document.getElementById('majorChartContainer').style.display = 'block';

    // 3. Live Guard Status Integration
    function updateDashGuard() {
        fetch("{{ route('admin.guard.status') }}")
            .then(r => r.json())
            .then(data => {
                const statusEl = document.getElementById('dash-guard-status');
                const labelEl = document.getElementById('dash-guard-label');
                const barEl = document.getElementById('dash-integrity-bar');
                const valEl = document.getElementById('dash-integrity-val');

                valEl.textContent = data.summary.percent + '%';
                barEl.style.width = data.summary.percent + '%';

                if (data.status === 'OPERATIONAL') {
                    statusEl.innerHTML = '<i class="bi bi-check-circle-fill text-success me-2"></i> OPERATIONAL';
                    labelEl.textContent = 'ALL SYSTEMS SECURE';
                    labelEl.className = 'x-small text-success fw-bold text-uppercase tracking-widest';
                } else {
                    statusEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> DEGRADED';
                    labelEl.textContent = data.summary.failed + ' ISSUES DETECTED';
                    labelEl.className = 'x-small text-danger fw-bold text-uppercase tracking-widest';
                }
            })
            .catch(e => {
                document.getElementById('dash-guard-status').textContent = 'OFFLINE';
            });
    }

    updateDashGuard();
    setInterval(updateDashGuard, 60000); // Update every minute
});
</script>
@endpush

@push('styles')
<style>
    .x-small { font-size: 0.7rem; }
    .pulse-dot {
        height: 8px;
        width: 8px;
        background-color: var(--bs-primary);
        border-radius: 50%;
        display: inline-block;
        animation: pulse-ring 1.5s infinite;
    }
    @keyframes pulse-ring {
        0% { transform: scale(0.9); opacity: 1; }
        50% { transform: scale(1.5); opacity: 0.5; }
        100% { transform: scale(0.9); opacity: 1; }
    }
    .activity-item { border-left: 2px solid #f1f5f9; padding-left: 20px; position: relative; }
    .activity-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 0;
        width: 10px;
        height: 10px;
        background: #fff;
        border: 2px solid var(--bs-primary);
        border-radius: 50%;
    }
    .hover-up-small:hover { transform: translateY(-5px); }
    .transition-all { transition: all 0.3s ease; }
</style>
@endpush

