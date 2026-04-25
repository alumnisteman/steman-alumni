@extends('layouts.app')

@section('content')
<style>
    .stories-bento-bg {
        background-color: #050505;
        color: #f8fafc;
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
        position: relative;
    }

    /* BENTO CARD BASE */
    .bento-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        padding: 24px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
        backdrop-filter: blur(10px);
    }

    .bento-card:hover {
        transform: translateY(-8px);
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(6, 182, 212, 0.4);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 20px rgba(6, 182, 212, 0.1);
    }

    /* SPOTLIGHT EFFECT */
    .bento-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(600px circle at var(--mouse-x) var(--mouse-y), rgba(6, 182, 212, 0.15), transparent 40%);
        opacity: 0;
        transition: opacity 0.5s;
        pointer-events: none;
    }

    .bento-card:hover::before {
        opacity: 1;
    }

    /* TYPOGRAPHY */
    .fw-black { font-weight: 900; }
    .text-cyan { color: #06b6d4; }
    .text-gold { color: #ffcc00; }

    /* PAGINATION STYLING */
    .pagination { gap: 8px; }
    .page-link {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff !important;
        border-radius: 12px !important;
        padding: 10px 18px;
        transition: all 0.3s;
    }
    .page-link:hover, .page-item.active .page-link {
        background: #06b6d4 !important;
        border-color: #06b6d4 !important;
        color: #000 !important;
    }

    /* GSAP ANIMATION CLASSES */
    .gsap-scroll-card { opacity: 0; transform: translateY(30px); }
</style>

<div class="stories-bento-bg pt-5 pb-5" id="stories-grid">
    <div class="container py-5">
        <!-- Header Section -->
        <div class="text-center mb-5 gsap-fade-up">
            <h6 class="text-cyan fw-bold text-uppercase tracking-widest mb-3">Inspirasi Alumni</h6>
            <h1 class="display-3 fw-black mb-4">JEJAK <span class="text-cyan">SUKSES</span></h1>
            <p class="text-white-50 lead mx-auto mb-5" style="max-width: 700px;">
                Eksplorasi perjalanan karir dan kisah inspiratif dari para lulusan {{ setting('school_name', 'SMKN 2 Ternate') }} yang telah mendunia.
            </p>
            
            <div class="d-flex justify-content-center gap-3 mb-5">
                <div class="px-4 py-2 rounded-pill bg-white bg-opacity-5 border border-white border-opacity-10">
                    <span class="text-gold fw-bold">{{ $stories->total() }}</span> <span class="small opacity-75">Kisah Terverifikasi</span>
                </div>
            </div>
        </div>

        <!-- Bento Grid -->
        <div class="row g-4 mb-5">
            @forelse($stories as $story)
            <div class="col-md-6 col-lg-4 gsap-scroll-card">
                <a href="{{ route('success_stories.show', $story) }}" class="text-decoration-none h-100 d-block magnetic-el">
                    <div class="bento-card d-flex flex-column">
                        <div class="position-relative rounded-4 overflow-hidden mb-3" style="height: 250px;">
                            <img src="{{ $story->image_path ? asset('storage/'.$story->image_path) : 'https://ui-avatars.com/api/?name='.urlencode($story->name).'&background=ffcc00&color=000&size=500' }}" 
                                 class="w-100 h-100" style="object-fit: cover; filter: brightness(0.8);" alt="{{ $story->name }}">
                            <div class="position-absolute bottom-0 w-100 p-3" style="background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);">
                                <span class="badge bg-warning text-dark mb-1">{{ $story->title }}</span>
                                <h5 class="fw-bold text-white mb-0">{{ $story->name }}</h5>
                                <small class="text-cyan opacity-75">{{ $story->major_year }}</small>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <i class="bi bi-quote text-white opacity-25 fs-1 position-absolute top-0 end-0 mt-2 me-3"></i>
                            <p class="text-white-50 small mb-0 mt-2 lh-lg">
                                "{{ \Illuminate\Support\Str::limit($story->quote, 120) }}"
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-top border-white border-opacity-10 d-flex justify-content-between align-items-center">
                            <span class="text-cyan small fw-bold">BACA KISAH</span>
                            <i class="bi bi-arrow-right text-cyan"></i>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="bento-card py-5">
                    <i class="bi bi-journal-x display-1 text-white-50 mb-3"></i>
                    <h3 class="text-white">Belum ada kisah sukses</h3>
                    <p class="text-white-50">Jadilah yang pertama menginspirasi!</p>
                    <a href="/" class="btn btn-outline-warning rounded-pill px-5 magnetic-el">Kembali ke Beranda</a>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5 gsap-fade-up magnetic-el" data-bs-theme="dark">
            {{ $stories->links() }}
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Spotlight Effect
        document.getElementById("stories-grid").onmousemove = e => {
            for(const card of document.getElementsByClassName("bento-card")) {
                const rect = card.getBoundingClientRect(),
                x = e.clientX - rect.left,
                y = e.clientY - rect.top;
                card.style.setProperty("--mouse-x", `${x}px`);
                card.style.setProperty("--mouse-y", `${y}px`);
            }
        };

        // GSAP Animations
        gsap.from(".gsap-fade-up", {
            y: 50, opacity: 0, duration: 1, stagger: 0.2, ease: "power4.out"
        });

        gsap.utils.toArray('.gsap-scroll-card').forEach((card, i) => {
            gsap.to(card, {
                scrollTrigger: {
                    trigger: card,
                    start: "top 85%",
                    toggleActions: "play none none none"
                },
                y: 0, opacity: 1, duration: 0.8, delay: i % 3 * 0.1, ease: "power2.out"
            });
        });

        // Magnetic Effect for interactive elements
        const magneticEls = document.querySelectorAll('.magnetic-el');
        magneticEls.forEach(el => {
            el.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                this.style.transform = `translate(${x * 0.3}px, ${y * 0.3}px)`;
            });
            el.addEventListener('mouseleave', function() {
                this.style.transform = `translate(0px, 0px)`;
            });
        });
    });
</script>
@endsection
