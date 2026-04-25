<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('site_name', 'IKATAN ALUMNI SMKN 2') }} - {{ setting('school_name', 'SMKN 2 TERNATE') }}</title>
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap"></noscript>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></noscript>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#ffcc00">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="preload" as="style" href="{{ asset('assets/css/modern-v5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modern-v5.css') }}" media="print" onload="this.media='all'">
    <script>
        (function() {
            try {
                const theme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (theme === 'dark' || (!theme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                }
            } catch (e) {}
        })();
    </script>
@stack('styles')
    <style>
        @font-face {
            font-family: "bootstrap-icons";
            src: url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2?dd67030699838ea613ee6dbda90effa6") format("woff2"),
                 url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff?dd67030699838ea613ee6dbda90effa6") format("woff");
            font-display: swap;
        }
        .running-text-wrapper{background:linear-gradient(90deg,#1e293b 0%,#334155 100%);color:#ffcc00;padding:8px 0;overflow:hidden;position:relative;z-index:1020;border-bottom:1px solid rgba(255,204,0,0.2);font-size:.85rem;font-weight:600;letter-spacing:.5px}.marquee-content{display:inline-block;white-space:nowrap;animation:marquee 35s linear infinite;will-change:transform}.marquee-content:hover{animation-play-state:paused}@keyframes marquee{0%{transform:translateX(100vw)}100%{transform:translateX(-100%)}}.running-text-label{position:absolute;left:0;top:0;bottom:0;background:#ffcc00;color:#1e293b;padding:0 15px;display:flex;align-items:center;z-index:10;font-weight:900;text-transform:uppercase;font-size:.75rem;box-shadow:5px 0 15px rgba(0,0,0,0.3)}:root{--billboard-height:250px;--ad-wrapper-padding:2.5rem;--total-ad-offset:calc(var(--billboard-height) + var(--ad-wrapper-padding))}.header-ad-wrapper{position:relative;z-index:1030;background:#fff;border-bottom:2px solid #ffcc00;transition:all .3s ease}.navbar.sticky-top{top:0;z-index:1050;box-shadow:0 4px 10px rgba(0,0,0,0.1)}.ad-close-btn{position:absolute;top:5px;right:10px;background:rgba(0,0,0,0.1);color:#666;border:none;border-radius:50%;width:24px;height:24px;font-size:12px;cursor:pointer;z-index:20;display:flex;align-items:center;justify-content:center;transition:all .2s}.ad-close-btn:hover{background:#ffcc00;color:#000}.ad-slot-container{width:100%;height:var(--billboard-height);display:flex;align-items:center;justify-content:center;background:#f8f9fa;overflow:hidden;border-radius:8px;border:1px solid rgba(0,0,0,0.05)}.ad-slot-container img{width:100%;height:100%;object-fit:contain;object-position:center;transition:transform .3s ease}.ad-slot-container:hover img{transform:scale(1.02)}@media (max-width:767px){:root{--billboard-height:150px;--ad-wrapper-padding:1rem}.navbar.sticky-top{top:0}.top-bar{display:none}}.header-ad-wrapper:empty{display:none}.bg-gradient-story{background:linear-gradient(45deg,#f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%)}.mobile-bottom-nav{position:fixed;bottom:0;left:0;right:0;background:#fff;display:flex;justify-content:space-around;align-items:center;height:70px;padding-bottom:env(safe-area-inset-bottom);z-index:2000;border-top:1px solid rgba(0,0,0,0.08);box-shadow:0 -5px 25px rgba(0,0,0,0.05)}.mobile-bottom-nav .nav-item{text-decoration:none;color:#94a3b8;display:flex;flex-direction:column;align-items:center;justify-content:center;flex:1;font-size:.65rem;font-weight:700;transition:all .2s cubic-bezier(0.4,0,0.2,1);position:relative}.mobile-bottom-nav .nav-item i{font-size:1.4rem;margin-bottom:2px}.mobile-bottom-nav .nav-item.active{color:#059669;transform:translateY(-2px)}.mobile-bottom-nav .nav-item.active::after{content:'';position:absolute;top:0;width:20px;height:3px;background:#059669;border-radius:0 0 10px 10px}.mobile-bottom-nav .action-btn{position:relative;top:-20px;z-index:2001}.mobile-bottom-nav .plus-icon{width:56px;height:56px;background:linear-gradient(135deg,#059669 0%,#10b981 100%);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 20px rgba(5,150,105,0.35);border:5px solid #fff;transition:transform .2s}.mobile-bottom-nav .plus-icon:active{transform:scale(0.9)}.mobile-bottom-nav .plus-icon i{margin-bottom:0;font-size:1.6rem}.mobile-header{display:none;background:#fff;padding:12px 15px;border-bottom:1px solid rgba(0,0,0,0.05);position:sticky;top:0;z-index:1050}@media (max-width:991px){body{padding-bottom:calc(70px + env(safe-area-inset-bottom))}.navbar{display:none!important}.mobile-header{display:flex;justify-content:space-between;align-items:center}.top-bar{display:none!important}.header-ad-wrapper{border-bottom:none}.container{padding-left:12px;padding-right:12px}h1{font-size:1.75rem!important}h2{font-size:1.5rem!important}h5{font-size:1.1rem!important}.btn-lg-mobile{padding:12px 20px;font-size:1.1rem;font-weight:700;border-radius:12px}}.dark .mobile-bottom-nav,.dark .mobile-header{background:#1e293b;border-color:rgba(255,255,255,0.1)}.dark .mobile-bottom-nav .plus-icon{border-color:#1e293b}.dark .mobile-bottom-nav .nav-item{color:#64748b}.dark .mobile-bottom-nav .nav-item.active{color:#10b981}.text-muted{color:#475569!important}.footer-link{color:#cbd5e1!important;transition:color .2s}.footer-link:hover{color:#fff!important}
    </style>
</head>
<body>
@php
    $runningText = setting('running_text', 'Selamat Datang di Portal Resmi IKATAN ALUMNI SMKN 2 Ternate - Jalin Silaturahmi, Bangun Kontribusi!');
@endphp
    <div class="header-ad-wrapper shadow-sm" id="main-header-ad">
        <button class="ad-close-btn" onclick="document.getElementById('main-header-ad').remove()" title="Tutup Iklan">
            <i class="bi bi-x"></i>
        </button>
        <div class="container text-center py-2 py-md-3">
            <x-ad-slot position="header" />
        </div>
    </div>

@if($runningText)
<div class="running-text-wrapper">
    <div class="running-text-label">
        <i class="bi bi-megaphone-fill me-2"></i> INFO
    </div>
    <div class="marquee-content">
        {{ $runningText }} &nbsp;&bull;&nbsp; {{ $runningText }} &nbsp;&bull;&nbsp; {{ $runningText }} &nbsp;&bull;&nbsp; {{ $runningText }}
    </div>
</div>
@endif
    <div class="top-bar bg-dark py-2 text-white small d-none d-lg-block">
        <div class="container d-flex justify-content-between">
            <div><i class="bi bi-geo-alt-fill me-2"></i> {{ setting('contact_address', 'Jl. Ki Hajar Dewantoro, Ternate') }}</div>
            <div>
                <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-white"><i class="bi bi-youtube"></i></a>
            </div>
        </div>
    </div>

    {{-- MOBILE HEADER --}}
    <div class="mobile-header shadow-sm d-lg-none">
        <a href="/" class="text-decoration-none fw-black text-dark dark:text-white d-flex align-items-center" style="font-size: 1.1rem; letter-spacing: -0.5px;">
            <img src="{{ asset('images/logo.jpg') }}" height="28" class="me-2" alt="Logo">
            <span class="text-success">STEMAN</span>&nbsp;ALUMNI
        </a>
        <div class="d-flex gap-3 align-items-center">
            @auth
            <a href="{{ route('alumni.chat') }}" class="text-dark dark:text-white position-relative d-flex align-items-center gap-2 px-2 py-1 rounded-pill bg-light bg-opacity-50">
                <i class="bi bi-chat-dots fs-4 text-success"></i>
                <span class="fw-bold text-success" style="font-size: 0.75rem;">PESAN</span>
                <span id="mobile-chat-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger p-1 border border-2 border-white" style="font-size: 0.5rem; display: none;"></span>
            </a>
            @endauth
            <button class="btn p-0 text-dark dark:text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <i class="bi bi-list fs-3"></i>
            </button>
        </div>
    </div>
    
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-uppercase fs-6 fs-md-4 d-flex align-items-center" href="/">
                <img src="{{ asset('images/logo.jpg') }}" height="35" class="me-2" alt="Logo">
                {{ setting('site_name', 'IKATAN ALUMNI SMKN 2') }}
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-1 text-dark"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/">BERANDA</a></li>
                    <li class="nav-item"><a class="nav-link" href="/profil">PROFIL</a></li>
                    <li class="nav-item"><a class="nav-link" href="/alumni">DIREKTORI</a></li>
                    <li class="nav-item"><a class="nav-link text-primary fw-bold" href="{{ route('alumni.network') }}"><i class="bi bi-globe-central-south-asia me-1"></i>3D NETWORK</a></li>
                    <li class="nav-item"><a class="nav-link text-info fw-bold" href="{{ route('global.network') }}"><i class="bi bi-diagram-3-fill me-1"></i>GLOBAL MESH</a></li>
                    <li class="nav-item"><a class="nav-link text-warning fw-bold" href="{{ route('ar.scanner') }}"><i class="bi bi-camera-fill me-1"></i>WebAR SCAN</a></li>
                    <li class="nav-item"><a class="nav-link" href="/gallery">GALERI</a></li>
                    <li class="nav-item"><a class="nav-link" href="/news">BERITA</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('analytics.index') }}">STATISTIK</a></li>
                    <li class="nav-item"><a class="nav-link text-danger fw-bold" href="{{ route('donations.index') }}"><i class="bi bi-heart-fill me-1"></i>DONASI</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('leaderboard') }}">PERINGKAT</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('forums.index') }}">FORUM</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('mentors.index') }}">MENTOR</a></li>
                    @auth
                    @if(auth()->user()->role == 'alumni')
                    <li class="nav-item"><a class="nav-link text-emerald-600 fw-bold" href="{{ route('alumni.health.index') }}"><i class="bi bi-heart-pulse-fill me-1"></i>KESEHATAN</a></li>
                    @endif
                    @endauth
                    <li class="nav-item"><a class="nav-link" href="/kontak">KONTAK</a></li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link position-relative px-3 d-flex align-items-center gap-2" href="{{ route('alumni.chat') }}">
                                <i class="bi bi-chat-dots-fill fs-5"></i>
                                <span class="fw-bold small">PESAN</span>
                                <span id="desktop-chat-badge" class="position-absolute top-10 start-80 translate-middle badge rounded-pill bg-danger d-none" style="font-size: 0.6rem;">
                                    0
                                </span>
                            </a>
                        </li>
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link position-relative px-3" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell-fill fs-5"></i>
                                <span id="notification-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" style="font-size: 0.6rem;">
                                    0
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notificationDropdown" id="notification-list" style="width: 300px; max-height: 400px; overflow-y: auto;">
                                <li class="dropdown-header fw-bold text-uppercase">Notifikasi Baru</li>
                                <li><hr class="dropdown-divider"></li>
                                <div id="notification-items">
                                    <li class="px-3 py-2 text-muted small text-center">Tidak ada notifikasi baru</li>
                                </div>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ auth()->user()->dashboardUrl() }}">DASHBOARD</a></li>
                        <li class="nav-item">
                            <a href="/logout" class="btn btn-dark btn-sm rounded-0 ms-lg-3 px-4 py-2">LOGOUT</a>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="/login">LOGIN</a></li>
                        <li class="nav-item"><a class="btn btn-dark btn-sm rounded-0 ms-lg-3 px-4 py-2" href="/register">JOIN ILUNI</a></li>
                    @endauth
                    <li class="nav-item ms-lg-2">
                        <button id="theme-toggle" class="btn btn-link nav-link px-2 transition-all" type="button" style="text-decoration: none;">
                            <i class="bi bi-moon-stars-fill"></i>
                            <i class="bi bi-sun-fill d-none text-warning"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- MOBILE OFFCANVAS MENU --}}
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel" style="width: 280px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="mobileMenuLabel">MENU UTAMA</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="list-group list-group-flush">
                <a href="/" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3">
                    <i class="bi bi-house-door fs-5 text-success"></i> BERANDA
                </a>
                <a href="/profil" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3">
                    <i class="bi bi-info-circle fs-5 text-primary"></i> PROFIL ALUMNI
                </a>
                <a href="/alumni" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3">
                    <i class="bi bi-people fs-5 text-info"></i> DIREKTORI
                </a>
                <a href="{{ route('alumni.network') }}" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3">
                    <i class="bi bi-globe-central-south-asia fs-5 text-success"></i> 3D NETWORK
                </a>
                <a href="/news" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3">
                    <i class="bi bi-newspaper fs-5 text-warning"></i> BERITA & INFO
                </a>
                <a href="{{ route('analytics.index') }}" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3">
                    <i class="bi bi-graph-up fs-5 text-danger"></i> STATISTIK
                </a>
                <a href="{{ route('donations.index') }}" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3">
                    <i class="bi bi-heart-fill fs-5 text-danger"></i> IKATAN ALUMNI FUND
                </a>
                <a href="{{ route('forums.index') }}" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3">
                    <i class="bi bi-chat-square-dots fs-5 text-primary"></i> FORUM DISKUSI
                </a>
                <a href="/gallery" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3">
                    <i class="bi bi-images fs-5 text-secondary"></i> GALERI FOTO
                </a>
                <hr class="my-2 opacity-10">
                @auth
                    <a href="{{ auth()->user()->dashboardUrl() }}" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-speedometer2 fs-5 text-success"></i> DASHBOARD
                    </a>
                    <a href="/logout" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3 text-danger">
                        <i class="bi bi-box-arrow-right fs-5"></i> KELUAR
                    </a>
                @else
                    <a href="/login" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3">
                        <i class="bi bi-box-arrow-in-right fs-5 text-success"></i> LOGIN
                    </a>
                @endauth
            </div>
        </div>
    </div>


    {{-- BOTTOM NAVIGATION (MOBILE ONLY) --}}
    <div class="mobile-bottom-nav d-lg-none">
        <a href="/" class="nav-item {{ request()->is('/') ? 'active' : '' }}">
            <i class="bi bi-house-door{{ request()->is('/') ? '-fill' : '' }}"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('feed.index') }}" class="nav-item {{ request()->is('feed*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap{{ request()->is('feed*') ? '-fill' : '' }}"></i>
            <span>Feed</span>
        </a>
        <a href="#" class="nav-item action-btn" data-bs-toggle="modal" data-bs-target="#createPostModal">
            <div class="plus-icon"><i class="bi bi-plus-lg"></i></div>
        </a>
        <a href="{{ route('alumni.chat') }}" class="nav-item {{ request()->is('chat*') ? 'active' : '' }}">
            <i class="bi bi-chat-dots{{ request()->is('chat*') ? '-fill' : '' }}"></i>
            <span>Pesan</span>
            <span id="bottom-chat-badge" class="position-absolute top-0 end-0 badge rounded-pill bg-danger d-none" style="font-size: 0.5rem; margin-top: 5px; margin-right: 15px;">0</span>
        </a>
        <a href="{{ auth()->check() ? auth()->user()->dashboardUrl() : '/login' }}" class="nav-item {{ request()->is('alumni/dashboard*') || request()->is('admin/dashboard*') ? 'active' : '' }}">
            <i class="bi bi-person-circle{{ request()->is('*/dashboard*') ? '-fill' : '' }}"></i>
            <span>Profil</span>
        </a>
    </div>

    @yield('content')

    <div class="footer-ad-wrapper py-5">
        <div class="container text-center">
            <x-ad-slot position="footer" />
        </div>
    </div>

    <footer id="contact-section" class="pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="fw-bold mb-4">{{ setting('site_name', 'IKATAN ALUMNI SMKN 2') }}</h5>
                    <p class="small opacity-75">{{ setting('site_description', 'Wadah silaturahmi, kolaborasi, dan kontribusi nyata lulusan ' . setting('school_name', 'SMKN 2 Ternate') . ' untuk almamater dan bangsa.') }}</p>
                </div>
                <div class="col-lg-2 mb-4 mb-lg-0">
                    <h6 class="fw-bold mb-4">TAUTAN CEPAT</h6>
                    <ul class="list-unstyled small">
                        <li><a href="/profil" class="footer-link">Tentang Kami</a></li>
                        <li><a href="/alumni" class="footer-link">Direktori Alumni</a></li>
                        <li><a href="/gallery" class="footer-link">Galeri Kegiatan</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4 mb-lg-0">
                    <h6 class="fw-bold mb-4">PROGRAM</h6>
                    <ul class="list-unstyled small">
                        <li><a href="{{ route('programs.show', 'beasiswa-alumni') }}" class="footer-link">{{ setting("program_scholarship", "Beasiswa") }}</a></li>
                        <li><a href="{{ route('programs.show', 'mentoring-karir') }}" class="footer-link">{{ setting("program_mentoring", "Mentoring Karir") }}</a></li>
                        <li><a href="{{ route('programs.show', 'social-impact') }}" class="footer-link">{{ setting("program_social_impact", "Social Impact") }}</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h6 class="fw-bold mb-4">KONTAK</h6>
                    <p class="small opacity-75 mb-1"><i class="bi bi-envelope me-2"></i> <a href="mailto:{{ setting('contact_email', 'sekretariat@alumni_smkn2.id') }}" class="text-white text-decoration-none">{{ setting('contact_email', 'sekretariat@alumni_smkn2.id') }}</a></p>
                    <p class="small opacity-75"><i class="bi bi-telephone me-2"></i> <a href="tel:{{ setting('contact_phone', '+62-123-4567-890') }}" class="text-white text-decoration-none">{{ setting('contact_phone', '+62-123-4567-890') }}</a></p>
                </div>
            </div>
            <hr class="opacity-25 bg-white">
            <p class="text-center small opacity-50 mb-0">&copy; 2026 Ikatan Alumni {{ setting('school_name', 'SMKN 2 Ternate') }}. All rights reserved.</p>
        </div>
    </footer>
    <script>
        window.authId = {{ auth()->id() ?? 'null' }};
        // --- GUARDIAN V3 SYSTEM (Self-Healing & Resilience) ---
        window.Guardian = {
            log: function(data) {
                if (window.fetch) {
                    fetch('/api/v1/guardian/log-error', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(data)
                    }).catch(() => {});
                }
            },
            safe: function(fn, context = 'Global') {
                try { return fn(); } 
                catch (e) { console.error(`Guardian suppressed error in ${context}:`, e.message); this.log({ message: e.message, context }); return null; }
            },
            cleanupModals: function() {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 1) {
                    console.warn('Guardian: Multiple backdrops detected, cleaning up zombie layers.');
                    for (let i = 0; i < backdrops.length - 1; i++) backdrops[i].remove();
                    document.body.classList.add('modal-open');
                }
            },
            auditUI: function() {
                console.log('Guardian: Performing UI Integrity Audit...');
                
                // 1. Check Bootstrap
                if (typeof bootstrap === 'undefined') {
                    console.error('Guardian: Bootstrap JS missing! Attempting emergency reload...');
                    this.log({ message: 'Bootstrap JS missing', context: 'UI_AUDIT' });
                    // Try to inject it if it's really gone
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';
                    document.head.appendChild(script);
                }

                // 2. Check Mobile Menu Accessibility
                const mobileBtn = document.querySelector('[data-bs-target="#mobileMenu"]');
                if (mobileBtn && window.getComputedStyle(mobileBtn).display !== 'none') {
                    // Check if it's covered by something
                    const rect = mobileBtn.getBoundingClientRect();
                    const elAtPoint = document.elementFromPoint(rect.left + rect.width/2, rect.top + rect.height/2);
                    if (elAtPoint && !mobileBtn.contains(elAtPoint) && !elAtPoint.contains(mobileBtn)) {
                        console.warn('Guardian: Mobile menu button might be blocked by:', elAtPoint);
                        elAtPoint.style.zIndex = '-1'; // Try to move the blocker back
                    }
                }
            }
        };

        window.onerror = function(message, source, lineno, colno, error) {
            Guardian.log({ message, source, lineno, colno, url: window.location.href });
            return false;
        };

        // Auto-cleanup backdrops on any modal change
        const observer = new MutationObserver(() => window.Guardian.cleanupModals());
        observer.observe(document.body, { childList: true });

        // Run UI Audit after a short delay
        window.addEventListener('load', () => {
            setTimeout(() => window.Guardian.auditUI(), 2000);
        });

        // Global Lazy Load for Images and iFrames (skips hero & pre-attributed images)
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll("img:not([loading])").forEach(function(img) {
                if (!img.closest(".hero-section") && !img.closest(".hero")) {
                    img.setAttribute("loading", "lazy");
                }
            });
            document.querySelectorAll("iframe:not([loading])").forEach(function(iframe) {
                iframe.setAttribute("loading", "lazy");
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme Toggle Logic with Animation
            const themeToggle = document.getElementById('theme-toggle');
            const sunIcon = themeToggle.querySelector('.bi-sun-fill');
            const moonIcon = themeToggle.querySelector('.bi-moon-stars-fill');

            const updateIcons = (theme) => {
                if (theme === 'dark') {
                    sunIcon.classList.remove('d-none');
                    moonIcon.classList.add('d-none');
                } else {
                    sunIcon.classList.add('d-none');
                    moonIcon.classList.remove('d-none');
                }
            };

            // Initial icon state
            updateIcons(localStorage.getItem('theme') || 'light');

            themeToggle.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                const theme = isDark ? 'dark' : 'light';
                localStorage.setItem('theme', theme);
                document.documentElement.setAttribute('data-bs-theme', theme);
                updateIcons(theme);
                
                // Add a little pop animation
                themeToggle.style.transform = 'scale(1.2) rotate(15deg)';
                setTimeout(() => {
                    themeToggle.style.transform = 'scale(1) rotate(0deg)';
                }, 200);
            });

            // Active link handling
            const currentPath = window.location.pathname;
            document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });

            // Navbar scroll effect
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    document.querySelector('.navbar').classList.add('navbar-scrolled');
                } else {
                    document.querySelector('.navbar').classList.remove('navbar-scrolled');
                }
            });
        });


        @auth
        // Real-time Notifications & Chat Badges with Echo/Reverb
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                const updateChatBadges = (count) => {
                    const desktopBadge = document.getElementById('desktop-chat-badge');
                    const mobileBadge = document.getElementById('mobile-chat-badge');
                    const bottomBadge = document.getElementById('bottom-chat-badge');

                    [desktopBadge, mobileBadge, bottomBadge].forEach(badge => {
                        if (badge) {
                            if (count > 0) {
                                badge.innerText = count > 99 ? '99+' : count;
                                badge.classList.remove('d-none');
                                if (badge.id === 'mobile-chat-badge') badge.style.display = 'block';
                            } else {
                                badge.classList.add('d-none');
                                if (badge.id === 'mobile-chat-badge') badge.style.display = 'none';
                            }
                        }
                    });
                };

                // Initial fetch for chat unread count
                fetch('/api/chat/unread-count')
                    .then(r => r.json())
                    .then(data => updateChatBadges(data.count))
                    .catch(() => {});

                const handleNotification = (data) => {
                    console.log('New notification:', data);
                    
                    // Update notification count
                    const countBadge = document.getElementById('notification-count');
                    let currentCount = parseInt(countBadge.innerText) || 0;
                    currentCount++;
                    countBadge.innerText = currentCount;
                    countBadge.classList.remove('d-none');

                    // Prepend to list
                    const itemsContainer = document.getElementById('notification-items');
                    const emptyMsg = itemsContainer?.querySelector('.text-muted');
                    if (emptyMsg) emptyMsg.remove();

                    if (itemsContainer) {
                        const newItem = document.createElement('li');
                        newItem.innerHTML = `
                            <a class="dropdown-item px-3 py-2 border-bottom" href="${data.action_url ?? '#'}">
                                <div class="fw-bold small text-primary mb-1">${data.title ?? 'Notifikasi Baru'}</div>
                                <div class="small text-wrap">${data.message ?? ''}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Baru saja</div>
                            </a>
                        `;
                        itemsContainer.insertBefore(newItem, itemsContainer.firstChild);
                    }
                };

                // Listen to Chat Channel
                window.Echo.private(`chat.${window.authId}`)
                    .listen('NewMessageEvent', (e) => {
                        // Re-fetch count
                        fetch('/api/chat/unread-count')
                            .then(r => r.json())
                            .then(data => updateChatBadges(data.count));

                        // Show Notification if not on chat page or chat not open
                        if (typeof currentChatUserId === 'undefined' || currentChatUserId != e.sender_id) {
                            showChatNotification(e.sender.name, e.message, e.sender.avatar);
                        }
                    });

                function showChatNotification(name, message, avatar) {
                    // Create toast if not exists
                    let container = document.getElementById('toast-container');
                    if (!container) {
                        container = document.createElement('div');
                        container.id = 'toast-container';
                        container.className = 'toast-container position-fixed top-0 end-0 p-3';
                        container.style.zIndex = '9999';
                        document.body.appendChild(container);
                    }

                    const toastId = 'toast-' + Date.now();
                    const toastHtml = `
                        <div id="${toastId}" class="toast align-items-center text-white bg-dark border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 15px; background: linear-gradient(135deg, #1a1a1a, #333) !important;">
                            <div class="d-flex p-2">
                                <div class="toast-body d-flex align-items-center gap-3">
                                    <img src="${avatar}" class="rounded-circle" width="40" height="40" style="object-fit: cover; border: 2px solid #28a745;">
                                    <div>
                                        <div class="fw-bold text-success" style="font-size: 0.85rem;">Pesan Baru dari ${name}</div>
                                        <div class="small text-white-50 text-truncate" style="max-width: 200px;">${message}</div>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', toastHtml);
                    
                    const toastEl = document.getElementById(toastId);
                    const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
                    toast.show();

                    // Play subtle sound
                    const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
                    audio.play().catch(() => {}); // catch if browser blocks audio

                    // Remove from DOM after hide
                    toastEl.addEventListener('hidden.bs.toast', () => {
                        toastEl.remove();
                    });
                }

                // Listen to Private User Channel
                window.Echo.private(`App.Models.User.{{ auth()->id() }}`)
                    .listen('.new-notification', handleNotification);

                // Listen to Public Notifications Channel
                window.Echo.channel('notifications')
                    .listen('.new-notification', handleNotification);
            }
        });
        @endauth
    </script>
    <!-- Core Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize PWA Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('PWA ServiceWorker registered with scope:', registration.scope);
                }).catch(error => {
                    console.log('PWA ServiceWorker registration failed:', error);
                });
            });
        }
    </script>
    @stack('scripts')
    @include('components.ai-chat-bubble')
    @include('components.global-modals')
</body>
</html>
