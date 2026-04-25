@extends('layouts.app')

@section('content')
<style>
    :root {
        --card-width: 450px;
        --card-height: 280px;
        --glass-bg: rgba(255, 255, 255, 0.08);
        --glass-border: rgba(255, 255, 255, 0.2);
        --primary-glow: #3f37c9;
        --secondary-glow: #480ca8;
    }

    /* Outer wrapper: only for background + blobs — MUST NOT have perspective here */
    .id-card-outer {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at 50% 50%, #0f172a 0%, #020617 100%);
        position: relative;
        /* NO overflow:hidden here - it breaks 3D transforms */
    }

    /* Clip blobs separately so they don't affect 3D context */
    .blob-wrapper {
        position: absolute;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        z-index: 0;
    }

    /* Card area: this is the perspective context */
    .id-card-container {
        position: relative;
        z-index: 10;
        display: flex;
        flex-direction: column;
        align-items: center;
        perspective: 1200px;
    }

    /* Animated background blobs */
    .blob {
        position: absolute;
        width: 300px;
        height: 300px;
        background: var(--primary-glow);
        filter: blur(80px);
        border-radius: 50%;
        z-index: 0;
        opacity: 0.3;
        animation: float 20s infinite alternate;
    }
    .blob-2 {
        background: var(--secondary-glow);
        right: 10%;
        top: 20%;
        animation-duration: 15s;
    }

    @keyframes float {
        0% { transform: translate(0, 0) scale(1); }
        100% { transform: translate(100px, 50px) scale(1.2); }
    }

    .card-3d {
        width: var(--card-width);
        height: var(--card-height);
        position: relative;
        transform-style: preserve-3d;
        -webkit-transform-style: preserve-3d;
        /* Remove CSS transition for transform so JS tilt is smooth */
        transition: transform 0.1s ease-out;
        cursor: pointer;
        z-index: 10;
        /* Will-change for performance */
        will-change: transform;
    }

    /* Click/tap toggle class for mobile and desktop */
    .card-3d.is-flipped {
        /* Flipped state logic handled partly by JS now, but we set a base */
        transform: rotateY(180deg);
        -webkit-transform: rotateY(180deg);
    }

    .card-side {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        border-radius: 20px;
        border: 1px solid var(--glass-border);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }

        background: var(--card-bg, rgba(15, 23, 42, 0.6));
        backdrop-filter: blur(15px);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 35px; /* Web-friendly safe area */
        color: white;
        box-sizing: border-box;
    }

    /* Admin Theme: Onyx Black & Gold */
    .role-admin .card-front {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
        border: 1px solid rgba(212, 175, 55, 0.3) !important;
    }
    .role-admin .logo-text {
        background: linear-gradient(to right, #d4af37, #f9e29c) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
    }
    .role-admin .profile-frame {
        background: linear-gradient(45deg, #d4af37, #b8860b) !important;
        box-shadow: 0 0 20px rgba(212, 175, 55, 0.3) !important;
    }
    .role-admin .status-badge {
        background: rgba(212, 175, 55, 0.2) !important;
        color: #f9e29c !important;
        border: 1px solid rgba(212, 175, 55, 0.4) !important;
    }
    .role-admin .meta-item span { color: #94a3b8; }
    .role-admin .meta-item b { color: #f9e29c; }
    .role-admin .card-footer { border-top: 1px solid rgba(212, 175, 55, 0.2); }
    .role-admin .card-front::after {
        content: 'OFFICIAL ADMINISTRATOR';
        position: absolute;
        top: 20px;
        right: 35px;
        font-size: 0.5rem;
        font-weight: 900;
        letter-spacing: 2px;
        color: #d4af37;
        opacity: 0.8;
    }

    .card-front::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 50%, rgba(255,255,255,0.05) 100%);
        pointer-events: none;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo-text {
        font-family: 'Outfit', sans-serif;
        font-weight: 900;
        letter-spacing: -1px;
        font-size: 1.5rem;
        background: linear-gradient(to right, #fff, #94a3b8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .chip-icon {
        width: 45px;
        height: 35px;
        background: linear-gradient(135deg, #d4af37 0%, #f9e29c 50%, #b8860b 100%);
        border-radius: 6px;
        position: relative;
        overflow: hidden;
    }
    .chip-line {
        position: absolute;
        background: rgba(0,0,0,0.2);
        width: 100%;
        height: 1px;
        top: 50%;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        justify-content: center;
        width: 100%;
        gap: 12px;
        margin-top: -10px; /* Center adjustment */
    }

    .profile-frame {
        width: 85px;
        height: 85px;
        border-radius: 50%;
        padding: 4px;
        background: linear-gradient(45deg, #3f37c9, #4cc9f0);
        box-shadow: 0 0 20px rgba(63, 55, 201, 0.4);
    }

    .profile-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        object-position: top;
        border: 2px solid #0f172a;
    }

    .info-content h2 {
        font-size: 1.35rem;
        font-weight: 800;
        margin-bottom: 2px;
        letter-spacing: 1px;
    }

    .info-content p {
        font-size: 0.85rem;
        opacity: 0.8;
        margin: 0;
        color: #94a3b8;
    }

    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        border-top: 1px solid rgba(255,255,255,0.1);
        padding-top: 15px;
    }

    .meta-item span {
        display: block;
        font-size: 0.6rem;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 1px;
    }
    .meta-item b {
        font-size: 0.9rem;
        font-family: 'JetBrains Mono', monospace;
    }

    .status-badge {
        background: rgba(34, 197, 94, 0.2);
        color: #4ade80;
        padding: 4px 12px;
        border-radius: 100px;
        font-size: 0.65rem;
        font-weight: 800;
        border: 1px solid rgba(74, 222, 128, 0.3);
    }

    /* BACK SIDE */
    .card-back {
        background: white;
        transform: rotateY(180deg);
        -webkit-transform: rotateY(180deg);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start; /* Naikkan elemen ke atas */
        padding: 40px 35px 25px 35px; /* Safe area dan dorongan dari atas */
        color: #1e293b;
        box-sizing: border-box;
    }

    .qr-container {
        padding: 8px;
        background: #f8fafc;
        border-radius: 15px;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 10px;
    }

    .scanning-text {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 3px;
        color: #64748b;
        margin-bottom: 5px;
    }

    .terms-text {
        font-size: 0.55rem;
        opacity: 0.7;
        line-height: 1.5;
        max-width: 85%;
        text-align: center;
        margin-top: 5px;
    }

    /* Holographic Glare Overlay */
    .holo-glare {
        position: absolute;
        inset: 0;
        border-radius: 20px;
        background: linear-gradient(
            125deg, 
            rgba(255,255,255,0) 10%, 
            rgba(255,255,255,0.4) 30%, 
            rgba(6, 182, 212, 0.6) 40%, 
            rgba(139, 92, 246, 0.6) 50%, 
            rgba(255,255,255,0.4) 60%, 
            rgba(255,255,255,0) 80%
        );
        background-size: 200% 200%;
        background-position: 50% 50%;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
        z-index: 50;
        mix-blend-mode: color-dodge;
    }

    .card-3d:hover .holo-glare {
        opacity: 1;
    }

    /* Subtle Border Glow on Hover */
    .card-3d::before {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 22px;
        background: linear-gradient(45deg, #06b6d4, #8b5cf6, #ec4899, #06b6d4);
        z-index: -1;
        opacity: 0;
        filter: blur(10px);
        transition: opacity 0.3s;
    }
    .card-3d:hover::before {
        opacity: 0.7;
    }

    /* Gamification Badges */
    .badges-container {
        display: flex;
        gap: 8px;
        margin-top: 10px;
        z-index: 10;
        position: relative;
    }

    .cyber-badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.1), 0 0 10px var(--badge-color, #3b82f6);
        color: var(--badge-color, #3b82f6);
        position: relative;
        transform-style: preserve-3d;
        transition: transform 0.3s ease;
    }

    .cyber-badge:hover {
        transform: scale(1.2) translateZ(20px);
        z-index: 20;
    }

    .cyber-badge::after {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 50%;
        background: var(--badge-color, #3b82f6);
        z-index: -1;
        opacity: 0.3;
        filter: blur(4px);
    }

    /* ── PRINT / DOWNLOAD STYLES ── */
    @media print {
        /* Hide everything except the cards */
        body > *:not(.id-card-outer) { display: none !important; }
        .blob-wrapper, .btn-group-id, #flipHint { display: none !important; }

        .id-card-outer {
            background: white !important;
            min-height: auto;
        }

        .id-card-container {
            perspective: none !important;
        }

        /* Stack both sides vertically, flat */
        .card-3d {
            transform: none !important;
            -webkit-transform: none !important;
            transform-style: flat !important;
            -webkit-transform-style: flat !important;
            height: auto !important;
            position: relative;
            page-break-inside: avoid;
        }

        /* Show both sides flat, one below the other */
        .card-side {
            position: relative !important;
            backface-visibility: visible !important;
            -webkit-backface-visibility: visible !important;
            transform: none !important;
            -webkit-transform: none !important;
            width: var(--card-width);
            height: var(--card-height);
            display: flex !important;
            border-radius: 20px;
            margin-bottom: 20px;
            page-break-inside: avoid;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2) !important;
        }

        /* Front side: keep dark background for print */
        .card-front {
            background: #0f172a !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .logo-text {
            background: none !important;
            -webkit-background-clip: initial !important;
            -webkit-text-fill-color: initial !important;
            color: white !important;
        }

        .profile-frame {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Back side: already white */
        .card-back {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
        }

        /* Label separating the two sides */
        .card-front::after {
            content: 'Sisi Depan Kartu Alumni';
            display: block;
            position: absolute;
            bottom: -18px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 8px;
            color: #94a3b8;
            letter-spacing: 2px;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .card-back::after {
            content: 'Sisi Belakang (QR Code)';
            display: block;
            position: absolute;
            bottom: -18px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 8px;
            color: #94a3b8;
            letter-spacing: 2px;
            text-transform: uppercase;
            white-space: nowrap;
        }
    }

    @media (max-width: 500px) {
        :root {
            --card-width: 320px;
            --card-height: 200px;
        }
        .logo-text { font-size: 1.1rem; }
        .profile-frame { width: 60px; height: 60px; }
        .info-content h2 { font-size: 1rem; }
        .meta-item b { font-size: 0.7rem; }
    }
</style>

<div class="id-card-outer role-{{ auth()->user()?->role ?? 'alumni' }}">
    <!-- Blobs in their own overflow:hidden wrapper -->
    <div class="blob-wrapper">
        <div class="blob"></div>
        <div class="blob blob-2"></div>
    </div>

    <!-- Card perspective container -->
    <div class="id-card-container">
    <div class="card-3d" id="alumniCard">
        <!-- FRONT -->
        <div class="card-side card-front">
            <!-- Holographic Glare -->
            <div class="holo-glare" id="holoGlare"></div>
            
            <div class="card-header">
                <div class="logo-text">STEMAN ALUMNI</div>
                <div class="chip-icon">
                    <div class="chip-line"></div>
                    <div style="position:absolute; left:30%; top:0; width:1px; height:100%; background:rgba(0,0,0,0.2);"></div>
                    <div style="position:absolute; right:30%; top:0; width:1px; height:100%; background:rgba(0,0,0,0.2);"></div>
                </div>
            </div>

            <div class="user-info">
                <div class="profile-frame">
                    <img src="{{ $user->profile_picture_url }}" class="profile-img">
                </div>
                <div class="info-content">
                    <h2 class="text-white">{{ strtoupper($user->name) }}</h2>
                    <p>{{ $user->major }} • CLASS OF {{ $user->graduation_year }}</p>
                    
                    @if(isset($user->badges) && $user->badges->count() > 0)
                    <div class="badges-container">
                        @foreach($user->badges as $badge)
                        <div class="cyber-badge" style="--badge-color: {{ $badge->color_theme }};" title="{{ $badge->name }}">
                            {!! $badge->icon_url ?? '<i class="bi bi-star-fill"></i>' !!}
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <div class="card-footer">
                <div class="meta-item">
                    <span>Registration ID</span>
                    <b>#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</b>
                </div>
                <div class="status-badge {{ $user->is_verified ? 'bg-warning bg-opacity-20 text-warning border-warning' : '' }}">
                    <i class="bi bi-{{ $user->is_verified ? 'gem' : 'patch-check-fill' }} me-1"></i> 
                    {{ (auth()->user()?->role === 'admin') ? 'SYSTEM AUTHORITY' : ($user->is_verified ? 'VIP VERIFIED MEMBER' : 'ALUMNI MEMBER') }}
                </div>
            </div>
        </div>

        <!-- BACK -->
        <div class="card-side card-back">
            <div class="scanning-text">SCAN QR CODE</div>
            <div class="qr-container">
                {!! $qrCode !!}
            </div>
            
            <div class="fw-bold mb-2 text-center" style="font-size: 0.65rem; color: #475569; letter-spacing: 1.5px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 8px; width: 80%;">
                DITERBITKAN: {{ now()->translatedFormat('d M Y') }}
            </div>
            
            <p class="terms-text">This digital identity card is a valid verification tool for the SMK N 2 Ternate Alumni Network. Scan the code to view live verification status.</p>
            <div class="mt-auto border-top w-100 pt-3 opacity-50 text-center" style="font-size: 0.5rem; letter-spacing: 2px;">
                POWERED BY STEMAN PORTAL V5.0
            </div>
        </div>
    </div>

    <div class="btn-group-id mt-5 d-flex gap-3">
        <button onclick="printCard()" class="btn btn-outline-light rounded-pill px-4">
            <i class="bi bi-download me-2"></i>Download ID
        </button>
        <a href="{{ route('alumni.dashboard') }}" class="btn btn-primary rounded-pill px-4 shadow-lg">
            Dashboard
        </a>
    </div>

    <p class="mt-4 text-slate-500 small" id="flipHint" style="color: #94a3b8;">
        <i class="bi bi-phone-flip me-2"></i> <span id="hintText">Klik kartu untuk membalik</span>
    </p>
    </div><!-- end id-card-container -->
</div><!-- end id-card-outer -->

<div class="container py-5">
    <div class="text-center mb-5">
        <h3 class="fw-black text-uppercase tracking-wider">💎 STEMAN PRIVILEGE NETWORK</h3>
        <p class="text-muted">Tunjukkan Digital ID Card Anda di merchant alumni berikut untuk mendapatkan penawaran eksklusif.</p>
    </div>

    <div class="row g-4 justify-content-center">
        @forelse($discountedBusinesses as $biz)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 hover-lift">
                    <div class="position-relative">
                        <img src="{{ $biz->logo_url ?? 'https://dummyimage.com/600x400/f8f9fa/adb5bd&text=No+Logo' }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm fw-bold">
                                <i class="bi bi-tag-fill me-1"></i> PROMO ALUMNI
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">{{ $biz->name }}</h5>
                        <p class="text-muted small mb-3"><i class="bi bi-geo-alt me-1"></i> {{ $biz->location }}</p>
                        
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 border border-primary border-opacity-10 mb-3 text-center">
                            <p class="mb-0 fw-bold" style="font-size: 0.9rem;">{{ $biz->discount_details }}</p>
                        </div>
                        
                        <a href="{{ route('alumni.business.show', $biz->id) }}" class="btn btn-outline-dark btn-sm w-100 rounded-pill fw-bold">Lihat Detail Bisnis</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="bg-light p-5 rounded-5 d-inline-block">
                    <i class="bi bi-shop display-4 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Belum ada merchant terdaftar di area Anda.</h5>
                    <p class="small text-muted">Punya bisnis? <a href="{{ route('alumni.business.create') }}" class="fw-bold text-primary">Daftarkan di sini</a> dan berikan diskon!</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
    const card = document.getElementById('alumniCard');
    const hintText = document.getElementById('hintText');
    const glare = document.getElementById('holoGlare');
    let isFlipped = false;

    // ── 3D HOLOGRAPHIC TILT EFFECT ──
    if (window.matchMedia("(any-hover: hover)").matches) {
        card.addEventListener('mousemove', (e) => {
            if (isFlipped) return; // Disable tilt when flipped to back

            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            // Calculate rotation (max 15 degrees)
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -15;
            const rotateY = ((x - centerX) / centerX) * 15;

            // Apply transform
            card.style.transform = `perspective(1200px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.05, 1.05, 1.05)`;

            // Update Holographic Glare Position
            const glareX = (x / rect.width) * 100;
            const glareY = (y / rect.height) * 100;
            glare.style.backgroundPosition = `${glareX}% ${glareY}%`;
        });

        card.addEventListener('mouseleave', () => {
            if (!isFlipped) {
                card.style.transform = `perspective(1200px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
                card.style.transition = `transform 0.5s ease-out`;
                setTimeout(() => { card.style.transition = `transform 0.1s ease-out`; }, 500);
            }
            hintText.textContent = 'Klik untuk membalik kartu';
        });

        card.addEventListener('mouseenter', () => {
            hintText.textContent = 'Klik untuk melihat QR Code';
        });
    }

    // Toggle flip on click/tap (works on mobile & desktop)
    card.addEventListener('click', function () {
        isFlipped = !isFlipped;
        this.classList.toggle('is-flipped');
        
        if (isFlipped) {
            // Apply flip transform with transition
            card.style.transition = `transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)`;
            card.style.transform = `perspective(1200px) rotateY(180deg) scale3d(1.05, 1.05, 1.05)`;
            hintText.textContent = 'Klik lagi untuk kembali ke depan';
        } else {
            // Revert flip
            card.style.transition = `transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)`;
            card.style.transform = `perspective(1200px) rotateY(0deg) scale3d(1, 1, 1)`;
            hintText.textContent = 'Klik untuk melihat QR Code';
            setTimeout(() => { card.style.transition = `transform 0.1s ease-out`; }, 800);
        }
    });

    // ── PRINT / DOWNLOAD: Render both sides in a new window ──
    function printCard() {
        // Grab the two card sides HTML
        const frontHTML = document.querySelector('.card-front').innerHTML;
        const backHTML  = document.querySelector('.card-back').innerHTML;

        const cardWidth  = '{{ $cardWidth ?? "450px" }}'  || '450px';
        const cardHeight = '{{ $cardHeight ?? "280px" }}' || '280px';

        const printWindow = window.open('', '_blank', 'width=600,height=800');
        printWindow.document.write(`
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Alumni STEMAN</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: white;
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }
        .print-label {
            font-size: 9px;
            letter-spacing: 3px;
            color: #94a3b8;
            text-transform: uppercase;
            text-align: center;
            margin-top: 4px;
        }
        .card-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        /* FRONT CARD */
        .print-front {
            width: 450px;
            height: 280px;
            padding: 35px; /* Safe area web friendly */
            border-radius: 20px;
            background: #0f172a !important;
            color: white !important;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .print-front::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 50%, rgba(255,255,255,0.05) 100%);
        }
        /* BACK CARD */
        .print-back {
            width: 450px;
            height: 280px;
            padding: 40px 35px 25px 35px; /* Safe area web friendly */
            border-radius: 20px;
            background: white;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border: 1px solid #e2e8f0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        /* Front inner styles */
        .card-header { display:flex; justify-content:space-between; align-items:center; }
        .logo-text { font-weight:900; font-size:1.5rem; letter-spacing:-1px; color: white !important; -webkit-text-fill-color: white !important; }
        .chip-icon { width:45px; height:35px; background:linear-gradient(135deg,#d4af37,#f9e29c,#b8860b) !important; border-radius:6px; position:relative; -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; }
        .chip-line { position:absolute; background:rgba(0,0,0,0.2); width:100%; height:1px; top:50%; }
        .user-info { display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; gap:12px; width:100%; margin-top:-10px; }
        .profile-frame { width:85px; height:85px; border-radius:50%; padding:4px; background:linear-gradient(45deg,#3f37c9,#4cc9f0); -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        .profile-img { width:100%; height:100%; border-radius:50%; object-fit:cover; object-position:top; border:2px solid #0f172a; }
        .info-content h2 { font-size:1.35rem; font-weight:800; margin-bottom:2px; letter-spacing:1px; }
        .info-content p { font-size:0.85rem; opacity:0.8; margin:0; color:#94a3b8; }
        .card-footer { display:flex; justify-content:space-between; align-items:flex-end; border-top:1px solid rgba(255,255,255,0.1); padding-top:15px; }
        .meta-item span { display:block; font-size:0.6rem; text-transform:uppercase; color:#64748b; letter-spacing:1px; }
        .meta-item b { font-size:0.9rem; }
        .status-badge { background:rgba(34,197,94,0.2); color:#4ade80; padding:4px 12px; border-radius:100px; font-size:0.65rem; font-weight:800; border:1px solid rgba(74,222,128,0.3); }
        /* Back inner styles */
        .scanning-text { font-size:0.75rem; font-weight:700; letter-spacing:3px; color:#64748b; margin-bottom:5px; }
        .qr-container { padding:8px; background:#f8fafc; border-radius:15px; margin-bottom:10px; }
        .qr-container svg, .qr-container img { display:block; }
        .terms-text { font-size:0.55rem; opacity:0.7; line-height:1.5; max-width:85%; text-align:center; margin-top:5px; margin-bottom:0; }
        .shine-effect { display: none; }
        @media print {
            body { padding: 10px; }
            .print-front, .print-back { break-inside: avoid; page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="card-wrapper">
        <div class="print-front">${frontHTML}</div>
        <div class="print-label">Sisi Depan &mdash; Kartu Alumni STEMAN</div>
    </div>
    <div class="card-wrapper">
        <div class="print-back">${backHTML}</div>
        <div class="print-label">Sisi Belakang &mdash; QR Code Verifikasi</div>
    </div>
    <script>
        // Auto print after fonts load
        window.onload = function() {
            setTimeout(function() { window.print(); }, 800);
        };
    <\/script>
</body>
</html>`);
        printWindow.document.close();
    }
</script>

@endsection
