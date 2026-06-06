@extends('layouts.admin')

@section('admin-content')
@php
    $placeholders  = ['your-google-client-id','your-google-client-secret'];
    $googleReady   = !empty(config('services.google.client_id'))  && !in_array(config('services.google.client_id'),  $placeholders);
    $telegramReady = !empty(config('services.telegram.bot_token')) && !in_array(config('services.telegram.bot_token'), ['123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11']);
@endphp

<div class="container-fluid px-4 py-4" style="background: #f0f2f5; min-height: 100vh;">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-black mb-0 text-dark" style="letter-spacing: -1px;">SYSTEM <span class="text-primary">GUARD</span></h1>
            <p class="text-muted mb-0 small"><i class="bi bi-shield-check-fill text-success me-1"></i>Monitoring & Self-Healing Otomatis — Refresh tiap <span id="countdown" class="fw-bold text-primary">30</span> detik</p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <div id="guard-status-badge" class="glass-badge bg-secondary text-white fw-bold px-3 py-2 rounded-pill d-flex align-items-center">
                <span class="spinner-border spinner-border-sm me-2"></span> Memuat...
            </div>
            <button class="btn btn-white shadow-sm border rounded-pill px-3" onclick="loadStatus(true)" title="Refresh sekarang">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>

    {{-- Kartu Ringkasan --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 text-center h-100">
                <div class="text-uppercase small fw-bold text-muted mb-2">Integritas Sistem</div>
                <div class="d-flex align-items-center justify-content-center">
                    <div class="fw-black fs-1 text-primary" id="s-percent">—</div>
                    <div class="ms-2 text-start">
                        <div class="small fw-bold lh-1">%</div>
                        <div class="text-muted x-small" id="s-total">0 Cek</div>
                    </div>
                </div>
                <div class="progress mt-3 rounded-pill" style="height:6px;">
                    <div id="integrity-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:0%"></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 text-center h-100" style="border-left:6px solid #198754;">
                <div class="text-uppercase small fw-bold text-muted mb-2">Normal</div>
                <div class="fw-black fs-1 text-success" id="s-passed">—</div>
                <div class="small fw-bold text-success opacity-75">MODUL STABIL</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 text-center h-100" style="border-left:6px solid #dc3545;">
                <div class="text-uppercase small fw-bold text-muted mb-2">Anomali</div>
                <div class="fw-black fs-1 text-danger" id="s-failed">—</div>
                <div class="small fw-bold text-danger opacity-75">PERLU PERHATIAN</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 h-100 d-flex flex-column justify-content-between">
                <div class="text-uppercase small fw-bold text-muted mb-2">Aksi Perbaikan</div>
                <div class="d-grid gap-2">
                    <button class="btn btn-danger btn-sm rounded-pill fw-bold" onclick="executeAction('autofix')" id="btn-autofix">
                        <i class="bi bi-magic me-1"></i> Auto-Fix Semua
                    </button>
                    <button class="btn btn-primary btn-sm rounded-pill fw-bold" onclick="executeAction('optimize')" id="btn-optimize">
                        <i class="bi bi-rocket-takeoff-fill me-1"></i> Optimasi Cache
                    </button>
                    <button class="btn btn-outline-danger btn-sm rounded-pill fw-bold" onclick="executeAction('clear-cache')" id="btn-clear-cache">
                        <i class="bi bi-trash-fill me-1"></i> Hapus Semua Cache
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Konfigurasi --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark"><i class="bi bi-gear-fill me-2 text-secondary"></i>Status Konfigurasi Layanan</span>
                    <span class="x-small text-muted">Konfigurasi di <code>.env</code> server</span>
                </div>
                <div class="card-body px-4 pb-3 pt-0">
                    <div class="row g-3">

                        {{-- Telegram --}}
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-4 {{ $telegramReady ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : 'bg-danger bg-opacity-10 border border-danger border-opacity-25' }}">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-telegram me-2 {{ $telegramReady ? 'text-success' : 'text-danger' }} fs-5"></i>
                                    <span class="fw-bold small">Telegram</span>
                                </div>
                                <div class="x-small {{ $telegramReady ? 'text-success' : 'text-danger' }}">
                                    {{ $telegramReady ? 'Aktif — notifikasi berjalan' : 'Belum dikonfigurasi' }}
                                </div>
                                @if(!$telegramReady)
                                <div class="x-small text-muted mt-1">Isi TELEGRAM_BOT_TOKEN di .env</div>
                                @endif
                            </div>
                        </div>

                        {{-- Google OAuth --}}
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-4 {{ $googleReady ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : 'bg-warning bg-opacity-10 border border-warning border-opacity-25' }}">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-google me-2 {{ $googleReady ? 'text-success' : 'text-warning' }} fs-5"></i>
                                    <span class="fw-bold small">Google Login</span>
                                </div>
                                <div class="x-small {{ $googleReady ? 'text-success' : 'text-warning' }}">
                                    {{ $googleReady ? 'Aktif — login Google bisa dipakai' : 'Belum dikonfigurasi' }}
                                </div>
                                @if(!$googleReady)
                                <div class="x-small text-muted mt-1">Isi GOOGLE_CLIENT_ID di .env</div>
                                @endif
                            </div>
                        </div>

                        {{-- Scheduler Heartbeat --}}
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25" id="scheduler-card">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-clock-history me-2 text-success fs-5"></i>
                                    <span class="fw-bold small">Scheduler</span>
                                </div>
                                <div class="x-small text-success" id="scheduler-status">Memeriksa...</div>
                            </div>
                        </div>

                        {{-- Backup Harian --}}
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-4 bg-info bg-opacity-10 border border-info border-opacity-25">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-database-fill me-2 text-info fs-5"></i>
                                    <span class="fw-bold small">Backup Otomatis</span>
                                </div>
                                <div class="x-small text-info">Setiap hari pukul 02:00</div>
                                <div class="x-small text-muted mt-1">Cron aktif di server</div>
                            </div>
                        </div>

                    </div>

                    {{-- Panduan Aktivasi Google --}}
                    @if(!$googleReady)
                    <div class="alert alert-warning border-0 rounded-4 mt-3 mb-0 small">
                        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Cara Aktifkan Login Google:</strong>
                        <ol class="mb-0 mt-2 ps-3">
                            <li>Buka <a href="https://console.cloud.google.com" target="_blank" class="fw-bold">console.cloud.google.com</a></li>
                            <li>Buat project → APIs & Services → OAuth 2.0 Client ID</li>
                            <li>Tambahkan Authorized redirect URI: <code>https://alumni-steman.my.id/auth/google/callback</code></li>
                            <li>Salin Client ID dan Client Secret</li>
                            <li>Isi di <code>.env</code> server: <code>GOOGLE_CLIENT_ID=...</code> dan <code>GOOGLE_CLIENT_SECRET=...</code></li>
                            <li>Jalankan: <code>docker exec steman_app php artisan config:cache</code></li>
                        </ol>
                    </div>
                    @endif

                    {{-- Panduan Aktivasi Telegram --}}
                    @if(!$telegramReady)
                    <div class="alert alert-info border-0 rounded-4 mt-3 mb-0 small">
                        <strong><i class="bi bi-telegram me-2"></i>Cara Aktifkan Notifikasi Telegram:</strong>
                        <ol class="mb-0 mt-2 ps-3">
                            <li>Buka Telegram → cari <strong>@BotFather</strong> → kirim <code>/newbot</code></li>
                            <li>Salin token yang diberikan</li>
                            <li>Kirim pesan ke bot → buka <code>https://api.telegram.org/botTOKEN/getUpdates</code> → catat <code>chat.id</code></li>
                            <li>Isi di <code>.env</code>: <code>TELEGRAM_BOT_TOKEN=...</code> dan <code>TELEGRAM_CHAT_ID=...</code></li>
                            <li>Jalankan: <code>docker exec steman_app php artisan config:cache</code></li>
                        </ol>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Monitor & Circuit Breaker --}}
    <div class="row g-4">
        {{-- Health Monitor --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                <div class="card-header bg-dark text-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <span class="fw-black fs-5"><i class="bi bi-cpu-fill me-2 text-info"></i>MONITOR INTI (21 Titik)</span>
                    <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-50 px-3 py-2 rounded-pill" id="last-checked">LIVE</span>
                </div>
                <div class="card-body p-0" id="checks-list" style="max-height: 520px; overflow-y: auto;">
                    <div class="text-center py-5">
                        <div class="spinner-grow text-primary" role="status"></div>
                        <p class="mt-3 text-muted fw-bold">Menyinkronkan data sistem...</p>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 text-center">
                    <button class="btn btn-dark btn-sm rounded-pill px-4 fw-bold" onclick="executeAction('maintenance')" id="btn-maintenance">
                        <i class="bi bi-shield-shaded me-2"></i>Jalankan Diagnostik Mendalam (SRE)
                    </button>
                </div>
            </div>
        </div>

        {{-- Circuit Breakers & Anomali --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden mb-4">
                <div class="card-header bg-dark text-white border-0 py-3 px-4">
                    <span class="fw-black fs-5"><i class="bi bi-lightning-charge-fill me-2 text-warning"></i>CIRCUIT BREAKER</span>
                    <div class="x-small opacity-50 mt-1">OPEN = terlalu banyak kegagalan, CLOSED = normal</div>
                </div>
                <div class="card-body p-0" style="max-height: 280px; overflow-y:auto;" id="circuits-list">
                    <div class="text-center py-4 text-muted small">Memuat...</div>
                </div>
            </div>

            <div class="card border-0 shadow-lg rounded-5 overflow-hidden text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                <div class="card-body p-4">
                    <h5 class="fw-black mb-3"><i class="bi bi-stars me-2"></i>ANALISIS ANOMALI</h5>
                    <div id="anomalies-list" class="mt-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast Notifikasi --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="liveToast" class="toast align-items-center text-white bg-dark border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-bold"><span id="toast-message"></span></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .fw-black { font-weight: 800; }
    .x-small { font-size: 0.7rem; }

    .glass-card {
        background: rgba(255,255,255,0.75);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 24px;
        box-shadow: 0 8px 32px rgba(31,38,135,0.07);
        transition: all 0.3s ease;
    }
    .glass-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(31,38,135,0.12); }

    .glass-badge { backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2); }

    .check-row {
        padding: 14px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(0,0,0,0.04);
        transition: background 0.2s;
    }
    .check-row:last-child { border-bottom: none; }
    .check-row:hover { background: rgba(13,110,253,0.04); }
    .check-row.row-ok { border-left: 3px solid #198754; }
    .check-row.row-fail { border-left: 3px solid #dc3545; }

    .pulse-dot {
        width: 10px; height: 10px;
        border-radius: 50%; display: inline-block;
        animation: pulse-anim 1.5s infinite;
    }
    @keyframes pulse-anim { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(1.3)} }

    .insight-card {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 16px; padding: 12px 16px; margin-bottom: 10px;
    }

    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 10px; }
</style>
@endpush

@push('scripts')
<script>
const STATUS_URL = "{{ route('admin.guard.status') }}";
let countdown = 30;

const LABEL = {
    db_down:           'Koneksi Database',
    redis_down:        'Koneksi Redis',
    queue_overload:    'Antrian Queue',
    meili_down:        'Meilisearch (Search)',
    disk_low:          'Ruang Disk',
    storage_broken:    'Folder Storage',
    log_bloated:       'Ukuran File Log',
    session_domain:    'Domain Session',
    captcha_patch:     'Patch Captcha',
    nginx_down:        'Nginx Web Server',
    audit_broken:      'Integritas Audit Log',
    route_mismatch:    'Integritas Route',
    route_shadowing:   'Route Shadowing',
    smoke_test:        'Akses Halaman Publik',
    migration_mismatch:'Migrasi Database',
    symlink_broken:    'Symlink Storage',
    ai_offline:        'AI Service',
    earth_data_mismatch:'Data Koordinat Alumni',
    news_api_down:     'News API',
    scheduler_dead:    'Scheduler Heartbeat',
    queue_worker_dead: 'Worker Antrian',
};

function showToast(msg, type = 'success') {
    const el = document.getElementById('liveToast');
    document.getElementById('toast-message').innerHTML = msg;
    el.classList.remove('bg-dark','bg-danger','bg-success','bg-warning');
    el.classList.add('bg-' + type);
    bootstrap.Toast.getOrCreateInstance(el).show();
}

function executeAction(action) {
    const btn = document.getElementById('btn-' + action);
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';

    const urls = {
        autofix:      "{{ route('admin.guard.autofix') }}",
        optimize:     "{{ route('admin.guard.optimize') }}",
        'clear-cache':"{{ route('admin.guard.clear-cache') }}",
        maintenance:  "{{ route('admin.guard.maintenance') }}"
    };

    fetch(urls[action], {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        const ok = d.status === 'success' || d.status === 'partial';
        showToast((ok ? '✅ ' : '❌ ') + d.message, ok ? 'success' : 'danger');
        loadStatus();
    })
    .catch(() => showToast('🚨 Gagal menghubungi server. Cek koneksi.', 'danger'))
    .finally(() => { btn.disabled = false; btn.innerHTML = orig; });
}

function loadStatus(manual = false) {
    if (manual) { countdown = 30; }

    fetch(STATUS_URL)
        .then(r => r.json())
        .then(data => {
            renderSummary(data.summary, data.status);
            renderChecks(data.checks, data.issues);
            renderCircuits(data.circuits);
            renderAnomalies(data.anomalies);
            renderSchedulerCard(data.checks);
            document.getElementById('last-checked').textContent = 'Diperbarui ' + new Date().toLocaleTimeString('id-ID');
        })
        .catch(() => {
            const b = document.getElementById('guard-status-badge');
            b.className = 'glass-badge bg-danger text-white fw-bold px-3 py-2 rounded-pill d-flex align-items-center';
            b.innerHTML = '🚨 KONEKSI TERPUTUS';
        });
}

function renderSummary(s, status) {
    document.getElementById('s-total').textContent   = s.total + ' Cek';
    document.getElementById('s-passed').textContent  = s.passed;
    document.getElementById('s-failed').textContent  = s.failed;
    document.getElementById('s-percent').textContent = s.percent;

    const bar = document.getElementById('integrity-bar');
    bar.style.width = s.percent + '%';
    bar.className   = 'progress-bar progress-bar-striped progress-bar-animated ' +
                      (s.percent >= 90 ? 'bg-success' : s.percent >= 70 ? 'bg-warning' : 'bg-danger');

    const badge = document.getElementById('guard-status-badge');
    if (status === 'OPERATIONAL') {
        badge.className = 'glass-badge bg-success text-white fw-bold px-3 py-2 rounded-pill d-flex align-items-center shadow-lg';
        badge.innerHTML = '<span class="pulse-dot bg-white me-2"></span> SEMUA SISTEM NORMAL';
    } else {
        badge.className = 'glass-badge bg-danger text-white fw-bold px-3 py-2 rounded-pill d-flex align-items-center shadow-lg';
        badge.innerHTML = '🚨 ' + s.failed + ' MASALAH TERDETEKSI';
    }
}

function renderChecks(checks, issues) {
    const el = document.getElementById('checks-list');
    el.innerHTML = '';
    for (const [key, status] of Object.entries(checks)) {
        const ok    = status === 'OK';
        const label = LABEL[key] || key.replace(/_/g, ' ').toUpperCase();
        const errText = !ok ? (' — ' + status.substring(0, 40)) : '';
        el.innerHTML += `
        <div class="check-row ${ok ? 'row-ok' : 'row-fail'}">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 px-2 py-1" style="background:${ok ? 'rgba(25,135,84,0.08)' : 'rgba(220,53,69,0.08)'}">
                    <i class="bi ${ok ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger'}"></i>
                </div>
                <div>
                    <div class="fw-bold small">${label}</div>
                    ${!ok ? `<div class="x-small text-danger">${errText}</div>` : ''}
                </div>
            </div>
            <span class="badge rounded-pill px-3 py-2 fw-bold small ${ok
                ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'
                : 'bg-danger  bg-opacity-10 text-danger  border border-danger  border-opacity-25'}">
                ${ok ? 'NORMAL' : 'ERROR'}
            </span>
        </div>`;
    }
}

function renderSchedulerCard(checks) {
    const ok = checks && checks['scheduler_dead'] === 'OK';
    const card   = document.getElementById('scheduler-card');
    const status = document.getElementById('scheduler-status');
    if (!card) return;
    if (ok) {
        card.className = 'p-3 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25';
        status.className = 'x-small text-success';
        status.textContent = 'Berjalan normal — heartbeat aktif';
    } else {
        card.className = 'p-3 rounded-4 bg-danger bg-opacity-10 border border-danger border-opacity-25';
        status.className = 'x-small text-danger';
        status.textContent = 'Scheduler kemungkinan mati!';
    }
}

function renderCircuits(circuits) {
    const el = document.getElementById('circuits-list');
    el.innerHTML = '';
    let hasOpen = false;
    for (const [key, data] of Object.entries(circuits)) {
        if (data.total_failures === 0 && data.state === 'CLOSED') continue;
        hasOpen = true;
        const color   = { CLOSED:'#22c55e', OPEN:'#ef4444', HALF:'#f59e0b' }[data.state] || '#94a3b8';
        const bgClass = { CLOSED:'success', OPEN:'danger', HALF:'warning' }[data.state] || 'secondary';
        const label   = LABEL[key] || key.replace(/_/g,' ');
        el.innerHTML += `
        <div class="check-row px-4">
            <div class="d-flex align-items-center gap-2">
                <span class="pulse-dot" style="background:${color}"></span>
                <span class="fw-bold x-small">${label}</span>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="text-muted x-small">Gagal: ${data.total_failures}x</span>
                <span class="badge bg-${bgClass} rounded-pill px-2" style="font-size:.6rem;font-weight:800;">${data.state}</span>
            </div>
        </div>`;
    }
    if (!hasOpen) {
        el.innerHTML = '<div class="text-center py-4 text-success small fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Semua circuit breaker CLOSED — Tidak ada gangguan</div>';
    }
}

function renderAnomalies(anomalies) {
    const el = document.getElementById('anomalies-list');
    if (!anomalies || anomalies.length === 0) {
        el.innerHTML = `<div class="insight-card border-0">
            <div class="small fw-bold">STATUS AI: OPTIMAL</div>
            <div class="x-small opacity-75 mt-1">Tidak ada anomali atau kegagalan berulang terdeteksi saat ini. Sistem berjalan stabil.</div>
        </div>`;
        return;
    }
    el.innerHTML = anomalies.map(a => `
    <div class="insight-card bg-white text-dark">
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold x-small">${LABEL[a.issue] || a.issue.replace(/_/g,' ')}</span>
            <span class="badge bg-danger rounded-pill x-small">ANOMALI</span>
        </div>
        <div class="x-small mt-1 text-muted">
            Gagal <strong>${a.total}x</strong> — Circuit: <strong>${a.state}</strong>.
            Disarankan: cek log server atau jalankan Auto-Fix.
        </div>
    </div>`).join('');
}

// Countdown timer
setInterval(() => {
    countdown--;
    const el = document.getElementById('countdown');
    if (el) el.textContent = countdown;
    if (countdown <= 0) {
        countdown = 30;
        loadStatus();
    }
}, 1000);

// Load pertama
loadStatus();
</script>
@endpush
