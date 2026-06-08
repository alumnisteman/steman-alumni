<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Ulang Tahun STEMAN 🎉</title>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;600;700&family=Permanent+Marker&family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #fffef5;
            font-family: 'Caveat', cursive;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Doodle background pattern */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(255,200,50,0.12) 0%, transparent 60%),
                radial-gradient(circle at 80% 80%, rgba(255,100,100,0.10) 0%, transparent 60%),
                radial-gradient(circle at 50% 50%, rgba(100,180,255,0.08) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .doodle-bg {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        /* Hand-drawn SVG doodles scattered around */
        .doodle-bg svg {
            position: absolute;
            opacity: 0.15;
        }

        /* Confetti */
        .confetti-container {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .confetti-piece {
            position: absolute;
            width: 10px;
            height: 14px;
            top: -20px;
            border-radius: 2px;
            animation: confettiFall linear infinite;
        }

        @keyframes confettiFall {
            0% { transform: translateY(-20px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
        }

        /* Main container */
        .main-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        /* Hero card */
        .hero-card {
            background: #fff;
            border: 3px solid #1a1a1a;
            border-radius: 20px;
            box-shadow: 8px 8px 0 #1a1a1a;
            padding: 50px 40px;
            max-width: 780px;
            width: 100%;
            position: relative;
            text-align: center;
        }

        /* Doodle border decoration */
        .hero-card::before {
            content: '';
            position: absolute;
            inset: 6px;
            border: 2px dashed rgba(0,0,0,0.12);
            border-radius: 15px;
            pointer-events: none;
        }

        /* Corner stars */
        .corner-deco {
            position: absolute;
            font-size: 1.6rem;
            animation: spinStar 4s linear infinite;
        }
        .corner-deco.tl { top: -18px; left: -18px; }
        .corner-deco.tr { top: -18px; right: -18px; animation-direction: reverse; }
        .corner-deco.bl { bottom: -18px; left: -18px; animation-direction: reverse; }
        .corner-deco.br { bottom: -18px; right: -18px; }
        @keyframes spinStar { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }

        /* Floating balloons */
        .balloon {
            position: absolute;
            font-size: 2.5rem;
            animation: floatUp 3s ease-in-out infinite alternate;
        }
        .balloon.b1 { top: -50px; left: 8%; animation-delay: 0s; }
        .balloon.b2 { top: -60px; right: 8%; animation-delay: 0.8s; }
        .balloon.b3 { top: -40px; left: 30%; animation-delay: 0.4s; }
        .balloon.b4 { top: -55px; right: 28%; animation-delay: 1.2s; }
        @keyframes floatUp {
            0% { transform: translateY(0) rotate(-5deg); }
            100% { transform: translateY(-16px) rotate(5deg); }
        }

        /* HUT badge */
        .hut-badge {
            display: inline-block;
            background: #ffdd00;
            border: 2px solid #1a1a1a;
            border-radius: 50px;
            padding: 6px 24px;
            font-family: 'Caveat', cursive;
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 18px;
            box-shadow: 3px 3px 0 #1a1a1a;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Main title */
        .main-title {
            font-family: 'Permanent Marker', cursive;
            font-size: clamp(2rem, 6vw, 3.8rem);
            color: #e63946;
            line-height: 1.15;
            margin-bottom: 8px;
            text-shadow: 3px 3px 0 rgba(230,57,70,0.2);
            position: relative;
        }

        .main-title .steman-word {
            color: #1d3557;
            display: inline-block;
            position: relative;
        }

        /* Underline doodle under STEMAN */
        .main-title .steman-word::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0; right: 0;
            height: 6px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 10'%3E%3Cpath d='M0 8 Q25 2 50 7 Q75 12 100 5' stroke='%23e63946' stroke-width='3' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat center;
            background-size: 100% 100%;
        }

        /* Emoji party */
        .party-emojis {
            font-size: 2.5rem;
            margin: 16px 0;
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .party-emojis span {
            display: inline-block;
            animation: wiggle 1.5s ease-in-out infinite;
        }
        .party-emojis span:nth-child(2) { animation-delay: 0.2s; }
        .party-emojis span:nth-child(3) { animation-delay: 0.4s; }
        .party-emojis span:nth-child(4) { animation-delay: 0.6s; }
        .party-emojis span:nth-child(5) { animation-delay: 0.8s; }
        @keyframes wiggle {
            0%,100%{transform:rotate(-8deg) scale(1)}
            50%{transform:rotate(8deg) scale(1.15)}
        }

        /* Tagline */
        .tagline {
            font-family: 'Caveat', cursive;
            font-size: clamp(1.4rem, 3.5vw, 2rem);
            color: #2d6a4f;
            font-weight: 700;
            margin: 10px 0 6px;
            line-height: 1.3;
        }

        /* Separator doodle */
        .doodle-separator {
            margin: 20px auto;
            width: 200px;
            height: 20px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 20'%3E%3Cpath d='M5 10 Q15 2 25 10 Q35 18 45 10 Q55 2 65 10 Q75 18 85 10 Q95 2 105 10 Q115 18 125 10 Q135 2 145 10 Q155 18 165 10 Q175 2 185 10 Q195 18 198 10' stroke='%23e63946' stroke-width='2.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat center;
            background-size: contain;
        }

        /* School name badge */
        .school-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #1d3557;
            color: #fff;
            border-radius: 12px;
            border: 2px solid #1a1a1a;
            padding: 10px 28px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            box-shadow: 4px 4px 0 #1a1a1a;
            margin-top: 6px;
            letter-spacing: 0.5px;
        }

        /* Doodle divider line */
        .hand-divider {
            border: none;
            height: 3px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 6'%3E%3Cpath d='M0 3 Q50 1 100 3 Q150 5 200 3 Q250 1 300 3 Q350 5 400 3' stroke='%23adb5bd' stroke-width='2' fill='none'/%3E%3C/svg%3E") no-repeat center;
            background-size: 100%;
            margin: 32px 0;
        }

        /* Values row */
        .values-row {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .value-chip {
            background: #fff;
            border: 2.5px solid #1a1a1a;
            border-radius: 50px;
            padding: 10px 22px;
            font-family: 'Caveat', cursive;
            font-size: 1.15rem;
            font-weight: 700;
            box-shadow: 3px 3px 0 #1a1a1a;
            transition: transform 0.15s;
            cursor: default;
        }
        .value-chip:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0 #1a1a1a; }
        .value-chip.red { background: #ffe8e8; border-color: #e63946; box-shadow: 3px 3px 0 #e63946; }
        .value-chip.blue { background: #e8f0ff; border-color: #1d3557; box-shadow: 3px 3px 0 #1d3557; }
        .value-chip.green { background: #e8f8ef; border-color: #2d6a4f; box-shadow: 3px 3px 0 #2d6a4f; }

        /* Message box */
        .message-box {
            background: #fffbeb;
            border: 2px solid #f59e0b;
            border-radius: 16px;
            padding: 20px 24px;
            margin: 0 0 28px;
            position: relative;
        }
        .message-box::before {
            content: '✍️';
            position: absolute;
            top: -14px; left: 20px;
            font-size: 1.4rem;
            background: #fffbeb;
            padding: 0 6px;
        }
        .message-box p {
            font-family: 'Caveat', cursive;
            font-size: 1.25rem;
            color: #78350f;
            line-height: 1.6;
            font-weight: 600;
        }

        /* Doodle illustrations - inline SVGs */
        .doodle-row {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 10px 0 24px;
            flex-wrap: wrap;
        }

        .doodle-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            font-family: 'Caveat', cursive;
            font-size: 1rem;
            font-weight: 700;
            color: #555;
        }

        /* Footer links */
        .card-footer-links {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-doodle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 50px;
            border: 2.5px solid #1a1a1a;
            font-family: 'Caveat', cursive;
            font-size: 1.15rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 4px 4px 0 #1a1a1a;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn-doodle:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0 #1a1a1a;
        }
        .btn-primary-doodle { background: #e63946; color: #fff; }
        .btn-secondary-doodle { background: #fff; color: #1a1a1a; }

        /* Floating doodle elements outside card */
        .float-el {
            position: fixed;
            font-size: 2rem;
            pointer-events: none;
            z-index: 5;
            animation: floatAround 6s ease-in-out infinite alternate;
        }
        .float-el:nth-child(1) { top: 10%; left: 3%; animation-delay: 0s; }
        .float-el:nth-child(2) { top: 60%; left: 2%; animation-delay: 1s; }
        .float-el:nth-child(3) { top: 20%; right: 3%; animation-delay: 0.5s; }
        .float-el:nth-child(4) { top: 70%; right: 2%; animation-delay: 1.5s; }
        .float-el:nth-child(5) { bottom: 10%; left: 10%; animation-delay: 2s; }
        .float-el:nth-child(6) { bottom: 15%; right: 8%; animation-delay: 0.8s; }
        @keyframes floatAround {
            0% { transform: translateY(0) rotate(-5deg) scale(1); }
            100% { transform: translateY(-20px) rotate(5deg) scale(1.08); }
        }

        /* Responsive */
        @media (max-width: 600px) {
            .hero-card { padding: 36px 20px; }
            .balloon { font-size: 1.8rem; }
            .float-el { display: none; }
        }
    </style>
</head>
<body>

    <!-- Confetti -->
    <div class="confetti-container" id="confettiContainer"></div>

    <!-- Floating doodle elements -->
    <div class="float-el">🎈</div>
    <div class="float-el">⭐</div>
    <div class="float-el">🎊</div>
    <div class="float-el">✨</div>
    <div class="float-el">🏫</div>
    <div class="float-el">🎓</div>

    <!-- Doodle background SVGs -->
    <div class="doodle-bg">
        <!-- Scribble circles -->
        <svg width="120" height="120" style="top:5%; left:5%;">
            <circle cx="60" cy="60" r="50" fill="none" stroke="#e63946" stroke-width="3"
                    stroke-dasharray="8 6" stroke-linecap="round"/>
        </svg>
        <svg width="80" height="80" style="bottom:8%; right:8%;">
            <circle cx="40" cy="40" r="35" fill="none" stroke="#1d3557" stroke-width="3"
                    stroke-dasharray="6 5" stroke-linecap="round"/>
        </svg>
        <!-- Stars -->
        <svg width="60" height="60" style="top:15%; right:12%;">
            <polygon points="30,5 34,22 52,22 37,33 43,50 30,40 17,50 23,33 8,22 26,22"
                     fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linejoin="round"/>
        </svg>
        <svg width="45" height="45" style="bottom:20%; left:10%;">
            <polygon points="22,3 26,16 39,16 29,25 33,38 22,30 11,38 15,25 5,16 18,16"
                     fill="none" stroke="#2d6a4f" stroke-width="2" stroke-linejoin="round"/>
        </svg>
        <!-- Spirals / waves -->
        <svg width="150" height="40" style="top:45%; left:2%;">
            <path d="M10 20 Q30 5 50 20 Q70 35 90 20 Q110 5 130 20 Q145 30 150 20"
                  stroke="#a0aec0" stroke-width="2.5" fill="none" stroke-linecap="round"/>
        </svg>
        <svg width="150" height="40" style="top:45%; right:2%;">
            <path d="M10 20 Q30 5 50 20 Q70 35 90 20 Q110 5 130 20 Q145 30 150 20"
                  stroke="#a0aec0" stroke-width="2.5" fill="none" stroke-linecap="round"/>
        </svg>
    </div>

    <!-- Main -->
    <div class="main-wrapper">
        <div class="hero-card">

            <!-- Corner decorations -->
            <span class="corner-deco tl">⭐</span>
            <span class="corner-deco tr">✨</span>
            <span class="corner-deco bl">🎊</span>
            <span class="corner-deco br">🎉</span>

            <!-- Balloons -->
            <span class="balloon b1">🎈</span>
            <span class="balloon b2">🎈</span>
            <span class="balloon b3">🎀</span>
            <span class="balloon b4">🎁</span>

            <!-- Badge -->
            <div class="hut-badge">🎂 Hari Ulang Tahun</div>

            <!-- Main title -->
            <h1 class="main-title">
                Selamat Ulang Tahun<br>
                <span class="steman-word">STEMAN</span>!
            </h1>

            <!-- Emojis -->
            <div class="party-emojis">
                <span>🎉</span>
                <span>🎊</span>
                <span>🎂</span>
                <span>🥳</span>
                <span>🎈</span>
            </div>

            <!-- Tagline -->
            <p class="tagline">Bersatu &middot; Berkarya &middot; Berprestasi</p>

            <!-- Doodle separator -->
            <div class="doodle-separator"></div>

            <!-- School badge -->
            <div class="school-badge">
                🏫 Alumni SMKN 2 Ternate
            </div>

            <!-- Divider -->
            <hr class="hand-divider">

            <!-- Doodle values row -->
            <div class="values-row">
                <div class="value-chip red">❤️ Bersatu</div>
                <div class="value-chip blue">💡 Berkarya</div>
                <div class="value-chip green">🏆 Berprestasi</div>
            </div>

            <!-- Message box -->
            <div class="message-box">
                <p>
                    Semoga STEMAN terus tumbuh, berkembang, dan melahirkan alumni-alumni
                    terbaik yang membawa harum nama <strong>SMKN 2 Ternate</strong>
                    ke seluruh penjuru negeri. Tetap kompak &amp; semangat! 💪
                </p>
            </div>

            <!-- Doodle illustration row -->
            <div class="doodle-row">
                <div class="doodle-item">
                    <svg width="70" height="70" viewBox="0 0 70 70">
                        <!-- Mortar board (graduation cap) doodle -->
                        <rect x="20" y="35" width="30" height="18" rx="4" fill="none" stroke="#1d3557" stroke-width="2.5" stroke-linecap="round"/>
                        <polygon points="35,20 10,35 35,42 60,35" fill="none" stroke="#1d3557" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>
                        <circle cx="35" cy="20" r="4" fill="none" stroke="#e63946" stroke-width="2"/>
                        <line x1="58" y1="35" x2="62" y2="50" stroke="#1d3557" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="62" cy="52" r="3" fill="#e63946"/>
                    </svg>
                    <span>Lulus Bareng</span>
                </div>
                <div class="doodle-item">
                    <svg width="70" height="70" viewBox="0 0 70 70">
                        <!-- Star trophy doodle -->
                        <path d="M35 8 L40 25 L58 25 L44 36 L49 53 L35 43 L21 53 L26 36 L12 25 L30 25 Z"
                              fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>
                        <circle cx="35" cy="30" r="5" fill="none" stroke="#e63946" stroke-width="2"/>
                    </svg>
                    <span>Berprestasi</span>
                </div>
                <div class="doodle-item">
                    <svg width="70" height="70" viewBox="0 0 70 70">
                        <!-- Handshake / togetherness doodle -->
                        <path d="M10 40 Q20 30 30 35 Q35 38 40 35 Q50 30 60 40"
                              fill="none" stroke="#2d6a4f" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="20" cy="28" r="8" fill="none" stroke="#2d6a4f" stroke-width="2.5"/>
                        <circle cx="50" cy="28" r="8" fill="none" stroke="#e63946" stroke-width="2.5"/>
                        <path d="M20 36 L20 55" stroke="#2d6a4f" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M50 36 L50 55" stroke="#e63946" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M16 55 L24 55" stroke="#2d6a4f" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M46 55 L54 55" stroke="#e63946" stroke-width="2.5" stroke-linecap="round"/>
                        <!-- Heart between them -->
                        <path d="M35 20 C35 17 32 14 29 17 C26 20 29 25 35 29 C41 25 44 20 41 17 C38 14 35 17 35 20Z"
                              fill="none" stroke="#e63946" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                    <span>Bersatu</span>
                </div>
                <div class="doodle-item">
                    <svg width="70" height="70" viewBox="0 0 70 70">
                        <!-- Lightbulb / berkarya doodle -->
                        <path d="M35 12 C25 12 18 20 18 29 C18 36 22 42 28 45 L28 54 L42 54 L42 45 C48 42 52 36 52 29 C52 20 45 12 35 12Z"
                              fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="29" y1="58" x2="41" y2="58" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="31" y1="62" x2="39" y2="62" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M35 20 L33 30 L37 30 L35 38" stroke="#e63946" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Berkarya</span>
                </div>
            </div>

            <!-- CTA buttons -->
            <div class="card-footer-links">
                <a href="{{ url('/') }}" class="btn-doodle btn-primary-doodle">
                    🏠 Beranda Alumni
                </a>
                <a href="{{ url('/birthday') }}" class="btn-doodle btn-secondary-doodle">
                    🎂 Ucapkan Selamat
                </a>
            </div>
        </div>

        <!-- Footer text -->
        <p style="margin-top: 24px; font-family: 'Caveat', cursive; font-size: 1rem; color: #888; text-align: center;">
            ✍️ Dibuat dengan ❤️ oleh tim Alumni STEMAN &mdash; SMKN 2 Ternate
        </p>
    </div>

    <script>
        // Generate confetti
        const colors = ['#e63946','#f59e0b','#2d6a4f','#1d3557','#ff6b6b','#ffd93d','#6bcb77','#4d96ff'];
        const container = document.getElementById('confettiContainer');

        for (let i = 0; i < 60; i++) {
            const piece = document.createElement('div');
            piece.className = 'confetti-piece';
            piece.style.cssText = `
                left: ${Math.random() * 100}%;
                width: ${6 + Math.random() * 8}px;
                height: ${10 + Math.random() * 10}px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                animation-duration: ${3 + Math.random() * 5}s;
                animation-delay: ${Math.random() * 5}s;
                border-radius: ${Math.random() > 0.5 ? '50%' : '2px'};
                opacity: ${0.6 + Math.random() * 0.4};
            `;
            container.appendChild(piece);
        }
    </script>
</body>
</html>
