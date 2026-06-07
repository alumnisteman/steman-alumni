<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Steman Alumni - Coming Soon</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #ffcc00;
            --secondary: #0f172a;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--secondary);
            overflow: hidden;
            margin: 0;
        }

        /* Cinematic Preloader */
        #preloader {
            position: fixed;
            inset: 0;
            z-index: 100;
            background: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 1s ease-out, visibility 1s;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 1; }
        }

        /* Animated Mesh Background */
        .mesh-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: var(--secondary);
            overflow: hidden;
        }
        .mesh-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.4;
            animation: drift 20s infinite alternate ease-in-out;
        }
        @keyframes drift {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.5); }
        }

        /* Parallax Scene */
        #parallax-scene {
            position: relative;
            z-index: 10;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1000px;
        }

        /* Glassmorphism Card */
        .premium-glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.8);
            transform-style: preserve-3d;
            transition: transform 0.1s ease-out;
        }

        .gradient-text {
            background: linear-gradient(135deg, #fff 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ===== FLIP COUNTDOWN ===== */
        .countdown-wrap {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            margin-bottom: 12px;
        }

        .flip-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 90px;
        }

        .flip-card {
            position: relative;
            width: 90px;
            height: 90px;
            perspective: 400px;
        }

        .flip-front, .flip-back {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            font-weight: 900;
            color: #fff;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backface-visibility: hidden;
            letter-spacing: -2px;
        }

        .flip-front::after {
            content: '';
            position: absolute;
            left: 0; right: 0; bottom: 50%;
            height: 1px;
            background: rgba(0,0,0,0.3);
        }

        .flip-back {
            transform: rotateX(180deg);
            background: rgba(255, 204, 0, 0.12);
            border-color: rgba(255, 204, 0, 0.2);
        }

        .flip-card.flipping .flip-front {
            animation: flipTop 0.3s ease-in forwards;
        }
        .flip-card.flipping .flip-back {
            animation: flipBottom 0.3s ease-out 0.3s forwards;
        }

        @keyframes flipTop {
            0%   { transform: rotateX(0deg); }
            100% { transform: rotateX(-90deg); }
        }
        @keyframes flipBottom {
            0%   { transform: rotateX(90deg); }
            100% { transform: rotateX(0deg); }
        }

        .flip-label {
            margin-top: 10px;
            font-size: 0.6rem;
            text-transform: uppercase;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 3px;
        }

        /* Seconds pulse ring */
        .seconds-ring {
            position: absolute;
            inset: -4px;
            border-radius: 22px;
            border: 2px solid rgba(255, 204, 0, 0.5);
            animation: ringPulse 1s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes ringPulse {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.03); }
        }

        /* Target date badge */
        .target-date-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 204, 0, 0.1);
            border: 1px solid rgba(255, 204, 0, 0.25);
            color: var(--primary);
            border-radius: 999px;
            padding: 6px 18px;
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 32px;
            letter-spacing: 0.5px;
        }

        /* Launched overlay */
        #launched-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 200;
            background: var(--secondary);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }
        #launched-overlay.show {
            display: flex;
            animation: fadeIn 0.8s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; } to { opacity: 1; }
        }
        .rocket-launch {
            font-size: 5rem;
            animation: rocketFly 1.5s ease-in-out infinite alternate;
        }
        @keyframes rocketFly {
            0% { transform: translateY(0) rotate(-10deg); }
            100% { transform: translateY(-20px) rotate(10deg); }
        }
    </style>
