@extends('layouts.app')

@section('content')
<style>
.birthday-hero {
    background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 30%, #ff6b6b 60%, #ffd93d 100%);
    background-size: 200% 200%;
    animation: gradientShift 4s ease infinite;
    padding: 70px 0 50px;
    position: relative; overflow: hidden;
}
@keyframes gradientShift {
    0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%}
}
.birthday-hero::before {
    content: '🎂 🎉 🎁 🎈 🥳 🎊 🎂 🎉 🎁 🎈';
    position: absolute;
    top: -10px; left: 0; right: 0;
    font-size: 2rem;
    opacity: 0.15;
    letter-spacing: 20px;
    animation: floatText 8s linear infinite;
    white-space: nowrap;
}
@keyframes floatText { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }

/* Birthday Card — Hari Ini */
.bday-card-today {
    background: linear-gradient(135deg, #fff5f5, #fff0f0);
    border: 2px solid #fecaca;
    border-radius: 20px;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}
.bday-card-today:hover { transform: translateY(-4px); box-shadow: 0 15px 40px rgba(239,68,68,0.15); }
.bday-card-today::after {
    content: '🎂';
    position: absolute; right: 16px; top: 16px;
    font-size: 2rem;
    animation: bounce 1s ease infinite alternate;
}
@keyframes bounce { 0%{transform:translateY(0) scale(1)} 100%{transform:translateY(-6px) scale(1.1)} }

.avatar-bday {
    width: 64px; height: 64px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fca5a5;
}
.avatar-bday-placeholder {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, #fca5a5, #f87171);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
    border: 3px solid #fca5a5;
}

/* Month card */
.bday-card-month {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    padding: 1rem 1.2rem;
    display: flex; align-items: center; gap: 1rem;
    transition: all 0.2s;
}
.bday-card-month:hover { border-color: #fca5a5; background: #fff5f5; }
.days-badge {
    background: linear-gradient(135deg, #fca5a5, #f87171);
    color: #fff;
    border-radius: 10px;
    padding: 6px 12px;
    font-size: 0.8rem;
    font-weight: 700;
    white-space: nowrap;
    flex-shrink: 0;
}
.days-badge.today { background: linear-gradient(135deg, #059669, #10b981); }

/* Greet button */
.greet-btn {
    background: linear-gradient(135deg, #ff6b6b, #ff8e53);
    color: #fff;
    border: none;
    border-radius: 30px;
    padding: 8px 20px;
    font-size: 0.82rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}
.greet-btn:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(255,107,107,0.4); }
.greet-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
.greet-btn.sent { background: linear-gradient(135deg, #059669, #10b981); }

/* Month selector */
.month-nav-btn {
    border: 1.5px solid #e2e8f0;
    background: transparent;
    border-radius: 10px;
    padding: 6px 14px;
    font-size: 0.82rem;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 600;
    color: #64748b;
}
.month-nav-btn.active { background: #ff6b6b; border-color: #ff6b6b; color: #fff; }

/* Empty state */
.empty-cake {
    text-align: center; padding: 3rem 0;
}
.empty-cake .emoji { font-size: 4rem; animation: pulse 2s ease infinite; }
@keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.1)} }

/* Confetti particle */
.confetti { position: fixed; top: -10px; pointer-events: none; z-index: 9999; }

/* Dark mode */
.dark .bday-card-today { background: #1a0a0a; border-color: rgba(252,165,165,0.3); }
.dark .bday-card-month { background: #1e293b; border-color: rgba(255,255,255,0.08); }
.dark .bday-card-month:hover { background: #2d1010; border-color: rgba(252,165,165,0.3); }
</style>

<section class="birthday-hero">
    <div class="container position-relative text-white text-center">
        <div style="font-size: 4rem; margin-bottom: 0.5rem; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.2));">🎂</div>
        <h1 class="display-6 fw-black mb-2">Alumni Berulang Tahun 🎉</h1>
        <p class="opacity-80 mb-0">Jangan lupa ucapkan selamat! Bikin hari mereka makin spesial 💖</p>
    </div>
</section>

<div class="container py-5">

    @if ($todayBirthdays->isNotEmpty())
    {{-- TODAY --}}
    <div class="mb-5">
        <div class="d-flex align-items-center gap-3 mb-4">
            <h4 class="fw-black mb-0 text-danger">🎉 Ulang Tahun Hari Ini!</h4>
            <span class="badge bg-danger rounded-pill">{{ $todayBirthdays->count() }} orang</span>
        </div>

        <div class="row g-3">
            @foreach ($todayBirthdays as $alumni)
            <div class="col-md-6 col-lg-4">
                <div class="bday-card-today">
                    <div class="d-flex gap-3 align-items-start">
                        @if ($alumni->profile_picture)
                        <img src="{{ $alumni->profile_picture_url }}" alt="{{ $alumni->name }}" class="avatar-bday">
                        @else
                        <div class="avatar-bday-placeholder">{{ strtoupper(substr($alumni->name, 0, 1)) }}</div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1">{{ $alumni->name }}</h6>
                            <div class="text-muted small mb-1">
                                {{ $alumni->major ?? 'Alumni Steman' }}
                                @if ($alumni->graduation_year)• {{ $alumni->graduation_year }}@endif
                            </div>
                            @if ($alumni->city_name)
                            <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $alumni->city_name }}</div>
                            @endif
                            <div class="text-danger fw-bold small mt-1">🎂 {{ $alumni->age }} tahun hari ini!</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        @auth
                        <button class="greet-btn {{ $sentToday->contains($alumni->id) ? 'sent' : '' }}"
                                data-id="{{ $alumni->id }}"
                                onclick="sendGreeting({{ $alumni->id }}, this)"
                                {{ $sentToday->contains($alumni->id) ? 'disabled' : '' }}>
                            {{ $sentToday->contains($alumni->id) ? '✅ Terkirim!' : '🎉 Ucapkan Selamat' }}
                        </button>
                        @endauth
                        <a href="{{ route('alumni.show', $alumni) }}"
                           class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            Profil
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="empty-cake mb-5">
        <div class="emoji">🎂</div>
        <h5 class="mt-3 text-muted">Belum ada yang ulang tahun hari ini</h5>
        <p class="text-muted small">Lengkapi profil dengan tanggal lahirmu agar teman bisa mengucapkan selamat!</p>
        @auth
        <a href="{{ route('profile.edit') }}" class="btn btn-danger rounded-pill px-4 mt-2">
            <i class="bi bi-person-gear me-2"></i>Update Profil
        </a>
        @endauth
    </div>
    @endif

    {{-- THIS MONTH --}}
    @php
        $monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    @endphp
    <div>
        <h5 class="fw-bold mb-4">📅 Ulang Tahun Bulan {{ $monthNames[$month] }}</h5>

        @if ($monthBirthdays->isEmpty() && $todayBirthdays->isEmpty())
        <div class="empty-cake">
            <div class="emoji">📅</div>
            <p class="text-muted">Tidak ada alumni yang berulang tahun bulan ini</p>
        </div>
        @else
        <div class="row g-3">
            @foreach ($monthBirthdays as $alumni)
            <div class="col-md-6">
                <div class="bday-card-month">
                    <div class="days-badge">
                        @if ($alumni->days_until == 0)
                        <span>HARI INI</span>
                        @elseif ($alumni->days_until == 1)
                        <span>BESOK</span>
                        @else
                        <span>{{ $alumni->days_until }} hari lagi</span>
                        @endif
                    </div>
                    @if ($alumni->profile_picture)
                    <img src="{{ $alumni->profile_picture_url }}" alt="{{ $alumni->name }}"
                         style="width:44px;height:44px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                    @else
                    <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#fca5a5,#f87171);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;flex-shrink:0;">
                        {{ strtoupper(substr($alumni->name, 0, 1)) }}
                    </div>
                    @endif
                    <div class="flex-grow-1">
                        <div class="fw-bold" style="font-size: 0.9rem;">{{ $alumni->name }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            {{ \Carbon\Carbon::parse($alumni->birthday)->format('d M') }}
                            @if($alumni->major) · {{ $alumni->major }} @endif
                        </div>
                    </div>
                    <a href="{{ route('alumni.show', $alumni) }}" class="btn btn-sm btn-outline-secondary rounded-pill" style="flex-shrink:0; font-size:0.75rem;">
                        Lihat
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Greeting Modal --}}
<div class="modal fade" id="greetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow-lg text-center p-3">
            <div style="font-size: 4rem; animation: bounce 0.5s ease infinite alternate;">🎉</div>
            <h5 class="fw-bold mt-2">Ucapan Terkirim!</h5>
            <p class="text-muted small" id="greetModalMsg">Semoga hari mereka makin spesial 💖</p>
            <button class="btn btn-danger rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function sendGreeting(userId, btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    const message = getRandomGreeting();

    fetch(`/birthday/greet/${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.classList.add('sent');
            btn.innerHTML = '✅ Terkirim!';
            launchConfetti();
            document.getElementById('greetModalMsg').textContent =
                `${data.total_greetings} alumni sudah mengucapkan selamat hari ini! 🎊`;
            new bootstrap.Modal(document.getElementById('greetModal')).show();
        } else {
            btn.disabled = false;
            btn.innerHTML = '🎉 Ucapkan Selamat';
            alert(data.error);
        }
    });
}

function getRandomGreeting() {
    const greetings = [
        '🎉 Selamat Ulang Tahun! Semoga sukses dan selalu sehat, kak!',
        '🎂 Happy Birthday! Makin sukses ya kak, bangga jadi alumni Steman bareng kamu!',
        '🥳 Selamat ultah! Semoga impian-impianmu tercapai semua!',
        '🎁 Happy birthday! Semoga rezeki mengalir deras dan kesehatan selalu menyertai!',
        '🌟 Selamat ulang tahun! Tetap semangat menginspirasi alumni Steman ya!',
    ];
    return greetings[Math.floor(Math.random() * greetings.length)];
}

function launchConfetti() {
    const colors = ['#ff6b6b','#ffd93d','#6bcb77','#4d96ff','#ff6bcb'];
    for (let i = 0; i < 60; i++) {
        const el = document.createElement('div');
        el.className = 'confetti';
        el.style.cssText = `
            left: ${Math.random() * 100}vw;
            background: ${colors[Math.floor(Math.random() * colors.length)]};
            width: ${Math.random() * 8 + 5}px;
            height: ${Math.random() * 8 + 5}px;
            border-radius: ${Math.random() > 0.5 ? '50%' : '2px'};
            animation: fall ${Math.random() * 2 + 1.5}s linear ${Math.random() * 0.5}s forwards;
        `;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }
}
</script>
<style>
@keyframes fall {
    0% { transform: translateY(-10px) rotate(0deg); opacity: 1; }
    100% { transform: translateY(100vh) rotate(${Math.random() * 720}deg); opacity: 0; }
}
</style>
@endpush
@endsection
