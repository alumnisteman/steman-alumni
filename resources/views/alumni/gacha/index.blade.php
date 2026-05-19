@extends('layouts.app')

@section('content')
<style>
.gacha-page {
    background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #0f0728 100%);
    color: #fff;
    min-height: calc(100vh - 60px);
    position: relative;
    overflow: hidden;
    padding-bottom: 3rem;
}

/* Cyber Neon Glowing Header */
.gacha-title-glow {
    font-size: 3rem;
    font-weight: 900;
    text-transform: uppercase;
    background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 50%, #06b6d4 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 30px rgba(139, 92, 246, 0.3);
}

/* Glassmorphism Stat Cards */
.gacha-stat-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 20px;
    padding: 1.2rem;
    transition: all 0.3s;
}
.gacha-stat-card:hover {
    transform: translateY(-5px);
    border-color: rgba(139, 92, 246, 0.4);
    box-shadow: 0 10px 25px rgba(139, 92, 246, 0.15);
}

/* Filter Controls Box */
.gacha-filter-box {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 24px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
}
.gacha-filter-box select, .gacha-filter-box input {
    background: rgba(0, 0, 0, 0.4);
    border: 1.5px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    border-radius: 12px;
}
.gacha-filter-box select:focus, .gacha-filter-box input:focus {
    background: rgba(0, 0, 0, 0.6);
    border-color: #8b5cf6;
    color: #fff;
    box-shadow: 0 0 10px rgba(139, 92, 246, 0.3);
}

