@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black text-dark mb-1">DASHBOARD ADMIN (VERSION 4.2)</h2>
            <p class="text-muted">Selamat datang kembali, {{ auth()->user()->name }}. Panel kendali sistem Steman Alumni.</p>
            <p class="small text-danger fw-bold">SERVER TIME: {{ now() }} | ROLE: {{ auth()->user()->role }}</p>
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
            <div class="col-md-3">
                <a href="{{ route('admin.users.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none transition-all hover-up-small h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-person-plus-fill fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Tambah User</h6>
                        <p class="text-muted small mb-0">Kelola alumni & admin</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.news.create') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none transition-all hover-up-small h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-newspaper fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Tulis Berita</h6>
                        <p class="text-muted small mb-0">Upload info & berita</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.gallery.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none transition-all hover-up-small h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-images fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Upload Foto</h6>
                        <p class="text-muted small mb-0">Update galeri kegiatan</p>
                    </div>
                </a>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.programs.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none transition-all hover-up-small h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-mortarboard fs-3 text-warning"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Program</h6>
                        <p class="text-muted small mb-0">Kelola kegiatan</p>
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
        </div>

        <!-- Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-4 rounded-4 shadow-sm border-0 bg-white h-100 transition-all hover-up-small">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3"><i class="bi bi-people text-primary fs-3"></i></div>
                        <div class="text-end">
                            <h3 class="fw-black mb-0">{{ number_format($totalAlumni) }}</h3>
                            <p class="text-muted small mb-0">Total Alumni</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-4 rounded-4 shadow-sm border-0 bg-white h-100 transition-all hover-up-small">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3"><i class="bi bi-briefcase text-success fs-3"></i></div>
                        <div class="text-end">
                            <h3 class="fw-black mb-0">{{ number_format($totalJobs) }}</h3>
                            <p class="text-muted small mb-0">Lowongan Kerja</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-4 rounded-4 shadow-sm border-0 bg-white h-100 transition-all hover-up-small">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded-3"><i class="bi bi-mortarboard text-info fs-3"></i></div>
                        <div class="text-end">
                            <h3 class="fw-black mb-0">{{ number_format($totalMajors) }}</h3>
                            <p class="text-muted small mb-0">Program Studi</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card p-4 rounded-4 shadow-sm border-0 bg-white h-100 transition-all hover-up-small">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="bg-dark bg-opacity-10 p-3 rounded-3"><i class="bi bi-shield-lock text-dark fs-3"></i></div>
                        <div class="text-end">
                            <h3 class="fw-black mb-0">{{ number_format($totalAdmins) }}</h3>
                            <p class="text-muted small mb-0">Admin Cabang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Latest Activities -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white py-4 border-0">
                        <h5 class="fw-bold mb-0 text-dark">AKTIVITAS ALUMNI TERBARU</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Alumni</th>
                                    <th>Tahun Lulus</th>
                                    <th>Status Pekerjaan</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentActivities as $activity)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                {{ substr($activity->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $activity->name }}</div>
                                                <div class="text-muted small">{{ $activity->major }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark">{{ $activity->graduation_year }}</span></td>
                                    <td>{{ $activity->current_job ?: 'Mencari Kerja' }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('alumni.show', $activity->id) }}" class="btn btn-sm btn-outline-dark rounded-pill">Profil</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Health Radar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
                    <h5 class="fw-bold mb-4">STATUS SISTEM</h5>
                    <div class="d-flex flex-column gap-4">
                        <div class="radar-item">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small fw-bold">PENGGUNAAN STORAGE</span>
                                <span class="small text-{{ $healthRadar['storage']['status'] }}">{{ $healthRadar['storage']['percent'] }}%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 8px;">
                                <div class="progress-bar bg-{{ $healthRadar['storage']['percent'] > 80 ? 'danger' : 'success' }}" style="width: {{ $healthRadar['storage']['percent'] }}%"></div>
                            </div>
                        </div>
                        
                        <div class="p-3 rounded-4 bg-light">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-cloud-check text-success fs-4 me-3"></i>
                                <div>
                                    <div class="small fw-bold">BACKUP TERAKHIR</div>
                                    <div class="text-muted small">{{ $healthRadar['backup']['date'] }} ({{ $healthRadar['backup']['size'] }})</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center py-3">
                             <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 mb-3" style="width: 80px; height: 80px;">
                                 <i class="bi bi-shield-check text-success fs-1 animate-pulse"></i>
                             </div>
                             <h6 class="fw-bold mb-1">DATA INTEGRITY: OK</h6>
                             <p class="text-muted small">Sistem mendeteksi semua file dan database dalam kondisi stabil.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Insights -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-20 p-3 rounded-circle me-4">
                            <i class="bi bi-stars fs-2"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">AI INSIGHTS & ANALYTICS</h5>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach($aiInsights as $insight)
                                    <span class="badge bg-white bg-opacity-10 fw-normal py-2 px-3 border border-white border-opacity-25 rounded-pill">{{ $insight }}</span>
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