</head>
<body class="bg-secondary text-white">

    <!-- Cinematic Preloader -->
    <div id="preloader">
        <div style="display:flex; flex-direction:column; align-items:center; animation: pulse 2s infinite ease-in-out;">
            <div class="w-16 h-16 border-4 border-primary border-t-transparent rounded-full animate-spin mb-4" style="width:64px;height:64px;border:4px solid #ffcc00;border-top-color:transparent;border-radius:50%;animation:spin 0.8s linear infinite;"></div>
            <div style="color:var(--primary);font-weight:900;letter-spacing:6px;font-size:0.75rem;">STMN.ALMNI</div>
        </div>
    </div>
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>

    <!-- Portal Is Live Overlay -->
    <div id="launched-overlay">
        <div class="rocket-launch">🚀</div>
        <h2 style="font-size:2.5rem;font-weight:900;color:var(--primary);margin:16px 0 8px;">PORTAL IS LIVE!</h2>
        <p style="color:rgba(255,255,255,0.6);margin-bottom:32px;">Mengarahkan Anda ke portal...</p>
        <div id="redirect-countdown" style="font-size:1rem;color:rgba(255,255,255,0.4);">Redirect dalam <span id="redirect-secs">5</span> detik</div>
    </div>

    <!-- Animated Mesh BG -->
    <div class="mesh-bg">
        <div class="mesh-circle" style="width:600px;height:600px;background:#ffcc00;top:-192px;left:-192px;animation-delay:0s;"></div>
        <div class="mesh-circle" style="width:500px;height:500px;background:#4f46e5;bottom:-192px;right:-192px;animation-duration:25s;animation-delay:-5s;"></div>
        <div class="mesh-circle" style="width:400px;height:400px;background:#7c3aed;top:50%;left:50%;animation-duration:30s;animation-delay:-10s;"></div>
    </div>

    <!-- Content -->
    <div id="parallax-scene">
        <div id="main-card" class="premium-glass p-8 md:p-16 rounded-[48px] max-w-4xl w-[90%] text-center" style="padding:48px 56px;border-radius:48px;max-width:860px;width:90%;">

            <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;background:rgba(255,204,0,0.15);color:var(--primary);border-radius:999px;font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:4px;margin-bottom:32px;">
                <span style="position:relative;display:flex;width:8px;height:8px;">
                    <span style="position:absolute;width:100%;height:100%;border-radius:50%;background:var(--primary);opacity:0.75;animation:ping 1s cubic-bezier(0,0,0.2,1) infinite;"></span>
                    <span style="position:relative;width:8px;height:8px;border-radius:50%;background:var(--primary);"></span>
                </span>
                Launching Soon
            </div>
            <style>@keyframes ping { 75%,100% { transform:scale(2); opacity:0; } }</style>

            <h1 style="font-size:clamp(2.5rem,6vw,4.5rem);font-weight:900;margin-bottom:24px;line-height:1.1;letter-spacing:-2px;">
                THE NEXT <br>
                <span class="gradient-text">EVOLUTION</span>
            </h1>

            <p style="font-size:1.1rem;color:rgba(148,163,184,1);margin-bottom:40px;max-width:560px;margin-left:auto;margin-right:auto;line-height:1.8;">
                Kami sedang melakukan kalibrasi sistem besar-besaran untuk menghadirkan pengalaman alumni yang lebih cerdas, modern, dan terintegrasi.
            </p>

            @php
                $rawDate = setting('launch_date');
                $hasDate = !empty($rawDate) && strtotime($rawDate) > 0;
                $launchDateStr = $hasDate ? $rawDate : now()->addDays(7)->format('Y-m-d H:i:s');
                $bulanId = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $ts = strtotime($launchDateStr);
                $launchFormatted = date('d', $ts) . ' ' . $bulanId[(int)date('n', $ts)] . ' ' . date('Y, H:i', $ts) . ' WIT';
            @endphp

            <!-- Target Date Badge -->
            <div class="target-date-badge">
                <i class="bi bi-calendar-check"></i>
                Target Launch: {{ $launchFormatted }}
                @if(!$hasDate)
                    <span style="opacity:0.6;font-size:0.7rem;">(belum diatur)</span>
                @endif
            </div>

            <!-- Flip Countdown -->
            <div class="countdown-wrap" id="countdown-wrap" data-target="{{ \Carbon\Carbon::parse($launchDateStr)->timestamp * 1000 }}">
                <div class="flip-box">
                    <div class="flip-card" id="flip-days">
                        <div class="flip-front" id="days-front">00</div>
                        <div class="flip-back" id="days-back">00</div>
                    </div>
                    <div class="flip-label">Hari</div>
                </div>
                <div class="flip-box">
                    <div class="flip-card" id="flip-hours">
                        <div class="flip-front" id="hours-front">00</div>
                        <div class="flip-back" id="hours-back">00</div>
                    </div>
                    <div class="flip-label">Jam</div>
                </div>
                <div class="flip-box">
                    <div class="flip-card" id="flip-minutes">
                        <div class="flip-front" id="minutes-front">00</div>
                        <div class="flip-back" id="minutes-back">00</div>
                    </div>
                    <div class="flip-label">Menit</div>
                </div>
                <div class="flip-box">
                    <div class="flip-card" id="flip-seconds">
                        <div class="seconds-ring"></div>
                        <div class="flip-front" id="seconds-front">00</div>
                        <div class="flip-back" id="seconds-back">00</div>
                    </div>
                    <div class="flip-label">Detik</div>
                </div>
            </div>

            <!-- CTAs -->
            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:16px;margin-top:40px;">
                <a href="/news" style="padding:14px 40px;background:var(--primary);color:var(--secondary);font-weight:800;border-radius:16px;text-decoration:none;transition:transform 0.2s;display:inline-flex;align-items:center;gap:8px;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    BACA BERITA TERBARU <i class="bi bi-arrow-right"></i>
                </a>
                <a href="/login" style="padding:14px 40px;background:rgba(255,255,255,0.08);color:#fff;font-weight:800;border-radius:16px;text-decoration:none;border:1px solid rgba(255,255,255,0.12);transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                    MASUK PORTAL
                </a>
            </div>

            <div style="margin-top:48px;padding-top:24px;border-top:1px solid rgba(255,255,255,0.06);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;color:rgba(100,116,139,1);font-size:0.85rem;">
                <div>&copy; 2026 {{ setting('school_name', 'SMKN 2 Ternate') }}</div>
                <div style="display:flex;gap:24px;">
                    <a href="#" style="color:inherit;text-decoration:none;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='inherit'">Instagram</a>
                    <a href="#" style="color:inherit;text-decoration:none;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='inherit'">Twitter</a>
                    <a href="#" style="color:inherit;text-decoration:none;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='inherit'">LinkedIn</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preloader
        window.addEventListener('load', () => {
            const preloader = document.getElementById('preloader');
            preloader.style.opacity = '0';
            setTimeout(() => preloader.style.visibility = 'hidden', 1000);
        });

        // Mouse Parallax
        const scene = document.getElementById('parallax-scene');
        const card = document.getElementById('main-card');
        scene.addEventListener('mousemove', (e) => {
            const x = (window.innerWidth / 2 - e.pageX) / 60;
            const y = (window.innerHeight / 2 - e.pageY) / 60;
            card.style.transform = `rotateY(${x}deg) rotateX(${-y}deg)`;
        });

        // ===== FLIP COUNTDOWN =====
        const targetMs = parseInt(document.getElementById('countdown-wrap').dataset.target);

        const prevValues = { days: -1, hours: -1, minutes: -1, seconds: -1 };

        function pad(n) { return n.toString().padStart(2, '0'); }

        function flip(id, newVal) {
            const card = document.getElementById('flip-' + id);
            const front = document.getElementById(id + '-front');
            const back  = document.getElementById(id + '-back');

            if (front.textContent === pad(newVal)) return;

            back.textContent = pad(newVal);
            card.classList.remove('flipping');
            void card.offsetWidth;
            card.classList.add('flipping');

            setTimeout(() => {
                front.textContent = pad(newVal);
                card.classList.remove('flipping');
            }, 650);
        }

        function updateCountdown() {
            const now = Date.now();
            const distance = targetMs - now;

            if (distance <= 0) {
                flip('days', 0); flip('hours', 0); flip('minutes', 0); flip('seconds', 0);
                showLaunched();
                return;
            }

            const days    = Math.floor(distance / 86400000);
            const hours   = Math.floor((distance % 86400000) / 3600000);
            const minutes = Math.floor((distance % 3600000) / 60000);
            const seconds = Math.floor((distance % 60000) / 1000);

            if (days    !== prevValues.days)    { flip('days', days);       prevValues.days    = days; }
            if (hours   !== prevValues.hours)   { flip('hours', hours);     prevValues.hours   = hours; }
            if (minutes !== prevValues.minutes) { flip('minutes', minutes); prevValues.minutes = minutes; }
            if (seconds !== prevValues.seconds) { flip('seconds', seconds); prevValues.seconds = seconds; }
        }

        function showLaunched() {
            clearInterval(countdownInterval);
            const overlay = document.getElementById('launched-overlay');
            overlay.classList.add('show');

            let secs = 5;
            const tick = setInterval(() => {
                secs--;
                document.getElementById('redirect-secs').textContent = secs;
                if (secs <= 0) {
                    clearInterval(tick);
                    window.location.href = '/';
                }
            }, 1000);
        }

        const countdownInterval = setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
</body>
</html>
