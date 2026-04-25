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
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--secondary);
            overflow: hidden;
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
        .preloader-logo {
            width: 120px;
            height: 120px;
            position: relative;
            animation: pulse 2s infinite ease-in-out;
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

        /* Mouse Parallax Container */
        #parallax-scene {
            position: relative;
            z-index: 10;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1000px;
        }

        /* Glassmorphism Card Enhanced */
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
        }

        .countdown-item {
            @apply flex flex-col items-center justify-center p-4 bg-white/5 rounded-3xl border border-white/5 min-w-[100px];
        }
    </style>
</head>
<body class="bg-secondary text-white">

    <!-- Cinematic Preloader -->
    <div id="preloader">
        <div class="preloader-logo flex flex-col items-center">
            <div class="w-16 h-16 border-4 border-primary border-t-transparent rounded-full animate-spin mb-4"></div>
            <div class="text-primary font-black tracking-widest text-sm uppercase">STMN.ALMNI</div>
        </div>
    </div>

    <!-- Animated Mesh BG -->
    <div class="mesh-bg">
        <div class="mesh-circle w-[600px] h-[600px] bg-primary -top-48 -left-48" style="animation-delay: 0s;"></div>
        <div class="mesh-circle w-[500px] h-[500px] bg-accent -bottom-48 -right-48" style="animation-duration: 25s; animation-delay: -5s;"></div>
        <div class="mesh-circle w-[400px] h-[400px] bg-indigo-600 top-1/2 left-1/2" style="animation-duration: 30s; animation-delay: -10s;"></div>
    </div>

    <!-- Content -->
    <div id="parallax-scene">
        <div id="main-card" class="premium-glass p-8 md:p-16 rounded-[48px] max-w-4xl w-[90%] text-center animate-in fade-in slide-in-from-bottom-8 duration-1000">
            
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary/20 text-primary rounded-full text-xs font-bold uppercase tracking-widest mb-8">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                Launching Soon
            </div>

            <h1 class="text-5xl md:text-7xl font-black mb-6 tracking-tighter leading-tight">
                THE NEXT <br>
                <span class="gradient-text">EVOLUTION</span>
            </h1>

            <p class="text-lg md:text-xl text-slate-400 mb-12 max-w-2xl mx-auto leading-relaxed">
                Kami sedang melakukan kalibrasi sistem besar-besaran untuk menghadirkan pengalaman alumni yang lebih cerdas, modern, dan terintegrasi.
            </p>

            <!-- Countdown -->
            <div class="flex flex-wrap justify-center gap-4 mb-12" data-date="{{ setting('launch_date', now()->addDays(5)->format('Y-m-d\TH:i')) }}">
                <div class="countdown-item">
                    <span id="days" class="text-3xl md:text-4xl font-black text-white">00</span>
                    <span class="text-[10px] uppercase font-bold text-primary tracking-widest mt-1">Hari</span>
                </div>
                <div class="countdown-item">
                    <span id="hours" class="text-3xl md:text-4xl font-black text-white">00</span>
                    <span class="text-[10px] uppercase font-bold text-primary tracking-widest mt-1">Jam</span>
                </div>
                <div class="countdown-item">
                    <span id="minutes" class="text-3xl md:text-4xl font-black text-white">00</span>
                    <span class="text-[10px] uppercase font-bold text-primary tracking-widest mt-1">Menit</span>
                </div>
                <div class="countdown-item">
                    <span id="seconds" class="text-3xl md:text-4xl font-black text-white">00</span>
                    <span class="text-[10px] uppercase font-bold text-primary tracking-widest mt-1">Detik</span>
                </div>
            </div>

            <!-- CTAs -->
            <div class="flex flex-col md:flex-row items-center justify-center gap-4">
                <a href="/news" class="w-full md:w-auto px-10 py-4 bg-primary text-secondary font-extrabold rounded-2xl hover:scale-105 transition-transform shadow-xl shadow-primary/20">
                    BACA BERITA TERBARU <i class="bi bi-arrow-right ms-2"></i>
                </a>
                <a href="/login" class="w-full md:w-auto px-10 py-4 bg-white/10 hover:bg-white/20 text-white font-extrabold rounded-2xl transition-all border border-white/10">
                    MASUK PORTAL
                </a>
            </div>

            <div class="mt-16 pt-8 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-4 text-slate-500 text-sm">
                <div>&copy; 2026 {{ setting('school_name', 'SMKN 2 Ternate') }}</div>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-primary transition-colors">Instagram</a>
                    <a href="#" class="hover:text-primary transition-colors">Twitter</a>
                    <a href="#" class="hover:text-primary transition-colors">LinkedIn</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preloader Logic
        window.addEventListener('load', () => {
            const preloader = document.getElementById('preloader');
            preloader.style.opacity = '0';
            setTimeout(() => {
                preloader.style.visibility = 'hidden';
            }, 1000);
        });

        // Mouse Parallax Logic
        const scene = document.getElementById('parallax-scene');
        const card = document.getElementById('main-card');

        scene.addEventListener('mousemove', (e) => {
            const x = (window.innerWidth / 2 - e.pageX) / 50;
            const y = (window.innerHeight / 2 - e.pageY) / 50;
            card.style.transform = `rotateY(${x}deg) rotateX(${-y}deg)`;
        });

        // Countdown Logic
        const targetDateStr = document.querySelector('[data-date]').dataset.date;
        const targetDate = new Date(targetDateStr).getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                document.getElementById("days").innerText = "00";
                document.getElementById("hours").innerText = "00";
                document.getElementById("minutes").innerText = "00";
                document.getElementById("seconds").innerText = "00";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("days").innerText = days.toString().padStart(2, '0');
            document.getElementById("hours").innerText = hours.toString().padStart(2, '0');
            document.getElementById("minutes").innerText = minutes.toString().padStart(2, '0');
            document.getElementById("seconds").innerText = seconds.toString().padStart(2, '0');
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
</body>
</html>
