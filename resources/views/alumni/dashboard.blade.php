@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center p-4 bg-white" style="border-top: 5px solid #ffcc00; border-radius: 15px;">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <img src="{{ $user->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=ffcc00&color=000&size=200' }}" 
                         class="rounded-circle border border-4 border-light shadow-sm" width="120" height="120" style="object-fit: cover;">
                </div>
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted small mb-3">Angkatan {{ $user->graduation_year }} • {{ $user->major }}</p>
                <hr class="opacity-10">
                <div class="d-grid gap-2">
                    <a href="/alumni/profile" class="btn btn-outline-dark rounded-0 fw-bold py-2"><i class="bi bi-person-gear me-2"></i>EDIT PROFIL</a>
                    <a href="{{ route('alumni.card') }}" class="btn btn-warning rounded-0 fw-bold py-2 shadow-sm"><i class="bi bi-qr-code-scan me-2"></i>KARTU DIGITAL SAYA</a>
                    <a href="/alumni" class="btn btn-outline-dark rounded-0 fw-bold py-2"><i class="bi bi-person-lines-fill me-2"></i>DIREKTORI REKAN</a>
                    <a href="/alumni/messages" class="btn btn-outline-dark rounded-0 fw-bold py-2"><i class="bi bi-envelope me-2"></i>PESAN SAYA</a>
                    <a href="{{ route('alumni.business.index') }}" class="btn btn-primary rounded-0 fw-bold py-2 shadow-sm text-white mt-1"><i class="bi bi-shop me-2"></i>BISNIS ALUMNI</a>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-4 p-4 text-white" style="background: #1a1a1a; border-radius: 15px;">
                <h6 class="fw-bold mb-3" style="color: #ffcc00;"><i class="bi bi-shield-check me-2"></i>STATUS KEANGGOTAAN</h6>
                @if($user->status === 'pending')
                <div class="d-flex align-items-center mb-3">
                    <div class="h5 mb-0 fw-bold text-warning">Menunggu Verifikasi</div>
                    <i class="bi bi-hourglass-split ms-2 text-warning"></i>
                </div>
                <p class="small opacity-75 mb-0">Akses Anda masih dibatasi. Admin sedang mengecek profil Anda.</p>
                @else
                <div class="d-flex align-items-center mb-3">
                    <div class="h5 mb-0 fw-bold">Verified Member</div>
                    <i class="bi bi-check-circle-fill ms-2" style="color: #ffcc00;"></i>
                </div>
                <p class="small opacity-75 mb-0">ID: ILUNI-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                @endif
            </div>

            <!-- Phase 6: Badges & Gamification -->
            <div class="card border-0 shadow-sm mt-4 p-4 glass-card" style="border-radius: 15px;">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-patch-check-fill text-primary me-2"></i>BADGE & PENCAPAIAN</h6>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($userBadges as $badge)
                        <div class="badge-item text-center p-2 rounded-3" style="width: 80px; background: rgba(67, 97, 238, 0.05); border: 1px solid rgba(67, 97, 238, 0.1);">
                            <i class="bi {{ $badge->icon }} fs-3 text-primary d-block mb-1"></i>
                            <span class="small fw-bold text-dark" style="font-size: 0.6rem;">{{ $badge->name }}</span>
                        </div>
                    @empty
                        <p class="small text-muted mb-0 italic text-center w-100">Lengkapi profil untuk dapat badge!</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main Column -->
        <div class="col-lg-8">
            @if($user->status === 'pending')
            <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 15px; border-left: 5px solid #ffcc00 !important;">
                <h5 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Akun Belum Diverifikasi</h5>
                <p class="mb-0 small">Anda belum bisa menggunakan fitur Forum, Pekerjaan, dan Direktori. Mohon tunggu proses validasi data Anda oleh Administrator.</p>
            </div>
            @endif

            <h2 class="section-heading mt-0 mb-4">DASHBOARD ALUMNI</h2>

            <!-- NEW: Futuristic Features Section -->
            <div class="row g-3 mb-5">
                <div class="col-12">
                    <div class="p-4 rounded-4 position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white;">
                        <div class="position-relative z-1">
                            <h5 class="fw-black mb-3 text-uppercase tracking-wider" style="color: #ffcc00; font-size: 0.8rem;">STEMAN NEXT-GEN FEATURES 🛸</h5>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="p-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-10 h-100 transition-all hover-translate-y">
                                        <i class="bi bi-person-badge-fill fs-3 text-warning mb-2 d-block"></i>
                                        <h6 class="fw-bold mb-1 small">3D Digital ID</h6>
                                        <p class="opacity-50 mb-3" style="font-size: 0.65rem;">Kartu identitas futuristik dengan teknologi Glassmorphism.</p>
                                        <a href="{{ route('alumni.card') }}" class="btn btn-warning btn-sm w-100 fw-bold rounded-pill">LIHAT KARTU</a>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-10 h-100 transition-all hover-translate-y">
                                        <i class="bi bi-globe-americas fs-3 text-info mb-2 d-block"></i>
                                        <h6 class="fw-bold mb-1 small">3D Global Mesh</h6>
                                        <p class="opacity-50 mb-3" style="font-size: 0.65rem;">Visualisasi jaringan alumni Steman di seluruh dunia secara 3D.</p>
                                        <a href="{{ route('alumni.network') }}" class="btn btn-info btn-sm w-100 fw-bold rounded-pill text-white">BUKA BOLA DUNIA</a>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-10 h-100 transition-all hover-translate-y">
                                        <i class="bi bi-camera-reels-fill fs-3 text-danger mb-2 d-block"></i>
                                        <h6 class="fw-bold mb-1 small">Nostalgia Feed</h6>
                                        <p class="opacity-50 mb-3" style="font-size: 0.65rem;">Bagikan foto lama dan kenangan indah bersama teman seangkatan.</p>
                                        <a href="{{ route('nostalgia.index') }}" class="btn btn-danger btn-sm w-100 fw-bold rounded-pill">BUKA FEED</a>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-10 h-100 transition-all hover-translate-y">
                                        <i class="bi bi-compass-fill fs-3 text-primary mb-2 d-block"></i>
                                        <h6 class="fw-bold mb-1 small">Career Navigator</h6>
                                        <p class="opacity-50 mb-3" style="font-size: 0.65rem;">Prediksi cerdas jalur karir berdasarkan data riil alumni.</p>
                                        <a href="{{ route('analytics.index') }}" class="btn btn-primary btn-sm w-100 fw-bold rounded-pill">CEK ANALISA</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Abstract decoration -->
                        <div class="position-absolute end-0 top-0 translate-middle pointer-events-none opacity-10" style="width: 300px; height: 300px; background: radial-gradient(circle, #ffcc00 0%, transparent 70%);"></div>
                    </div>
                </div>
            </div>
            
            <h4 class="fw-bold mb-4 d-flex align-items-center">
                <i class="bi bi-star-fill text-warning me-2"></i> QUICK STATS
            </h4>
            <div class="row g-3 mb-5">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background: #fdfdfd; border-radius: 15px;">
                        <div class="text-muted small text-uppercase fw-bold mb-2">Pekerjaan</div>
                        <div class="h6 fw-bold mb-0 text-primary">{{ $user->current_job ?? 'Belum Diatur' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background: #fdfdfd; border-radius: 15px;">
                        <div class="text-muted small text-uppercase fw-bold mb-2">major</div>
                        <div class="h6 fw-bold mb-0">{{ $user->major }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background: #fdfdfd; border-radius: 15px;">
                        <div class="text-muted small text-uppercase fw-bold mb-2">Tahun Lulus</div>
                        <div class="h6 fw-bold mb-0">{{ $user->graduation_year }}</div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-end mb-4">
                <h4 class="fw-bold mb-0">AI PERSONALIZED PREDICTION</h4>
            </div>

            <div id="ai-prediction-container" class="card border-0 shadow-sm mb-4 glass-card p-4" style="background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%); color: white; border-radius: 20px;">
                <div class="d-flex align-items-start gap-3">
                    <div class="icon-glass bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-robot fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div id="ai-prediction-content">
                            <div class="skeleton-shimmer mb-2" style="height: 20px; width: 80%;"></div>
                            <div class="skeleton-shimmer" style="height: 20px; width: 60%;"></div>
                        </div>
                        <div id="career-snippet-content" class="mt-3 d-none">
                            <div class="p-2 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10">
                                <small class="fw-bold d-block mb-1">DATA INSIGHT:</small>
                                <small class="opacity-75" id="career-snippet-text"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Networking Recommendations -->
            <div id="ai-recommendations-wrapper" class="d-none">
                <div class="d-flex justify-content-between align-items-end mb-4 mt-5">
                    <h4 class="fw-bold mb-0">AI NETWORKING MATCHES</h4>
                    <a href="/alumni" class="text-primary small fw-bold text-decoration-none">LIHAT SEMUA <i class="bi bi-chevron-right small"></i></a>
                </div>

                <div id="ai-recommendations-container" class="row g-3 mb-5">
                    <!-- Recommendations will be injected here -->
                </div>
            </div>
            
            <div id="ai-recommendations-skeleton" class="row g-3 mb-5">
                <div class="col-md-4"><div class="skeleton-shimmer rounded-4" style="height: 180px;"></div></div>
                <div class="col-md-4"><div class="skeleton-shimmer rounded-4" style="height: 180px;"></div></div>
                <div class="col-md-4"><div class="skeleton-shimmer rounded-4" style="height: 180px;"></div></div>
            </div>

            <h4 class="fw-bold mb-4">INFORMASI KOMUNITAS</h4>

            @if($recommendedJobs->isNotEmpty())
            <div class="card border-0 shadow-sm mb-5 overflow-hidden" style="border-radius: 15px;">
                <div class="card-header bg-primary text-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-uppercase tracking-wider"><i class="bi bi-stars me-2"></i> Lowongan Sesuai major {{ $user->major }}</h6>
                    <a href="/jobs?tab=recommended" class="text-white small fw-bold text-decoration-none">LIHAT SEMUA <i class="bi bi-chevron-right small"></i></a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($recommendedJobs as $job)
                        <a href="{{ $job->external_link ?? route('jobs.show', $job->slug) }}" target="{{ $job->external_link ? '_blank' : '_self' }}" class="list-group-item list-group-item-action border-0 p-3 d-flex align-items-center transition-all">
                            <div class="me-3 bg-primary-subtle rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-briefcase text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0 text-dark">{{ $job->title }}</h6>
                                <p class="text-muted small mb-0">{{ $job->company }} · {{ $job->location }}</p>
                            </div>
                            <div class="text-end me-3">
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-bold" style="font-size: 0.7rem;">{{ $job->match_percentage }}% Match</span>
                            </div>
                            <i class="bi bi-chevron-right text-muted opacity-50"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Global Alumni Distribution Map -->
            <div class="mb-4">
                <x-alumni-map 
                    id="user-dashboard-map" 
                    :locations="$alumniLocations" 
                    :nationalCount="$nationalCount" 
                    :internationalCount="$internationalCount" 
                    height="350px"
                />
            </div>

            <div class="row g-4">
                @foreach($latestNews as $item)
                <div class="col-md-6">
                    <div class="news-card card h-100" style="border: 1px solid #eee;">
                        @if($item->thumbnail)
                            <img src="{{ $item->thumbnail }}" class="card-img-top" alt="{{ $item->title }}" loading="lazy">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="height: 150px;">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                        <div class="card-body p-3">
                            <div class="date-tag mb-1" style="font-size: 0.7rem;">{{ $item->created_at->format('d/m/Y') }}</div>
                            <h6 class="fw-bold mb-1">{{ \Illuminate\Support\Str::limit($item->title, 40) }}</h6>
                            <a href="/news/{{ $item->slug }}" class="btn btn-link link-dark fw-bold p-0 small text-decoration-none">BACA <i class="bi bi-chevron-right small"></i></a>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($latestNews->isEmpty())
                <div class="col-12 text-center py-4 bg-light rounded-3">
                    <p class="text-muted mb-0">Belum ada berita komunitas terbaru.</p>
                </div>
                @endif
            </div>

            <!-- Phase 5: Career Path Timeline -->
            <div class="card border-0 shadow-sm glass-card p-4 mb-5" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-calendar-range text-primary me-2"></i>Timeline Karir & Pendidikan</h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.7rem;">Update History</button>
                </div>
                <div class="timeline ps-3" style="border-left: 2px solid rgba(67, 97, 238, 0.2);">
                    <div class="timeline-item position-relative mb-4">
                        <div class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -27px; top: 6px; border: 3px solid #fff;"></div>
                        <h6 class="fw-bold mb-1">Sekarang</h6>
                        <p class="small text-muted mb-0">
                            {{ $user->current_job ?? 'Belum ada data pekerjaan' }}
                            @if($user->company_university) at {{ $user->company_university }} @endif
                        </p>
                    </div>
                    <div class="timeline-item position-relative">
                        <div class="position-absolute bg-secondary bg-opacity-50 rounded-circle" style="width: 12px; height: 12px; left: -27px; top: 6px; border: 3px solid #fff;"></div>
                        <h6 class="fw-bold mb-1">Tahun Lulus ({{ $user->graduation_year }})</h6>
                        <p class="small text-muted mb-0">Lulus dari SMKN 2 Ternate - major {{ $user->major }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-5 p-5 text-center bg-light" style="border-radius: 20px; border: 2px dashed #ddd;">
                <i class="bi bi-calendar-check display-4 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold">BELUM ADA AGENDA REUNI</h5>
                <p class="text-muted mb-0">Halaman ini akan diperbarui segera setelah agenda kegiatan alumni dipublikasikan oleh Pengurus.</p>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route('dashboard.ai.data') }}')
        .then(response => response.json())
        .then(data => {
            // 1. Update Prediction
            const predictionContent = document.getElementById('ai-prediction-content');
            if (data.aiPrediction) {
                predictionContent.innerHTML = `<p class="small opacity-75 mb-0">${data.aiPrediction}</p>`;
            } else {
                predictionContent.innerHTML = `<p class="small opacity-75 mb-0">Berdasarkan data profil, Anda memiliki potensi besar di bidang {{ $user->major }}. Rekomendasi: Ambil sertifikasi keahlian tambahan.</p>`;
            }

            // 2. Update Career Snippet
            if (data.careerSnippet) {
                document.getElementById('career-snippet-content').classList.remove('d-none');
                document.getElementById('career-snippet-text').innerHTML = `Mayoritas alumni {{ $user->major }} kini sukses sebagai <b>${data.careerSnippet.pekerjaan}</b>`;
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
                        <div class="card h-100 border-0 shadow-sm p-3 position-relative overflow-hidden transition-all hover-translate-y" style="border-radius: 20px; background: white;">
                            <div class="d-flex align-items-center mb-3">
                                <img src="${rec.profile_picture}" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">${rec.name}</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.7rem;">${rec.major}</p>
                                </div>
                            </div>
                            <div class="p-2 rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-10 mb-3">
                                <p class="mb-0 text-dark" style="font-size: 0.75rem; line-height: 1.4;">
                                    <i class="bi bi-magic text-primary me-1"></i> ${rec.ai_reason}
                                </p>
                            </div>
                            <a href="/alumni/${rec.id}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold w-100 mt-auto">LIHAT PROFIL</a>
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

<style>
.skeleton-shimmer {
    background: linear-gradient(90deg, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.1) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}
@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}
</style>
@endsection
