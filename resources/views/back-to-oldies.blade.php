<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ setting('site_tagline', 'Back to Oldies, Connected For Tomorrow') }} — {{ setting('school_name', 'SMKN 2 TERNATE') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Fira+Code:wght@300;400;700&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --cyan:   #00ffff;
            --pink:   #ff00ff;
            --yellow: #ffff00;
            --green:  #00ff88;
            --dark:   #050508;
            --dark2:  #0a0a14;
        }

        html { scroll-behavior: smooth; }

        body {
            background: var(--dark);
            color: #e2e8f0;
            font-family: 'Fira Code', monospace;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* ── GRID BACKGROUND ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(0,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,255,255,0.04) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: 0;
            pointer-events: none;
            animation: gridPulse 6s ease-in-out infinite;
        }
        @keyframes gridPulse {
            0%, 100% { opacity: 0.6; }
            50%       { opacity: 1; }
        }

        /* ── SCANLINES ── */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0,0,0,0.08) 2px,
                rgba(0,0,0,0.08) 4px
            );
            z-index: 0;
            pointer-events: none;
        }

        .page-wrapper {
            position: relative;
            z-index: 1;
        }

        /* ──────────────────────────────
           HERO
        ────────────────────────────── */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 24px;
            position: relative;
            overflow: hidden;
        }

        /* Radial glow center */
        .hero::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(0,255,255,0.07) 0%, transparent 70%);
            pointer-events: none;
            animation: glowPulse 4s ease-in-out infinite;
        }
        @keyframes glowPulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1);   opacity: 0.7; }
            50%       { transform: translate(-50%, -50%) scale(1.15); opacity: 1;   }
        }

        canvas#particles {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            max-width: 960px;
        }

        /* School badge */
        .school-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--cyan);
            padding: 8px 24px;
            font-family: 'Fira Code', monospace;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--cyan);
            text-shadow: 0 0 8px var(--cyan);
            box-shadow: 0 0 10px rgba(0,255,255,0.25), inset 0 0 10px rgba(0,255,255,0.05);
            margin-bottom: 36px;
            clip-path: polygon(12px 0, 100% 0, calc(100% - 12px) 100%, 0 100%);
            animation: badgePulse 3s ease-in-out infinite;
        }
        @keyframes badgePulse {
            0%, 100% { box-shadow: 0 0 10px rgba(0,255,255,0.25), inset 0 0 10px rgba(0,255,255,0.05); }
            50%       { box-shadow: 0 0 20px rgba(0,255,255,0.5),  inset 0 0 15px rgba(0,255,255,0.1);  }
        }
        .school-badge .dot {
            width: 8px; height: 8px;
            background: var(--cyan);
            box-shadow: 0 0 8px var(--cyan);
            animation: blink 1s step-start infinite;
        }
        @keyframes blink { 50% { opacity: 0; } }

        /* ── MAIN TITLE: BACK TO OLDIES ── */
        .title-back {
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            font-size: clamp(2.8rem, 8vw, 7rem);
            line-height: 1;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: -0.02em;
            position: relative;
            display: inline-block;
            margin-bottom: 0;
        }

        /* Glitch effect */
        .title-back::before,
        .title-back::after {
            content: attr(data-text);
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            font-size: inherit;
        }
        .title-back::before {
            color: var(--pink);
            text-shadow: -3px 0 var(--pink);
            clip: rect(0, 9999px, 0, 0);
            animation: glitch1 3.5s infinite;
        }
        .title-back::after {
            color: var(--cyan);
            text-shadow: 3px 0 var(--cyan);
            clip: rect(0, 9999px, 0, 0);
            animation: glitch2 3s infinite;
        }
        @keyframes glitch1 {
            0%   { clip: rect(0,9999px,0,0); }
            5%   { clip: rect(42px,9999px,78px,0); transform: translate(-3px,0); }
            10%  { clip: rect(10px,9999px,55px,0); transform: translate(3px,0); }
            15%  { clip: rect(0,9999px,0,0); }
            70%  { clip: rect(68px,9999px,20px,0); transform: translate(-2px,0); }
            75%  { clip: rect(0,9999px,0,0); }
            100% { clip: rect(0,9999px,0,0); }
        }
        @keyframes glitch2 {
            0%   { clip: rect(0,9999px,0,0); }
            8%   { clip: rect(80px,9999px,30px,0); transform: translate(3px,0); }
            13%  { clip: rect(25px,9999px,90px,0); transform: translate(-3px,0); }
            18%  { clip: rect(0,9999px,0,0); }
            60%  { clip: rect(50px,9999px,15px,0); transform: translate(2px,0); }
            65%  { clip: rect(0,9999px,0,0); }
            100% { clip: rect(0,9999px,0,0); }
        }

        /* Neon glow on text */
        .neon-cyan {
            color: var(--cyan);
            text-shadow:
                0 0 4px var(--cyan),
                0 0 10px var(--cyan),
                0 0 25px var(--cyan),
                0 0 50px var(--cyan);
        }
        .neon-pink {
            color: var(--pink);
            text-shadow:
                0 0 4px var(--pink),
                0 0 10px var(--pink),
                0 0 25px var(--pink);
        }
        .neon-yellow {
            color: var(--yellow);
            text-shadow:
                0 0 4px var(--yellow),
                0 0 10px var(--yellow),
                0 0 25px var(--yellow);
        }
        .neon-green {
            color: var(--green);
            text-shadow:
                0 0 4px var(--green),
                0 0 10px var(--green),
                0 0 25px var(--green);
        }

        /* Separator line */
        .neon-line {
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--pink), var(--cyan), transparent);
            margin: 18px 0;
            box-shadow: 0 0 8px var(--cyan);
            animation: lineScan 3s linear infinite;
        }
        @keyframes lineScan {
            0%   { background-position: -200% center; }
            100% { background-position: 200% center; }
        }

        /* ── SUBTITLE: CONNECTED FOR TOMORROW ── */
        .title-connected {
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            font-size: clamp(1.2rem, 4vw, 3rem);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-top: 12px;
            animation: fadeInUp 1s ease 0.4s both;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── SCHOOL NAME ── */
        .school-name {
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            font-size: clamp(1.6rem, 5vw, 4rem);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 20px;
            position: relative;
            animation: fadeInUp 1s ease 0.7s both;
        }

        /* ──────────────────────────────
           STATS BAR
        ────────────────────────────── */
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 0;
            margin-top: 48px;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease 1s both;
        }
        .stat-item {
            flex: 1;
            min-width: 140px;
            max-width: 200px;
            padding: 20px 16px;
            text-align: center;
            border: 1px solid rgba(0,255,255,0.2);
            position: relative;
            transition: all 0.3s;
        }
        .stat-item:hover {
            border-color: var(--cyan);
            background: rgba(0,255,255,0.05);
            transform: translateY(-4px);
        }
        .stat-num {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            font-weight: 900;
            color: var(--cyan);
            text-shadow: 0 0 10px var(--cyan);
            display: block;
        }
        .stat-label {
            font-size: 0.65rem;
            letter-spacing: 0.2em;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            margin-top: 4px;
            display: block;
        }

        /* ── CTA BUTTONS ── */
        .cta-group {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 44px;
            animation: fadeInUp 1s ease 1.2s both;
        }
        .btn-cyber {
            font-family: 'Fira Code', monospace;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            text-decoration: none;
            padding: 14px 32px;
            border: 2px solid var(--cyan);
            color: var(--cyan);
            background: transparent;
            cursor: pointer;
            clip-path: polygon(12px 0, 100% 0, calc(100% - 12px) 100%, 0 100%);
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }
        .btn-cyber::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--cyan);
            transform: translateX(-101%);
            transition: transform 0.25s ease;
            z-index: -1;
        }
        .btn-cyber:hover {
            color: #000;
            box-shadow: 0 0 20px var(--cyan), 0 0 40px var(--cyan);
        }
        .btn-cyber:hover::before {
            transform: translateX(0);
        }
        .btn-cyber-pink {
            border-color: var(--pink);
            color: var(--pink);
        }
        .btn-cyber-pink::before { background: var(--pink); }
        .btn-cyber-pink:hover {
            color: #000;
            box-shadow: 0 0 20px var(--pink), 0 0 40px var(--pink);
        }

        /* ──────────────────────────────
           SECTION: MOTTO / VISI
        ────────────────────────────── */
        .section-motto {
            padding: 80px 24px;
            background: linear-gradient(180deg, var(--dark) 0%, var(--dark2) 50%, var(--dark) 100%);
            border-top: 1px solid rgba(0,255,255,0.1);
            border-bottom: 1px solid rgba(255,0,255,0.1);
            position: relative;
            z-index: 1;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
        }
        .section-label {
            font-size: 0.7rem;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: var(--cyan);
            font-family: 'Fira Code', monospace;
            margin-bottom: 12px;
            display: block;
        }
        .section-title {
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            font-size: clamp(1.6rem, 4vw, 3rem);
            color: #fff;
            margin-bottom: 48px;
            text-transform: uppercase;
        }

        /* Motto cards */
        .motto-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        @media (max-width: 768px) {
            .motto-grid { grid-template-columns: 1fr; }
        }

        .motto-card {
            background: rgba(10,10,20,0.9);
            border: 1px solid rgba(0,255,255,0.15);
            padding: 32px 24px;
            position: relative;
            overflow: hidden;
            clip-path: polygon(0 0, calc(100% - 20px) 0, 100% 20px, 100% 100%, 20px 100%, 0 calc(100% - 20px));
            transition: all 0.3s;
        }
        .motto-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 100%;
            transition: all 0.3s;
        }
        .motto-card.cyan::before  { background: var(--cyan); box-shadow: 0 0 10px var(--cyan); }
        .motto-card.pink::before  { background: var(--pink); box-shadow: 0 0 10px var(--pink); }
        .motto-card.green::before { background: var(--green); box-shadow: 0 0 10px var(--green); }
        .motto-card:hover {
            transform: translateY(-6px);
            background: rgba(0,255,255,0.04);
        }
        .motto-card.cyan:hover  { border-color: var(--cyan); box-shadow: 0 12px 40px rgba(0,255,255,0.12); }
        .motto-card.pink:hover  { border-color: var(--pink); box-shadow: 0 12px 40px rgba(255,0,255,0.12); }
        .motto-card.green:hover { border-color: var(--green); box-shadow: 0 12px 40px rgba(0,255,136,0.12); }

        .motto-icon {
            font-size: 2.5rem;
            margin-bottom: 16px;
            display: block;
        }
        .motto-card h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .motto-card p {
            font-size: 0.85rem;
            line-height: 1.8;
            color: rgba(255,255,255,0.6);
        }

        /* ──────────────────────────────
           SECTION: TIMELINE / JOURNEY
        ────────────────────────────── */
        .section-journey {
            padding: 80px 24px;
            position: relative;
            z-index: 1;
        }
        .timeline {
            position: relative;
            margin-top: 48px;
            padding-left: 40px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 16px;
            top: 0; bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, var(--cyan), var(--pink), var(--green));
            box-shadow: 0 0 8px var(--cyan);
        }
        .timeline-item {
            position: relative;
            margin-bottom: 40px;
            padding-left: 30px;
        }
        .timeline-dot {
            position: absolute;
            left: -30px;
            top: 6px;
            width: 14px; height: 14px;
            border: 2px solid var(--cyan);
            background: var(--dark);
            box-shadow: 0 0 8px var(--cyan);
            transform: rotate(45deg);
        }
        .timeline-year {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.75rem;
            color: var(--cyan);
            letter-spacing: 0.2em;
            margin-bottom: 6px;
        }
        .timeline-title {
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: #fff;
            margin-bottom: 8px;
        }
        .timeline-desc {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.55);
            line-height: 1.7;
        }

        /* ──────────────────────────────
           FOOTER
        ────────────────────────────── */
        footer {
            text-align: center;
            padding: 40px 24px;
            border-top: 1px solid rgba(0,255,255,0.1);
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.3);
            position: relative;
            z-index: 1;
        }
        footer .footer-logo {
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            font-size: 1.1rem;
            color: var(--cyan);
            text-shadow: 0 0 10px var(--cyan);
            display: block;
            margin-bottom: 10px;
        }

        /* ── CORNER DECORATIONS ── */
        .corner-tl, .corner-tr, .corner-bl, .corner-br {
            position: fixed;
            width: 60px; height: 60px;
            pointer-events: none;
            z-index: 10;
            opacity: 0.4;
        }
        .corner-tl { top: 16px; left: 16px;
            border-top: 2px solid var(--cyan); border-left: 2px solid var(--cyan); }
        .corner-tr { top: 16px; right: 16px;
            border-top: 2px solid var(--pink); border-right: 2px solid var(--pink); }
        .corner-bl { bottom: 16px; left: 16px;
            border-bottom: 2px solid var(--green); border-left: 2px solid var(--green); }
        .corner-br { bottom: 16px; right: 16px;
            border-bottom: 2px solid var(--yellow); border-right: 2px solid var(--yellow); }

        /* ── SCROLL INDICATOR ── */
        .scroll-hint {
            margin-top: 48px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            animation: fadeInUp 1s ease 1.5s both;
        }
        .scroll-hint span { font-size: 0.65rem; letter-spacing: 0.2em; color: rgba(0,255,255,0.5); }
        .scroll-arrow {
            width: 20px; height: 30px;
            border: 2px solid rgba(0,255,255,0.4);
            border-radius: 10px;
            position: relative;
        }
        .scroll-arrow::after {
            content: '';
            position: absolute;
            top: 6px; left: 50%;
            transform: translateX(-50%);
            width: 4px; height: 8px;
            background: var(--cyan);
            border-radius: 2px;
            box-shadow: 0 0 6px var(--cyan);
            animation: scrollBounce 1.5s ease-in-out infinite;
        }
        @keyframes scrollBounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50%       { transform: translateX(-50%) translateY(8px); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .stats-bar { gap: 8px; }
            .stat-item { min-width: 100px; padding: 14px 10px; }
            .stat-num  { font-size: 1.4rem; }
        }
    </style>