/* GACHA CAPSULE MACHINE ARTWORK */
.gacha-machine-container {
    width: 320px;
    margin: 2rem auto;
    position: relative;
}
.gacha-machine {
    width: 100%;
    background: linear-gradient(135deg, #db2777 0%, #9d174d 100%);
    border: 6px solid #475569;
    border-radius: 50px 50px 30px 30px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), inset 0 10px 0 rgba(255, 255, 255, 0.2);
    position: relative;
    padding: 20px;
    z-index: 2;
}
.gacha-globe {
    width: 100%;
    height: 200px;
    background: rgba(255, 255, 255, 0.15);
    border: 6px solid #334155;
    border-radius: 40px;
    position: relative;
    overflow: hidden;
    box-shadow: inset 0 20px 30px rgba(0,0,0,0.3);
    margin-bottom: 20px;
}
.gacha-balls-container {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0; left: 0;
}
.gacha-ball {
    position: absolute;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    box-shadow: inset -5px -5px 10px rgba(0,0,0,0.3), inset 3px 3px 5px rgba(255,255,255,0.4);
    animation: jitter 1s ease-in-out infinite alternate;
}
/* Random placements and colors for capsule balls inside globe */
.gacha-ball-1 { background: #ec4899; top: 120px; left: 40px; animation-delay: 0.1s; }
.gacha-ball-2 { background: #8b5cf6; top: 130px; left: 80px; animation-delay: 0.3s; }
.gacha-ball-3 { background: #06b6d4; top: 110px; left: 120px; animation-delay: 0.2s; }
.gacha-ball-4 { background: #eab308; top: 140px; left: 160px; animation-delay: 0.5s; }
.gacha-ball-5 { background: #10b981; top: 125px; left: 200px; animation-delay: 0.4s; }
.gacha-ball-6 { background: #ec4899; top: 90px; left: 60px; animation-delay: 0.6s; }
.gacha-ball-7 { background: #06b6d4; top: 80px; left: 100px; animation-delay: 0.1s; }
.gacha-ball-8 { background: #8b5cf6; top: 85px; left: 150px; animation-delay: 0.3s; }
.gacha-ball-9 { background: #eab308; top: 95px; left: 180px; animation-delay: 0.2s; }
.gacha-ball-10 { background: #10b981; top: 60px; left: 120px; animation-delay: 0.7s; }

@keyframes jitter {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(3px, -3px) scale(0.98); }
}
.gacha-machine-spinning .gacha-ball {
    animation: bounceCrazy 0.1s infinite alternate !important;
}
@keyframes bounceCrazy {
    0% { transform: translate(random(-10,10)px, random(-10,10)px) rotate(0deg); }
    100% { transform: translate(random(-10,10)px, random(-10,10)px) rotate(360deg); }
}

/* Knob interface */
.gacha-knob-container {
    width: 90px;
    height: 90px;
    margin: 10px auto;
    position: relative;
}
.gacha-knob {
    width: 100%;
    height: 100%;
    background: #475569;
    border: 6px solid #1e293b;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 6px 15px rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.gacha-knob::before {
    content: '';
    width: 65%;
    height: 12px;
    background: #cbd5e1;
    border-radius: 6px;
    position: absolute;
}
.gacha-knob-spin {
    transform: rotate(360deg);
}

/* Chute and Dispenser */
.gacha-chute {
    width: 80px;
    height: 60px;
    background: #1e293b;
    border: 4px solid #334155;
    border-radius: 10px 10px 0 0;
    margin: 20px auto 0;
    position: relative;
    overflow: hidden;
}
.gacha-chute-door {
    position: absolute;
    inset: 0;
    background: #64748b;
    border-bottom: 4px solid #475569;
    transition: transform 0.3s;
}
.gacha-chute-open .gacha-chute-door {
    transform: translateY(-80%);
}

/* Dropped Capsule ball animations */
.dropped-capsule-target {
    position: absolute;
    bottom: -10px;
    left: calc(50% - 25px);
    width: 50px;
    height: 50px;
    z-index: 10;
    display: none;
}
.dropped-capsule {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(135deg, #06b6d4 50%, #ffffff 50%);
    box-shadow: 0 10px 20px rgba(0,0,0,0.5), inset 3px 3px 8px rgba(255,255,255,0.6);
    border: 3px solid #1e293b;
}
.dropped-capsule-animate {
    display: block;
    animation: capsuleDrop 1s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}
@keyframes capsuleDrop {
    0% { transform: translateY(-70px) scale(0.3); opacity: 0; }
    30% { transform: translateY(0) scale(1); opacity: 1; }
    45% { transform: translateY(-20px) rotate(45deg); }
    60% { transform: translateY(0) rotate(90deg); }
    75% { transform: translateY(-8px) rotate(135deg); }
    100% { transform: translateY(0) rotate(180deg) scale(1); }
}

/* REVEALED ALUMNI CARD */
.gacha-card-backdrop {
    position: fixed;
    inset: 0;
    z-index: 1000;
    background: rgba(15, 7, 40, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.5s;
    backdrop-filter: blur(15px);
}
.gacha-card-backdrop.active {
    opacity: 1;
    pointer-events: all;
}
.gacha-card-glow {
    width: 100%;
    max-width: 380px;
    background: linear-gradient(135deg, rgba(236,72,153,0.15), rgba(139,92,246,0.15));
    border-radius: 30px;
    padding: 3px;
    box-shadow: 0 0 40px rgba(139, 92, 246, 0.4);
    transform: scale(0.7);
    transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.gacha-card-backdrop.active .gacha-card-glow {
    transform: scale(1);
}
.gacha-card {
    background: #1e1b4b;
    border-radius: 27px;
    padding: 2.2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.gacha-card::before {
    content: '';
    position: absolute;
    top: -50%; left: -50%; width: 200%; height: 200%;
    background: conic-gradient(from 0deg, transparent, rgba(139,92,246,0.4), transparent, transparent);
    animation: rotateConic 8s linear infinite;
    z-index: 1;
    pointer-events: none;
}
@keyframes rotateConic {
    100% { transform: rotate(360deg); }
}
.gacha-card-inner {
    position: relative;
    z-index: 2;
}
.gacha-card-avatar {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 4px solid #ec4899;
    object-fit: cover;
    margin: 0 auto 1.5rem;
    box-shadow: 0 0 20px rgba(236,72,153,0.5);
}
.gacha-pill {
    background: rgba(139, 92, 246, 0.2);
    border: 1px solid rgba(139, 92, 246, 0.4);
    border-radius: 30px;
    padding: 4px 12px;
    font-size: 0.75rem;
    font-weight: 700;
}

/* Match Overlay */
.match-overlay {
    position: fixed;
    inset: 0;
    z-index: 1100;
    background: rgba(0,0,0,0.95);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.5s;
    text-align: center;
}
.match-overlay.active {
    opacity: 1;
    pointer-events: all;
}
.match-title {
    font-size: 3.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, #f59e0b, #ec4899, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: bounceMatch 1s ease infinite alternate;
}
@keyframes bounceMatch {
    0% { transform: scale(0.95) translateY(0); }
    100% { transform: scale(1.05) translateY(-10px); }
}

/* Confetti particles */
.gacha-confetti {
    position: absolute;
    width: 6px; height: 6px;
    z-index: 99;
    pointer-events: none;
}
</style>

<div class="gacha-page">
    <div class="container py-5">
        
        <!-- Header -->
        <div class="text-center mb-5">
            <div style="font-size: 4rem; filter: drop-shadow(0 0 20px rgba(236, 72, 153, 0.6));" class="mb-2">🎰</div>
            <h1 class="gacha-title-glow">Alumni Gacha</h1>
            <p class="text-muted col-lg-6 mx-auto">Roll kapsul gachamu dan temukan alumni baru secara acak! Tambah jejaring sosial, jalin silaturahmi, dan dapatkan bonus 15 XP jika saling terhubung (Mutual Match)! 🎁</p>
        </div>

        <!-- Stats Grid -->
        <div class="row g-3 mb-5 justify-content-center">
            <div class="col-6 col-md-3">
                <div class="gacha-stat-card text-center">
                    <div class="text-muted small">Total Mutual Matches</div>
                    <h3 class="fw-black text-pink-500 mb-0 mt-1" style="color: #ec4899;">{{ number_format($stats['total_connects']) }}</h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="gacha-stat-card text-center">
                    <div class="text-muted small">Alumni Aktif</div>
                    <h3 class="fw-black text-purple-500 mb-0 mt-1" style="color: #a855f7;">{{ number_format($stats['active_alumni']) }}</h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="gacha-stat-card text-center">
                    <div class="text-muted small">Roll Hari Ini</div>
                    <h3 class="fw-black text-cyan-500 mb-0 mt-1" style="color: #06b6d4;">{{ number_format($stats['today_spins']) }}</h3>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            
            <!-- Capsule Machine Area -->
            <div class="col-lg-5 text-center">
                <div class="gacha-machine-container">
                    <div class="gacha-machine" id="gachaMachine">
                        <!-- Globe Window with Bouncing Balls -->
                        <div class="gacha-globe">
                            <div class="gacha-balls-container">
                                @for($i = 1; $i <= 10; $i++)
                                <div class="gacha-ball gacha-ball-{{ $i }}"></div>
                                @endfor
                            </div>
                        </div>

                        <!-- Knob -->
                        <div class="gacha-knob-container">
                            <div class="gacha-knob" id="gachaKnob" onclick="triggerSpin()"></div>
                        </div>

                        <!-- Dispenser Chute -->
                        <div class="gacha-chute" id="gachaChute">
                            <div class="gacha-chute-door"></div>
                        </div>

                        <!-- Dropping Capsule ball target -->
                        <div class="dropped-capsule-target" id="droppedCapsule">
                            <div class="dropped-capsule"></div>
                        </div>
                    </div>
                </div>

                <!-- Roll Button -->
                <button class="btn btn-lg rounded-pill px-5 fw-bold text-white shadow-lg mt-3" 
                        style="background: linear-gradient(135deg, #ec4899, #8b5cf6); border: none;"
                        id="spinBtn" onclick="triggerSpin()">
                    🎰 ROLL GACHA (SPIN)
                </button>
            </div>

            <!-- Custom Filters Area -->
            <div class="col-lg-4">
                <div class="gacha-filter-box">
                    <h5 class="fw-bold mb-4"><i class="bi bi-sliders me-2"></i>Filter Radar Gacha</h5>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted">Jurusan Spesifik</label>
                        <select id="filterMajor" class="form-select">
                            <option value="">Semua Jurusan</option>
                            @foreach($majors as $m)
                                <option value="{{ $m->name }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Domisili (Kota/Kabupaten)</label>
                        <input type="text" id="filterCity" class="form-control" placeholder="Contoh: Ternate, Jakarta...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Minat / Hobi</label>
                        <input type="text" id="filterInterest" class="form-control" placeholder="Contoh: Coding, Gaming, Bisnis...">
                    </div>

                    <p class="small text-muted mb-0"><i class="bi bi-info-circle me-1"></i> Radar Gacha akan mencocokkan alumni secara acak yang memenuhi kriteria di atas.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- GACHA RESULT DISPLAY MODAL BACKDROP -->
<div class="gacha-card-backdrop" id="resultModal">
    <div class="gacha-card-glow">
        <div class="gacha-card">
            <div class="gacha-card-inner">
                <!-- Capsule Burst Visual -->
                <div style="font-size: 3rem; animation: bounce 0.6s infinite alternate;" class="mb-3">💥</div>
                
                <!-- Avatar -->
                <img src="" id="resAvatar" class="gacha-card-avatar" alt="Avatar">
                
                <!-- Name & Credentials -->
                <h4 class="fw-black mb-1" id="resName">-</h4>
                <div class="text-muted small mb-2 d-flex justify-content-center gap-2">
                    <span id="resMajor">-</span>
                    <span>•</span>
                    <span id="resYear">-</span>
                </div>

                <!-- Domicile -->
                <div class="small text-pink-400 mb-3" style="color: #ec4899;" id="resCity">
                    <i class="bi bi-geo-alt me-1"></i>-
                </div>

                <!-- Bio -->
                <p class="small text-slate-300 opacity-80 px-2 py-3 bg-black bg-opacity-20 rounded-3 mb-3 text-start" id="resBio" style="min-height: 70px;">
                    -
                </p>

                <!-- Interests Tags -->
                <div class="d-flex flex-wrap gap-2 justify-content-center mb-4" id="resInterests">
                    <!-- tags dynamically loaded -->
                </div>

                <!-- Choices Button Controls -->
                <div class="row g-2">
                    <div class="col-6">
                        <button class="btn btn-outline-secondary w-100 rounded-pill py-2.5 fw-bold" onclick="submitChoice('skip')">
                            ✕ LEWATI
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold" style="background: #ec4899; border: none;" onclick="submitChoice('connect')">
                            💖 CONNECT
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MUTUAL MATCH LIGHTBOX OVERLAY -->
<div class="match-overlay" id="matchOverlay">
    <div style="font-size: 5rem;" class="mb-3">💖 💥 💖</div>
    <h1 class="match-title">MUTUAL MATCH!</h1>
    <p class="text-white col-lg-5 col-10 mx-auto fs-5 mb-4">
        Kamu dan <strong id="matchPartnerName" class="text-warning">-</strong> saling mengirimkan koneksi! Kalian sekarang terhubung.<br>
        <span class="text-success fw-bold"><i class="bi bi-award"></i> Bonus +15 XP</span> telah ditambahkan ke profil kalian berdua!
    </p>

    <!-- Avatar comparison -->
    <div class="d-flex align-items-center justify-content-center gap-4 mb-5">
        <img src="{{ auth()->user()->profile_picture_url }}" class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover; border-color: #8b5cf6 !important; border-width: 4px !important;">
        <div class="h3 fw-black text-danger">💖</div>
        <img src="" id="matchPartnerAvatar" class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover; border-color: #ec4899 !important; border-width: 4px !important;">
    </div>

    <div class="d-flex gap-3 justify-content-center">
        <a href="" id="matchPartnerProfile" class="btn btn-outline-light rounded-pill px-4 py-2.5 fw-bold">LIHAT PROFIL</a>
        <a href="{{ route('alumni.chat') }}" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold" style="background: #8b5cf6; border: none;">KIRIM PESAN</a>
        <button class="btn btn-secondary rounded-pill px-4 py-2.5 fw-bold" onclick="closeMatch()">ROLL LAGI</button>
    </div>
</div>

<script>
let currentAlumniId = null;
let isSpinning = false;

// Synthesize Audio using Web Audio API (Storage-friendly retro sounds!)
const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

function playSound(type) {
    if (audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    
    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    osc.connect(gain);
    gain.connect(audioCtx.destination);

    if (type === 'spin') {
        // Retro spin sound (sliding pitch)
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(150, audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(800, audioCtx.currentTime + 1.2);
        gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
        gain.gain.linearRampToValueAtTime(0.01, audioCtx.currentTime + 1.2);
        osc.start();
        osc.stop(audioCtx.currentTime + 1.2);
    } else if (type === 'drop') {
        // Clonk sound
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(200, audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(50, audioCtx.currentTime + 0.3);
        gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
        gain.gain.linearRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.3);
    } else if (type === 'reveal') {
        // Happy reveal chime
        osc.type = 'sine';
        osc.frequency.setValueAtTime(440, audioCtx.currentTime);
        osc.frequency.setValueAtTime(554.37, audioCtx.currentTime + 0.1);
        osc.frequency.setValueAtTime(659.25, audioCtx.currentTime + 0.2);
        osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.4);
        gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
        gain.gain.linearRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.5);
    } else if (type === 'match') {
        // Triumphant match fanfare!
        osc.type = 'square';
        osc.frequency.setValueAtTime(523.25, audioCtx.currentTime); // C5
        osc.frequency.setValueAtTime(659.25, audioCtx.currentTime + 0.15); // E5
        osc.frequency.setValueAtTime(783.99, audioCtx.currentTime + 0.3); // G5
        osc.frequency.setValueAtTime(1046.50, audioCtx.currentTime + 0.45); // C6
        gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
        gain.gain.linearRampToValueAtTime(0.001, audioCtx.currentTime + 0.9);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.9);
    }
}

function triggerSpin() {
    if (isSpinning) return;
    isSpinning = true;

    // Reset modals and state
    document.getElementById('resultModal').classList.remove('active');
    document.getElementById('matchOverlay').classList.remove('active');

    const machine = document.getElementById('gachaMachine');
    const knob = document.getElementById('gachaKnob');
    const chute = document.getElementById('gachaChute');
    const capsule = document.getElementById('droppedCapsule');
    const spinBtn = document.getElementById('spinBtn');

    spinBtn.disabled = true;
    spinBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SPINNING...';

    // 1. Play sound
    playSound('spin');

    // 2. Animate knob turn
    knob.classList.add('gacha-knob-spin');
    
    // 3. Jitter balls inside the globe
    machine.classList.add('gacha-machine-spinning');

    // 4. Query Ajax for candidate
    fetch('/gacha/spin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            major: document.getElementById('filterMajor').value,
            city: document.getElementById('filterCity').value,
            interest: document.getElementById('filterInterest').value,
        })
    })
    .then(r => r.json())
    .then(data => {
        // Delay visual results slightly so user experiences the spin animation
        setTimeout(() => {
            // Stop machine shaking
            machine.classList.remove('gacha-machine-spinning');
            knob.classList.remove('gacha-knob-spin');

            if (data.empty) {
                // No candidates found
                alert(data.message || 'Tidak ada alumni yang ditemukan dengan filter ini.');
                isSpinning = false;
                spinBtn.disabled = false;
                spinBtn.innerHTML = '🎰 ROLL GACHA (SPIN)';
                return;
            }

            // Prep Modal details
            const alumni = data.alumni;
            currentAlumniId = alumni.id;
            
            document.getElementById('resAvatar').src = alumni.avatar;
            document.getElementById('resName').textContent = alumni.name;
            document.getElementById('resMajor').textContent = alumni.major;
            document.getElementById('resYear').textContent = alumni.graduation_year;
            document.getElementById('resCity').innerHTML = `<i class="bi bi-geo-alt me-1"></i>${alumni.city}`;
            document.getElementById('resBio').textContent = alumni.bio;
            
            // Build tags
            const tagsWrap = document.getElementById('resInterests');
            tagsWrap.innerHTML = '';
            if (alumni.interests) {
                alumni.interests.split(',').forEach(item => {
                    if(item.trim()) {
                        const span = document.createElement('span');
                        span.className = 'gacha-pill';
                        span.textContent = `#${item.trim()}`;
                        tagsWrap.appendChild(span);
                    }
                });
            }

            // Drop Capsule animation
            chute.classList.add('gacha-chute-open');
            capsule.classList.add('dropped-capsule-animate');
            playSound('drop');

            // Wait for capsule bounce animation to finish, then reveal modal
            setTimeout(() => {
                // Open result card modal
                document.getElementById('resultModal').classList.add('active');
                playSound('reveal');
                launchPops();

                // Clean up animation classes
                chute.classList.remove('gacha-chute-open');
                capsule.classList.remove('dropped-capsule-animate');
                isSpinning = false;
                spinBtn.disabled = false;
                spinBtn.innerHTML = '🎰 ROLL GACHA (SPIN)';
            }, 1000);

        }, 1200);
    })
    .catch(err => {
        console.error(err);
        machine.classList.remove('gacha-machine-spinning');
        knob.classList.remove('gacha-knob-spin');
        isSpinning = false;
        spinBtn.disabled = false;
        spinBtn.innerHTML = '🎰 ROLL GACHA (SPIN)';
        alert('Terjadi kesalahan radar saat memindai alumni. Silakan coba kembali.');
    });
}

function submitChoice(action) {
    if (!currentAlumniId) return;

    // Close the card reveal modal immediately
    document.getElementById('resultModal').classList.remove('active');

    fetch('/gacha/connect', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            target_id: currentAlumniId,
            action: action
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.is_mutual) {
            // It's a match! Show matching overlay
            const partner = data.target;
            document.getElementById('matchPartnerName').textContent = partner.name;
            document.getElementById('matchPartnerAvatar').src = partner.avatar;
            document.getElementById('matchPartnerProfile').href = partner.profile_url;
            
            playSound('match');
            document.getElementById('matchOverlay').classList.add('active');
            launchMutualConfetti();
        } else {
            // Spin again automatically or wait
            if (action === 'connect') {
                alert('Permintaan koneksi dikirim! Menunggu alumni tersebut menyukai balik untuk Mutual Match 💖');
            }
        }
    })
    .catch(err => console.error(err));
}

function closeMatch() {
    document.getElementById('matchOverlay').classList.remove('active');
}

// Simple floating stars / particles upon capsule open
function launchPops() {
    const parent = document.getElementById('resultModal');
    const colors = ['#ec4899', '#8b5cf6', '#06b6d4', '#eab308'];
    for(let i=0; i<30; i++) {
        const p = document.createElement('div');
        p.className = 'gacha-confetti';
        p.style.left = '50%';
        p.style.top = '40%';
        p.style.background = colors[Math.floor(Math.random() * colors.length)];
        p.style.borderRadius = '50%';
        p.style.width = Math.random()*8+6 + 'px';
        p.style.height = Math.random()*8+6 + 'px';
        
        const angle = Math.random() * Math.PI * 2;
        const speed = Math.random() * 8 + 4;
        const velX = Math.cos(angle) * speed;
        const velY = Math.sin(angle) * speed;
        
        parent.appendChild(p);
        
        let posX = window.innerWidth / 2;
        let posY = window.innerHeight * 0.4;
        let opacity = 1;
        
        const anim = setInterval(() => {
            posX += velX;
            posY += velY + 0.2; // slight gravity
            opacity -= 0.03;
            p.style.left = posX + 'px';
            p.style.top = posY + 'px';
            p.style.opacity = opacity;
            
            if(opacity <= 0) {
                clearInterval(anim);
                p.remove();
            }
        }, 20);
    }
}

// Larger confetti for Mutual Matches
function launchMutualConfetti() {
    const parent = document.getElementById('matchOverlay');
    const colors = ['#f59e0b', '#ec4899', '#8b5cf6', '#10b981'];
    for(let i=0; i<80; i++) {
        const p = document.createElement('div');
        p.className = 'gacha-confetti';
        p.style.left = Math.random() * 100 + 'vw';
        p.style.top = '-10px';
        p.style.background = colors[Math.floor(Math.random() * colors.length)];
        p.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
        p.style.width = Math.random()*10+5 + 'px';
        p.style.height = Math.random()*10+5 + 'px';
        
        parent.appendChild(p);
        
        let posY = -10;
        let posX = parseFloat(p.style.left);
        let rot = 0;
        const speedY = Math.random() * 5 + 3;
        const speedX = Math.random() * 2 - 1;
        
        const anim = setInterval(() => {
            posY += speedY;
            posX += speedX;
            rot += 5;
            p.style.top = posY + 'px';
            p.style.left = posX + 'vw';
            p.style.transform = `rotate(${rot}deg)`;
            
            if(posY > window.innerHeight) {
                clearInterval(anim);
                p.remove();
            }
        }, 20);
    }
}
</script>
@endsection
