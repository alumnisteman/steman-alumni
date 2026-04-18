<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('site_name', 'IKATAN ALUMNI SMKN 2') }} - {{ setting('school_name', 'SMKN 2 TERNATE') }}</title>
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="preload" as="image" href="/storage/uploads/settings/hero.webp" type="image/webp">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" media="print" onload="this.media='all'">
    <link rel="manifest" href="/assets/manifest.json">
    <meta name="theme-color" content="#ffcc00">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="preload" as="style" href="/assets/css/modern-v5.css">
    <link rel="stylesheet" href="/assets/css/modern-v5.css" media="print" onload="this.media='all'">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-bs-theme', 'light');
        }

        // --- GLOBAL GUARDIAN SHIELD (Anti-Blank) ---
        window.onerror = function(message, source, lineno, colno, error) {
            console.error('Guardian Shield intercepted error:', message);
            // Non-blocking log to server
            if (window.fetch) {
                fetch('/api/v1/guardian/log-error', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ message, source, lineno, colno, url: window.location.href })
                }).catch(() => {});
            }
            // Fail gracefully for critical components
            if (message.includes('WebGL') || message.includes('Globe')) {
                const mapContainer = document.getElementById('3d-globe-container');
                if (mapContainer) {
                    mapContainer.innerHTML = '<div class="alert alert-warning m-4">Peta 3D sedang memulihkan diri... Silakan refresh jika masalah berlanjut.</div>';
                }
            }
            return false; // Let it log to console too
        };
    </script>
