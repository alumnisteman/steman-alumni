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
            <a href="/logout" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
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
        <div class="row g-3 mb-5">
            {{-- Total Alumni --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 h-100 stat-card" style="border-top: 4px solid #6366f1 !important;">
                    <div class="card-body p-3 text-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;">
                            <i class="bi bi-mortarboard-fill fs-4"></i>
                        </div>
                        <div class="display-6 fw-black text-dark">{{ number_format($totalAlumni) }}</div>
                        <div class="small text-muted fw-semibold">Total Alumni</div>
                    </div>
                </div>
            </div>
            {{-- User Online --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 h-100 stat-card" style="border-top: 4px solid #22c55e !important;">
                    <div class="card-body p-3 text-center">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;">
                            <i class="bi bi-circle-fill fs-5 animate-pulse text-success"></i>
                        </div>
                        <div class="display-6 fw-black text-dark">{{ number_format($onlineUsers) }}</div>
                        <div class="small text-muted fw-semibold">User Online</div>
                        <div class="badge bg-success bg-opacity-10 text-success rounded-pill mt-1" style="font-size:0.6rem">LIVE 15 MIN</div>
                    </div>
                </div>
            </div>
            {{-- Dana Yayasan --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 h-100 stat-card" style="border-top: 4px solid #f59e0b !important;">
                    <div class="card-body p-3 text-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;">
                            <i class="bi bi-building-fill-check fs-4"></i>
                        </div>
                        <div class="fw-black text-dark" style="font-size:1.1rem;">Rp {{ number_format($foundationTotal, 0, ',', '.') }}</div>
                        <div class="small text-muted fw-semibold">💰 Dana Yayasan</div>
                        <a href="{{ route('admin.campaigns.index') }}" class="badge bg-warning bg-opacity-10 text-warning rounded-pill mt-1 text-decoration-none" style="font-size:0.6rem">Kelola →</a>
                    </div>
                </div>
            </div>
            {{-- Dana Reuni --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 h-100 stat-card" style="border-top: 4px solid #ec4899 !important;">
                    <div class="card-body p-3 text-center">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;background:rgba(236,72,153,0.1);">
                            <i class="bi bi-stars fs-4 text-pink"></i>
                        </div>
                        <div class="fw-black text-dark" style="font-size:1.1rem;">Rp {{ number_format($reunionTotal, 0, ',', '.') }}</div>
                        <div class="small text-muted fw-semibold">🎉 Dana Reuni</div>
                        <a href="{{ route('admin.campaigns.index') }}" class="badge rounded-pill mt-1 text-decoration-none" style="font-size:0.6rem;background:rgba(236,72,153,0.1);color:#ec4899;">Kelola →</a>
                    </div>
                </div>
            </div>
            {{-- Pending Verifikasi --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 h-100 stat-card" style="border-top: 4px solid #ef4444 !important;">
                    <div class="card-body p-3 text-center">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;">
                            <i class="bi bi-person-exclamation fs-4"></i>
                        </div>
                        <div class="display-6 fw-black text-dark">{{ number_format($pendingAlumni) }}</div>
                        <div class="small text-muted fw-semibold">Pending Verif</div>
                        @if($pendingAlumni > 0)
                            <a href="{{ route('admin.users.verification') }}" class="badge bg-danger bg-opacity-10 text-danger rounded-pill mt-1 text-decoration-none" style="font-size:0.6rem">Tinjau →</a>
                        @endif
                    </div>
                </div>
            </div>
            {{-- Employment Rate --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 h-100 stat-card" style="border-top: 4px solid #0ea5e9 !important;">
                    <div class="card-body p-3 text-center">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;">
                            <i class="bi bi-briefcase-fill fs-4"></i>
                        </div>
                        <div class="display-6 fw-black text-dark">{{ $employedPercentage }}%</div>
                        <div class="small text-muted fw-semibold">Terserap Kerja</div>
                        <div class="progress rounded-pill mt-2" style="height:4px;">
                            <div class="progress-bar bg-info" style="width:{{ $employedPercentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Pulse Quick Link --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white overflow-hidden" style="border-left: 5px solid #06b6d4 !important;">
                    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-info bg-opacity-25 rounded-3 p-2">
                                <i class="bi bi-activity text-info fs-3"></i>
                            </div>
                            <div>
                                <div class="fw-black text-white">SYSTEM PULSE <span class="badge bg-info rounded-pill ms-2" style="font-size:0.6rem;">REALTIME</span></div>
                                <div class="small text-white-50">Monitor CPU, Memory, Queue, dan kesehatan server secara live</div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.system.pulse') }}" class="btn btn-info btn-sm rounded-pill px-4 fw-bold">
                                <i class="bi bi-activity me-1"></i>Buka Pulse
                            </a>
                            <a href="{{ route('admin.guard.dashboard') }}" class="btn btn-outline-light btn-sm rounded-pill px-4 fw-bold">
                                <i class="bi bi-shield-check me-1"></i>System Guard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Row -->
        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>PINTASAN CEPAT</h5>
        <div class="row g-3 mb-5">
            <div class="col-md-2">
                <a href="{{ route('admin.users.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none transition-all hover-up-small h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-person-plus-fill fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Tambah User</h6>
                        <p class="text-muted small mb-0">Kelola alumni</p>
                    </div>
                </a>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.news.create') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none transition-all hover-up-small h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-newspaper fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Tulis Berita</h6>
                        <p class="text-muted small mb-0">Upload info</p>
                    </div>
                </a>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.health.trends') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none transition-all hover-up-small h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-heart-pulse-fill fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Kesehatan</h6>
                        <p class="text-muted small mb-0">Tren Alumni</p>
                    </div>
                </a>
            </div>
            <div class="col-md-2">
                <a href="{{ route('alumni.card') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none transition-all hover-up-small h-100 bg-dark">
                    <div class="card-body p-4 text-center">
                        <div class="bg-warning bg-opacity-20 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-id-card fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-white mb-1">Admin Card</h6>
                        <p class="text-white-50 small mb-0">Kunci akses cepat</p>
                    </div>
                </a>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.settings.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none transition-all hover-up-small h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-gear-fill fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Pengaturan</h6>
                        <p class="text-muted small mb-0">Setting branding</p>
                    </div>
                </a>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.system.logs') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none transition-all hover-up-small h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-terminal fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Log Sistem</h6>
                        <p class="text-muted small mb-0">Audit teknis</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- AI Insights Block -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-5 bg-dark text-white p-1 overflow-hidden">
                    <div class="card-body p-4 p-lg-5 position-relative">
                        <!-- Neon decoration -->
                        <div class="position-absolute top-0 end-0 p-5 mt-5 me-5 opacity-25">
                            <i class="bi bi-stars display-1 text-primary animate-pulse"></i>
                        </div>
                        
                        <div class="row align-items-center position-relative z-1">
                            <div class="col-lg-1 d-none d-lg-block">
                                <div class="bg-primary bg-opacity-25 p-3 rounded-circle text-center">
                                    <i class="bi bi-cpu text-primary fs-1"></i>
                                </div>
                            </div>
                            <div class="col-lg-11 ps-lg-4">
                                <h4 class="fw-black mb-3 text-primary"><i class="bi bi-stars me-2"></i>GEMINI AI INTELLIGENCE</h4>
                                <div class="row g-3">
                                    @foreach($aiInsights as $insight)
                                        @if(!empty(trim($insight)))
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start p-3 rounded-4 bg-white bg-opacity-5 hover-up-small border border-white border-opacity-10 h-100">
                                                <i class="bi bi-check2-circle text-primary me-3 fs-4"></i>
                                                <div class="small leading-relaxed text-secondary">{{ ltrim($insight, " -•*") }}</div>
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

        <div class="row g-4 mb-5">
            <!-- War Room: Live Activity -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-5 bg-white h-100 overflow-hidden">
                    <div class="card-header bg-dark py-4 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1 text-white"><i class="bi bi-activity text-primary me-2"></i>WAR ROOM: LIVE ACTIVITY</h5>
                            <p class="text-white-50 small mb-0">Pantauan aktivitas sistem secara real-time</p>
                        </div>
                        <span class="badge bg-primary rounded-pill animate-pulse">LIVE MONITOR</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-feed p-4" style="max-height: 500px; overflow-y: auto;">
                            @foreach($recentActivities as $log)
                            <div class="activity-item d-flex gap-4 mb-4 pb-4 border-bottom border-light animate-reveal">
                                <div class="activity-user-avatar">
                                    @if($log->user && $log->user->profile_picture)
                                        <img src="{{ asset('storage/' . $log->user->profile_picture) }}" class="rounded-circle shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                                            {{ substr($log->user->name ?? '?', 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="activity-content flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold text-dark mb-0">{{ $log->user->name ?? 'System' }}</h6>
                                        <span class="small text-muted">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="badge bg-light text-primary small mb-2 border">{{ strtoupper($log->action) }}</div>
                                    <p class="text-muted small mb-0">{{ $log->description }}</p>
                                    @if($log->ip_address)
                                        <div class="mt-2 small text-secondary opacity-50"><i class="bi bi-geo-alt me-1"></i> IP: {{ $log->ip_address }}</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Radar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-5 h-100 p-4 bg-white border-top border-primary border-5">
                    <h5 class="fw-bold mb-4 text-dark">HEALTH RADAR</h5>
                    <div class="d-flex flex-column gap-4">
                        <div class="radar-item">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small fw-bold">STORAGE NODES</span>
                                <span class="small text-{{ $healthRadar['storage']['status'] }} fw-black">{{ $healthRadar['storage']['percent'] }}% full</span>
                            </div>
                            <div class="progress rounded-pill bg-light" style="height: 12px;">
                                <div class="progress-bar bg-primary shadow-sm" style="width: {{ $healthRadar['storage']['percent'] }}%"></div>
                            </div>
                        </div>
                        
                        <div class="p-4 rounded-5 bg-dark text-white border-start border-primary border-4 shadow">
                            <div class="d-flex align-items-center mb-0">
                                <i class="bi bi-hdd-network text-primary fs-2 me-4"></i>
                                <div>
                                    <div class="small text-white-50">LAST SECURE BACKUP</div>
                                    <div class="fw-bold">{{ $healthRadar['backup']['date'] }}</div>
                                    <div class="small badge bg-primary mt-1">{{ $healthRadar['backup']['size'] }} compressed</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center py-4 rounded-5 border-2 border-dashed border-{{ $healthRadar['integrity']['color'] }} bg-{{ $healthRadar['integrity']['color'] }} bg-opacity-5">
                             <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-{{ $healthRadar['integrity']['color'] }} shadow-lg mb-3" style="width: 100px; height: 100px;">
                                 <i class="bi bi-{{ $healthRadar['integrity']['status'] === 'SECURE' ? 'shield-lock-fill' : 'exclamation-triangle-fill' }} text-white display-5 animate-pulse"></i>
                             </div>
                             <h4 class="fw-black text-dark mb-1">STATUS: {{ $healthRadar['integrity']['status'] }}</h4>
                             <p class="text-muted small px-3">{{ $healthRadar['integrity']['message'] }}</p>
                             <a href="{{ $healthRadar['logs_url'] }}" class="btn btn-dark btn-sm rounded-pill px-4">Review Logs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-slate-700 { color: #334155; }
    .border-white-10 { border-color: rgba(255, 255, 255, 0.1) !important; }
    .text-pink { color: #ec4899 !important; }
    
    /* Stat Cards */
    .stat-card { transition: all 0.25s ease; cursor: default; }
    .stat-card:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(0,0,0,0.12) !important; }
    
    @keyframes reveal {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-reveal { animation: reveal 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards; }
    
    @keyframes pulse-radar {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(1.1); }
        100% { opacity: 1; transform: scale(1); }
    }
    .animate-pulse { animation: pulse-radar 2s infinite ease-in-out; }
    .hover-up-small:hover { transform: translateY(-5px); }
    .transition-all { transition: all 0.3s ease; }
</style>
@endpush

