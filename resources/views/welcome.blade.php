@extends('layouts.app')

@section('content')
<style>
    /* Premium Dark Mode Bento Grid for Landing Page */
    .landing-bento {
        background-color: #050505;
        color: #f8fafc;
        font-family: 'Inter', sans-serif;
        overflow-x: hidden;
        position: relative;
    }

    /* FIX NAVBAR CLASH */
    .navbar, .mobile-header, .top-bar { 
        background: rgba(5, 5, 5, 0.8) !important; 
        backdrop-filter: blur(10px) !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
    }
    .navbar .nav-link, .navbar-brand, .mobile-header a, .mobile-header i { 
        color: #fff !important; 
    }
    .mobile-bottom-nav {
        background: #0a0a0a !important;
        border-top-color: rgba(255, 255, 255, 0.05) !important;
    }

    /* CUSTOM GLOWING CURSOR - Only active on desktop and within bento context */
    @media (min-width: 992px) {
        .landing-bento { cursor: none; }
        .custom-cursor {
            position: fixed; top: 0; left: 0; width: 20px; height: 20px;
            border-radius: 50%; pointer-events: none; z-index: 9999;
            background: #06b6d4; mix-blend-mode: screen;
            transform: translate(-50%, -50%); transition: width 0.2s, height 0.2s, background-color 0.2s;
            box-shadow: 0 0 20px #06b6d4, 0 0 40px #06b6d4;
            opacity: 0; /* Hidden until mouse moves */
        }
        .custom-cursor-trail {
            position: fixed; top: 0; left: 0; width: 40px; height: 40px;
            border-radius: 50%; pointer-events: none; z-index: 9998;
            border: 1px solid rgba(6, 182, 212, 0.5);
            transform: translate(-50%, -50%); transition: all 0.1s ease-out;
            opacity: 0;
        }
        .cursor-hover { width: 60px; height: 60px; background: rgba(99, 102, 241, 0.5); box-shadow: 0 0 30px #4f46e5; border: none; }
    }
    @media (max-width: 991px) {
        .custom-cursor, .custom-cursor-trail { display: none !important; }
        .landing-bento { cursor: auto !important; }
    }

    /* Hero Section Specifics */
    .hero-bento {
        position: relative; width: 100%; min-height: 90vh;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        background-color: #000;
    }
    #particleCanvas {
        position: absolute; inset: 0; width: 100%; height: 100%;
        z-index: 1; pointer-events: none;
    }
    .hero-overlay {
        position: absolute; inset: 0; z-index: 2;
        background: radial-gradient(circle at center, transparent 0%, #050505 100%);
    }
    .hero-content { position: relative; z-index: 10; text-align: center; }

    /* Grid System */
    .bento-grid-wrapper {
        display: grid; grid-template-columns: repeat(12, 1fr); gap: 1.5rem; padding: 3rem 0;
    }
    @media (max-width: 991px) {
        .bento-grid-wrapper { display: flex; flex-direction: column; gap: 1rem; }
        .span-8, .span-6, .span-4, .span-3 { grid-column: span 12 !important; width: 100%; }
    }

    /* SPOTLIGHT EFFECT CARDS */
    .bento-card {
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 32px; padding: 2rem;
        position: relative; overflow: hidden;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    /* The spotlight pseudo-element */
    .bento-card::before {
        content: ""; position: absolute; inset: 0; border-radius: inherit;
        background: radial-gradient(
            800px circle at var(--mouse-x) var(--mouse-y),
            rgba(255, 255, 255, 0.06),
            transparent 40%
        );
        z-index: 0; opacity: 0; transition: opacity 0.5s; pointer-events: none;
    }
    .bento-card:hover::before { opacity: 1; }

    /* The glowing border spotlight pseudo-element */
    .bento-card::after {
        content: ""; position: absolute; inset: 0; border-radius: inherit;
        padding: 1px; /* border thickness */
        background: radial-gradient(
            400px circle at var(--mouse-x) var(--mouse-y),
            rgba(6, 182, 212, 0.5),
            transparent 40%
        );
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        z-index: 0; opacity: 0; transition: opacity 0.5s; pointer-events: none;
    }
    .bento-card:hover::after { opacity: 1; }

    .bento-card > * { position: relative; z-index: 1; }
    .bento-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.6); }

    /* Spans */
    .span-12 { grid-column: span 12; }
    .span-8 { grid-column: span 8; }
    .span-6 { grid-column: span 6; }
    .span-4 { grid-column: span 4; }
    .span-3 { grid-column: span 3; }

    /* Utilities */
    .fw-black { font-weight: 900; }
    .tracking-tighter { letter-spacing: -0.05em; }
    .tracking-widest { letter-spacing: 0.2em; }
    .text-cyan { color: #06b6d4; }
    .text-gold { color: #ffcc00; }
    
    .hover-lift { transition: transform 0.3s; }
    .hover-lift:hover { transform: translateY(-3px); }

    .extra-small { font-size: 0.75rem; }

    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    
    .activity-scroll::-webkit-scrollbar { width: 4px; }
    .activity-scroll::-webkit-scrollbar-track { background: transparent; }
    .activity-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    /* Online Avatars Animation */
    .avatar-stack { display: flex; align-items: center; }
    .avatar-stack img { 
        width: 32px; height: 32px; border-radius: 50%; border: 2px solid #050505; 
        margin-left: -12px; transition: transform 0.3s;
    }
    .avatar-stack img:first-child { margin-left: 0; }
    .avatar-stack img:hover { transform: translateY(-5px); z-index: 10; }

    @keyframes rotate-avatars {
        0% { opacity: 0; transform: scale(0.8); }
        10% { opacity: 1; transform: scale(1); }
        90% { opacity: 1; transform: scale(1); }
        100% { opacity: 0; transform: scale(0.8); }
    }
    .online-avatar-cycle {
        width: 40px; height: 40px; border-radius: 50%; border: 2px solid #06b6d4;
        object-fit: cover; animation: rotate-avatars 5s infinite;
    }

    .pulse-avatar { animation: avatar-pulse 2s infinite; }
    @keyframes avatar-pulse {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    /* GSAP reveal classes */
    .gsap-fade-up { opacity: 0; }
    .gsap-scroll-card { opacity: 0; }
</style>

<div class="landing-bento" id="main-grid">
    <!-- Custom Cursor -->
    <div class="custom-cursor"></div>
    <div class="custom-cursor-trail"></div>

    <!-- Hero Section -->
    <section class="hero-bento">
        <canvas id="particleCanvas"></canvas>
        <div class="hero-overlay"></div>
        
        <div class="container hero-content">
            <div class="gsap-fade-up">
                <h6 class="text-cyan fw-bold text-uppercase tracking-widest mb-3">Selamat Datang di Portal Alumni</h6>
                <h1 class="display-1 fw-black tracking-tighter text-white mb-4" style="line-height: 0.9;">
                    CONNECT. <span class="text-cyan">INSPIRE.</span><br>CONTRIBUTE.
                </h1>
                <p class="lead text-white-50 mx-auto mb-5" style="max-width: 600px;">
                    Satu wadah untuk ribuan jejak. Kembali, berbagi, dan bangun masa depan bersama almamater tercinta.
                </p>
                
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-cyan btn-lg px-5 py-3 rounded-pill fw-bold text-dark magnetic-el">GABUNG SEKARANG</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-white btn-lg px-5 py-3 rounded-pill fw-bold magnetic-el">MASUK PORTAL</a>
                    @else
                        <a href="{{ route('alumni.dashboard') }}" class="btn btn-cyan btn-lg px-5 py-3 rounded-pill fw-bold text-dark magnetic-el">DASHBOARD SAYA</a>
                        <a href="{{ route('public.profile') }}" class="btn btn-outline-white btn-lg px-5 py-3 rounded-pill fw-bold magnetic-el">PROFIL DIGITAL</a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <div class="container pb-5">
        <div class="bento-grid-wrapper">
            
            <!-- Quick Stats -->
            <div class="span-4 gsap-scroll-card">
                <div class="bento-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="bg-cyan bg-opacity-20 p-3 rounded-4 position-relative">
                                <i class="bi bi-people-fill text-cyan fs-2"></i>
                                <span class="position-absolute top-0 start-100 translate-middle p-2 bg-success border border-light rounded-circle shadow-sm" style="animation: pulse-green 2s infinite;"></span>
                            </div>
                            <div class="text-end">
                                <span class="text-white-50 small d-block">Global Network</span>
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    @php 
                                        $displayAvatars = !empty($onlineAvatars) ? $onlineAvatars : ($featuredAvatars ?? []);
                                        $isOnline = !empty($onlineAvatars);
                                    @endphp
                                    
                                    @if(!empty($displayAvatars))
                                        <div class="avatar-cycle-container" style="width: 40px; height: 40px; position: relative;">
                                            <img id="online-avatar-img" src="{{ $displayAvatars[0] }}" 
                                                 class="rounded-circle border border-2 {{ $isOnline ? 'border-success' : 'border-cyan' }} shadow-sm {{ $isOnline ? 'pulse-avatar' : '' }}" 
                                                 style="width: 40px; height: 40px; object-fit: cover; transition: all 0.5s ease;">
                                        </div>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const avatars = @json($displayAvatars);
                                                if (avatars.length > 1) {
                                                    let current = 0;
                                                    const img = document.getElementById('online-avatar-img');
                                                    setInterval(() => {
                                                        img.style.opacity = '0';
                                                        img.style.transform = 'scale(0.8)';
                                                        setTimeout(() => {
                                                            current = (current + 1) % avatars.length;
                                                            img.src = avatars[current];
                                                            img.style.opacity = '1';
                                                            img.style.transform = 'scale(1)';
                                                        }, 500);
                                                    }, 4000);
                                                }
                                            });
                                        </script>
                                    @endif
                                    
                                    @if($isOnline)
                                        <span class="badge bg-success bg-opacity-10 text-success small fw-bold">
                                            <span class="pulse-dot me-1" style="background: #22c55e;"></span>
                                            {{ $onlineCount }} ONLINE
                                        </span>
                                    @else
                                        <span class="badge bg-cyan bg-opacity-10 text-cyan small fw-bold">
                                            ALUMNI TERDAFTAR
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <h2 class="display-5 fw-black text-white mb-1">{{ $totalAlumni ?? 1250 }}</h2>
                        <p class="text-white-50">Alumni Terdaftar</p>
                    </div>
                    <div class="mt-4">
                        <div class="progress bg-white bg-opacity-10" style="height: 6px; border-radius: 3px;">
                            <div class="progress-bar bg-cyan" style="width: 85%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- News Spotlight -->
            <div class="span-8 gsap-scroll-card">
                <div class="bento-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-black text-white m-0">KABAR TERKINI</h4>
                        <a href="{{ route('news.index') }}" class="text-cyan text-decoration-none small fw-bold magnetic-el">LIHAT SEMUA <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="row g-4">
                        @foreach($latestNews ?? [] as $news)
                        <div class="col-md-6">
                            <a href="{{ route('news.show', $news->slug) }}" class="text-decoration-none group">
                                <div class="rounded-4 overflow-hidden mb-3" style="height: 160px;">
                                    <img src="{{ $news->thumbnail ? (Str::startsWith($news->thumbnail, 'http') ? $news->thumbnail : asset($news->thumbnail)) : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=2070&auto=format&fit=crop' }}" class="w-100 h-100 object-fit-cover hover-lift" alt="News">
                                </div>
                                <h6 class="text-white fw-bold line-clamp-2">{{ $news->title }}</h6>
                                <span class="text-white-50 small">{{ $news->created_at->diffForHumans() }}</span>
                            </a>
                        </div>
                        @endforeach
                        @if($latestNews->isEmpty())
                            <div class="text-center py-5 w-100">
                                <p class="text-white-50">Belum ada berita terbaru.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Success Stories (Interactive) -->
            <div class="span-8 gsap-scroll-card">
                <div class="bento-card" style="min-height: 400px; background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(5, 5, 5, 1) 100%);">
                    <div class="row h-100 align-items-center">
                        <div class="col-lg-7">
                            <h6 class="text-gold fw-bold mb-3">JEJAK SUKSES</h6>
                            <h2 class="display-5 fw-black text-white mb-4">Inspirasi Dari<br>Para Pemenang.</h2>
                            <p class="text-white-50 mb-5">
                                Simak perjalanan karir alumni {{ setting('school_name', 'SMKN 2 Ternate') }} yang telah berhasil menembus pasar global dan industri ternama.
                            </p>
                            <a href="{{ route('success_stories.index') }}" class="btn btn-gold btn-lg px-5 py-3 rounded-pill fw-bold text-dark magnetic-el">BACA KISAH MEREKA</a>
                        </div>
                        <div class="col-lg-5 d-none d-lg-block">
                            <div class="position-relative">
                                @foreach($successStories ?? [] as $index => $story)
                                    <div class="bento-card position-absolute shadow-lg" style="top: {{ $index * 20 }}px; left: {{ $index * 20 }}px; transform: rotate({{ $index * 2 }}deg); width: 100%;">
                                        <div class="d-flex gap-3 align-items-center">
                                            <img src="{{ $story->image_path ? asset('storage/'.$story->image_path) : 'https://ui-avatars.com/api/?name='.urlencode($story->name) }}" class="rounded-circle" width="50" height="50">
                                            <div>
                                                <h6 class="text-white fw-bold mb-0">{{ $story->name }}</h6>
                                                <small class="text-cyan">{{ $story->title }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Events & Maps -->
            <div class="span-4 gsap-scroll-card">
                <div class="bento-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h4 class="fw-black text-white mb-4">ALUMNI MAP</h4>
                        <div class="bg-dark rounded-4 p-4 border border-white border-opacity-5 text-center mb-4">
                            <i class="bi bi-geo-alt text-cyan display-4"></i>
                        </div>
                        <p class="text-white-50 small">Pantau persebaran alumni SMKN 2 Ternate di seluruh penjuru dunia secara real-time.</p>
                    </div>
                    <a href="{{ route('global.network') }}" class="btn btn-outline-white w-100 rounded-pill py-3 fw-bold magnetic-el">BUKA PETA</a>
                </div>
            </div>

            <!-- Job Board Preview -->
            <div class="span-8 gsap-scroll-card">
                <div class="bento-card h-100">
                    <div class="row align-items-center">
                        <div class="col-lg-4">
                            <h4 class="fw-black text-white mb-2">PELUANG KARIR</h4>
                            <p class="text-white-50 small">Eksklusif untuk alumni Steman. Temukan karir impian Anda di sini.</p>
                            <a href="{{ route('jobs.index') }}" class="btn btn-cyan btn-sm px-4 py-2 rounded-pill fw-bold text-dark mt-3 magnetic-el">EKSPLORASI</a>
                        </div>
                        <div class="col-lg-8">
                            <div class="d-flex flex-column gap-2">
                                @forelse($latestJobs ?? [] as $job)
                                    <a href="/jobs/{{ $job->slug }}" class="d-flex gap-3 text-decoration-none p-2 rounded-4 hover-lift border border-white border-opacity-10 magnetic-el" style="background: rgba(255, 255, 255, 0.05);">
                                        <div class="bg-success bg-opacity-25 text-success rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                            <i class="bi bi-building fs-5"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <h6 class="text-white fw-bold mb-0 small text-truncate">{{ $job->title }}</h6>
                                            <p class="text-white-50 extra-small mb-0 text-truncate">{{ $job->company }}</p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-4 text-white-50">Belum ada lowongan.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LIVE ACTIVITY FEED (Transparency) -->
            <div class="span-4 gsap-scroll-card">
                <div class="bento-card h-100" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.2);">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-black text-white m-0 small tracking-widest text-uppercase"><i class="bi bi-broadcast text-success me-2"></i>LIVE NETWORK ACTIVITY</h5>
                        <div class="spinner-grow spinner-grow-sm text-success" role="status"></div>
                    </div>
                    <div class="activity-scroll pe-2" style="max-height: 280px; overflow-y: auto;">
                        <div class="d-flex flex-column gap-3">
                            @forelse($recentActivities ?? [] as $activity)
                                <div class="d-flex gap-3 align-items-start border-bottom border-white border-opacity-5 pb-3">
                                    <img src="{{ $activity->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($activity->user->name ?? 'Guest') }}" 
                                         class="rounded-circle mt-1" width="32" height="32" style="object-fit: cover;">
                                    <div>
                                        <p class="text-white small fw-bold mb-0">{{ $activity->user->name ?? 'User' }}</p>
                                        <p class="text-white-50 extra-small mb-1">{{ $activity->description }}</p>
                                        <span class="text-success extra-small fw-bold">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-white-50 small text-center py-4">Belum ada aktivitas terbaru.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Insights (Futuristic) -->
            <div class="span-4 gsap-scroll-card">
                <div class="bento-card h-100" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(5, 5, 5, 1) 100%);">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="spinner-grow spinner-grow-sm text-cyan" role="status"></div>
                        <span class="text-cyan small fw-bold tracking-widest">AI PREDICTION</span>
                    </div>
                    
                    @if(!empty($aiInsights))
                        @php $insight = $aiInsights[array_rand($aiInsights)]; @endphp
                        <h5 class="text-white fw-black mb-3">{{ $insight['title'] ?? 'Wawasan Masa Depan' }}</h5>
                        <p class="text-white-50 small mb-4">
                            {{ $insight['description'] ?? 'AI sedang menganalisis tren karir alumni terbaru...' }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-white bg-opacity-10 rounded-pill text-cyan">Confidence: {{ $insight['confidence'] ?? '90%' }}</span>
                            <i class="bi {{ $insight['icon'] ?? 'bi-cpu' }} text-white fs-3"></i>
                        </div>
                    @else
                        <h5 class="text-white fw-black mb-3">Menganalisis Data...</h5>
                        <p class="text-white-50 small">Sistem sedang memproses algoritma networking untuk Anda.</p>
                    @endif
                </div>
            </div>

            <!-- Photo Gallery (Masonry Style) -->
            <div class="span-12 gsap-scroll-card">
                <div class="bento-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-black text-white m-0">LENSA NOSTALGIA</h4>
                            <p class="text-white-50 small m-0">Momen berharga di setiap sudut sekolah.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('ar.scanner') }}" class="btn btn-warning btn-sm px-4 py-2 rounded-pill fw-bold text-dark magnetic-el"><i class="bi bi-camera-fill me-1"></i> WebAR NOSTALGIA</a>
                            <a href="{{ route('gallery.index') }}" class="text-cyan text-decoration-none small fw-bold magnetic-el d-flex align-items-center">LIHAT GALERI <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        @forelse($latestPhotos ?? [] as $index => $photo)
                            <div class="{{ $index == 0 ? 'col-md-6' : 'col-md-3' }}">
                                <div class="rounded-4 overflow-hidden position-relative group" style="height: 250px;">
                                    <img src="{{ asset(ltrim($photo->file_path, '/')) }}" class="w-100 h-100 object-fit-cover hover-lift" alt="Gallery">
                                    <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-gradient-dark opacity-0 group-hover-opacity-100 transition-all">
                                        <p class="text-white small m-0">{{ $photo->title }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5 text-white-50">Belum ada foto galeri.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // 1. Force Dark Mode Theme
    document.documentElement.classList.add('dark');
    document.documentElement.setAttribute('data-bs-theme', 'dark');

    // 2. Custom Cursor & Magnetic Elements
    const cursor = document.querySelector('.custom-cursor');
    const cursorTrail = document.querySelector('.custom-cursor-trail');
    const magnetics = document.querySelectorAll('.magnetic-el');
    
    if (window.matchMedia("(any-hover: hover)").matches && cursor) {
        let mouseX = 0, mouseY = 0;
        let trailX = 0, trailY = 0;
        
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
            
            cursor.style.opacity = "1";
            cursorTrail.style.opacity = "1";
            
            gsap.to(cursor, { x: mouseX, y: mouseY, duration: 0 });
        });

        // Spring physics for trail
        const animateTrail = () => {
            trailX += (mouseX - trailX) * 0.15;
            trailY += (mouseY - trailY) * 0.15;
            
            cursorTrail.style.x = trailX;
            cursorTrail.style.y = trailY;
            
            gsap.to(cursorTrail, { x: trailX, y: trailY, duration: 0 });
            requestAnimationFrame(animateTrail);
        };
        animateTrail();

        // Magnetic hover logic
        magnetics.forEach(el => {
            el.addEventListener('mouseenter', () => {
                cursor.classList.add('cursor-hover');
                cursorTrail.style.opacity = '0';
            });
            el.addEventListener('mousemove', (e) => {
                const rect = el.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                gsap.to(el, { x: x * 0.3, y: y * 0.3, duration: 0.3, ease: "power2.out" });
            });
            el.addEventListener('mouseleave', () => {
                cursor.classList.remove('cursor-hover');
                cursorTrail.style.opacity = '1';
                gsap.to(el, { x: 0, y: 0, duration: 0.5, ease: "elastic.out(1, 0.3)" });
            });
        });
    }

    // 3. Spotlight Cards Hover Effect
    for(const card of document.getElementsByClassName("bento-card")) {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect(),
                  x = e.clientX - rect.left,
                  y = e.clientY - rect.top;
            card.style.setProperty("--mouse-x", `${x}px`);
            card.style.setProperty("--mouse-y", `${y}px`);
        });
    }

    // 4. GSAP Scroll Animations
    gsap.registerPlugin(ScrollTrigger);

    // Hero timeline
    const tl = gsap.timeline();
    tl.fromTo(".gsap-fade-up", 
        { y: 50, opacity: 0 },
        { y: 0, opacity: 1, duration: 1, stagger: 0.2, ease: "power3.out" }
    );

    // Scroll reveal for cards
    gsap.utils.toArray('.gsap-scroll-card').forEach((card, i) => {
        gsap.fromTo(card, 
            { y: 50, opacity: 0, scale: 0.95 },
            { 
                y: 0, opacity: 1, scale: 1, duration: 0.8, ease: "power3.out",
                scrollTrigger: {
                    trigger: card,
                    start: "top 85%",
                    toggleActions: "play none none reverse"
                }
            }
        );
    });

    // 5. Interactive Particle Mesh
    const canvas = document.getElementById('particleCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let particles = [];
        let mouse = { x: -1000, y: -1000, radius: 150 };

        const resizeCanvas = () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            initParticles();
        };

        window.addEventListener('resize', resizeCanvas);
        
        // Follow mouse ONLY if hover is supported
        if (window.matchMedia("(any-hover: hover)").matches) {
            document.addEventListener('mousemove', (e) => {
                // Avoid getBoundingClientRect on every move
                // Instead, use page coordinates
                const rect = canvas.parentElement.getBoundingClientRect();
                if (rect.bottom > 0) { // Only calculate if hero is visible
                    mouse.x = e.clientX - rect.left;
                    mouse.y = e.clientY - rect.top;
                }
            });
        }

        class Particle {
            constructor(x, y) {
                this.x = x;
                this.y = y;
                this.baseX = x;
                this.baseY = y;
                this.size = Math.random() * 1.5 + 0.5;
                this.vx = (Math.random() - 0.5) * 0.5;
                this.vy = (Math.random() - 0.5) * 0.5;
            }
            update() {
                this.x += this.vx;
                this.y += this.vy;

                // Bounce off edges
                if (this.x < 0 || this.x > canvas.width) this.vx *= -1;
                if (this.y < 0 || this.y > canvas.height) this.vy *= -1;

                // Mouse interaction
                let dx = mouse.x - this.x;
                let dy = mouse.y - this.y;
                let distance = Math.sqrt(dx * dx + dy * dy);
                if (distance < mouse.radius) {
                    const force = (mouse.radius - distance) / mouse.radius;
                    this.x -= dx * force * 0.03;
                    this.y -= dy * force * 0.03;
                }
            }
            draw() {
                ctx.fillStyle = 'rgba(6, 182, 212, 0.6)'; // Cyan glow
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        const initParticles = () => {
            particles = [];
            let particleCount = (canvas.width * canvas.height) / 10000;
            if (particleCount > 150) particleCount = 150; // Cap for performance
            
            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle(Math.random() * canvas.width, Math.random() * canvas.height));
            }
        };

        let animationId;
        let isVisible = true;

        const animateParticles = () => {
            if (!isVisible) return; // Pause animation when not visible

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();

                // Connect nodes
                for (let j = i; j < particles.length; j++) {
                    let dx = particles[i].x - particles[j].x;
                    let dy = particles[i].y - particles[j].y;
                    let dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 120) {
                        ctx.strokeStyle = `rgba(6, 182, 212, ${1 - dist / 120})`;
                        ctx.lineWidth = 0.5;
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
            }
            animationId = requestAnimationFrame(animateParticles);
        };

        resizeCanvas();

        // Use IntersectionObserver to pause animation when out of view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    isVisible = true;
                    animateParticles();
                } else {
                    isVisible = false;
                    cancelAnimationFrame(animationId);
                }
            });
        });
        observer.observe(canvas.parentElement);
    }
});
</script>
@endsection
