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

    .id-card-container {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        perspective: 1500px;
        background: radial-gradient(circle at 50% 50%, #0f172a 0%, #020617 100%);
        overflow: hidden;
        position: relative;
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
        transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        -webkit-transition: -webkit-transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        z-index: 10;
    }

    /* Hover on desktop */
    @media (hover: hover) {
        .card-3d:hover {
            transform: rotateY(180deg) scale(1.05);
            -webkit-transform: rotateY(180deg) scale(1.05);
        }
    }

    /* Click/tap toggle class for mobile and desktop */
    .card-3d.is-flipped {
        transform: rotateY(180deg) scale(1.05);
        -webkit-transform: rotateY(180deg) scale(1.05);
    }

    .card-side {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 20px;
        border: 1px solid var(--glass-border);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }

    /* FRONT SIDE */
    .card-front {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(15px);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 30px;
        color: white;
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
        align-items: center;
        gap: 20px;
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
        border: 2px solid #0f172a;
    }

    .info-content h2 {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 2px;
        letter-spacing: 0.5px;
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
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 30px;
        color: #1e293b;
    }

    .qr-container {
        padding: 10px;
        background: #f8fafc;
        border-radius: 15px;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .scanning-text {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 3px;
        color: #64748b;
        margin-bottom: 5px;
    }

    .terms-text {
        font-size: 0.6rem;
        opacity: 0.6;
        line-height: 1.4;
        max-width: 80%;
    }

    .shine-effect {
        position: absolute;
        top: 0; left: -100%;
        width: 50%; height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.2), transparent);
        transform: skewX(-25deg);
        transition: 0.5s;
    }
    .card-3d:hover .shine-effect {
        left: 200%;
        transition: 0.8s;
    }

    /* Print styling */
    @media print {
        .id-card-container { background: white; min-height: auto; }
        .blob, .btn-group-id { display: none; }
        .card-3d { transform: none !important; }
        .card-back { position: relative; top: 20px; transform: none; }
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

<div class="id-card-container">
    <div class="blob"></div>
    <div class="blob blob-2"></div>

    <div class="card-3d" id="alumniCard">
        <!-- FRONT -->
        <div class="card-side card-front">
            <div class="shine-effect"></div>
            
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
                    @if($user->foto_profil)
                        <img src="{{ \Illuminate\Support\Str::startsWith($user->foto_profil, '/storage/') ? $user->foto_profil : asset('storage/' . $user->foto_profil) }}" class="profile-img">
                    @else
                        <div class="profile-img bg-slate-800 d-flex align-items-center justify-content-center text-white fw-bold fs-3">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="info-content">
                    <h2 class="text-white">{{ strtoupper($user->name) }}</h2>
                    <p>{{ $user->jurusan }} • CLASS OF {{ $user->tahun_lulus }}</p>
                </div>
            </div>

            <div class="card-footer">
                <div class="meta-item">
                    <span>Registration ID</span>
                    <b>#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</b>
                </div>
                <div class="status-badge">
                    <i class="bi bi-patch-check-fill me-1"></i> VERIFIED MEMBER
                </div>
            </div>
        </div>

        <!-- BACK -->
        <div class="card-side card-back">
            <div class="scanning-text">SCAN QR CODE</div>
            <div class="qr-container">
                {!! $qrCode !!}
            </div>
            <p class="terms-text">This digital identity card is a valid verification tool for the SMK N 2 Ternate Alumni Network. Scan the code to view live verification status.</p>
            <div class="mt-4 border-top w-100 pt-3 opacity-50 text-center" style="font-size: 0.5rem; letter-spacing: 2px;">
                POWERED BY STEMAN PORTAL V5.0
            </div>
        </div>
    </div>

    <div class="btn-group-id mt-5 d-flex gap-3">
        <button onclick="window.print()" class="btn btn-outline-light rounded-pill px-4">
            <i class="bi bi-download me-2"></i>Download ID
        </button>
        <a href="{{ route('alumni.dashboard') }}" class="btn btn-primary rounded-pill px-4 shadow-lg">
            Dashboard
        </a>
    </div>

    <p class="mt-4 text-slate-500 small animate-pulse" id="flipHint">
        <i class="bi bi-phone-flip me-2"></i> <span id="hintText">Klik atau hover untuk membalik kartu</span>
    </p>
</div>

<script>
    const card = document.getElementById('alumniCard');
    const hintText = document.getElementById('hintText');

    // Toggle flip on click/tap (works on mobile & desktop)
    card.addEventListener('click', function () {
        this.classList.toggle('is-flipped');
        if (this.classList.contains('is-flipped')) {
            hintText.textContent = 'Klik lagi untuk kembali ke depan';
        } else {
            hintText.textContent = 'Klik atau hover untuk membalik kartu';
        }
    });

    // On desktop with hover: also sync hint text
    card.addEventListener('mouseenter', function () {
        hintText.textContent = 'Lepas kursor atau klik untuk kembali';
    });
    card.addEventListener('mouseleave', function () {
        if (!this.classList.contains('is-flipped')) {
            hintText.textContent = 'Klik atau hover untuk membalik kartu';
        }
    });
</script>

@endsection