@stack('styles')
    <style>
        /* Running Text / Marquee Styles */
        .running-text-wrapper {
            background: linear-gradient(90deg, #1e293b 0%, #334155 100%);
            color: #ffcc00;
            padding: 8px 0;
            overflow: hidden;
            position: relative;
            z-index: 9999;
            border-bottom: 1px solid rgba(255,204,0,0.2);
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .marquee-content {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 35s linear infinite;
            will-change: transform;
        }

        .marquee-content:hover {
            animation-play-state: paused;
        }

        @@keyframes marquee {
            0%   { transform: translateX(100vw); }
            100% { transform: translateX(-100%); }
        }

        .running-text-label {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            background: #ffcc00;
            color: #1e293b;
            padding: 0 15px;
            display: flex;
            align-items: center;
            z-index: 10;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 0.75rem;
            box-shadow: 5px 0 15px rgba(0,0,0,0.3);
        }

        /* Sindonews-Style Sticky Management */
        :root {
            --billboard-height: 250px;
            --ad-wrapper-padding: 2.5rem; /* py-2 py-md-3 approx */
            --total-ad-offset: calc(var(--billboard-height) + var(--ad-wrapper-padding));
        }

        .header-ad-wrapper {
            position: relative; /* Changed from sticky to relative */
            z-index: 1030;
            background: #fff;
            border-bottom: 2px solid #ffcc00;
            transition: all 0.3s ease;
        }

        .navbar.sticky-top {
            top: 0; /* Changed from var(--total-ad-offset) to 0 for cleaner reading */
            z-index: 1050;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .ad-close-btn {
            position: absolute;
            top: 5px;
            right: 10px;
            background: rgba(0,0,0,0.1);
            color: #666;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 12px;
            cursor: pointer;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .ad-close-btn:hover {
            background: #ffcc00;
            color: #000;
        }

        /* Ad Image Fix: Prevent distortion (Anti-Lonjong) */
        .ad-slot-container {
            width: 100%;
            height: var(--billboard-height);
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa; /* Light neutral bg for gaps */
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .ad-slot-container img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* The Magic: proportional scaling */
            object-position: center;
            transition: transform 0.3s ease;
        }

        .ad-slot-container:hover img {
            transform: scale(1.02); /* Subtle hover effect */
        }

        @media (max-width: 767px) {
            :root {
                --billboard-height: 150px;
                --ad-wrapper-padding: 1rem;
            }
            .navbar.sticky-top {
                top: 0;
            }
            /* Hide top-bar on mobile if ad is sticky to save space */
            .top-bar { display: none; }
        }

        /* Optimization: Hide ad-wrapper if no ad is rendered */
        .header-ad-wrapper:empty { display: none; }

        /* MOBILE BOTTOM NAV SYSTEM */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 70px;
            padding-bottom: env(safe-area-inset-bottom);
            z-index: 2000;
            border-top: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 -5px 25px rgba(0,0,0,0.05);
        }
        .mobile-bottom-nav .nav-item {
            text-decoration: none;
            color: #94a3b8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            font-size: 0.65rem;
            font-weight: 700;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .mobile-bottom-nav .nav-item i {
            font-size: 1.4rem;
            margin-bottom: 2px;
        }
        .mobile-bottom-nav .nav-item.active {
            color: #059669;
            transform: translateY(-2px);
        }
        .mobile-bottom-nav .nav-item.active::after {
            content: '';
            position: absolute;
            top: 0;
            width: 20px;
            height: 3px;
            background: #059669;
            border-radius: 0 0 10px 10px;
        }
        .mobile-bottom-nav .action-btn {
            position: relative;
            top: -20px;
            z-index: 2001;
        }
        .mobile-bottom-nav .plus-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.35);
            border: 5px solid #fff;
            transition: transform 0.2s;
        }
        .mobile-bottom-nav .plus-icon:active {
            transform: scale(0.9);
        }
        .mobile-bottom-nav .plus-icon i {
            margin-bottom: 0;
            font-size: 1.6rem;
        }

        /* Mobile Header Layout */
        .mobile-header {
            display: none;
            background: #fff;
            padding: 12px 15px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1050;
        }

        @media (max-width: 991px) {
            body { padding-bottom: 85px; }
            .navbar { display: none !important; }
            .mobile-header { display: flex; justify-content: space-between; align-items: center; }
            .top-bar { display: none !important; }
            .header-ad-wrapper { border-bottom: none; }
            
            /* Large Buttons for Mobile */
            .btn-lg-mobile {
                padding: 12px 20px;
                font-size: 1.1rem;
                font-weight: 700;
                border-radius: 12px;
            }
        }
        
        .dark .mobile-bottom-nav, .dark .mobile-header {
            background: #1e293b;
            border-color: rgba(255,255,255,0.1);
        }
        .dark .mobile-bottom-nav .plus-icon {
            border-color: #1e293b;
        }
        .dark .mobile-bottom-nav .nav-item { color: #64748b; }
        .dark .mobile-bottom-nav .nav-item.active { color: #10b981; }
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
        <a href="/" class="text-decoration-none fw-black text-dark dark:text-white" style="font-size: 1.1rem; letter-spacing: -0.5px;">
            <span class="text-success">STEMAN</span> ALUMNI
        </a>
        <div class="d-flex gap-3 align-items-center">
            <a href="{{ route('alumni.messages') }}" class="text-dark dark:text-white position-relative">
                <i class="bi bi-chat-dots fs-4"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger p-1 border border-2 border-white" style="font-size: 0.5rem; display: none;"></span>
            </a>
            <button class="btn p-0 text-dark dark:text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list fs-3"></i>
            </button>
        </div>
    </div>
    
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-uppercase fs-6 fs-md-4" href="/">
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
                    <li class="nav-item"><a class="nav-link" href="/gallery">GALERI</a></li>
                    <li class="nav-item"><a class="nav-link" href="/news">BERITA</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('analytics.index') }}">STATISTIK</a></li>
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
        <a href="{{ route('alumni.network') }}" class="nav-item {{ request()->is('alumni/network*') ? 'active' : '' }}">
            <i class="bi bi-globe-central-south-asia"></i>
            <span>Peta</span>
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

        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/assets/sw.js');
            });
        }

        @auth
        // Real-time Notifications with Echo/Reverb
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                const handleNotification = (data) => {
                    console.log('New notification:', data);
                    
                    // Update count
                    const countBadge = document.getElementById('notification-count');
                    let currentCount = parseInt(countBadge.innerText) || 0;
                    currentCount++;
                    countBadge.innerText = currentCount;
                    countBadge.classList.remove('d-none');

                    // Prepend to list
                    const itemsContainer = document.getElementById('notification-items');
                    const emptyMsg = itemsContainer.querySelector('.text-muted');
                    if (emptyMsg) emptyMsg.remove();

                    const newItem = document.createElement('li');
                    newItem.innerHTML = `
                        <a class="dropdown-item px-3 py-2 border-bottom" href="${data.action_url ?? '#'}">
                            <div class="fw-bold small text-primary mb-1">${data.title ?? 'Notifikasi Baru'}</div>
                            <div class="small text-wrap">${data.message ?? ''}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Baru saja</div>
                        </a>
                    `;
                    itemsContainer.insertBefore(newItem, itemsContainer.firstChild);
                };

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
    <script>
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
    @stack('scripts')
    @include('components.ai-chat-bubble')
</body>
</html>
