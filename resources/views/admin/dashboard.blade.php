@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black text-dark mb-1 text-uppercase">DASHBOARD {{ auth()->user()->role }} (VERSION 4.2)</h2>
            <p class="text-muted">Selamat datang kembali, {{ auth()->user()->name }}. Anda masuk sebagai <strong>{{ ucfirst(auth()->user()->role) }}</strong> Portal Steman.</p>
            <p class="small text-danger fw-bold"><i class="bi bi-cpu-fill me-1"></i> SERVER TIME: {{ now()->format('H:i:s') }} | <i class="bi bi-shield-check me-1"></i> STATUS: SECURE</p>
        </div>
        <div>
            <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill border">
                <i class="bi bi-clock-fill text-primary me-2"></i> {{ date('l, d M Y') }}
            </span>
        </div>
    </div>

    <div class="admin-dashboard-content">
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
                        
                        <div class="text-center py-4 rounded-5 border-2 border-dashed border-primary bg-primary bg-opacity-5">
                             <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary shadow-lg mb-3" style="width: 100px; height: 100px;">
                                 <i class="bi bi-shield-lock-fill text-white display-5 animate-pulse"></i>
                             </div>
                             <h4 class="fw-black text-dark mb-1">STATUS: SECURE</h4>
                             <p class="text-muted small px-3">End-to-end audit integrity confirmed. System is running at peak performance.</p>
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
