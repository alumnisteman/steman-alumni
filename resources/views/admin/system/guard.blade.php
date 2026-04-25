@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black mb-1">🛡 SYSTEM GUARD</h2>
            <p class="text-muted mb-0">Realtime monitoring & auto-healing dashboard</p>
        </div>
        <div class="d-flex gap-2">
            <span id="guard-status-badge" class="badge bg-secondary fs-6 px-3 py-2">
                <span class="spinner-border spinner-border-sm me-1"></span> Checking...
            </span>
            <button class="btn btn-warning btn-sm fw-bold" onclick="runMaintenance()" id="btn-maintenance">
                <i class="bi bi-tools"></i> Run Maintenance
            </button>
            <button class="btn btn-outline-primary btn-sm" onclick="loadStatus()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </div>

    {{-- Overall Summary --}}
    <div class="row g-3 mb-4" id="summary-cards">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                <div class="text-muted small mb-1">TOTAL CHECKS</div>
                <div class="fw-black fs-2" id="s-total">—</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 bg-success bg-opacity-10">
                <div class="text-success small mb-1">PASSED</div>
                <div class="fw-black fs-2 text-success" id="s-passed">—</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 bg-danger bg-opacity-10">
                <div class="text-danger small mb-1">FAILED</div>
                <div class="fw-black fs-2 text-danger" id="s-failed">—</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 bg-primary bg-opacity-10">
                <div class="text-primary small mb-1">INTEGRITY</div>
                <div class="fw-black fs-2 text-primary" id="s-percent">—</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Health Checks --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-dark text-white rounded-top-4 py-3 px-4 d-flex justify-content-between">
                    <span class="fw-bold"><i class="bi bi-activity me-2 text-primary"></i>HEALTH CHECKS</span>
                    <span class="small text-white-50" id="last-checked">—</span>
                </div>
                <div class="card-body p-0" id="checks-list">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border text-primary mb-3"></div>
                        <div>Loading checks...</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Circuit Breakers --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-dark text-white rounded-top-4 py-3 px-4">
                    <span class="fw-bold"><i class="bi bi-lightning-charge-fill me-2 text-warning"></i>CIRCUIT BREAKERS</span>
                </div>
                <div class="card-body p-0" id="circuits-list">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border text-warning mb-3"></div>
                        <div>Loading circuits...</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Anomaly Detection --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-dark text-white rounded-top-4 py-3 px-4">
                    <span class="fw-bold"><i class="bi bi-cpu me-2 text-info"></i>AI ANOMALY DETECTION</span>
                </div>
                <div class="card-body" id="anomalies-list">
                    <div class="text-center py-3 text-muted">Analyzing...</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .check-row { border-bottom: 1px solid rgba(0,0,0,0.05); padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; transition: background 0.2s; }
    .check-row:last-child { border-bottom: none; }
    .check-row:hover { background: rgba(0,0,0,0.02); }
    .circuit-badge { font-size: 0.65rem; font-weight: 900; letter-spacing: 1px; }
    .pulse-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; animation: pulse-anim 1.5s infinite; }
    @keyframes pulse-anim { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(1.3)} }
    .anomaly-card { border-left: 4px solid #f59e0b; background: rgba(245,158,11,0.05); border-radius: 8px; padding: 12px 16px; margin-bottom: 8px; }
    .anomaly-card.critical { border-color: #ef4444; background: rgba(239,68,68,0.05); }
</style>
@endpush

@push('scripts')
<script>
const STATUS_URL = "{{ route('admin.guard.status') }}";

function loadStatus() {
    fetch(STATUS_URL)
        .then(r => r.json())
        .then(data => {
            renderSummary(data.summary, data.status);
            renderChecks(data.checks);
            renderCircuits(data.circuits);
            renderAnomalies(data.anomalies);
            document.getElementById('last-checked').textContent = 'Updated: ' + new Date().toLocaleTimeString('id-ID');
        })
        .catch(e => {
            document.getElementById('guard-status-badge').className = 'badge bg-danger fs-6 px-3 py-2';
            document.getElementById('guard-status-badge').innerHTML = '🚨 API Error';
        });
}

function runMaintenance() {
    const btn = document.getElementById('btn-maintenance');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Running...';

    fetch("{{ route('admin.guard.maintenance') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        alert('✅ Maintenance Success: ' + data.message);
        loadStatus();
    })
    .catch(e => alert('🚨 Maintenance Failed'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-tools"></i> Run Maintenance';
    });
}

function renderSummary(s, status) {
    document.getElementById('s-total').textContent = s.total;
    document.getElementById('s-passed').textContent = s.passed;
    document.getElementById('s-failed').textContent = s.failed;
    document.getElementById('s-percent').textContent = s.percent + '%';

    const badge = document.getElementById('guard-status-badge');
    if (status === 'OPERATIONAL') {
        badge.className = 'badge bg-success fs-6 px-3 py-2';
        badge.innerHTML = '✅ ALL SYSTEMS OPERATIONAL';
    } else {
        badge.className = 'badge bg-danger fs-6 px-3 py-2';
        badge.innerHTML = '🚨 SYSTEM DEGRADED — ' + s.failed + ' issue(s)';
    }
}

function renderChecks(checks) {
    const el = document.getElementById('checks-list');
    el.innerHTML = '';
    for (const [key, status] of Object.entries(checks)) {
        const ok = status === 'OK';
        el.innerHTML += `
            <div class="check-row">
                <span class="fw-bold small text-uppercase">${key.replace(/_/g, ' ')}</span>
                <span class="badge ${ok ? 'bg-success' : 'bg-danger'} rounded-pill px-3">
                    ${ok ? '✓ OK' : '✗ ' + status.substring(0, 50)}
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
            <div class="check-row">
                <div>
                    <span class="pulse-dot me-2" style="background:${dotColor}"></span>
                    <span class="fw-bold small text-uppercase">${key.replace(/_/g, ' ')}</span>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="text-muted small">Failures: ${data.total_failures}</span>
                    <span class="badge bg-${stateColor} circuit-badge rounded-pill px-2">${data.state}</span>
                </div>
            </div>`;
    }
}

function renderAnomalies(anomalies) {
    const el = document.getElementById('anomalies-list');
    if (!anomalies || anomalies.length === 0) {
        el.innerHTML = '<div class="text-center text-success py-3"><i class="bi bi-check-circle-fill fs-4 me-2"></i>No anomalies detected. System behavior is normal.</div>';
        return;
    }
    el.innerHTML = anomalies.map(a => `
        <div class="anomaly-card ${a.total >= 5 ? 'critical' : ''}">
            <div class="d-flex justify-content-between">
                <span class="fw-bold text-uppercase small">${a.issue.replace(/_/g, ' ')}</span>
                <span class="badge bg-warning text-dark">⚠ ${a.total} total failures</span>
            </div>
            <div class="text-muted small mt-1">
                AI Pattern: Recurring ${a.issue} detected ${a.total} times. Circuit: <strong>${a.state}</strong>.
                ${a.total >= 5 ? '🚨 High failure rate — manual inspection recommended.' : 'Monitor closely.'}
            </div>
        </div>`).join('');
}

// Initial load + auto-refresh every 30 seconds
loadStatus();
setInterval(loadStatus, 30000);
</script>
@endpush
