@extends('layouts.app')

@section('content')
<style>
    /* Cyberpunk / Arcade Gamification Palette */
    .landing-bento {
        background-color: #050505; /* Pitch Black */
        color: #e2e8f0;
        font-family: 'Fira Code', 'Courier New', monospace; /* Terminal font */
        overflow-x: hidden;
        position: relative;
    }

    /* Cyberpunk Grid Background */
    .landing-bento::before {
        content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background-image: 
            linear-gradient(rgba(0, 255, 255, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0, 255, 255, 0.03) 1px, transparent 1px);
        background-size: 40px 40px;
        z-index: 0; pointer-events: none;
    }

    /* FIX NAVBAR CLASH */
    .navbar, .mobile-header, .top-bar { 
        background: rgba(5, 5, 5, 0.9) !important; 
        backdrop-filter: none !important;
        border-bottom: 1px solid #0ff !important;
        box-shadow: 0 0 10px rgba(0, 255, 255, 0.2);
    }
    .navbar .nav-link, .navbar-brand, .mobile-header a, .mobile-header i { 
        color: #fff !important; 
        font-family: 'Inter', sans-serif;
    }

    /* CUSTOM GLOWING CURSOR (Neon Cyan) */
    @media (min-width: 992px) {
        .landing-bento { cursor: crosshair; }
        .custom-cursor {
            position: fixed; top: 0; left: 0; width: 4px; height: 4px;
            border-radius: 0; pointer-events: none; z-index: 9999;
            background: #0ff;
            transform: translate(-50%, -50%); transition: width 0.1s, height 0.1s;
            box-shadow: 0 0 10px #0ff, 0 0 20px #0ff;
            opacity: 0;
        }
        .custom-cursor-trail {
            position: fixed; top: 0; left: 0; width: 30px; height: 30px;
            border-radius: 0; pointer-events: none; z-index: 9998;
            border: 1px dashed rgba(0, 255, 255, 0.5);
            transform: translate(-50%, -50%); transition: all 0.1s ease-out;
            opacity: 0;
        }
        .cursor-hover { width: 20px; height: 20px; background: transparent; border: 2px solid #f0f; box-shadow: 0 0 15px #f0f; }
    }

    /* Hero Section */
    .hero-bento {
        position: relative; width: 100%; min-height: 95vh;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; text-align: center;
        background: #000;
    }

    .hero-title {
        font-size: clamp(2.5rem, 6vw, 5.5rem);
        line-height: 1.1;
        font-family: 'Inter', sans-serif;
        text-transform: uppercase;
        position: relative;
    }
    
    /* Cyberpunk Glitch Animation */
    .glitch-text {
        position: relative;
        color: #fff;
    }
    .glitch-text::before, .glitch-text::after {
        content: attr(data-text);
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #000;
    }
    .glitch-text::before {
        left: 2px;
        text-shadow: -2px 0 #f0f;
        clip: rect(24px, 550px, 90px, 0);
        animation: glitch-anim-2 3s infinite linear alternate-reverse;
    }
    .glitch-text::after {
        left: -2px;
        text-shadow: -2px 0 #0ff;
        clip: rect(85px, 550px, 140px, 0);
        animation: glitch-anim 2.5s infinite linear alternate-reverse;
    }
    @keyframes glitch-anim {
        0% { clip: rect(13px, 9999px, 86px, 0); }
        20% { clip: rect(66px, 9999px, 12px, 0); }
        40% { clip: rect(14px, 9999px, 75px, 0); }
        60% { clip: rect(98px, 9999px, 34px, 0); }
        80% { clip: rect(45px, 9999px, 99px, 0); }
        100% { clip: rect(23px, 9999px, 56px, 0); }
    }
    @keyframes glitch-anim-2 {
        0% { clip: rect(65px, 9999px, 100px, 0); }
        20% { clip: rect(3px, 9999px, 45px, 0); }
        40% { clip: rect(88px, 9999px, 12px, 0); }
        60% { clip: rect(22px, 9999px, 77px, 0); }
        80% { clip: rect(55px, 9999px, 33px, 0); }
        100% { clip: rect(99px, 9999px, 11px, 0); }
    }

    .hero-content {
        position: relative;
        z-index: 10;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    
    .text-gradient-neon {
        color: #0ff;
        text-shadow: 0 0 5px #0ff, 0 0 10px #0ff, 0 0 20px #0ff;
    }
    
    #particleCanvas {
        position: absolute; inset: 0; width: 100%; height: 100%;
        z-index: 1; pointer-events: none;
    }
    .hero-overlay {
        position: absolute; inset: 0; z-index: 2;
        pointer-events: none;
    }

    /* Bento Cards -> Arcade Panels */
    .bento-card {
        background: rgba(10, 10, 10, 0.9);
        border: 1px solid #333;
        padding: 2.5rem;
        position: relative; 
        overflow: hidden;
        transition: all 0.3s ease;
        clip-path: polygon(0 0, calc(100% - 20px) 0, 100% 20px, 100% 100%, 20px 100%, 0 calc(100% - 20px));
    }
    .bento-card::before {
        content: ""; position: absolute; top: 0; left: 0; width: 4px; height: 100%;
        background: #0ff;
        opacity: 0.5;
        transition: opacity 0.3s;
    }

    .bento-card:hover { 
        transform: translateY(-5px); 
        border-color: #0ff;
        box-shadow: 0 10px 30px rgba(0, 255, 255, 0.1);
    }
    .bento-card:hover::before { opacity: 1; box-shadow: 0 0 10px #0ff; }

    /* Cyberpunk Buttons */
    .btn-neon-violet {
        background: transparent; color: #0ff;
        border: 1px solid #0ff;
        font-family: 'Fira Code', monospace;
        font-weight: bold;
        text-transform: uppercase;
        border-radius: 0;
        position: relative;
        box-shadow: 0 0 10px rgba(0, 255, 255, 0.2), inset 0 0 10px rgba(0, 255, 255, 0.1);
        transition: all 0.2s;
        clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);
    }
    .btn-neon-violet:hover {
        background: #0ff; color: #000;
        box-shadow: 0 0 20px #0ff, 0 0 40px #0ff;
        transform: translateY(-2px);
    }

    .btn-outline-light {
        background: transparent; color: #f0f !important;
        border: 1px solid #f0f;
        border-radius: 0;
        font-family: 'Fira Code', monospace;
        clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 10px, 100% 100%, 10px 100%, 0 calc(100% - 10px));
        box-shadow: 0 0 10px rgba(255, 0, 255, 0.2);
    }
    .btn-outline-light:hover {
        background: #f0f; color: #000 !important;
        box-shadow: 0 0 20px #f0f;
    }

    /* Colors & Utilities */
    .text-violet { color: #f0f !important; }
    .text-neon-cyan { color: #0ff !important; text-shadow: 0 0 5px #0ff; }
    .text-neon-pink { color: #f0f !important; text-shadow: 0 0 5px #f0f; }
    .text-gold { color: #ff0 !important; text-shadow: 0 0 5px #ff0; }
    
    .badge-neon {
        background: rgba(0, 255, 255, 0.1);
        color: #0ff;
        border: 1px solid #0ff;
        padding: 0.5rem 1rem;
        font-family: 'Fira Code', monospace;
        font-weight: bold;
        font-size: 0.75rem;
        letter-spacing: 0.1em;
        box-shadow: 0 0 5px rgba(0,255,255,0.5);
    }

    .bento-card > * { position: relative; z-index: 1; }

    /* Grid System */
    .bento-grid-wrapper {
        display: grid; grid-template-columns: repeat(12, 1fr); gap: 1.5rem; padding: 3rem 0;
        font-family: 'Inter', sans-serif; /* Keep reading text legible */
    }
    @media (max-width: 991px) {
        .bento-grid-wrapper { display: flex; flex-direction: column; gap: 1rem; }
        .span-8, .span-6, .span-4, .span-3 { grid-column: span 12 !important; width: 100%; }
    }

    .span-12 { grid-column: span 12; }
    .span-8 { grid-column: span 8; }
    .span-6 { grid-column: span 6; }
    .span-4 { grid-column: span 4; }
    .span-3 { grid-column: span 3; }

    .fw-black { font-weight: 900; }
    .tracking-tighter { letter-spacing: -0.05em; }
    .tracking-widest { letter-spacing: 0.2em; }
    
    .hover-lift { transition: transform 0.2s; }
    .hover-lift:hover { transform: translateY(-3px); }
    .extra-small { font-size: 0.75rem; font-family: 'Fira Code', monospace; }

    /* Leaderboard Arcade Glow */
    .leaderboard-panel {
        border-color: #ff0 !important;
    }
    .leaderboard-panel::before { background: #ff0 !important; }
    .leaderboard-panel:hover { box-shadow: 0 0 20px rgba(255, 255, 0, 0.2) !important; }

    /* Scrollbars */
    .activity-scroll::-webkit-scrollbar { width: 6px; }
    .activity-scroll::-webkit-scrollbar-track { background: #111; border-left: 1px solid #333; }
    .activity-scroll::-webkit-scrollbar-thumb { background: #0ff; }

    /* Online Avatars */
    .avatar-stack img { 
        width: 32px; height: 32px; border-radius: 0; border: 1px solid #0ff; 
        margin-left: -12px; transition: transform 0.2s;
        clip-path: polygon(20% 0%, 80% 0%, 100% 20%, 100% 80%, 80% 100%, 20% 100%, 0% 80%, 0% 20%);
    }
    .avatar-stack img:first-child { margin-left: 0; }
    .avatar-stack img:hover { transform: translateY(-5px); z-index: 10; box-shadow: 0 0 10px #0ff; }

    .gsap-fade-up { }
    .gsap-scroll-card { }

    /* Podcast / Audio Terminal Styles */
    .podcast-card {
        background: rgba(10, 10, 10, 0.8);
        border: 1px solid rgba(0, 255, 255, 0.2);
        padding: 1rem;
        transition: all 0.3s;
        position: relative;
        clip-path: polygon(0 0, 100% 0, 100% calc(100% - 15px), calc(100% - 15px) 100%, 0 100%);
    }
    .podcast-card:hover {
        border-color: #0ff;
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.2);
        background: rgba(0, 255, 255, 0.05);
    }
    .podcast-category-tag {
        font-size: 0.6rem;
        padding: 2px 8px;
        border: 1px solid currentColor;
        text-transform: uppercase;
        font-family: 'Fira Code', monospace;
        letter-spacing: 1px;
    }
    .audio-visualizer {
        display: flex;
        align-items: flex-end;
        gap: 2px;
        height: 15px;
    }
    .visualizer-bar {
        width: 2px;
        background: #0ff;
        animation: audio-pulse 1.2s infinite ease-in-out;
    }
    .visualizer-bar:nth-child(2) { animation-delay: 0.2s; }
    .visualizer-bar:nth-child(3) { animation-delay: 0.4s; }
    .visualizer-bar:nth-child(4) { animation-delay: 0.1s; }
    .visualizer-bar:nth-child(5) { animation-delay: 0.3s; }
    
    @keyframes audio-pulse {
        0%, 100% { height: 4px; }
        50% { height: 15px; }
    }

    .play-btn-neon {
        width: 40px; height: 40px;
        border-radius: 0;
        border: 1px solid #0ff;
        display: flex; align-items: center; justify-content: center;
        color: #0ff;
        background: transparent;
        transition: all 0.3s;
        clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
    }
    .play-btn-neon:hover {
        background: #0ff; color: #000;
        box-shadow: 0 0 15px #0ff;
    }
</style>

<div class="landing-bento" id="main-grid">
    <!-- Custom Cursor -->
    <div class="custom-cursor"></div>
    <div class="custom-cursor-trail"></div>

    <!-- Hero Section -->
    <section class="hero-bento">
        <canvas id="particleCanvas"></canvas>
        <div class="hero-overlay" style="background: radial-gradient(circle at center, transparent 0%, #030014 100%);"></div>
        
        <div class="container hero-content">
            <div>
                <h6 class="text-violet fw-bold text-uppercase tracking-widest mb-3">STEMAN Connect</h6>
                <h1 class="hero-title fw-black tracking-tighter text-white mb-2 glitch-text" data-text="WELCOME BACK, ALUMNI!">
                    WELCOME <span class="text-gradient-neon">BACK, ALUMNI!</span><br>CONNECT, GROW & REUNITE
                </h1>
                
                <div class="my-4 text-white-50">
                    <p class="h4 fw-bold text-white mb-1">Ribuan alumni. Satu jaringan tanpa batas.</p>
                    <p class="lead mb-4">Tempat koneksi berubah jadi kolaborasi,<br>pengalaman menjadi inspirasi,<br>dan alumni tumbuh bersama membangun masa depan.</p>
                    <div class="badge-neon mb-4 d-inline-block">
                        YOUR DIGITAL HUB NOW ....!!
                    </div>
                </div>
                
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-neon-violet btn-lg px-5 py-3 fw-bold magnetic-el">GABUNG SEKARANG</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-5 py-3 fw-bold magnetic-el">MASUK PORTAL</a>
                    @else
                        <a href="{{ route('alumni.dashboard') }}" class="btn btn-neon-violet btn-lg px-5 py-3 fw-bold magnetic-el">DASHBOARD SAYA</a>
                        <a href="{{ route('public.profile') }}" class="btn btn-outline-light btn-lg px-5 py-3 fw-bold magnetic-el">PROFIL DIGITAL</a>
                    @endguest
                </div>

                <!-- QUICK SEARCH -->
                <div class="mx-auto" style="max-width: 500px;">
                    <form action="{{ route('alumni.index') }}" method="GET" class="position-relative">
                        <input type="text" name="search" placeholder="Cari teman angkatan atau pekerjaan..." 
                               class="form-control form-control-lg bg-dark bg-opacity-50 border-white border-opacity-10 rounded-pill px-5 text-neon-cyan placeholder-white-50 shadow-lg"
                               style="backdrop-filter: blur(10px); height: 60px; border: 1px solid rgba(139, 92, 246, 0.5); color: #22d3ee !important;">
                        <button type="submit" class="btn btn-neon-violet rounded-circle position-absolute end-0 top-50 translate-middle-y me-2" style="width: 45px; height: 45px; border: none;">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="container pb-5">
        <div class="bento-grid-wrapper">
            
            <!-- Alumni Leaderboard & School Pride -->
            <div class="span-4 gsap-scroll-card">
                <div class="bento-card leaderboard-panel h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-black text-gold m-0 small tracking-widest text-uppercase"><i class="bi bi-trophy-fill me-2"></i>ALUMNI LEADERBOARD</h5>
                        <a href="{{ route('leaderboard') }}" class="text-white-50 extra-small text-decoration-none">LIHAT SEMUA</a>
                    </div>
                    
                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach($topAlumni ?? [] as $index => $alumnus)
                            <div class="d-flex align-items-center gap-3 p-2 rounded-4 border border-white border-opacity-5 bg-black">
                                <div class="position-relative avatar-stack">
                                    <img src="{{ $alumnus->profile_picture_url }}" width="45" height="45" style="object-fit: cover;" loading="lazy">
                                    <span class="position-absolute bottom-0 end-0 badge rounded-0 p-1 {{ $index == 0 ? 'bg-gold' : ($index == 1 ? 'bg-secondary' : 'bg-bronze') }}" style="width: 18px; height: 18px; font-size: 10px;">{{ $index + 1 }}</span>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="text-neon-cyan fw-bold mb-0 text-truncate">{{ $alumnus->name }}</h6>
                                    <p class="text-gold extra-small mb-0">{{ number_format($alumnus->points) }} Points</p>
                                </div>
                                <div class="text-end">
                                    @php $alumnusBadges = $alumnus->badges ?? collect(); @endphp
                                    @foreach($alumnusBadges->take(1) as $badge)
                                        <img src="{{ asset('storage/'.$badge->icon_path) }}" width="24" title="{{ $badge->name }}" loading="lazy">
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-auto pt-3 border-top border-white border-opacity-10">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-white-50 extra-small">Total Kontribusi Komunitas</span>
                            <span class="text-gold fw-bold">{{ number_format($totalPoints ?? 0) }} Pts</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- News Spotlight -->
            <div class="span-8 gsap-scroll-card">
                <div class="bento-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-black text-white m-0">KABAR TERKINI</h4>
                        <a href="{{ route('news.index') }}" class="text-violet text-decoration-none small fw-bold magnetic-el">LIHAT SEMUA <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="row g-4">
                        @foreach($latestNews ?? [] as $news)
                        <div class="col-md-6">
                            <a href="{{ route('news.show', $news->slug) }}" class="text-decoration-none group">
                                <div class="rounded-4 overflow-hidden mb-3" style="height: 160px; border: 1px solid rgba(255,255,255,0.05);">
                                    <img src="{{ $news->thumbnail ? (Str::startsWith($news->thumbnail, 'http') ? $news->thumbnail : asset($news->thumbnail)) : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=2070&auto=format&fit=crop' }}" class="w-100 h-100 object-fit-cover hover-lift" alt="News" loading="lazy">
                                </div>
                                <h6 class="text-white fw-bold line-clamp-2">{{ $news->title }}</h6>
                                <span class="text-white-50 extra-small">{{ $news->created_at->diffForHumans() }}</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Success Stories -->
            <div class="span-8 gsap-scroll-card">
                <div class="bento-card" style="min-height: 400px; background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(3, 0, 20, 1) 100%);">
                    <div class="row h-100 align-items-center">
                        <div class="col-lg-7">
                            <span class="badge-neon mb-3">JEJAK SUKSES</span>
                            <h2 class="display-5 fw-black text-white mb-4">Inspirasi Dari<br><span class="text-gradient-neon">Para Pemenang.</span></h2>
                            <p class="text-white-50 mb-5">
                                Simak perjalanan karir alumni {{ $schoolName ?? 'SMKN 2 Ternate' }} yang telah berhasil menembus pasar global dan industri ternama.
                            </p>
                            <a href="{{ route('success_stories.index') }}" class="btn btn-neon-violet btn-lg px-5 py-3 fw-bold magnetic-el">BACA KISAH MEREKA</a>
                        </div>
                        <div class="col-lg-5 d-none d-lg-block">
                            <div class="position-relative">
                                @foreach($successStories ?? [] as $index => $story)
                                    <div class="bento-card position-absolute shadow-lg" style="top: {{ $index * 20 }}px; left: {{ $index * 20 }}px; transform: rotate({{ $index * 2 }}deg); width: 100%; border-color: rgba(139, 92, 246, 0.2); background: rgba(10, 10, 30, 0.9);">
                                        <div class="d-flex gap-3 align-items-center">
                                            <img src="{{ $story->image_path ? asset('storage/'.$story->image_path) : 'https://ui-avatars.com/api/?name='.urlencode($story->name) }}" class="rounded-circle" width="50" height="50" loading="lazy">
                                            <div>
                                                <h6 class="text-white fw-bold mb-0">{{ $story->name }}</h6>
                                                <small class="text-violet">{{ $story->title }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Map Preview -->
            <div class="span-4 gsap-scroll-card">
                <div class="bento-card h-100 d-flex flex-column justify-content-between" style="border-color: rgba(34, 211, 238, 0.2);">
                    <div>
                        <h4 class="fw-black text-white mb-4">ALUMNI MAP</h4>
                        <div class="bg-white bg-opacity-5 rounded-4 p-4 text-center mb-4" style="border: 1px solid rgba(34, 211, 238, 0.1);">
                            <i class="bi bi-geo-alt text-neon-cyan display-4"></i>
                        </div>
                        <p class="text-white-50 small">Pantau persebaran alumni {{ $schoolName ?? 'SMKN 2 Ternate' }} di seluruh penjuru dunia secara real-time.</p>
                    </div>
                    <a href="{{ route('global.network') }}" class="btn btn-outline-light w-100 py-3 fw-bold magnetic-el">BUKA PETA</a>
                </div>
            </div>

            <!-- Career Section -->
            <div class="span-8 gsap-scroll-card">
                <div class="bento-card h-100">
                    <div class="row align-items-center">
                        <div class="col-lg-4">
                            <h4 class="fw-black text-white mb-2">PELUANG KARIR</h4>
                            <p class="text-white-50 small">Eksklusif untuk alumni Steman. Temukan karir impian Anda di sini.</p>
                            <a href="{{ route('jobs.index') }}" class="btn btn-neon-violet btn-sm px-4 py-2 fw-bold mt-3 magnetic-el">EKSPLORASI</a>
                        </div>
                        <div class="col-lg-8">
                            <div class="d-flex flex-column gap-2">
                                @forelse($latestJobs ?? [] as $job)
                                    <a href="/jobs/{{ $job->slug }}" class="d-flex gap-3 text-decoration-none p-3 rounded-4 hover-lift border border-white border-opacity-5 magnetic-el" style="background: rgba(255, 255, 255, 0.02);">
                                        <div class="bg-violet bg-opacity-20 text-violet rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px;">
                                            <i class="bi bi-briefcase fs-5"></i>
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

            <!-- Live Feed Card -->
            <div class="span-4 gsap-scroll-card">
                <div class="bento-card h-100" style="background: rgba(34, 197, 94, 0.05); border-color: rgba(34, 197, 94, 0.2);">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-black text-white m-0 small tracking-widest text-uppercase"><i class="bi bi-broadcast text-success me-2"></i>LIVE ACTIVITY</h5>
                        <div class="spinner-grow spinner-grow-sm text-success" role="status"></div>
                    </div>
                    <div class="activity-scroll pe-2" style="max-height: 280px; overflow-y: auto;">
                        <div class="d-flex flex-column gap-3">
                            @forelse($recentActivities ?? [] as $activity)
                                <div class="d-flex gap-3 align-items-start border-bottom border-white border-opacity-5 pb-3">
                                    <img src="{{ $activity->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($activity->user->name ?? 'Guest') }}" 
                                         class="rounded-circle mt-1" width="32" height="32" style="object-fit: cover; border: 1px solid rgba(255,255,255,0.1);" loading="lazy">
                                    <div>
                                        <p class="text-white extra-small fw-bold mb-0">{{ $activity->user->name ?? 'User' }}</p>
                                        <p class="text-white-50 extra-small mb-1">{{ $activity->description }}</p>
                                        <span class="text-success extra-small fw-bold">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-white-50 small text-center py-4">Belum ada aktivitas.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Podcast / Audio Terminal -->
            <div class="span-8 gsap-scroll-card">
                <div class="bento-card h-100" style="background: rgba(0, 255, 255, 0.02);">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-black text-white m-0 small tracking-widest text-uppercase"><i class="bi bi-mic text-neon-cyan me-2"></i>AUDIO TERMINAL: PODCAST ALUMNI</h5>
                            <p class="text-white-50 extra-small m-0 mt-1">Underrated but powerful career transmissions.</p>
                        </div>
                        <a href="{{ route('podcasts.index') }}" class="btn-neon-violet btn-sm px-3">BROWSE ALL</a>
                    </div>
                    
                    <div class="row g-3">
                        @forelse($latestPodcasts ?? [] as $podcast)
                            <div class="col-md-4">
                                <div class="podcast-card h-100 d-flex flex-column">
                                    <div class="mb-2 d-flex justify-content-between align-items-center">
                                        <span class="podcast-category-tag {{ $podcast->category == 'career' ? 'text-neon-cyan' : ($podcast->category == 'overseas' ? 'text-gold' : 'text-neon-pink') }}">
                                            {{ strtoupper($podcast->category) }}
                                        </span>
                                        <div class="audio-visualizer">
                                            <div class="visualizer-bar"></div>
                                            <div class="visualizer-bar"></div>
                                            <div class="visualizer-bar"></div>
                                            <div class="visualizer-bar"></div>
                                            <div class="visualizer-bar"></div>
                                        </div>
                                    </div>
                                    <h6 class="text-white fw-bold small mb-1 line-clamp-2">{{ $podcast->title }}</h6>
                                    <p class="extra-small text-white-50 mb-3">{{ $podcast->guest_name }}</p>
                                    
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <span class="extra-small text-neon-cyan fw-bold"><i class="bi bi-clock-history me-1"></i>{{ $podcast->duration }}</span>
                                        <button class="play-btn-neon" onclick="playAudio('{{ $podcast->audio_link }}', '{{ $podcast->title }}')">
                                            <i class="bi bi-play-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4">
                                <div class="border border-white border-opacity-10 p-4" style="background: rgba(255,255,255,0.02);">
                                    <i class="bi bi-reception-1 text-white-50 fs-2 mb-2"></i>
                                    <p class="text-white-50 small m-0 uppercase tracking-widest">TRANSMISSION PENDING...</p>
                                    <p class="extra-small text-white-20">Coming soon: Career Stories, Overseas Experiences & Startup Journeys.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Nostalgia Gallery -->
            <div class="span-12 gsap-scroll-card">
                <div class="bento-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-black text-white m-0">LENSA NOSTALGIA</h4>
                            <p class="text-white-50 small m-0">Momen berharga di setiap sudut sekolah.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-warning btn-sm px-4 py-2 rounded-pill fw-bold text-dark magnetic-el"><i class="bi bi-camera-fill me-1"></i> WebAR NOSTALGIA</a>
                            <a href="{{ route('gallery.index') }}" class="text-neon-cyan text-decoration-none small fw-bold magnetic-el d-flex align-items-center">LIHAT GALERI <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        @forelse($latestPhotos ?? [] as $index => $photo)
                            <div class="{{ $index == 0 ? 'col-md-6' : 'col-md-3' }}">
                                <div class="rounded-4 overflow-hidden position-relative group" style="height: 250px; border: 1px solid rgba(255,255,255,0.05);">
                                    <img src="{{ asset(ltrim($photo->file_path, '/')) }}" class="w-100 h-100 object-fit-cover hover-lift" alt="Gallery" loading="lazy">
                                    <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-gradient-dark opacity-0 group-hover-opacity-100 transition-all" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
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

    // Scroll reveal for cards - play ONCE only, never reverse
    gsap.utils.toArray('.gsap-scroll-card').forEach((card, i) => {
        gsap.fromTo(card, 
            { y: 40, opacity: 0, scale: 0.97 },
            { 
                y: 0, opacity: 1, scale: 1, duration: 0.6, ease: "power3.out",
                clearProps: "all",
                scrollTrigger: {
                    trigger: card,
                    start: "top 90%",
                    toggleActions: "play none none none",
                    once: true
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

        let isVisible = true;
        let animationId;

        const initParticles = () => { 
            particles = [];
            let num = Math.min(window.innerWidth / 15, 80);
            for(let i=0; i<num; i++) {
                particles.push(new Particle(Math.random() * canvas.width, Math.random() * canvas.height));
            }
        };
        
        const animateParticles = () => { 
            if (!isVisible) return;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            particles.forEach(p => { 
                p.update(); 
                p.draw(); 
            });
            
            // Draw connections
            for(let i=0; i<particles.length; i++) {
                for(let j=i+1; j<particles.length; j++) {
                    let dx = particles[i].x - particles[j].x;
                    let dy = particles[i].y - particles[j].y;
                    let dist = Math.sqrt(dx*dx + dy*dy);
                    if (dist < 120) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(139, 92, 246, ${0.2 - dist/600})`;
                        ctx.lineWidth = 0.5;
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
                    if (animationId) {
                        cancelAnimationFrame(animationId);
                    }
                }
            });
        });
        observer.observe(canvas.parentElement);
    });
</script>

@include('podcasts.player_script')

@endsection
