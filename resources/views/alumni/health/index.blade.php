@extends('layouts.app')
@section('title', 'Pusat Kesehatan AI - STEMAN')

@push('styles')
<style>
.health-hero {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
    border-radius: 20px;
    position: relative;
    overflow: hidden;
}
.health-hero::after {
    content: '\f4eb';
    font-family: 'Bootstrap Icons';
    font-size: 12rem;
    color: rgba(255,255,255,0.06);
    position: absolute;
    right: -20px;
    top: -20px;
    line-height: 1;
}
.bmi-gauge {
    width: 100%;
    height: 16px;
    border-radius: 8px;
    background: linear-gradient(to right, #3b82f6 0%, #22c55e 30%, #f59e0b 60%, #ef4444 100%);
    position: relative;
}
.bmi-needle {
    position: absolute;
    top: -6px;
    width: 4px;
    height: 28px;
    background: #1e293b;
    border-radius: 2px;
    transition: left 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateX(-50%);
}
.chat-wrap {
    height: 460px;
    display: flex;
    flex-direction: column;
}
.chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 1.25rem;
    background: #f8fafc;
    scroll-behavior: smooth;
}
.bubble {
    max-width: 78%;
    padding: 10px 16px;
    border-radius: 18px;
    font-size: 0.875rem;
    line-height: 1.6;
    word-break: break-word;
}
.bubble.bot {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 4px;
    color: #1e293b;
}
.bubble.user {
    background: #059669;
    color: #fff;
    border-bottom-right-radius: 4px;
}
.typing span {
    display: inline-block;
    width: 7px; height: 7px;
    background: #94a3b8;
    border-radius: 50%;
    margin: 0 2px;
    animation: bounce 1.2s infinite;
}
.typing span:nth-child(2){ animation-delay:.2s }
.typing span:nth-child(3){ animation-delay:.4s }
@keyframes bounce {
    0%,80%,100%{ transform:translateY(0) }
    40%{ transform:translateY(-6px) }
}
.tab-pill .nav-link { border-radius: 50px !important; font-weight: 600; color: #64748b; }
.tab-pill .nav-link.active { background: #059669 !important; color: #fff !important; }
.bmi-badge { font-size: 1.5rem; font-weight: 800; }
.recommend-card {
    border-left: 4px solid #059669;
    background: #f0fdf4;
    border-radius: 0 12px 12px 0;
    padding: 1rem 1.25rem;
    margin-bottom: 0.75rem;
}
</style>
@endpush

@section('content')
<div class="container py-4">

    {{-- HERO --}}
    <div class="health-hero text-white p-4 p-md-5 mb-4 shadow">
        <h1 class="fw-black mb-1"><i class="bi bi-heart-pulse me-2"></i>Pusat Kesehatan AI</h1>
        <p class="mb-0 opacity-75 lead">Asisten gaya hidup sehat & deteksi dini untuk alumni STEMAN.</p>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
    <div class="alert alert-success rounded-3 border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning rounded-3 border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}</div>
    @endif

    {{-- 40+ REMINDER --}}
    @if($isOver40)
    <div class="alert border-0 shadow-sm rounded-4 mb-4" style="background:#fff1f2; border-left:5px solid #e11d48 !important;">
        <h5 class="fw-bold text-danger"><i class="bi bi-bell-fill me-2"></i>Health Risk Reminder – Usia 40+</h5>
        <p class="text-danger mb-3">Sangat disarankan untuk rutin melakukan pemeriksaan kesehatan berikut:</p>
        <div class="row g-2">
            <div class="col-md-4"><div class="card border-0 shadow-sm rounded-3 p-3 d-flex flex-row align-items-center gap-3"><div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2"><i class="bi bi-heart-fill fs-5"></i></div><div><div class="fw-bold small">Tekanan Darah</div><div class="text-muted" style="font-size:.78rem">Tiap 1 bulan</div></div></div></div>
            <div class="col-md-4"><div class="card border-0 shadow-sm rounded-3 p-3 d-flex flex-row align-items-center gap-3"><div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2"><i class="bi bi-droplet-fill fs-5"></i></div><div><div class="fw-bold small">Gula Darah</div><div class="text-muted" style="font-size:.78rem">Tiap 3 bulan</div></div></div></div>
            <div class="col-md-4"><div class="card border-0 shadow-sm rounded-3 p-3 d-flex flex-row align-items-center gap-3"><div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2"><i class="bi bi-activity fs-5"></i></div><div><div class="fw-bold small">Kolesterol</div><div class="text-muted" style="font-size:.78rem">Tiap 6 bulan</div></div></div></div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        {{-- LEFT: FORMS --}}
        <div class="col-lg-4">
            <ul class="nav tab-pill bg-white shadow-sm border p-1 rounded-pill mb-4" id="hTab" role="tablist">
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link active w-100 py-2" data-bs-toggle="pill" data-bs-target="#pLifestyle" type="button"><i class="bi bi-calculator me-1"></i>Kalkulator</button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 py-2 text-danger" data-bs-toggle="pill" data-bs-target="#pWarning" type="button"><i class="bi bi-shield-plus me-1"></i>Early Warning</button>
                </li>
            </ul>

            <div class="tab-content">
                {{-- LIFESTYLE TAB --}}
                <div class="tab-pane fade show active" id="pLifestyle">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold text-success mb-4"><i class="bi bi-bar-chart-line me-2"></i>Kalkulator Gaya Hidup</h5>

                        {{-- BMI LIVE PREVIEW --}}
                        <div class="card bg-light border-0 rounded-3 p-3 mb-4" id="bmi-preview" style="display:none!important">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold">BMI Anda</span>
                                <span class="badge rounded-pill px-3 py-2" id="bmi-badge" style="font-size:.9rem">–</span>
                            </div>
                            <div class="bmi-gauge mb-1"><div class="bmi-needle" id="bmi-needle"></div></div>
                            <div class="d-flex justify-content-between" style="font-size:.7rem;color:#94a3b8">
                                <span>Underweight</span><span>Normal</span><span>Overweight</span><span>Obese</span>
                            </div>
                            <p class="mt-2 mb-0 small fw-bold text-center" id="bmi-label"></p>
                        </div>

                        <form action="{{ route('alumni.health.lifestyle') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Berat Badan (kg)</label>
                                <input type="number" name="weight" id="inp-weight" min="30" max="300" class="form-control form-control-lg bg-light rounded-3 border-0" placeholder="Contoh: 70" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Tinggi Badan (cm)</label>
                                <input type="number" name="height" id="inp-height" min="100" max="250" class="form-control form-control-lg bg-light rounded-3 border-0" placeholder="Contoh: 170" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Level Aktivitas Fisik</label>
                                <select name="activity_level" id="inp-activity" class="form-select form-select-lg bg-light rounded-3 border-0">
                                    <option value="Rendah">🚶 Rendah (Jarang Olahraga)</option>
                                    <option value="Sedang">🏃 Sedang (1–3x Seminggu)</option>
                                    <option value="Tinggi">🏋️ Tinggi (Sering Olahraga)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100 rounded-3 fw-bold shadow-sm">
                                <i class="bi bi-stars me-2"></i>Analisis dengan AI
                            </button>
                        </form>
                    </div>
                </div>

                {{-- EARLY WARNING TAB --}}
                <div class="tab-pane fade" id="pWarning">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold text-danger mb-1"><i class="bi bi-shield-plus me-2"></i>AI Early Warning</h5>
                        <p class="text-muted small mb-4">Ceritakan gejala yang Anda rasakan. AI akan memberikan edukasi awal.</p>
                        <form action="{{ route('alumni.health.symptoms') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Keluhan / Gejala</label>
                                <textarea name="symptoms" rows="6" required class="form-control bg-light rounded-3 border-0" placeholder="Contoh: Saya sering merasa cepat lelah, kepala pusing, dan nafsu makan berkurang..."></textarea>
                                <div class="form-text">AI hanya memberikan edukasi, bukan diagnosis medis.</div>
                            </div>
                            <button type="submit" class="btn btn-danger btn-lg w-100 rounded-3 fw-bold shadow-sm">
                                <i class="bi bi-search me-2"></i>Cek Keluhan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-start gap-2 mt-3 p-3 rounded-3 bg-light">
                <i class="bi bi-shield-lock-fill text-success mt-1"></i>
                <p class="mb-0 text-muted" style="font-size:.8rem"><strong>Data Aman:</strong> Berat badan, tinggi, dan keluhan Anda dienkripsi penuh. Tidak ada pihak ketiga yang bisa mengaksesnya.</p>
            </div>
        </div>

        {{-- RIGHT: RESULTS + CHATBOT --}}
        <div class="col-lg-8 d-flex flex-column gap-4">

            {{-- AI RESULT --}}
            @if(isset($profile) && $profile->ai_recommendation)
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header border-0 bg-success bg-opacity-10 d-flex justify-content-between align-items-center py-3 px-4">
                    <h6 class="mb-0 fw-bold text-success"><i class="bi bi-robot me-2"></i>Hasil Analisis AI Terakhir</h6>
                    <span class="badge bg-success rounded-pill">{{ $profile->updated_at->diffForHumans() }}</span>
                </div>
                <div class="card-body p-4">
                    @if($profile->bmi_category)
                    <div class="d-flex gap-2 mb-3">
                        <span class="badge bg-light text-dark border fw-normal px-3 py-2">BMI: <strong>{{ $profile->bmi_category }}</strong></span>
                        <span class="badge bg-light text-dark border fw-normal px-3 py-2">Aktivitas: <strong>{{ $profile->activity_level }}</strong></span>
                    </div>
                    @endif
                    <div class="text-muted" style="white-space:pre-wrap;line-height:1.8;font-size:.9rem">{{ $profile->ai_recommendation }}</div>
                </div>
            </div>
            @endif

            {{-- CHATBOT --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden chat-wrap">
                <div class="d-flex align-items-center gap-3 px-4 py-3 bg-dark text-white">
                    <div class="position-relative">
                        <img src="https://ui-avatars.com/api/?name=AI&background=059669&color=fff&bold=true" width="44" height="44" class="rounded-circle border border-2 border-success">
                        <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-dark rounded-circle" style="width:12px;height:12px"></span>
                    </div>
                    <div>
                        <div class="fw-bold lh-1">Steman Health Chatbot</div>
                        <small class="text-white-50">Siap menjawab seputar gaya hidup sehat</small>
                    </div>
                </div>

                <div class="chat-body" id="chatBody">
                    <div class="d-flex gap-2 mb-3">
                        <img src="https://ui-avatars.com/api/?name=AI&background=059669&color=fff&bold=true" width="32" height="32" class="rounded-circle flex-shrink-0 align-self-end">
                        <div>
                            <div class="bubble bot shadow-sm">Halo! Saya asisten kesehatan AI Anda. Silakan tanya apa saja seputar gaya hidup, nutrisi, atau olahraga yang aman untuk usia 40+ 😊</div>
                            <div class="text-muted mt-1" style="font-size:.7rem">Sekarang</div>
                        </div>
                    </div>
                </div>

                <div class="px-3 py-3 bg-white border-top">
                    <form id="chatForm" class="d-flex gap-2">
                        <input id="chatInput" type="text" class="form-control rounded-pill bg-light border-0 px-4" placeholder="Ketik pesan Anda..." autocomplete="off" required>
                        <button type="submit" id="chatBtn" class="btn btn-success rounded-pill px-3 flex-shrink-0" style="width:46px;height:46px">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                    <p class="text-muted text-center mb-0 mt-2" style="font-size:.7rem">⚠️ AI bukan dokter. Selalu konsultasikan kondisi medis ke tenaga kesehatan profesional.</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── BMI LIVE CALCULATOR ─────────────────────────────────
(function() {
    const wInp  = document.getElementById('inp-weight');
    const hInp  = document.getElementById('inp-height');
    const preview = document.getElementById('bmi-preview');
    const badge   = document.getElementById('bmi-badge');
    const needle  = document.getElementById('bmi-needle');
    const label   = document.getElementById('bmi-label');

    const categories = [
        { max: 18.5, label: 'Underweight',  color: '#3b82f6', pos:  8 },
        { max: 24.9, label: 'Normal',        color: '#22c55e', pos: 35 },
        { max: 29.9, label: 'Overweight',    color: '#f59e0b', pos: 62 },
        { max: 999,  label: 'Obese',         color: '#ef4444', pos: 88 },
    ];

    function calcBMI() {
        const w = parseFloat(wInp.value);
        const h = parseFloat(hInp.value) / 100;
        if (!w || !h || h <= 0) { preview.style.display = 'none'; return; }
        const bmi = w / (h * h);
        const cat = categories.find(c => bmi < c.max);
        preview.style.removeProperty('display');
        badge.textContent  = bmi.toFixed(1);
        badge.style.background = cat.color;
        badge.style.color      = '#fff';
        needle.style.left      = cat.pos + '%';
        label.textContent  = cat.label;
        label.style.color  = cat.color;
    }

    wInp.addEventListener('input', calcBMI);
    hInp.addEventListener('input', calcBMI);
})();

// ── CHATBOT ──────────────────────────────────────────────
(function() {
    const form   = document.getElementById('chatForm');
    const input  = document.getElementById('chatInput');
    const btn    = document.getElementById('chatBtn');
    const body   = document.getElementById('chatBody');
    const CSRF   = document.querySelector('meta[name="csrf-token"]').content;

    function timeNow() {
        return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }

    function addBubble(text, role) {
        const wrap = document.createElement('div');
        wrap.className = `d-flex gap-2 mb-3 ${role === 'user' ? 'flex-row-reverse' : ''}`;
        const avatarHtml = role === 'bot'
            ? `<img src="https://ui-avatars.com/api/?name=AI&background=059669&color=fff&bold=true" width="32" height="32" class="rounded-circle flex-shrink-0 align-self-end">`
            : '';
        wrap.innerHTML = `
            ${avatarHtml}
            <div class="${role === 'user' ? 'text-end' : ''}">
                <div class="bubble ${role} shadow-sm">${text.replace(/\n/g,'<br>')}</div>
                <div class="text-muted mt-1" style="font-size:.7rem">${timeNow()}</div>
            </div>
        `;
        body.appendChild(wrap);
        body.scrollTop = body.scrollHeight;
        return wrap;
    }

    function addTyping() {
        const wrap = document.createElement('div');
        wrap.id = 'typing';
        wrap.className = 'd-flex gap-2 mb-3 align-items-end';
        wrap.innerHTML = `
            <img src="https://ui-avatars.com/api/?name=AI&background=059669&color=fff&bold=true" width="32" height="32" class="rounded-circle flex-shrink-0">
            <div class="bubble bot shadow-sm typing"><span></span><span></span><span></span></div>
        `;
        body.appendChild(wrap);
        body.scrollTop = body.scrollHeight;
    }

    function removeTyping() {
        const t = document.getElementById('typing');
        if (t) t.remove();
    }

    function setLoading(v) {
        input.disabled = v;
        btn.disabled   = v;
        btn.innerHTML  = v ? '<span class="spinner-border spinner-border-sm"></span>' : '<i class="bi bi-send-fill"></i>';
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const msg = input.value.trim();
        if (!msg) return;

        addBubble(msg, 'user');
        input.value = '';
        setLoading(true);
        addTyping();

        fetch("{{ route('alumni.health.chat') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: msg })
        })
        .then(r => r.json())
        .then(data => {
            removeTyping();
            addBubble(data.reply || 'Maaf, terjadi kesalahan.', 'bot');
        })
        .catch(() => {
            removeTyping();
            addBubble('Terjadi masalah jaringan. Silakan coba lagi.', 'bot');
        })
        .finally(() => setLoading(false));
    });
})();
</script>
@endpush
