@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.0/dist/apexcharts.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<style>
    /* Glassmorphism Theme Container */
    .dashboard-bg {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding-bottom: 3rem;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 20px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.12);
        background: rgba(255, 255, 255, 0.85);
    }
    
    /* Animated Gradient Text */
    .text-gradient {
        background: linear-gradient(45deg, #3a1c71, #d76d77, #ffaf7b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Icon Container */
    .icon-glass {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 12px;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* AI Insights Widget */
    .ai-insights-box {
        background: linear-gradient(120deg, #e0c3fc 0%, #8ec5fc 100%);
        border-radius: 20px;
        color: #2b2b2b;
        position: relative;
        overflow: hidden;
    }
    .ai-insights-box::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 60%);
        animation: rotateBg 15s linear infinite;
    }
    @keyframes rotateBg {
        100% { transform: rotate(360deg); }
    }
    .ai-content {
        position: relative;
        z-index: 1;
    }

    /* Timeline */
    .timeline {
        border-left: 2px solid rgba(67, 97, 238, 0.2);
        padding-left: 20px;
        margin-left: 10px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -27px;
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #4361ee;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
    }
</style>
@endpush

@section('content')
<div class="dashboard-bg pt-4">
    <div class="container">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="fw-bolder text-gradient mb-1">Admin Workspace</h2>
                <p class="text-muted small fw-medium">Welcome back, <span class="text-dark">{{ \Illuminate\Support\Facades\Auth::user()->name }}</span>! Layaknya kapten, kendalikan semuanya dari sini.</p>
            </div>
            <a href="/admin/export" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold border-0" style="background: linear-gradient(45deg, #4361ee, #3f37c9);">
                <i class="bi bi-cloud-download me-2"></i> Unduh Data Laporan
            </a>
        </div>

        <!-- AI Insights -->
        <div class="ai-insights-box p-4 mb-4 shadow-sm glass-card border-0">
            <div class="ai-content d-flex align-items-center">
                <div class="me-4 d-none d-md-block">
                    <i class="bi bi-robot fs-1 text-primary opacity-75"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-2"><i class="bi bi-stars text-warning me-2"></i> AI Data Insights</h5>
                    <ul class="mb-0 ps-3">
                        @foreach($aiInsights as $insight)
                            <li class="fw-medium">{{ $insight }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Summary Statistics (CountUp) -->
        <div class="row g-4 mb-4">
            <div class="col-md-4 col-sm-6">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary fw-semibold">Total Alumni</span>
                        <div class="icon-glass text-primary"><i class="bi bi-mortarboard fs-5"></i></div>
                    </div>
                    <h2 class="fw-bolder mb-0 count-up" data-value="{{ $totalAlumni }}">0</h2>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="glass-card p-4 bg-primary bg-opacity-10 border-primary border-opacity-25">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-primary fw-bold">Alumni Internasional</span>
                        <div class="icon-glass bg-primary text-white border-0"><i class="bi bi-globe-americas fs-5"></i></div>
                    </div>
                    <h2 class="fw-bolder mb-0 text-primary count-up" data-value="{{ $internationalCount }}">0</h2>
                    <p class="small text-primary opacity-75 mb-0 mt-2"><i class="bi bi-arrow-up-right me-1"></i>Berada di luar Indonesia</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary fw-semibold">Lowongan Aktif</span>
                        <div class="icon-glass text-success"><i class="bi bi-briefcase fs-5"></i></div>
                    </div>
                    <h2 class="fw-bolder mb-0 count-up" data-value="{{ $totalJobs }}">0</h2>
                </div>
            </div>
        </div>

        <!-- Geospatial Map Section -->
        <div class="mb-4">
            <x-alumni-map 
                id="admin-main" 
                :locations="$alumniLocations" 
                :nationalCount="$nationalCount" 
                :internationalCount="$internationalCount" 
                height="450px"
            />
        </div>


        <div class="row g-4 mb-4">
            <!-- ApexCharts Visualizations -->
            <div class="col-lg-8">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold mb-4 text-dark">Tren Kelulusan Angkatan</h5>
                    <div id="apexYearChart"></div>
                </div>
            </div>
            
            <!-- Real-time Activity Feed -->
            <div class="col-lg-4">
                <div class="glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0 text-dark">Aktivitas Terbaru</h5>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Live</span>
                    </div>
                    <div class="timeline" id="activityTimeline">
                        @forelse($recentActivities as $activity)
                            <div class="timeline-item">
                                <h6 class="fw-bold mb-1">{{ $activity->name }}</h6>
                                <p class="text-muted small mb-0">Bergabung sebagai {{ ucfirst($activity->role) }}</p>
                                <span class="text-secondary" style="font-size: 0.75rem;">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="text-muted small">Belum ada aktivitas.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions / Menu Management -->
        <h5 class="fw-bold mb-3 mt-5 px-2">Kumpulan Menu Manajemen</h5>
        <div class="row g-3">
            @php
                $menus = [
                    ['url' => '/admin/news', 'icon' => 'newspaper', 'color' => 'primary', 'title' => 'Berita', 'desc' => 'Kelola artikel'],
                    ['url' => '/admin/programs', 'icon' => 'calendar-event', 'color' => 'success', 'title' => 'Program', 'desc' => 'Beasiswa & Acara'],
                    ['url' => '/admin/gallery', 'icon' => 'image', 'color' => 'warning', 'title' => 'Galeri', 'desc' => 'Foto & Tiktok'],
                    ['url' => '/admin/contact', 'icon' => 'telephone', 'color' => 'danger', 'title' => 'Kontak', 'desc' => 'Info sekolah'],
                    ['url' => '/admin/messages', 'icon' => 'envelope', 'color' => 'info', 'title' => 'Pesan Masuk', 'desc' => 'Kotak masuk web'],
                    ['url' => '/admin/users', 'icon' => 'people', 'color' => 'primary', 'title' => 'Users', 'desc' => 'Alumni & Admin'],
                    ['url' => route('admin.jobs.index'), 'icon' => 'briefcase', 'color' => 'success', 'title' => 'Lowongan', 'desc' => 'Daftar karir'],
                    ['url' => route('admin.majors.index'), 'icon' => 'mortarboard', 'color' => 'warning', 'title' => 'Jurusan', 'desc' => 'Opsi keahlian'],
                    ['url' => '/admin/settings', 'icon' => 'gear', 'color' => 'secondary', 'title' => 'Pengaturan', 'desc' => 'Konfigurasi web']
                ];
            @endphp

            @foreach($menus as $menu)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ $menu['url'] }}" class="text-decoration-none">
                    <div class="glass-card p-3 h-100 d-flex flex-column align-items-center text-center">
                        <div class="bg-{{ $menu['color'] }} bg-opacity-10 text-{{ $menu['color'] }} rounded-circle mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-{{ $menu['icon'] }} fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ $menu['title'] }}</h6>
                        <span class="text-muted small" style="font-size: 0.75rem;">{{ $menu['desc'] }}</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.0/dist/apexcharts.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.0/countUp.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- CountUp Statistics ---
        const countElements = document.querySelectorAll('.count-up');
        countElements.forEach(el => {
            const val = el.getAttribute('data-value');
            var countUp = new countUp.CountUp(el, val, { duration: 2.5, separator: '.' });
            if (countUp && !countUp.error) {
                countUp.start();
            } else {
                el.innerHTML = val;
            }
        });

        var optionsYear = {
            series: [{
                name: 'Jumlah Lulusan',
                data: {!! json_encode($alumniByYear->pluck('total')) !!}
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                background: 'transparent'
            },
            colors: ['#4361ee'],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.1, stops: [0, 90, 100] }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: {!! json_encode($alumniByYear->pluck('tahun_lulus')) !!},
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#94a3b8' } }
            },
            yaxis: { show: false },
            grid: {
                borderColor: 'rgba(0,0,0,0.05)',
                strokeDashArray: 4,
            },
            theme: { mode: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light' }
        };

        var yearChart = new ApexCharts(document.querySelector("#apexYearChart"), optionsYear);
        yearChart.render();

        // Theme sync for chart
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                setTimeout(function() {
                    var currentTheme = document.documentElement.getAttribute('data-bs-theme');
                    yearChart.updateOptions({ theme: { mode: currentTheme } });
                }, 200);
            });
        }
    });
</script>
@endpush