</head>
<body>

    <!-- Corner decorations -->
    <div class="corner-tl"></div>
    <div class="corner-tr"></div>
    <div class="corner-bl"></div>
    <div class="corner-br"></div>

    <div class="page-wrapper">

        <!-- ═══════════════════════════════ HERO ═══════════════════════════════ -->
        <section class="hero">
            <canvas id="particles"></canvas>

            <div class="hero-inner">

                <!-- School badge -->
                <div class="school-badge">
                    <div class="dot"></div>
                    {{ strtoupper(setting('school_name', 'SMKN 2 TERNATE')) }} &mdash; STEMAN ALUMNI CONNECT
                    <div class="dot"></div>
                </div>

                <!-- MAIN TITLE -->
                @php
                    $taglineParts = explode(',', setting('site_tagline', 'Back to Oldies, Connected For Tomorrow'), 2);
                    $taglinePart1 = strtoupper(trim($taglineParts[0] ?? 'Back to Oldies'));
                    $taglinePart2 = strtoupper(trim($taglineParts[1] ?? 'Connected For Tomorrow'));
                @endphp
                <h1 class="title-back neon-cyan" data-text="{{ $taglinePart1 }}">
                    {{ $taglinePart1 }}
                </h1>

                <!-- Neon separator -->
                <div class="neon-line"></div>

                <!-- SUBTITLE -->
                <h2 class="title-connected">
                    <span class="neon-yellow">{{ $taglinePart2 }}</span>
                </h2>

                <!-- SCHOOL NAME -->
                <p class="school-name neon-yellow">
                    {{ strtoupper(setting('school_name', 'SMKN 2 TERNATE')) }}
                </p>

                <!-- Stats bar -->
                <div class="stats-bar">
                    <div class="stat-item">
                        <span class="stat-num neon-cyan" id="cnt-alumni">0</span>
                        <span class="stat-label">Alumni Terdaftar</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-num neon-pink" id="cnt-angkatan">0</span>
                        <span class="stat-label">Angkatan</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-num neon-green" id="cnt-kota">0</span>
                        <span class="stat-label">Kota Tersebar</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-num neon-yellow" id="cnt-nostalgia">100</span>
                        <span class="stat-label">% Nostalgia</span>
                    </div>
                </div>

                <!-- CTA -->
                <div class="cta-group">
                    <a href="{{ url('/') }}" class="btn-cyber">[ BERANDA ALUMNI ]</a>
                    <a href="{{ url('/register') }}" class="btn-cyber btn-cyber-pink">[ GABUNG SEKARANG ]</a>
                </div>

                <!-- Scroll hint -->
                <div class="scroll-hint">
                    <div class="scroll-arrow"></div>
                    <span>SCROLL DOWN</span>
                </div>

            </div>
        </section>

        <!-- ═══════════════════════════════ MOTTO ═══════════════════════════════ -->
        <section class="section-motto">
            <div class="container">
                <span class="section-label">// NILAI KAMI</span>
                <h2 class="section-title">
                    SPIRIT <span class="neon-cyan">STEMAN</span>
                </h2>

                <div class="motto-grid">
                    <div class="motto-card cyan">
                        <span class="motto-icon">📡</span>
                        <h3 class="neon-cyan">Terhubung</h3>
                        <p>
                            Menjaga ikatan sesama alumni SMKN 2 Ternate lintas
                            angkatan, lintas kota, lintas profesi — karena satu
                            almamater, selamanya satu keluarga.
                        </p>
                    </div>
                    <div class="motto-card pink">
                        <span class="motto-icon">💾</span>
                        <h3 class="neon-pink">Bernostalgia</h3>
                        <p>
                            "{{ $taglinePart1 }}" bukan sekadar slogan. Ini tentang
                            menghargai perjalanan, kenangan kelas, dan
                            persahabatan yang telah membentuk kita.
                        </p>
                    </div>
                    <div class="motto-card green">
                        <span class="motto-icon">🚀</span>
                        <h3 class="neon-green">Maju Bersama</h3>
                        <p>
                            "{{ $taglinePart2 }}" — bersama kita membangun
                            masa depan lebih cerah, saling support, berbagi
                            peluang, dan tumbuh tanpa batas.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════ JOURNEY ═══════════════════════════════ -->
        <section class="section-journey">
            <div class="container">
                <span class="section-label">// PERJALANAN KITA</span>
                <h2 class="section-title">
                    TIMELINE <span class="neon-pink">STEMAN</span>
                </h2>

                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-year">MASA LALU</div>
                        <h3 class="timeline-title neon-cyan">{{ ucwords(strtolower($taglinePart1)) }}</h3>
                        <p class="timeline-desc">
                            Kenangan bangku sekolah, teman seperjuangan, guru
                            inspiratif, dan momen-momen tak terlupakan di
                            lorong {{ setting('school_name', 'SMKN 2 Ternate') }} — semua tersimpan abadi di sini.
                        </p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot" style="border-color:var(--pink); box-shadow:0 0 8px var(--pink);"></div>
                        <div class="timeline-year" style="color:var(--pink);">MASA KINI</div>
                        <h3 class="timeline-title neon-pink">Bersatu & Berkarya</h3>
                        <p class="timeline-desc">
                            Alumni tersebar di berbagai kota, profesi, dan bidang.
                            Platform ini menjadi titik temu digital kita —
                            berbagi kabar, peluang kerja, dan inspirasi.
                        </p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot" style="border-color:var(--green); box-shadow:0 0 8px var(--green);"></div>
                        <div class="timeline-year" style="color:var(--green);">MASA DEPAN</div>
                        <h3 class="timeline-title neon-green">{{ ucwords(strtolower($taglinePart2)) }}</h3>
                        <p class="timeline-desc">
                            Bersama kita wujudkan ekosistem alumni yang kuat,
                            memberi dampak nyata bagi generasi STEMAN selanjutnya
                            dan mengharumkan nama {{ setting('school_name', 'SMKN 2 Ternate') }} ke seluruh nusantara.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════ FOOTER ═══════════════════════════════ -->
        <footer>
            <span class="footer-logo">STEMAN // {{ strtoupper(setting('school_name', 'SMKN 2 TERNATE')) }}</span>
            <p>{{ strtoupper(setting('site_tagline', 'Back to Oldies, Connected For Tomorrow')) }}</p>
            <p style="margin-top:8px;">
                &copy; {{ date('Y') }} Alumni {{ setting('school_name', 'SMKN 2 Ternate') }} &mdash; Ternate, Maluku Utara
            </p>
        </footer>

    </div>

    <script>
    // ── PARTICLE SYSTEM ──
    (function () {
        const canvas = document.getElementById('particles');
        const ctx    = canvas.getContext('2d');
        let W, H, particles = [];

        function resize() {
            W = canvas.width  = window.innerWidth;
            H = canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        const COLORS = ['#00ffff', '#ff00ff', '#ffff00', '#00ff88'];

        function createParticle() {
            return {
                x: Math.random() * W,
                y: Math.random() * H,
                vx: (Math.random() - 0.5) * 0.5,
                vy: (Math.random() - 0.5) * 0.5,
                r:  Math.random() * 1.5 + 0.5,
                color: COLORS[Math.floor(Math.random() * COLORS.length)],
                alpha: Math.random() * 0.6 + 0.2,
            };
        }

        for (let i = 0; i < 120; i++) particles.push(createParticle());

        function draw() {
            ctx.clearRect(0, 0, W, H);
            particles.forEach(p => {
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < 0 || p.x > W) p.vx *= -1;
                if (p.y < 0 || p.y > H) p.vy *= -1;

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = p.color;
                ctx.globalAlpha = p.alpha;
                ctx.shadowColor = p.color;
                ctx.shadowBlur  = 6;
                ctx.fill();
            });
            ctx.globalAlpha = 1;

            // Connecting lines between close particles
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const d  = Math.sqrt(dx * dx + dy * dy);
                    if (d < 100) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = '#00ffff';
                        ctx.globalAlpha = (1 - d / 100) * 0.15;
                        ctx.lineWidth   = 0.5;
                        ctx.stroke();
                        ctx.globalAlpha = 1;
                    }
                }
            }

            requestAnimationFrame(draw);
        }
        draw();
    })();

    // ── COUNTER ANIMATION ──
    function animateCounter(el, target, duration) {
        const start = performance.now();
        function update(now) {
            const elapsed  = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased    = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(eased * target);
            if (progress < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
    }

    // Stats (ambil dari PHP atau pakai angka default)
    const stats = {
        alumni:    {{ $totalAlumni ?? 14 }},
        angkatan:  {{ $distinctAngkatan ?? 5 }},
        kota:      {{ $distinctKota ?? 6 }},
    };

    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                animateCounter(document.getElementById('cnt-alumni'),    stats.alumni,    1500);
                animateCounter(document.getElementById('cnt-angkatan'),  stats.angkatan,  1200);
                animateCounter(document.getElementById('cnt-kota'),      stats.kota,      1300);
                observer.disconnect();
            }
        });
    }, { threshold: 0.3 });

    observer.observe(document.querySelector('.stats-bar'));
    </script>
</body>
</html>
