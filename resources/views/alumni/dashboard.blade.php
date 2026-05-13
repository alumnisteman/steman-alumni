@extends('layouts.app')
@section('content')

<style>
    /* Dark Mode Bento Grid Styling */
    .bento-dashboard {
        background-color: #050505; /* Deep black */
        color: #f8fafc;
        min-height: 100vh;
        padding: 3rem 0;
        font-family: 'Inter', sans-serif;
    }

    .bento-grid-wrapper {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 1.5rem;
    }

    .bento-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        padding: 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }

    .bento-card:hover {
        transform: translateY(-5px) scale(1.01);
        border-color: rgba(255, 255, 255, 0.2);
        box-shadow: 0 15px 35px rgba(0,0,0,0.5), 0 0 20px rgba(99, 102, 241, 0.15); /* Soft neon glow */
    }

    .bento-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.1), transparent 40%);
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: 0;
    }

    .bento-card:hover::before {
        opacity: 1;
    }

    .bento-card > * {
        position: relative;
        z-index: 1;
    }

    /* Grid Spans */
    .span-12 { grid-column: span 12; }
    .span-8 { grid-column: span 8; }
    .span-6 { grid-column: span 6; }
    .span-4 { grid-column: span 4; }
    .span-3 { grid-column: span 3; }

    @media (max-width: 991px) {
        .span-8, .span-6, .span-4, .span-3 { grid-column: span 12; }
    }

    /* specific accents */
    .accent-gradient {
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .bg-gradient-glass {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.2) 0%, rgba(6, 182, 212, 0.2) 100%);
        border: 1px solid rgba(79, 70, 229, 0.3);
    }
    
    .radar-sonar-active {
        width: 80px;
        height: 80px;
        background: rgba(6, 182, 212, 0.2);
        border-radius: 50%;
        position: relative;
        animation: sonar 2s infinite;
    }
    .radar-sonar-active::after {
        content: '';
        position: absolute;
        top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 15px; height: 15px;
        background: #06b6d4;
        border-radius: 50%;
        box-shadow: 0 0 20px #06b6d4;
    }
    @keyframes sonar {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: scale(3); opacity: 0; }
    }
    .animate-pulse { animation: pulse 1.5s infinite; }
    @keyframes pulse {
        0% { opacity: 0.5; }
        50% { opacity: 1; }
        100% { opacity: 0.5; }
    }
    .skeleton-shimmer {
        background: linear-gradient(90deg, rgba(255,255,255,0.05) 25%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.05) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 8px;
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
</style>

<div class="bento-dashboard">
    <div class="container">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-black mb-1 tracking-tighter text-white" style="font-size: 2.5rem;">ALUMNI <span class="accent-gradient">DASHBOARD</span></h2>
                <p class="text-white-50 mb-0">Selamat datang kembali, mari bangun koneksi masa depan.</p>
            </div>
            <div class="d-none d-md-block text-end">
                <div class="d-flex align-items-center gap-2">
                    <span class="position-relative d-flex" style="width: 12px; height: 12px;">
                        <span class="animate-pulse position-absolute inline-flex h-100 w-100 rounded-circle bg-info opacity-75"></span>
                        <span class="relative inline-flex rounded-circle h-100 w-100 bg-info"></span>
                    </span>
                    <span class="small fw-bold text-info">{{ $onlineCount ?? 0 }} alumni aktif</span>
                </div>
            </div>
        </div>

        @if($user->status === 'pending')
        <div class="alert bg-warning bg-opacity-10 border border-warning text-warning rounded-4 shadow-sm mb-4">
            <h5 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Akun Belum Diverifikasi</h5>
            <p class="mb-0 small opacity-75">Anda belum bisa menggunakan fitur Forum, Pekerjaan, dan Direktori. Mohon tunggu proses validasi data Anda oleh Administrator.</p>
        </div>
        @endif

        <div class="bento-grid-wrapper">
            
            <!-- 1. Profile Highlight (Span 4) -->
            <div class="bento-card span-4 text-center d-flex flex-column justify-content-center">
                <div class="position-relative d-inline-block mx-auto mb-3 mt-2">
                    <img src="{{ $user->profile_picture_url }}" class="rounded-circle border border-2" style="border-color: rgba(255,255,255,0.2); width: 110px; height: 110px; object-fit: cover;">
                    @if($user->status !== 'pending')
                    <div class="position-absolute bottom-0 end-0 bg-info rounded-circle border border-2 border-dark" style="width: 24px; height: 24px; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-check text-dark" style="font-size: 14px; font-weight: bold;"></i>
                    </div>
                    @endif
                </div>
                <h4 class="fw-bold mb-1 text-white">{{ $user->name }}</h4>
                <p class="text-white-50 small mb-4">Angkatan {{ $user->graduation_year }} • {{ $user->major }}</p>
                
                <div class="d-flex gap-2 justify-content-center mt-auto">
                    <a href="/alumni/profile" class="btn btn-outline-light rounded-pill btn-sm px-4 fw-bold"><i class="bi bi-pencil me-1"></i> Edit Profil</a>
                    <a href="{{ route('alumni.card') }}" class="btn btn-info rounded-pill btn-sm px-4 fw-bold text-dark"><i class="bi bi-qr-code-scan me-1"></i> 3D Card</a>
                </div>
            </div>

            <!-- 2. Ecosystem / Quick Links (Span 8) -->
            <div class="bento-card span-8 bg-gradient-glass d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="fw-bold text-info text-uppercase tracking-wider mb-1 small"><i class="bi bi-rocket-takeoff me-2"></i>Steman Next-Gen</h6>
                        <h3 class="fw-bold text-white mb-0">DIGITAL ECOSYSTEM</h3>
                    </div>
                    <i class="bi bi-grid-3x3-gap-fill text-white-50 fs-3"></i>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-6 col-md-3">
                        <a href="{{ route('alumni.card') }}" class="d-block p-3 rounded-4 bg-black bg-opacity-25 border border-white border-opacity-10 text-white text-decoration-none transition-all hover-translate-y h-100 text-center">
                            <i class="bi bi-person-badge fs-2 text-warning mb-2 d-block"></i>
                            <h6 class="fw-bold mb-0 small">3D Card</h6>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('alumni.network') }}" class="d-block p-3 rounded-4 bg-black bg-opacity-25 border border-white border-opacity-10 text-white text-decoration-none transition-all hover-translate-y h-100 text-center">
                            <i class="bi bi-globe-americas fs-2 text-info mb-2 d-block"></i>
                            <h6 class="fw-bold mb-0 small">Steman Earth</h6>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('alumni.yearbook') }}" class="d-block p-3 rounded-4 bg-black bg-opacity-25 border border-white border-opacity-10 text-white text-decoration-none transition-all hover-translate-y h-100 text-center">
                            <i class="bi bi-book-half fs-2 text-purple mb-2 d-block" style="color:#7c3aed;"></i>
                            <h6 class="fw-bold mb-0 small">Buku Kenangan</h6>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('analytics.index') }}" class="d-block p-3 rounded-4 bg-black bg-opacity-25 border border-white border-opacity-10 text-white text-decoration-none transition-all hover-translate-y h-100 text-center">
                            <i class="bi bi-compass fs-2 text-primary mb-2 d-block"></i>
                            <h6 class="fw-bold mb-0 small">Navigator</h6>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. AI Insights (Span 6) -->
            <div class="bento-card span-6 d-flex flex-column">
                <h6 class="fw-bold text-white mb-3"><i class="bi bi-magic text-info me-2"></i> AI CAREER INSIGHTS</h6>
                <div id="ai-prediction-content" class="flex-grow-1">
                    <div class="skeleton-shimmer mb-2" style="height: 15px; width: 90%;"></div>
                    <div class="skeleton-shimmer mb-2" style="height: 15px; width: 70%;"></div>
                    <div class="skeleton-shimmer" style="height: 15px; width: 85%;"></div>
                </div>
                <div class="row g-2 mt-3">
                    <div id="career-snippet-content" class="col-12 d-none">
                        <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10">
                            <small class="text-white-50" id="career-snippet-text"></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Quick Stats (Span 6) -->
            <div class="bento-card span-6">
                <h6 class="fw-bold text-white mb-3"><i class="bi bi-bar-chart-fill text-warning me-2"></i> QUICK STATS</h6>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-black bg-opacity-25 border border-white border-opacity-10">
                        <span class="text-white-50 small fw-bold text-uppercase">Pekerjaan</span>
                        <span class="fw-bold text-white">{{ $user->current_job ?? 'Belum Diatur' }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-black bg-opacity-25 border border-white border-opacity-10">
                        <span class="text-white-50 small fw-bold text-uppercase">Jurusan</span>
                        <span class="fw-bold text-white">{{ $user->major }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-black bg-opacity-25 border border-white border-opacity-10">
                        <span class="text-white-50 small fw-bold text-uppercase">Tahun Lulus</span>
                        <span class="fw-bold text-white">{{ $user->graduation_year }}</span>
                    </div>
                </div>
            </div>

            <!-- 6. AI Networking Matches (Span 12) -->
            <div class="bento-card span-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-white mb-0"><i class="bi bi-people-fill text-primary me-2"></i> AI NETWORKING MATCHES</h6>
                    <a href="/alumni" class="text-info small fw-bold text-decoration-none">Lihat Semua <i class="bi bi-chevron-right"></i></a>
                </div>

                <div id="ai-recommendations-wrapper" class="d-none">
                    <div id="ai-recommendations-container" class="row g-3">
                        <!-- Recommendations injected here -->
                    </div>
                </div>
                
                <div id="ai-recommendations-skeleton" class="row g-3">
                    <div class="col-md-4"><div class="skeleton-shimmer" style="height: 160px;"></div></div>
                    <div class="col-md-4"><div class="skeleton-shimmer" style="height: 160px;"></div></div>
                    <div class="col-md-4"><div class="skeleton-shimmer" style="height: 160px;"></div></div>
                </div>
            </div>

            <!-- 7. Job Recommendations (Span 6) -->
            @if($recommendedJobs->isNotEmpty())
            <div class="bento-card span-6">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-white mb-0"><i class="bi bi-briefcase-fill text-warning me-2"></i> REKOMENDASI KARIR</h6>
                    <a href="/jobs?tab=recommended" class="text-warning small fw-bold text-decoration-none">Lihat Semua</a>
                </div>
                <div class="d-flex flex-column gap-2">
                    @foreach($recommendedJobs->take(3) as $job)
                    <a href="{{ $job->external_link ?? route('jobs.show', $job->slug) }}" target="{{ $job->external_link ? '_blank' : '_self' }}" class="text-decoration-none p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 d-flex align-items-center transition-all hover-translate-y">
                        <div class="me-3 bg-warning bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-briefcase text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold text-white mb-0" style="font-size: 0.9rem;">{{ $job->title }}</h6>
                            <p class="text-white-50 small mb-0">{{ $job->company }}</p>
                        </div>
                        <span class="badge bg-success bg-opacity-25 text-success rounded-pill">{{ $job->match_percentage }}% Match</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- 8. Latest News (Span 6) -->
            <div class="bento-card span-6">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-white mb-0"><i class="bi bi-newspaper text-danger me-2"></i> BERITA TERKINI</h6>
                    <a href="/news" class="text-danger small fw-bold text-decoration-none">Semua Berita</a>
                </div>
                <div class="row g-3">
                    @foreach($latestNews->take(2) as $item)
                    <div class="col-6">
                        <a href="/news/{{ $item->slug }}" class="d-block text-decoration-none">
                            <div class="rounded-3 overflow-hidden mb-2" style="height: 100px; background: #222;">
                                @if($item->thumbnail)
                                    <img src="{{ $item->thumbnail }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $item->title }}">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center"><i class="bi bi-image text-white-50"></i></div>
                                @endif
                            </div>
                            <h6 class="text-white fw-bold small mb-1 lh-sm" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $item->title }}</h6>
                            <span class="text-white-50" style="font-size: 0.7rem;">{{ $item->created_at->format('d/m/Y') }}</span>
                        </a>
                    </div>
                    @endforeach
                    @if($latestNews->isEmpty())
                        <div class="col-12 text-center py-3"><p class="text-white-50 small mb-0">Belum ada berita.</p></div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Force dark mode logic for dashboard if required, or ensure body has dark theme visually
    document.documentElement.classList.add('dark');

    fetch('{{ route('dashboard.ai.data') }}')
        .then(response => response.json())
        .then(data => {
            // 1. Update Prediction
            const predictionContent = document.getElementById('ai-prediction-content');
            if (data.aiPrediction) {
                predictionContent.innerHTML = `<p class="small text-white-50 mb-0 lh-lg">${data.aiPrediction}</p>`;
            } else {
                predictionContent.innerHTML = `<p class="small text-white-50 mb-0 lh-lg">Berdasarkan data profil, Anda memiliki potensi besar di bidang {{ $user->major }}. Terus tingkatkan keahlian Anda.</p>`;
            }

            // 2. Update Career Snippet
            if (data.careerSnippet) {
                document.getElementById('career-snippet-content').classList.remove('d-none');
                document.getElementById('career-snippet-text').innerHTML = `Insight: Mayoritas alumni {{ $user->major }} kini fokus pada <b>${data.careerSnippet.pekerjaan}</b>`;
            }

            // 3. Update Recommendations
            const recContainer = document.getElementById('ai-recommendations-container');
            const recWrapper = document.getElementById('ai-recommendations-wrapper');
            const recSkeleton = document.getElementById('ai-recommendations-skeleton');

            if (data.aiRecommendations && data.aiRecommendations.length > 0) {
                let html = '';
                data.aiRecommendations.forEach(rec => {
                    html += `
                    <div class="col-md-4">
                        <div class="p-3 h-100 rounded-4 border border-white border-opacity-10 bg-black bg-opacity-25 transition-all hover-translate-y d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <img src="${rec.profile_picture}" class="rounded-circle me-3 border border-secondary" width="45" height="45" style="object-fit: cover;">
                                <div>
                                    <h6 class="fw-bold mb-0 text-white" style="font-size: 0.85rem;">${rec.name}</h6>
                                    <p class="text-white-50 mb-0" style="font-size: 0.7rem;">${rec.major}</p>
                                </div>
                            </div>
                            <div class="mb-3 flex-grow-1">
                                <p class="mb-0 text-white-50" style="font-size: 0.75rem; line-height: 1.4;">
                                    <i class="bi bi-stars text-info me-1"></i> ${rec.ai_reason}
                                </p>
                            </div>
                            <a href="/alumni/${rec.id}" class="btn btn-outline-info btn-sm rounded-pill fw-bold w-100 mt-auto" style="font-size:0.75rem;">LIHAT PROFIL</a>
                        </div>
                    </div>`;
                });
                recContainer.innerHTML = html;
                recWrapper.classList.remove('d-none');
                recSkeleton.classList.add('d-none');
            } else {
                recSkeleton.classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('AI Dashboard Loading Error:', error);
            document.getElementById('ai-recommendations-skeleton').classList.add('d-none');
        });
});
</script>
@endpush
@endsection
