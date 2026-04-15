<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('site_name', 'IKATAN ALUMNI SMKN 2') }} - {{ setting('school_name', 'SMKN 2 TERNATE') }}</title>
    <link rel="canonical" href="{{ url()->current() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="manifest" href="/assets/manifest.json">
    <meta name="theme-color" content="#ffcc00">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="/assets/css/modern-v5.css">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-bs-theme', 'light');
        }
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
    </style>
</head>
<body>
@php
    $runningText = setting('running_text', 'Selamat Datang di Portal Resmi IKATAN ALUMNI SMKN 2 Ternate - Jalin Silaturahmi, Bangun Kontribusi!');
@endphp
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
                            <form action="/logout" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-dark btn-sm rounded-0 ms-lg-3 px-4 py-2">LOGOUT</button>
                            </form>
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

    @yield('content')

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
    @stack('scripts')
    @include('components.ai-chat-bubble')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
