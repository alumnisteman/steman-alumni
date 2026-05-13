@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid px-4 py-4" style="background: #f0f2f5; min-height: 100vh;">
    {{-- Header Elite Section --}}
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h1 class="fw-black mb-0 text-dark" style="letter-spacing: -1px;">SYSTEM <span class="text-primary">GUARD</span></h1>
            <p class="text-muted mb-0"><i class="bi bi-shield-check-fill text-success"></i> Realtime Autonomous Monitoring & Self-Healing Command Center</p>
        </div>
        <div class="d-flex gap-2">
            <div id="guard-status-badge" class="glass-badge bg-secondary text-white fw-bold px-3 py-2 rounded-pill d-flex align-items-center">
                <span class="spinner-border spinner-border-sm me-2"></span> INITIALIZING...
            </div>
            <button class="btn btn-white shadow-sm border-0 rounded-pill px-3" onclick="loadStatus()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>

    {{-- Performance Pulse --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-3 text-center h-100">
                <div class="text-uppercase small fw-bold text-muted mb-2">System Integrity</div>
                <div class="d-flex align-items-center justify-content-center">
                    <div class="fw-black fs-1 text-primary" id="s-percent">—</div>
                    <div class="ms-2 text-start">
                        <div class="small fw-bold lh-1" id="s-total-label">CHECKS</div>
                        <div class="text-muted x-small" id="s-total">0 Total</div>
                    </div>
                </div>
                <div class="progress mt-3 rounded-pill" style="height: 6px;">
                    <div id="integrity-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 0%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 text-center h-100 border-start-success">
                <div class="text-uppercase small fw-bold text-muted mb-2">Operational</div>
                <div class="fw-black fs-1 text-success" id="s-passed">—</div>
                <div class="small fw-bold text-success opacity-75">STABLE MODULES</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 text-center h-100 border-start-danger">
                <div class="text-uppercase small fw-bold text-muted mb-2">Anomalies</div>
                <div class="fw-black fs-1 text-danger" id="s-failed">—</div>
                <div class="small fw-bold text-danger opacity-75">ATTENTION REQUIRED</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 h-100 d-flex flex-column justify-content-between">
                <div class="text-uppercase small fw-bold text-muted mb-2">Self-Healing Tools</div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm rounded-pill fw-bold" onclick="executeAction('optimize')" id="btn-optimize">
                        <i class="bi bi-rocket-takeoff-fill me-1"></i> OPTIMIZE
                    </button>
                    <button class="btn btn-outline-danger btn-sm rounded-pill fw-bold" onclick="executeAction('clear-cache')" id="btn-clear-cache">
                        <i class="bi bi-trash-fill me-1"></i> FLUSH CACHE
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Health Monitor --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                <div class="card-header bg-dark text-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <span class="fw-black fs-5"><i class="bi bi-cpu-fill me-2 text-info"></i>CORE MONITOR</span>
                    <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-50 px-3 py-2 rounded-pill" id="last-checked">LIVE</span>
                </div>
                <div class="card-body p-0" id="checks-list" style="max-height: 500px; overflow-y: auto;">
                    <div class="text-center py-5">
                        <div class="spinner-grow text-primary" role="status"></div>
                        <p class="mt-3 text-muted fw-bold">Synchronizing with Core...</p>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 text-center">
                    <button class="btn btn-dark btn-sm rounded-pill px-4 fw-bold" onclick="executeAction('maintenance')" id="btn-maintenance">
                        <i class="bi bi-shield-shaded me-2"></i>RUN SYSTEM DIAGNOSTIC (SRE)
                    </button>
                </div>
            </div>
        </div>

        {{-- Circuit Status & Intelligence --}}
        <div class="col-lg-5">
            {{-- Circuit Breakers --}}
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden mb-4">
                <div class="card-header bg-dark text-white border-0 py-3 px-4">
                    <span class="fw-black fs-5"><i class="bi bi-lightning-charge-fill me-2 text-warning"></i>CIRCUITS</span>
                </div>
                <div class="card-body p-0" id="circuits-list">
                    {{-- Dynamically populated --}}
                </div>
            </div>

            {{-- AI Insights --}}
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden bg-primary text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                <div class="card-body p-4">
                    <h5 class="fw-black mb-3"><i class="bi bi-stars me-2"></i>AI DIAGNOSTICS</h5>
                    <div id="anomalies-list" class="mt-2">
                        {{-- AI Insights here --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Notification Toast --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="liveToast" class="toast align-items-center text-white bg-dark border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <span id="toast-message"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 24px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        transition: all 0.3s ease;
    }
    .glass-card:hover { transform: translateY(-5px); box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.12); }
    
    .border-start-success { border-left: 6px solid #198754 !important; }
    .border-start-danger { border-left: 6px solid #dc3545 !important; }
    
    .glass-badge {
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .check-row { 
        padding: 16px 24px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        border-bottom: 1px solid rgba(0,0,0,0.03);
        transition: all 0.2s;
    }
    .check-row:last-child { border-bottom: none; }
    .check-row:hover { background: rgba(13, 110, 253, 0.03); }

    .pulse-dot { 
        width: 10px; 
        height: 10px; 
        border-radius: 50%; 
        display: inline-block; 
        animation: pulse-anim 1.5s infinite; 
    }
    @keyframes pulse-anim { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(1.3)} }

    .insight-card {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 12px 16px;
        margin-bottom: 10px;
    }

    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 10px; }
</style>
@endpush

@push('scripts')
<script>
const STATUS_URL = "{{ route('admin.guard.status') }}";

function showToast(msg, type = 'success') {
    const toastEl = document.getElementById('liveToast');
    const toastMsg = document.getElementById('toast-message');
    toastMsg.innerHTML = msg;
    toastEl.classList.remove('bg-dark', 'bg-danger', 'bg-success');
    toastEl.classList.add('bg-' + type);
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

function executeAction(action) {
    const btn = document.getElementById('btn-' + action);
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> PROCESSING...';

    const urls = {
        'optimize': "{{ route('admin.guard.optimize') }}",
        'clear-cache': "{{ route('admin.guard.clear-cache') }}",
        'maintenance': "{{ route('admin.guard.maintenance') }}"
    };

    fetch(urls[action], {
        method: 'POST',
        headers: { 
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if(data.status === 'success') {
            showToast('✅ ' + data.message, 'success');
        } else {
            showToast('❌ ' + data.message, 'danger');
        }
        loadStatus();
    })
    .catch(e => showToast('🚨 System failure executing action', 'danger'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

function loadStatus() {
    fetch(STATUS_URL)
        .then(r => r.json())
        .then(data => {
            renderSummary(data.summary, data.status);
            renderChecks(data.checks);
            renderCircuits(data.circuits);
            renderAnomalies(data.anomalies);
            document.getElementById('last-checked').textContent = 'UPDATED ' + new Date().toLocaleTimeString();
        })
        .catch(e => {
            const badge = document.getElementById('guard-status-badge');
            badge.className = 'glass-badge bg-danger text-white fw-bold px-3 py-2 rounded-pill d-flex align-items-center';
            badge.innerHTML = '🚨 CONNECTION LOST';
        });
}

function renderSummary(s, status) {
    document.getElementById('s-total').textContent = s.total + ' CHECKS';
    document.getElementById('s-passed').textContent = s.passed;
    document.getElementById('s-failed').textContent = s.failed;
    document.getElementById('s-percent').textContent = s.percent + '%';
    
    const bar = document.getElementById('integrity-bar');
    bar.style.width = s.percent + '%';
    if(s.percent < 80) bar.className = 'progress-bar bg-warning';
    if(s.percent < 50) bar.className = 'progress-bar bg-danger';

    const badge = document.getElementById('guard-status-badge');
    if (status === 'OPERATIONAL') {
        badge.className = 'glass-badge bg-success text-white fw-bold px-3 py-2 rounded-pill d-flex align-items-center shadow-lg';
        badge.innerHTML = '<span class="pulse-dot bg-white me-2"></span> ALL SYSTEMS ONLINE';
    } else {
        badge.className = 'glass-badge bg-danger text-white fw-bold px-3 py-2 rounded-pill d-flex align-items-center shadow-lg';
        badge.innerHTML = '🚨 ' + s.failed + ' CRITICAL ISSUES';
    }
}

function renderChecks(checks) {
    const el = document.getElementById('checks-list');
    el.innerHTML = '';
    for (const [key, status] of Object.entries(checks)) {
        const ok = status === 'OK';
        el.innerHTML += `
            <div class="check-row">
                <div class="d-flex align-items-center">
                    <div class="bg-light p-2 rounded-3 me-3 text-dark">
                        <i class="bi ${ok ? 'bi-check-all text-success' : 'bi-exclamation-triangle-fill text-danger'}"></i>
                    </div>
                    <span class="fw-bold small text-uppercase" style="letter-spacing: 0.5px;">${key.replace(/_/g, ' ')}</span>
                </div>
                <span class="badge ${ok ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'} rounded-pill px-3 py-2 fw-bold">
                    ${ok ? 'STABLE' : 'ERROR: ' + status.substring(0, 30)}
                </span>
            </div>`;
    }
}

function renderCircuits(circuits) {
    const el = document.getElementById('circuits-list');
    el.innerHTML = '';
    for (const [key, data] of Object.entries(circuits)) {
        const stateColor = { CLOSED: 'success', OPEN: 'danger', HALF: 'warning' }[data.state] || 'secondary';
        const dotColor = { CLOSED: '#22c55e', OPEN: '#ef4444', HALF: '#f59e0b' }[data.state] || '#94a3b8';
        el.innerHTML += `
            <div class="check-row px-4">
                <div class="d-flex align-items-center">
                    <span class="pulse-dot me-2" style="background:${dotColor}"></span>
                    <span class="fw-bold x-small text-uppercase">${key.replace(/_/g, ' ')}</span>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="text-muted x-small">Fails: ${data.total_failures}</span>
                    <span class="badge bg-${stateColor} rounded-pill px-2" style="font-size: 0.6rem; font-weight: 800;">${data.state}</span>
                </div>
            </div>`;
    }
}

function renderAnomalies(anomalies) {
    const el = document.getElementById('anomalies-list');
    if (!anomalies || anomalies.length === 0) {
        el.innerHTML = `
            <div class="insight-card border-0">
                <div class="small fw-bold">AI STATUS: OPTIMAL</div>
                <div class="x-small opacity-75">Sistem memantau anomali secara real-time. Tidak ada ancaman atau kegagalan berulang terdeteksi saat ini.</div>
            </div>`;
        return;
    }
    el.innerHTML = anomalies.map(a => `
        <div class="insight-card bg-white text-dark border-0">
            <div class="d-flex justify-content-between">
                <span class="fw-bold x-small text-uppercase">${a.issue.replace(/_/g, ' ')}</span>
                <span class="badge bg-danger rounded-pill x-small">ANOMALY</span>
            </div>
            <div class="x-small mt-1">
                Kelemahan terdeteksi pada <strong>${a.issue}</strong> dengan total ${a.total} kegagalan. AI menyarankan tindakan perbaikan segera.
            </div>
        </div>`).join('');
}

// Initial load + auto-refresh
loadStatus();
setInterval(loadStatus, 30000);
</script>
@endpush
