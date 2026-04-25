@extends('layouts.app')

@section('meta')
    <meta name="description" content="{{ Str::limit($successStory->quote, 160) }}">
    <meta property="og:title" content="Kisah Inspiratif: {{ $successStory->name }} - {{ $successStory->title }}">
    <meta property="og:description" content="{{ Str::limit($successStory->quote, 160) }}">
    <meta property="og:image" content="{{ $successStory->image_path ? asset('storage/'.$successStory->image_path) : 'https://ui-avatars.com/api/?name='.urlencode($successStory->name).'&size=600&background=ffcc00&color=000' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $successStory->name }} - {{ $successStory->title }}">
    <meta name="twitter:description" content="{{ Str::limit($successStory->quote, 160) }}">
    <meta name="twitter:image" content="{{ $successStory->image_path ? asset('storage/'.$successStory->image_path) : 'https://ui-avatars.com/api/?name='.urlencode($successStory->name).'&size=600&background=ffcc00&color=000' }}">
@endsection

@section('content')
<style>
    .story-detail-bg {
        background-color: #050505; color: #f8fafc;
        font-family: 'Inter', sans-serif;
        min-height: 100vh; position: relative; overflow-x: hidden;
    }

    /* CUSTOM GLOWING CURSOR */
    body { cursor: none; }
    .custom-cursor {
        position: fixed; top: 0; left: 0; width: 20px; height: 20px;
        border-radius: 50%; pointer-events: none; z-index: 9999;
        background: #06b6d4; mix-blend-mode: screen;
        transform: translate(-50%, -50%); transition: width 0.2s, height 0.2s, background-color 0.2s;
        box-shadow: 0 0 20px #06b6d4, 0 0 40px #06b6d4;
    }
    .custom-cursor-trail {
        position: fixed; top: 0; left: 0; width: 40px; height: 40px;
        border-radius: 50%; pointer-events: none; z-index: 9998;
        border: 1px solid rgba(6, 182, 212, 0.5);
        transform: translate(-50%, -50%); transition: all 0.1s ease-out;
    }
    .cursor-hover { width: 60px; height: 60px; background: rgba(99, 102, 241, 0.5); box-shadow: 0 0 30px #4f46e5; border: none; }

    /* SPOTLIGHT EFFECT CARDS */
    .bento-card {
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 32px; padding: 2rem;
        position: relative; overflow: hidden;
    }
    .bento-card::before {
        content: ""; position: absolute; inset: 0; border-radius: inherit;
        background: radial-gradient( 800px circle at var(--mouse-x) var(--mouse-y), rgba(255, 255, 255, 0.06), transparent 40% );
        z-index: 0; opacity: 0; transition: opacity 0.5s; pointer-events: none;
    }
    .bento-card:hover::before { opacity: 1; }
    .bento-card::after {
        content: ""; position: absolute; inset: 0; border-radius: inherit; padding: 1px;
        background: radial-gradient( 400px circle at var(--mouse-x) var(--mouse-y), rgba(6, 182, 212, 0.5), transparent 40% );
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor; mask-composite: exclude;
        z-index: 0; opacity: 0; transition: opacity 0.5s; pointer-events: none;
    }
    .bento-card:hover::after { opacity: 1; }
    .bento-card > * { position: relative; z-index: 1; }

    .cyber-grid {
        position: absolute; inset: 0; z-index: 0;
        background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                          linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        background-size: 50px 50px; pointer-events: none;
    }

    @media (max-width: 991px) {
        body { cursor: auto; }
        .custom-cursor, .custom-cursor-trail { display: none !important; }
        .bento-card { padding: 1.5rem; border-radius: 24px; }
    }
</style>

<!-- Custom Cursors -->
<div class="custom-cursor"></div>
<div class="custom-cursor-trail"></div>

<div class="story-detail-bg" id="detail-grid">
    <div class="cyber-grid"></div>
    
    <!-- Hero Header -->
    <div class="position-relative overflow-hidden py-5 gsap-fade-up" style="background: radial-gradient(circle at center, rgba(6, 182, 212, 0.1) 0%, #050505 100%); min-height: 400px; border-bottom: 1px solid rgba(255,255,255,0.05);">
        <div class="container position-relative z-index-1 mt-5">
            <div class="row align-items-center">
                <div class="col-lg-8 text-white">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/" class="text-white-50 text-decoration-none magnetic-el hover-text-white transition">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('success_stories.index') }}" class="text-white-50 text-decoration-none magnetic-el hover-text-white transition">Jejak Sukses</a></li>
                            <li class="breadcrumb-item active text-info" aria-current="page">{{ $successStory->name }}</li>
                        </ol>
                    </nav>
                    <h1 class="display-3 fw-black mb-3 text-white" style="letter-spacing: -1px; text-shadow: 0 0 40px rgba(6, 182, 212, 0.3);">{{ $successStory->name }}</h1>
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="badge bg-info text-dark fs-6 px-3 py-2 rounded-pill shadow-sm">{{ $successStory->title }}</span>
                        <div class="vr bg-white opacity-25"></div>
                        <span class="text-info fw-medium tracking-widest uppercase small"><i class="bi bi-mortarboard-fill me-2"></i>{{ $successStory->major_year }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container pb-5" style="margin-top: -60px; position: relative; z-index: 10;">
        <div class="row g-4">
            
            <!-- Left Sidebar (Photo & Actions) -->
            <div class="col-lg-4">
                <div class="bento-card p-3 mb-4 gsap-scroll-card">
                    <div class="rounded-4 overflow-hidden mb-4 position-relative" style="aspect-ratio: 1/1;">
                        <img id="alumni-image" src="{{ $successStory->image_path ? asset('storage/'.$successStory->image_path) : 'https://ui-avatars.com/api/?name='.urlencode($successStory->name).'&size=600&background=ffcc00&color=000' }}" 
                             class="w-100 h-100 object-fit-cover" alt="{{ $successStory->name }}">
                        <div class="position-absolute inset-0 bg-black opacity-10"></div>
                    </div>
                    
                    <div class="p-4 rounded-4 bg-dark bg-opacity-50 border border-info border-opacity-20 mb-4 magnetic-el" style="box-shadow: inset 0 0 20px rgba(6, 182, 212, 0.05);">
                        <h6 class="fw-bold text-info mb-3 small uppercase tracking-widest"><i class="bi bi-quote fs-4 me-2"></i>Kutipan Inspirasi</h6>
                        <p class="text-info fw-bold mb-0" style="font-size: 1.15rem; line-height: 1.6; font-style: italic; text-shadow: 0 0 10px rgba(6, 182, 212, 0.2);">"{{ $successStory->quote }}"</p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button id="generatePosterBtn" class="btn btn-info rounded-pill py-3 fw-bold text-dark border-0 magnetic-el">
                            <i class="bi bi-palette-fill me-2"></i>BUAT POSTER STORY
                        </button>
                        <p class="text-center text-white-50 small mt-2"><i class="bi bi-instagram me-1 text-danger"></i> Format Instagram Story</p>
                    </div>
                </div>

                <div class="bento-card p-4 d-none d-lg-block gsap-scroll-card">
                    <h6 class="fw-bold mb-3 text-white uppercase tracking-widest small">Bagikan Cerita Ini</h6>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center magnetic-el border-opacity-25" style="width: 50px; height: 50px;"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode('Kisah Inspiratif: ' . $successStory->name) }}" target="_blank" class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center magnetic-el border-opacity-25" style="width: 50px; height: 50px;"><i class="bi bi-twitter-x fs-5"></i></a>
                        <a href="https://wa.me/?text={{ urlencode('Baca Kisah Inspiratif dari ' . $successStory->name . ': ' . url()->current()) }}" target="_blank" class="btn btn-outline-success rounded-circle d-flex align-items-center justify-content-center magnetic-el border-opacity-50" style="width: 50px; height: 50px;"><i class="bi bi-whatsapp fs-5"></i></a>
                        <button onclick="copyToClipboard()" class="btn btn-outline-info rounded-circle d-flex align-items-center justify-content-center magnetic-el border-opacity-50" style="width: 50px; height: 50px;"><i class="bi bi-link-45deg fs-5"></i></button>
                    </div>
                </div>
            </div>

            <!-- Right Content (Narrative) -->
            <div class="col-lg-8">
                <div class="bento-card p-4 p-md-5 gsap-scroll-card" style="min-height: 100%;">
                    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom border-white border-opacity-10 pb-4 flex-wrap gap-3">
                        <h3 class="fw-black mb-0 text-white">REKAM <span style="color: #06b6d4;">JEJAK</span></h3>
                        <div class="text-white-50 small bg-white bg-opacity-5 px-3 py-1 rounded-pill">
                            <i class="bi bi-calendar3 me-2"></i>{{ $successStory->created_at->format('d M Y') }}
                        </div>
                    </div>
                    
                    <div class="success-story-narrative" style="font-size: 1.15rem; line-height: 1.9; color: #cbd5e1;">
                        {!! nl2br(e($successStory->content)) !!}
                    </div>

                    <div class="mt-5 pt-5 border-top border-white border-opacity-10">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-4">
                            <div class="d-lg-none">
                                <span class="me-3 small fw-bold text-uppercase text-white-50">Bagikan:</span>
                                <div class="d-inline-flex gap-3">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" class="text-white"><i class="bi bi-facebook fs-4"></i></a>
                                    <a href="https://wa.me/?text={{ urlencode(url()->current()) }}" class="text-success"><i class="bi bi-whatsapp fs-4"></i></a>
                                </div>
                            </div>
                            <div class="d-none d-lg-block">
                                <span class="text-white-50 small italic">"Semoga kisah ini menjadi penyemangat bagi kita semua."</span>
                            </div>
                            <a href="{{ route('success_stories.index') }}" class="btn btn-outline-light rounded-pill px-5 py-2 fw-bold magnetic-el border-opacity-25 hover-bg-white hover-text-dark transition">
                                <i class="bi bi-arrow-left me-2"></i>KEMBALI
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Canvas for Poster -->
<canvas id="posterCanvas" width="1080" height="1920" style="display:none;"></canvas>

<style>
    .fw-black { font-weight: 900 !important; }
    .success-story-narrative p { margin-bottom: 2rem; }
    .uppercase { text-transform: uppercase; }
    .tracking-widest { letter-spacing: 0.1em; }
    .transition { transition: all 0.3s ease; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<script>
    document.documentElement.classList.add('dark');

    // Spotlight Effect
    document.getElementById("detail-grid").onmousemove = e => {
        for(const card of document.getElementsByClassName("bento-card")) {
            const rect = card.getBoundingClientRect(),
                  x = e.clientX - rect.left, y = e.clientY - rect.top;
            card.style.setProperty("--mouse-x", `${x}px`);
            card.style.setProperty("--mouse-y", `${y}px`);
        }
    }

    // Custom Cursor & Magnetics
    const cursor = document.querySelector('.custom-cursor');
    const cursorTrail = document.querySelector('.custom-cursor-trail');
    const magnetics = document.querySelectorAll('.magnetic-el');
    
    if (window.matchMedia("(any-hover: hover)").matches) {
        let mouseX = 0, mouseY = 0, trailX = 0, trailY = 0;
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX; mouseY = e.clientY;
            cursor.style.left = mouseX + 'px'; cursor.style.top = mouseY + 'px';
        });

        const animateTrail = () => {
            trailX += (mouseX - trailX) * 0.2; trailY += (mouseY - trailY) * 0.2;
            cursorTrail.style.left = trailX + 'px'; cursorTrail.style.top = trailY + 'px';
            requestAnimationFrame(animateTrail);
        };
        animateTrail();

        magnetics.forEach(el => {
            el.addEventListener('mouseenter', () => { cursor.classList.add('cursor-hover'); cursorTrail.style.opacity = '0'; });
            el.addEventListener('mousemove', (e) => {
                const rect = el.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2, y = e.clientY - rect.top - rect.height / 2;
                gsap.to(el, { x: x * 0.2, y: y * 0.2, duration: 0.3, ease: "power2.out" });
            });
            el.addEventListener('mouseleave', () => {
                cursor.classList.remove('cursor-hover'); cursorTrail.style.opacity = '1';
                gsap.to(el, { x: 0, y: 0, duration: 0.5, ease: "elastic.out(1, 0.3)" });
            });
        });
    }

    // GSAP
    gsap.registerPlugin(ScrollTrigger);
    gsap.fromTo(".gsap-fade-up", { y: 30, opacity: 0 }, { y: 0, opacity: 1, duration: 1, ease: "power3.out" });
    gsap.utils.toArray('.gsap-scroll-card').forEach((card) => {
        gsap.fromTo(card, { y: 50, opacity: 0 }, { y: 0, opacity: 1, duration: 0.8, ease: "power3.out", scrollTrigger: { trigger: card, start: "top 90%" }});
    });

    // Poster Logic
    function copyToClipboard() {
        navigator.clipboard.writeText(window.location.href);
        Swal.fire({ icon: 'success', title: 'Link Tersalin!', text: 'Silakan bagikan ke teman-teman Anda.', timer: 2000, background: '#0f172a', color: '#fff', showConfirmButton: false });
    }

    document.getElementById('generatePosterBtn').addEventListener('click', async function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>MENGOLAH GAMBAR...';

        try {
            const canvas = document.getElementById('posterCanvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();
            img.crossOrigin = "anonymous";
            img.src = document.getElementById('alumni-image').src;

            await new Promise((resolve) => img.onload = resolve);

            // BG
            ctx.fillStyle = '#050505'; ctx.fillRect(0, 0, 1080, 1920);

            // Image
            ctx.drawImage(img, 0, 0, 1080, 1080);

            // Overlay
            const grad = ctx.createLinearGradient(0, 0, 0, 1080);
            grad.addColorStop(0.5, 'transparent'); grad.addColorStop(1, '#050505');
            ctx.fillStyle = grad; ctx.fillRect(0, 0, 1080, 1080);

            ctx.textAlign = 'center';
            ctx.fillStyle = '#ffffff'; ctx.font = '900 80px Inter, sans-serif';
            ctx.fillText('{{ strtoupper($successStory->name) }}', 540, 1180);

            ctx.fillStyle = '#06b6d4'; ctx.font = '700 40px Inter, sans-serif';
            ctx.fillText('{{ strtoupper($successStory->title) }}', 540, 1250);

            ctx.strokeStyle = 'rgba(255,255,255,0.1)'; ctx.lineWidth = 2;
            ctx.strokeRect(100, 1350, 880, 400);

            ctx.fillStyle = '#cbd5e1'; ctx.font = 'italic 500 40px Inter, sans-serif';
            wrapText(ctx, '"{{ $successStory->quote }}"', 540, 1450, 800, 60);

            ctx.fillStyle = '#06b6d4'; ctx.font = '800 40px Inter, sans-serif';
            ctx.fillText('JEJAK SUKSES ALUMNI STEMAN', 540, 1820);
            
            ctx.fillStyle = 'rgba(255,255,255,0.3)'; ctx.font = '400 30px Inter, sans-serif';
            ctx.fillText('alumni-steman.my.id', 540, 1870);

            const link = document.createElement('a');
            link.download = 'Poster_Inspirasi_{{ Str::slug($successStory->name) }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();

            Swal.fire({ icon: 'success', title: 'Poster Berhasil Dibuat!', text: 'Silakan cek folder Download Anda dan share ke IG/TikTok!', background: '#0f172a', color: '#fff', confirmButtonColor: '#06b6d4' });
        } catch (error) {
            Swal.fire('Error', 'Gagal membuat poster. Pastikan koneksi stabil.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-palette-fill me-2"></i>BUAT POSTER STORY';
        }
    });

    function wrapText(context, text, x, y, maxWidth, lineHeight) {
        const words = text.split(' '); let line = '';
        for (let n = 0; n < words.length; n++) {
            const testLine = line + words[n] + ' ';
            const metrics = context.measureText(testLine);
            if (metrics.width > maxWidth && n > 0) {
                context.fillText(line, x, y); line = words[n] + ' '; y += lineHeight;
            } else { line = testLine; }
        }
        context.fillText(line, x, y);
    }
</script>
@endsection
