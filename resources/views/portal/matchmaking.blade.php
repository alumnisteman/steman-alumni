@extends('layouts.app')

@section('title', 'Skill Matchmaking - STEMAN Alumni')

@push('styles')
<style>
    body {
        background: #0f172a; /* Dark theme */
        overflow-x: hidden;
    }
    
    .match-container {
        position: relative;
        height: 70vh;
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
        perspective: 1000px;
    }

    .tinder-card {
        position: absolute;
        width: 100%;
        height: 100%;
        background: #1e293b;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        overflow: hidden;
        transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        will-change: transform;
        border: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        flex-direction: column;
        user-select: none;
        touch-action: pan-y;
    }

    .tinder-card img {
        width: 100%;
        height: 65%;
        object-fit: cover;
        pointer-events: none;
    }

    .card-info {
        padding: 20px;
        flex-grow: 1;
        background: linear-gradient(to top, #1e293b 80%, transparent);
        position: relative;
    }

    .stamp {
        position: absolute;
        top: 40px;
        padding: 10px 20px;
        border: 4px solid;
        border-radius: 10px;
        font-size: 2rem;
        font-weight: 900;
        text-transform: uppercase;
        opacity: 0;
        z-index: 10;
        pointer-events: none;
    }

    .stamp.like {
        right: 40px;
        color: #10b981;
        border-color: #10b981;
        transform: rotate(-15deg);
    }

    .stamp.nope {
        left: 40px;
        color: #ef4444;
        border-color: #ef4444;
        transform: rotate(15deg);
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 30px;
    }

    .action-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .btn-nope {
        background: #1e293b;
        color: #ef4444;
        border: 2px solid #ef4444;
    }

    .btn-like {
        background: #1e293b;
        color: #10b981;
        border: 2px solid #10b981;
    }

    /* Match Overlay */
    #match-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.9);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.5s;
    }

    #match-overlay.active {
        opacity: 1;
        pointer-events: all;
    }

    .match-text {
        font-size: 4rem;
        font-weight: 900;
        background: linear-gradient(45deg, #f59e0b, #ef4444);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes popIn {
        0% { transform: scale(0.5); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="text-white fw-bold">Skill <span class="text-primary">Matchmaking</span></h1>
        <p class="text-gray-400">Temukan mentor, partner bisnis, atau teman seprofesi. Swipe Kanan untuk terhubung!</p>
    </div>

    @if($candidates->count() > 0)
    <div class="match-container" id="card-stack">
        @foreach($candidates->reverse() as $index => $user)
            <div class="tinder-card" data-id="{{ $user->id }}" style="z-index: {{ $index }};">
                <div class="stamp like">LIKE</div>
                <div class="stamp nope">NOPE</div>
                
                <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" onerror="this.src='/images/default-avatar.png'">
                
                <div class="card-info">
                    <h3 class="text-white fw-bold mb-1">{{ $user->name }}</h3>
                    <p class="text-blue-400 font-semibold mb-2"><i class="bi bi-briefcase-fill"></i> {{ $user->current_job ?? 'Alumni' }}</p>
                    <p class="text-gray-400 small mb-2"><i class="bi bi-geo-alt-fill"></i> {{ $user->city_name ?? 'Indonesia' }}</p>
                    <p class="text-gray-300 small mb-0">{{ Str::limit($user->bio, 80) ?? 'Belum ada bio.' }}</p>
                    
                    <div class="mt-3">
                        <span class="badge bg-secondary">{{ $user->major }}</span>
                        <span class="badge bg-secondary">Angkatan {{ $user->graduation_year }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="action-buttons">
        <button class="action-btn btn-nope" id="btn-nope"><i class="bi bi-x-lg"></i></button>
        <button class="action-btn btn-like" id="btn-like"><i class="bi bi-heart-fill"></i></button>
    </div>
    @else
    <div class="text-center py-5">
        <div class="display-1 text-muted mb-4"><i class="bi bi-emoji-frown"></i></div>
        <h3 class="text-white">Anda Sudah Melihat Semua Kandidat!</h3>
        <p class="text-gray-400">Kembali lagi besok untuk melihat alumni baru.</p>
        <a href="/global-network" class="btn btn-primary rounded-pill mt-3 px-4">Kembali ke Peta</a>
    </div>
    @endif
</div>

<!-- IT'S A MATCH OVERLAY -->
<div id="match-overlay">
    <div class="match-text mb-4">IT'S A MATCH!</div>
    <p class="text-white fs-5 mb-5 text-center">Anda dan alumni ini sama-sama tertarik untuk terhubung.<br>Sistem Gamifikasi memberikan Anda berdua +10 XP!</p>
    <button class="btn btn-light rounded-pill px-5 py-3 fw-bold" onclick="closeMatch()">Lanjut Swipe</button>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stack = document.getElementById('card-stack');
        if(!stack) return;

        const cards = stack.querySelectorAll('.tinder-card');
        let currentCardIndex = cards.length - 1;

        cards.forEach((card, index) => {
            const hammer = new Hammer(card);
            
            hammer.on('pan', function(event) {
                if (index !== currentCardIndex) return;
                
                // Add rotation based on X movement
                const xMulti = event.deltaX * 0.03;
                const yMulti = event.deltaY / 80;
                const rotate = xMulti * yMulti;
                
                card.style.transform = `translate(${event.deltaX}px, ${event.deltaY}px) rotate(${rotate}deg)`;

                // Show Stamps based on direction
                const likeStamp = card.querySelector('.stamp.like');
                const nopeStamp = card.querySelector('.stamp.nope');
                
                if (event.deltaX > 50) {
                    likeStamp.style.opacity = event.deltaX / 150;
                    nopeStamp.style.opacity = 0;
                } else if (event.deltaX < -50) {
                    nopeStamp.style.opacity = Math.abs(event.deltaX) / 150;
                    likeStamp.style.opacity = 0;
                }
            });

            hammer.on('panend', function(event) {
                if (index !== currentCardIndex) return;
                
                const likeStamp = card.querySelector('.stamp.like');
                const nopeStamp = card.querySelector('.stamp.nope');
                
                if (event.deltaX > 100) {
                    // Swiped Right (Like)
                    swipeCard(card, 'like', event.deltaX, event.deltaY);
                } else if (event.deltaX < -100) {
                    // Swiped Left (Nope)
                    swipeCard(card, 'pass', event.deltaX, event.deltaY);
                } else {
                    // Reset position
                    card.style.transform = '';
                    likeStamp.style.opacity = 0;
                    nopeStamp.style.opacity = 0;
                }
            });
        });

        // Button Controls
        document.getElementById('btn-like')?.addEventListener('click', () => {
            if(currentCardIndex >= 0) swipeCard(cards[currentCardIndex], 'like', window.innerWidth, 0);
        });
        
        document.getElementById('btn-nope')?.addEventListener('click', () => {
            if(currentCardIndex >= 0) swipeCard(cards[currentCardIndex], 'pass', -window.innerWidth, 0);
        });

        function swipeCard(card, action, x, y) {
            const targetId = card.getAttribute('data-id');
            const endX = Math.max(Math.abs(x) * 2, window.innerWidth);
            const moveOutWidth = action === 'like' ? endX : -endX;
            
            card.style.transform = `translate(${moveOutWidth}px, ${y}px) rotate(${action === 'like' ? 30 : -30}deg)`;
            card.style.opacity = 0;
            
            currentCardIndex--;

            // Send to server
            fetch('{{ route("matchmaking.swipe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    target_id: targetId,
                    action: action
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.match) {
                    document.getElementById('match-overlay').classList.add('active');
                }
            });
            
            setTimeout(() => card.remove(), 300);
            
            if (currentCardIndex < 0) {
                setTimeout(() => location.reload(), 1000);
            }
        }
    });

    function closeMatch() {
        document.getElementById('match-overlay').classList.remove('active');
    }
</script>
@endpush
