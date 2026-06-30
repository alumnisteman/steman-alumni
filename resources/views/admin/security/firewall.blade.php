@extends('layouts.admin')

@section('title', 'Firewall & Keamanan')

@section('admin-content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-black text-dark mb-1"><i class="bi bi-shield-fill-check text-danger me-2"></i>FIREWALL & KEAMANAN</h2>
            <p class="text-muted mb-0">Monitor IP yang diblokir & status geocoding alumni. Auto-refresh setiap 30 detik.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-outline-secondary btn-sm rounded-pill" onclick="refreshData()">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </button>
            <button class="btn btn-warning btn-sm rounded-pill" onclick="confirmUnblockAll()" {{ count($blockedIps) === 0 ? 'disabled' : '' }}>
                <i class="bi bi-unlock-fill me-1"></i>Buka Semua Blokir
            </button>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4" id="stat-cards">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100">
                <div class="fs-1 fw-black text-danger" id="stat-total">{{ $stats['total_blocked'] }}</div>
                <div class="text-muted small fw-semibold">Total IP Diblokir</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100" style="border-left: 4px solid #dc3545 !important;">
                <div class="fs-1 fw-black" style="color:#dc3545" id="stat-perm">{{ $stats['perm'] }}</div>
                <div class="text-muted small fw-semibold">Permanen</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100" style="border-left: 4px solid #fd7e14 !important;">
                <div class="fs-1 fw-black text-warning" id="stat-hard">{{ $stats['hard'] }}</div>
                <div class="text-muted small fw-semibold">Hard Ban (24 jam)</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100" style="border-left: 4px solid #ffc107 !important;">
                <div class="fs-1 fw-black text-warning" id="stat-soft">{{ $stats['soft'] }}</div>
                <div class="text-muted small fw-semibold">Soft Ban (15 mnt)</div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Tabel IP Diblokir --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0"><i class="bi bi-ban text-danger me-2"></i>IP Diblokir Aktif</h5>
                        <p class="text-muted small mb-0">Klik tombol untuk membuka blokir satu IP</p>
                    </div>
                    <span class="badge bg-danger rounded-pill" id="badge-count">{{ count($blockedIps) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="ip-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3 small fw-bold text-uppercase text-muted">Alamat IP</th>
                                    <th class="py-3 small fw-bold text-uppercase text-muted">Level Blokir</th>
                                    <th class="py-3 small fw-bold text-uppercase text-muted">Total Gagal</th>
                                    <th class="py-3 small fw-bold text-uppercase text-muted">Kedaluwarsa</th>
                                    <th class="py-3 small fw-bold text-uppercase text-muted">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="ip-tbody">
                                @forelse($blockedIps as $entry)
                                <tr id="row-{{ str_replace('.', '-', $entry['ip']) }}">
                                    <td class="px-4 py-3">
                                        <code class="fw-bold text-dark fs-6">{{ $entry['ip'] }}</code>
                                    </td>
                                    <td class="py-3">
                                        @if($entry['level'] === 'PERMANENT')
                                            <span class="badge bg-danger rounded-pill"><i class="bi bi-lock-fill me-1"></i>PERMANEN</span>
                                        @elseif($entry['level'] === 'HARD (24 jam)')
                                            <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-exclamation-triangle-fill me-1"></i>HARD (24 jam)</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill"><i class="bi bi-clock me-1"></i>SOFT (15 mnt)</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <span class="fw-bold text-danger">{{ number_format($entry['total']) }}</span>
                                        <span class="text-muted small"> percobaan</span>
                                    </td>
                                    <td class="py-3 text-muted small">
                                        @if($entry['expiry'])
                                            {{ \Carbon\Carbon::createFromTimestamp($entry['expiry'])->diffForHumans() }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <button class="btn btn-outline-success btn-sm rounded-pill"
                                            onclick="unblockIp('{{ $entry['ip'] }}')">
                                            <i class="bi bi-unlock me-1"></i>Buka
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr id="row-empty">
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-shield-check fs-1 text-success"></i>
                                        <p class="mt-2 mb-0 fw-semibold">Tidak ada IP yang diblokir saat ini.</p>
                                        <p class="small">Sistem aman — tidak ada aktivitas brute force yang terdeteksi.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Kanan --}}
        <div class="col-lg-4 d-flex flex-column gap-4">

            {{-- Geocoding Status --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Geocoding Alumni</h5>
                    <p class="text-muted small mb-0">Koordinat GPS alumni untuk peta jaringan</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Alumni tergeocoding</span>
                            <span class="fw-bold" id="geo-pct">{{ $stats['geocode_pct'] }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height:10px">
                            <div class="progress-bar bg-success rounded-pill" id="geo-bar"
                                style="width:{{ $stats['geocode_pct'] }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mt-1">
                            <span id="geo-count">{{ $stats['alumni_geocoded'] }} / {{ $stats['alumni_total'] }} alumni</span>
                            <span id="geo-remaining" class="{{ $stats['alumni_tanpa_koordinat'] > 0 ? 'text-warning' : 'text-success' }}">
                                {{ $stats['alumni_tanpa_koordinat'] > 0 ? $stats['alumni_tanpa_koordinat'].' belum' : '✓ Semua selesai' }}
                            </span>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Geocoding otomatis berjalan setiap jam via scheduler Laravel menggunakan Nominatim (OpenStreetMap).
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm rounded-pill" id="geocode-limit" style="width:auto">
                            <option value="10">10 alumni</option>
                            <option value="20" selected>20 alumni</option>
                            <option value="30">30 alumni</option>
                            <option value="50">50 alumni</option>
                        </select>
                        <button class="btn btn-primary btn-sm rounded-pill flex-grow-1" onclick="runGeocode()" id="btn-geocode">
                            <i class="bi bi-play-circle me-1"></i>Jalankan Sekarang
                        </button>
                    </div>
                    <div id="geocode-result" class="mt-2 small d-none"></div>
                </div>
            </div>

            {{-- Panduan Level Blokir --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle-fill text-info me-2"></i>Sistem Proteksi</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex align-items-start mb-3">
                            <span class="badge bg-secondary rounded-pill me-2 mt-1 flex-shrink-0">SOFT</span>
                            <span class="text-muted">10 gagal dalam 5 menit → blokir <strong>15 menit</strong></span>
                        </li>
                        <li class="d-flex align-items-start mb-3">
                            <span class="badge bg-warning text-dark rounded-pill me-2 mt-1 flex-shrink-0">HARD</span>
                            <span class="text-muted">30 gagal dalam 1 jam → blokir <strong>24 jam</strong></span>
                        </li>
                        <li class="d-flex align-items-start">
                            <span class="badge bg-danger rounded-pill me-2 mt-1 flex-shrink-0">PERM</span>
                            <span class="text-muted">100 gagal total → blokir <strong>30 hari</strong></span>
                        </li>
                    </ul>
                    <hr class="my-3">
                    <p class="text-muted small mb-0">
                        <i class="bi bi-clock-history me-1 text-primary"></i>
                        Rate limit login: <strong>8 percobaan/menit</strong> per IP.<br>
                        Semua blokir disimpan di Redis dan tercatat di log sistem.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Toast Notifikasi --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="toast-notif" class="toast align-items-center border-0 shadow-lg" role="alert">
        <div class="d-flex">
            <div class="toast-body fw-semibold" id="toast-msg">Berhasil.</div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let refreshTimer;

// ─── Helpers ───────────────────────────────────────────────────────────────

function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast-notif');
    const body  = document.getElementById('toast-msg');
    toast.className = `toast align-items-center border-0 shadow-lg text-bg-${type}`;
    body.textContent = msg;
    bootstrap.Toast.getOrCreateInstance(toast, { delay: 4000 }).show();
}

function rowId(ip) {
    return 'row-' + ip.replace(/\./g, '-');
}

// ─── Unblock Satu IP ───────────────────────────────────────────────────────

function unblockIp(ip) {
    if (!confirm(`Buka blokir IP ${ip}?\n\nIP ini akan diizinkan mencoba login kembali.`)) return;

    fetch('{{ route("admin.security.unblock") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ ip }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById(rowId(ip));
            if (row) row.remove();
            showToast(data.message, 'success');
            refreshData();
        } else {
            showToast('Gagal: ' + data.message, 'danger');
        }
    })
    .catch(() => showToast('Koneksi bermasalah.', 'danger'));
}

// ─── Unblock Semua ─────────────────────────────────────────────────────────

function confirmUnblockAll() {
    const total = parseInt(document.getElementById('stat-total').textContent);
    if (total === 0) return;
    if (!confirm(`Yakin ingin membuka SEMUA ${total} blokir IP?\n\nTindakan ini tidak bisa dibatalkan.`)) return;

    fetch('{{ route("admin.security.unblock-all") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success ? 'success' : 'danger');
        if (data.success) refreshData();
    })
    .catch(() => showToast('Koneksi bermasalah.', 'danger'));
}

// ─── Geocoding Manual ──────────────────────────────────────────────────────

function runGeocode() {
    const btn   = document.getElementById('btn-geocode');
    const result = document.getElementById('geocode-result');
    const limit = document.getElementById('geocode-limit').value;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';
    result.className = 'mt-2 small d-none';

    fetch('{{ route("admin.security.run-geocode") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ limit }),
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-play-circle me-1"></i>Jalankan Sekarang';
        result.className = 'mt-2 small alert ' + (data.success ? 'alert-success' : 'alert-danger') + ' py-2 px-3 border-0 rounded-3';
        result.textContent = data.message;
        if (data.success) refreshData();
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-play-circle me-1"></i>Jalankan Sekarang';
        showToast('Geocoding gagal — cek koneksi.', 'danger');
    });
}

// ─── Auto Refresh ──────────────────────────────────────────────────────────

function refreshData() {
    fetch('{{ route("admin.security.firewall-api") }}')
        .then(r => r.json())
        .then(data => {
            updateStats(data.stats);
            updateTable(data.blocked);
        })
        .catch(() => {});
}

function updateStats(s) {
    document.getElementById('stat-total').textContent = s.total_blocked;
    document.getElementById('stat-perm').textContent  = s.perm;
    document.getElementById('stat-hard').textContent  = s.hard;
    document.getElementById('stat-soft').textContent  = s.soft;
    document.getElementById('badge-count').textContent = s.total_blocked;

    document.getElementById('geo-pct').textContent    = s.geocode_pct + '%';
    document.getElementById('geo-bar').style.width    = s.geocode_pct + '%';
    document.getElementById('geo-count').textContent  = s.alumni_geocoded + ' / ' + s.alumni_total + ' alumni';

    const rem = document.getElementById('geo-remaining');
    rem.textContent  = s.alumni_tanpa_koordinat > 0 ? s.alumni_tanpa_koordinat + ' belum' : '✓ Semua selesai';
    rem.className    = s.alumni_tanpa_koordinat > 0 ? 'text-warning' : 'text-success';
}

function levelBadge(level) {
    if (level === 'PERMANENT')     return '<span class="badge bg-danger rounded-pill"><i class="bi bi-lock-fill me-1"></i>PERMANEN</span>';
    if (level === 'HARD (24 jam)') return '<span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-exclamation-triangle-fill me-1"></i>HARD (24 jam)</span>';
    return '<span class="badge bg-secondary rounded-pill"><i class="bi bi-clock me-1"></i>SOFT (15 mnt)</span>';
}

function updateTable(blocked) {
    const tbody = document.getElementById('ip-tbody');
    if (blocked.length === 0) {
        tbody.innerHTML = `<tr id="row-empty"><td colspan="5" class="text-center py-5 text-muted">
            <i class="bi bi-shield-check fs-1 text-success"></i>
            <p class="mt-2 mb-0 fw-semibold">Tidak ada IP yang diblokir saat ini.</p>
            <p class="small">Sistem aman — tidak ada aktivitas brute force yang terdeteksi.</p>
        </td></tr>`;
        return;
    }
    tbody.innerHTML = blocked.map(e => `
        <tr id="${rowId(e.ip)}">
            <td class="px-4 py-3"><code class="fw-bold text-dark fs-6">${e.ip}</code></td>
            <td class="py-3">${levelBadge(e.level)}</td>
            <td class="py-3"><span class="fw-bold text-danger">${e.total.toLocaleString()}</span><span class="text-muted small"> percobaan</span></td>
            <td class="py-3 text-muted small">${e.expiry ? 'Tersimpan di cache' : '—'}</td>
            <td class="py-3"><button class="btn btn-outline-success btn-sm rounded-pill" onclick="unblockIp('${e.ip}')"><i class="bi bi-unlock me-1"></i>Buka</button></td>
        </tr>
    `).join('');
}

// ─── Init ──────────────────────────────────────────────────────────────────
refreshTimer = setInterval(refreshData, 30000); // refresh setiap 30 detik
</script>
@endpush
@endsection
