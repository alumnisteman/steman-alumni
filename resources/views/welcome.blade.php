@extends('layouts.app')

@section('content')
<style>
    /* Metode Letterbox Presisi: Terpusat & Utuh 100% */
    .hero-section.hero-banner-bg {
        position: relative;
        width: 100%;
        min-height: 450px;
        overflow: hidden;
        background-color: #000;
        display: flex; /* Gunakan Flexbox untuk centering */
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    /* Penjaga Rasio 1280x670 */
    @media (min-width: 992px) {
        .hero-section.hero-banner-bg {
            aspect-ratio: 1280 / 670 !important;
            min-height: auto;
        }
    }

    .hero-main-img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain !important;
        object-position: center center !important; /* KUNCI: Selalu di tengah */
        position: relative;
        z-index: 2;
        display: block;
    }

    .hero-blur-bg {
        position: absolute;
        inset: -50px;
        width: calc(100% + 100px);
        height: calc(100% + 100px);
        object-fit: cover;
        filter: brightness(0.3);
        z-index: 1;
        opacity: 0.8;
    }

    .hero-content-overlay {
        position: absolute;
        inset: 0;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at center, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.85) 100%);
    }

    .glass-hero-card {
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 3rem;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
</style>

<!-- Hero Section -->
<div class="hero-section text-center text-white hero-banner-bg">
    <!-- Layer 1: Blur Background -->
    <img src="{{ setting('hero_background', asset('/assets/images/hero_iluni.png')) }}" class="hero-blur-bg" alt="Blur Background">
    
    <!-- Layer 2: Main Image (Flyer) - Mengatur Tinggi Kontainer secara Otomatis -->
    <img src="{{ setting('hero_background', asset('/assets/images/hero_iluni.png')) }}" class="hero-main-img" alt="Hero Banner">

    <!-- Layer 3: Konten Text Overlay -->
    <div class="hero-content-overlay">
        <div class="container py-4">
            <div class="glass-hero-card animate__animated animate__fadeInUp">
                <div class="badge-hero mb-4 rounded-pill">Official Alumni Portal</div>
                <h1 class="display-4 display-md-2 fw-black mb-4 hero-title text-uppercase" style="letter-spacing: -1px;">{!! nl2br(e(setting('hero_title', "PENGURUS PUSAT\nIKATAN ALUMNI SMKN 2"))) !!}</h1>
                <p class="lead fw-medium mb-5 opacity-90 hero-subtitle mx-auto px-3" style="max-width: 800px; color: #cbd5e1;">{{ setting('hero_subtitle', 'MENJALIN JEJARING, MEMBANGUN KONTRIBUSI.') }}</p>
                <div class="d-flex flex-column flex-md-row justify-content-center gap-3 gap-md-4 mt-2 px-4 px-md-0">
                    <a href="/register" class="btn btn-warning border-0 fw-bold px-5 py-3 rounded-pill shadow-lg hover-lift">JOIN NOW <i class="bi bi-arrow-right ms-2"></i></a>
                    <a href="/alumni" class="btn btn-outline-light fw-bold px-5 py-3 rounded-pill hover-lift">DIRECTORY</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chairman Section -->
<div class="py-4 py-md-5" style="background-color: #f8fafc;">
    <div class="container py-2 py-md-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-4 text-center">
                <div class="position-relative d-inline-block">
                     <img src="{{ setting('chairman_photo', 'https://ui-avatars.com/api/?name=Ketua+Umum&background=ffcc00&color=000&size=400') }}" 
                          onerror="this.src='https://ui-avatars.com/api/?name=Ketua+Umum&background=ffcc00&color=000&size=400'"
                          class="img-fluid rounded-4 shadow-lg border border-5 border-white" 
                          style="aspect-ratio: 3/4; height: 280px; width: 100%; max-width: 210px; object-fit: cover; object-position: top;" 
                          alt="Ketua Umum">
                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n3">
                        <span class="badge bg-warning text-dark px-4 py-2 rounded-pill shadow-sm fw-bold">KETUA UMUM</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <h6 class="text-primary fw-bold text-uppercase mb-2">Sambutan Ketua Umum</h6>
                <h2 class="fw-bold mb-4 font-heading">{{ setting('chairman_name', 'Nama Ketua Umum') }}</h2>
                <div class="lead text-muted mb-4 italic" style="font-size: 1.1rem; line-height: 1.8;">
                    "{!! nl2br(e(setting('chairman_message', 'Selamat datang di portal resmi Ikatan Alumni SMKN 2 Ternate. Mari kita jalin silaturahmi dan berkontribusi bersama untuk almamater.'))) !!}"
                </div>
                <p class="fw-bold text-dark mb-0">Periode Jabatan:</p>
                <p class="text-muted">{{ setting('chairman_period', '2024 - 2028') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Visi & Misi Section -->
<div class="py-5 bg-white">
    <div class="container py-4 px-4 px-md-2">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-6">
                <div class="h-100 p-4 p-md-5 rounded-4 shadow-sm border border-light bg-light">
                    <div class="d-flex align-items-center mb-4 text-primary">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3">
                            <i class="bi bi-eye-fill fs-2"></i>
                        </div>
                        <h2 class="fw-black mb-0">VISI KAMI</h2>
                    </div>
                    <div class="lead text-dark opacity-75" style="line-height: 1.8; font-size: 1.1rem;">
                        {!! nl2br(e(setting('vision', 'Menjadi wadah alumni yang solid, inovatif, dan berkontribusi nyata bagi almamater serta masyarakat luas.'))) !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="h-100 p-4 p-md-5 rounded-4 shadow-sm border border-light" style="background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);">
                    <div class="d-flex align-items-center mb-4 text-warning">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-4 me-3">
                            <i class="bi bi-bullseye fs-2"></i>
                        </div>
                        <h2 class="fw-black mb-0">MISI UTAMA</h2>
                    </div>
                    <div class="text-dark opacity-75" style="line-height: 1.8; font-size: 1rem;">
                        {!! nl2br(e(setting('mission', "1. Menjalin komunikasi antar alumni di seluruh penjuru dunia.\n2. Memberikan beasiswa dan dukungan karir bagi lulusan baru.\n3. Berkontribusi dalam pengembangan sarana dan prasarana sekolah."))) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Event Leadership Duo Section (Chairman & Secretary) -->
<div class="bg-light py-4 py-md-5">
    <div class="container py-4 py-md-5">
        <div class="row g-5">
            <!-- 1. Ketua Panitia -->
            <div class="col-lg-6">
                <div class="h-100 bg-white p-4 p-md-5 rounded-4 shadow-sm border-top border-4 border-primary">
                    <div class="d-flex align-items-center mb-4">
                        <div class="position-relative me-4">
                            <img src="{{ setting('event_chairman_photo', 'https://ui-avatars.com/api/?name=Ketua+Panitia&background=007bff&color=fff&size=400') }}" 
                                 class="rounded-circle shadow-sm border border-3 border-white" 
                                 alt="Ketua Panitia"
                                 style="height: 100px; width: 100px; object-fit: cover;">
                            <div class="position-absolute bottom-0 start-50 translate-middle-x">
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1 x-small shadow-sm">KETUA</span>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-primary fw-bold text-uppercase mb-1" style="font-size: 0.8rem;">Sambutan Ketua Panitia</h6>
                            <h4 class="fw-bold mb-0">{{ setting('event_chairman_name', 'Nama Ketua Panitia') }}</h4>
                            <small class="text-muted">{{ setting('event_chairman_period', 'Event Organizers') }}</small>
                        </div>
                    </div>
                    <div class="message-content position-relative">
                        <i class="bi bi-quote fs-1 text-primary opacity-10 position-absolute top-0 start-0"></i>
                        <p class="text-muted mb-0 lh-lg" style="font-style: italic; position: relative; z-index: 1; padding-top: 10px;">
                            "{!! nl2br(e(setting('event_chairman_message', 'Pesan sambutan dalam rangka kegiatan alumni...'))) !!}"
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2. Sekretaris Panitia -->
            <div class="col-lg-6">
                <div class="h-100 bg-white p-4 p-md-5 rounded-4 shadow-sm border-top border-4 border-success">
                    <div class="d-flex align-items-center mb-4">
                        <div class="position-relative me-4">
                            <img src="{{ setting('secretary_photo', 'https://ui-avatars.com/api/?name=Sekretaris&background=28a745&color=fff&size=400') }}" 
                                 class="rounded-circle shadow-sm border border-3 border-white" 
                                 alt="Sekretaris Panitia"
                                 style="height: 100px; width: 100px; object-fit: cover;">
                            <div class="position-absolute bottom-0 start-50 translate-middle-x">
                                <span class="badge bg-success text-white rounded-pill px-2 py-1 x-small shadow-sm">SEKRETARIS</span>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-success fw-bold text-uppercase mb-1" style="font-size: 0.8rem;">Sambutan Sekretaris Panitia</h6>
                            <h4 class="fw-bold mb-0">{{ setting('secretary_name', 'Nama Sekretaris') }}</h4>
                            <small class="text-muted">{{ setting('secretary_period', 'Event Committee') }}</small>
                        </div>
                    </div>
                    <div class="message-content position-relative">
                        <i class="bi bi-quote fs-1 text-success opacity-10 position-absolute top-0 start-0"></i>
                        <p class="text-muted mb-0 lh-lg" style="font-style: italic; position: relative; z-index: 1; padding-top: 10px;">
                            "{!! nl2br(e(setting('secretary_message', 'Pesan sambutan sekretaris dalam rangka persiapan kegiatan alumni...'))) !!}"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
</div>

<!-- Global Alumni Network Map -->
<div class="container py-4 py-md-5">
    <div class="text-center mb-5">
        <h6 class="text-primary fw-bold text-uppercase mb-2">Jejaring Global</h6>
        <h2 class="fw-black mb-0">PERSEBARAN ALUMNI</h2>
        <div class="section-divider mx-auto mt-3"></div>
        <p class="text-muted mx-auto" style="max-width: 600px;">Lulusan kami telah tersebar di berbagai instansi nasional maupun perusahaan internasional, membangun masa depan yang cerah di seluruh dunia.</p>
    </div>
    
    <x-alumni-map 
        id="home-global-map" 
        :locations="$alumniLocations" 
        :nationalCount="$nationalCount" 
        :internationalCount="$internationalCount" 
        height="500px"
    />
</div>

<!-- AI Insights Section -->
<div class="py-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white;">
    <div class="container py-4">
        <div class="row align-items-center mb-5">
            <div class="col-lg-7">
                <div class="d-flex align-items-center mb-3">
                    <div class="pulse-container me-3">
                        <div class="pulse-ring"></div>
                        <i class="bi bi-cpu-fill fs-4 text-warning"></i>
                    </div>
                    <h6 class="text-warning fw-bold text-uppercase mb-0">Smart Analytics</h6>
                </div>
                <h2 class="display-5 fw-black mb-3">AI ALUMNI INSIGHTS</h2>
                <p class="lead opacity-75">Algoritma cerdas kami menganalisis data besar alumni untuk memprediksi tren kolaborasi dan kegiatan masa depan.</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-stars me-1"></i> Powering NextGen Networking
                </div>
            </div>
        </div>

        <div class="row g-4">
            @foreach($aiInsights as $key => $insight)
                <div class="col-md-4">
                    <div class="card h-100 border-0 bg-white bg-opacity-25 rounded-4 p-4 shadow-lg ai-card">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="ai-icon-box bg-warning bg-opacity-20 text-warning rounded-3 p-3">
                                <i class="bi {{ $insight['icon'] }} fs-3"></i>
                            </div>
                            <div class="text-end">
                                <small class="d-block opacity-50">Confidence</small>
                                <span class="fw-bold text-warning">{{ $insight['confidence'] }}</span>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3">{{ $insight['title'] }}</h4>
                        <p class="opacity-75 mb-0" style="font-size: 0.95rem; line-height: 1.6;">{{ $insight['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .ai-card { transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.1) !important; }
    .ai-card:hover { 
        transform: translateY(-10px); 
        background-color: rgba(255,255,255,0.15) !important;
        border-color: rgba(255,193,7,0.4) !important;
    }
    .pulse-container { position: relative; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; }
    .pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: #ffc107;
        animation: pulse-ring 2s cubic-bezier(0.25, 1, 0.5, 1) infinite;
    }
    @keyframes pulse-ring {
        0% { transform: scale(.33); opacity: 1; }
        80%, 100% { transform: scale(1.5); opacity: 0; }
    }
</style>

<!-- News Section -->
<div class="container py-4 py-md-5 mt-2 mt-md-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end mb-4 gap-3">
        <div>
            <h2 class="fw-black mb-0">BERITA TERBARU</h2>
            <p class="text-muted small">Update info kegiatan alumni</p>
        </div>
        <a href="/news" class="btn btn-primary btn-sm rounded-pill px-4">Lihat Semua</a>
    </div>
    <div class="row g-4">
        @forelse($latestNews as $item)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden hover-lift">
                    <img src="{{ $item->thumbnail ?? 'https://via.placeholder.com/400x250' }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $item->title }}" loading="lazy">
                    <div class="card-body p-4">
                        <small class="text-primary fw-bold d-block mb-2">{{ $item->created_at->format('d M Y') }}</small>
                        <h5 class="fw-bold mb-3"><a href="/news/{{ $item->slug }}" class="text-dark text-decoration-none hover-text-primary">{{ $item->title }}</a></h5>
                        <p class="text-muted small mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($item->content), 90) }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">Belum ada berita terbaru.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Loker Section -->
<div class="bg-light py-4 py-md-5">
    <div class="container py-2 py-md-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end mb-4 gap-3">
            <div>
                <h2 class="fw-black mb-0">LOWONGAN KERJA</h2>
                <p class="text-muted small">Peluang karir khusus alumni</p>
            </div>
            <a href="/jobs" class="btn btn-outline-primary btn-sm rounded-pill px-4">Cek Semua Loker</a>
        </div>
        <div class="row g-4">
            @forelse($latestJobs ?? [] as $job)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 hover-lift">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4">
                                <i class="bi bi-briefcase-fill fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="badge bg-secondary mb-2 rounded-pill">{{ $job->type }}</div>
                                <h5 class="fw-bold mb-1">{{ $job->title }}</h5>
                                <p class="text-muted small mb-3"><i class="bi bi-building me-1"></i> {{ $job->company }} | <i class="bi bi-geo-alt me-1"></i> {{ $job->location }}</p>
                                <a href="/jobs/{{ $job->slug }}" class="btn btn-link text-primary p-0 text-decoration-none fw-bold hover-translate-x">Lihat Detail <i class="bi bi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4">
                    <p class="text-muted">Belum ada lowongan kerja tersedia.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Gallery Section -->
<div class="container py-4 py-md-5 mt-2 mt-md-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end mb-4 gap-3">
        <div>
            <h2 class="fw-black mb-0">GALERI STEMAN</h2>
            <p class="text-muted small">Moment kebersamaan alumni</p>
        </div>
        <a href="/gallery" class="btn btn-outline-dark btn-sm rounded-pill px-4">Buka Galeri</a>
    </div>
    <div class="row g-3">
        @foreach($latestPhotos as $photo)
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <img src="{{ $photo->file_path }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $photo->title }}" loading="lazy">
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Success Stories / Hall of Fame -->
<div class="py-5" style="background-color: #fff;">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h6 class="text-primary fw-bold text-uppercase mb-2">Inspirasi Steman</h6>
            <h2 class="fw-black mb-0">JEJAK SUKSES ALUMNI</h2>
            <div class="section-divider mx-auto mt-3"></div>
            <p class="text-muted mx-auto" style="max-width: 600px;">Kisah nyata perjuangan dan pencapaian alumni yang membanggakan almamater di kancah nasional maupun internasional.</p>
        </div>

        <div class="row g-4">
            @forelse($successStories as $story)
            <div class="col-md-4">
                <a href="{{ \Illuminate\Support\Facades\Route::has('success-stories.show') ? route('success-stories.show', $story->id) : '#' }}" class="text-decoration-none card-link h-100">
                    <div class="card h-100 border-0 shadow-lg rounded-4 overflow-hidden hover-lift success-card">
                        <div class="success-img-wrapper">
                            <img src="{{ $story->image_path ? asset('storage/'.$story->image_path) : 'https://ui-avatars.com/api/?name='.urlencode($story->name).'&background=ffcc00&color=000&size=500' }}" 
                                 class="card-img-top h-100" style="object-fit: cover;" alt="{{ $story->name }}">
                            <div class="success-overlay p-4">
                                <span class="badge bg-warning text-dark mb-2">{{ $story->title }}</span>
                                <h5 class="fw-bold text-white mb-0">{{ $story->name }}</h5>
                                <small class="text-white opacity-75">{{ $story->major_year }}</small>
                            </div>
                        </div>
                        <div class="card-body p-4 bg-white">
                            <p class="text-muted italic mb-0" style="font-size: 0.9rem;">"{{ \Illuminate\Support\Str::limit($story->quote, 120) }}"</p>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">Belum ada kisah sukses yang ditonjolkan.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .card-link { display: block; }
    .success-card { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .success-img-wrapper { position: relative; height: 320px; overflow: hidden; }
    .success-overlay {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.3) 50%, transparent 100%);
        z-index: 2;
    }
    .success-card:hover { transform: translateY(-15px); }
</style>

<!-- Join Section -->
<div class="bg-dark text-white py-5 mt-5">
    <div class="container py-5 text-center">
        <h2 class="display-5 fw-black mb-4" style="color: #ffcc00;">BELUM TERGABUNG?</h2>
        <p class="lead mb-5 opacity-75 mx-auto" style="max-width: 800px;">Mari perkuat jaringan alumni {{ setting('school_name', 'SMKN 2 Ternate') }}. Daftarkan diri Anda sekarang untuk mendapatkan info lowongan kerja, networking, dan program pengembangan diri lainnya.</p>
        <a href="/register" class="btn btn-warning border-0 fw-bold px-5 py-3 rounded-pill shadow-lg">GABUNG SEKARANG</a>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .hero-title { 
        letter-spacing: -2px; 
        line-height: 1;
        text-shadow: 2px 2px 20px rgba(0,0,0,0.5);
    }
    .badge-hero {
        background: #ffcc00;
        color: #000;
        display: inline-block;
        padding: 8px 20px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.75rem;
    }
    .btn-hero {
        background: #ffcc00;
        color: #000;
        transition: all 0.3s ease;
    }
    .btn-hero:hover {
        background: #e6b800;
        transform: translateY(-5px);
    }
    .btn-hero-outline {
        border: 2px solid #fff;
        transition: all 0.3s ease;
    }
    .btn-hero-outline:hover {
        background: #fff;
        color: #000;
        transform: translateY(-5px);
    }
    .shadow-hover { transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease; }
    .shadow-hover:hover { transform: translateY(-10px); box-shadow: 0 1.5rem 4rem rgba(0,0,0,.15)!important; }
    .font-heading { font-family: 'Inter', sans-serif; letter-spacing: -0.5px; }
    
    .section-divider {
        width: 60px;
        height: 4px;
        background: #ffcc00;
        margin-bottom: 2rem;
    }
</style>
@endsection
