@extends('layouts.admin')

@section('admin-content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="section-title mb-0"><i class="bi bi-play-circle-fill me-2 text-warning"></i>Simulator Tema Event</h2>
        <p class="text-muted small mb-0">Pratinjau tampilan banner berdasarkan tanggal yang dipilih</p>
    </div>
    <a href="{{ route('admin.event-themes.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
    </a>
</div>

{{-- Date Picker Card --}}
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-calendar3 me-2 text-primary"></i>Pilih Tanggal Simulasi</h6>
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Tanggal</label>
                <input type="date" id="sim-date" class="form-control form-control-lg rounded-3"
                       value="{{ now()->format('Y-m-d') }}"
                       min="2020-01-01" max="2035-12-31">
            </div>
            <div class="col-md-auto">
                <button class="btn btn-primary rounded-pill px-4 fw-bold" onclick="runSimulator()">
                    <i class="bi bi-play-fill me-2"></i>Simulasikan
                </button>
            </div>
            <div class="col-md-auto">
                <button class="btn btn-outline-secondary rounded-pill px-3" onclick="setToday()">
                    <i class="bi bi-calendar-check me-2"></i>Hari Ini
                </button>
            </div>
        </div>

        {{-- Quick Shortcut Buttons --}}
        <div class="mt-3">
            <small class="text-muted fw-semibold me-2">Cepat lihat:</small>
            @php
                $shortcuts = [
                    ['label'=>'Isra Mi\'raj','date'=>'2026-01-16','icon'=>'🌙'],
                    ['label'=>'Ramadan','date'=>'2026-02-20','icon'=>'🌙'],
                    ['label'=>'Nuzulul Quran','date'=>'2026-03-06','icon'=>'📖'],
                    ['label'=>'Idul Fitri','date'=>'2026-03-20','icon'=>'🌙'],
                    ['label'=>'Hari Buruh','date'=>'2026-05-01','icon'=>'✊'],
                    ['label'=>'Idul Adha','date'=>'2026-05-27','icon'=>'🐑'],
                    ['label'=>'Tahun Baru Islam','date'=>'2026-06-17','icon'=>'🌙'],
                    ['label'=>'HUT RI','date'=>'2026-08-17','icon'=>'🇮🇩'],
                    ['label'=>'Maulid Nabi','date'=>'2026-08-26','icon'=>'🌹'],
                    ['label'=>'HUT STEMAN','date'=>'2026-11-15','icon'=>'🎓'],
                    ['label'=>'Natal','date'=>'2026-12-25','icon'=>'🎄'],
                    ['label'=>'Tahun Baru','date'=>'2027-01-01','icon'=>'🎆'],
                ];
            @endphp
            @foreach($shortcuts as $s)
                <button class="btn btn-sm btn-outline-secondary rounded-pill me-1 mb-1"
                        onclick="setDate('{{ $s['date'] }}')">
                    {{ $s['icon'] }} {{ $s['label'] }}
                </button>
            @endforeach
        </div>
    </div>
</div>

{{-- Preview Result --}}
<div id="preview-area" style="display:none">

    {{-- Banner Preview --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 p-4 pb-2">
            <h6 class="fw-bold mb-0"><i class="bi bi-eye-fill me-2 text-success"></i>Pratinjau Banner</h6>
        </div>
        <div class="card-body p-4 pt-2">
            <div id="banner-preview-wrap" class="rounded-3 overflow-hidden" style="border:2px dashed #e2e8f0;min-height:80px;"></div>
        </div>
    </div>

    {{-- Theme Info Card --}}
    <div id="theme-info-card" class="card border-0 rounded-4 shadow-sm mb-4"></div>

    {{-- All Themes Timeline --}}
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-transparent border-0 p-4 pb-2">
            <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2 text-info"></i>Semua Tema pada Tanggal Ini</h6>
        </div>
        <div class="card-body p-0">
            <div id="all-themes-list" class="list-group list-group-flush rounded-4"></div>
        </div>
    </div>
</div>

{{-- Empty State --}}
<div id="empty-state" class="text-center py-5">
    <div style="font-size:4rem">🗓️</div>
    <p class="text-muted mt-2">Pilih tanggal dan klik <strong>Simulasikan</strong> untuk melihat pratinjau tema.</p>
</div>

@endsection

@push('scripts')
<script>
    const PREVIEW_URL = '{{ route('admin.event-themes.simulator.preview') }}';
    const CSRF        = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function setDate(d) {
        document.getElementById('sim-date').value = d;
        runSimulator();
    }
    function setToday() {
        const today = new Date().toISOString().split('T')[0];
        setDate(today);
    }

    function runSimulator() {
        const date = document.getElementById('sim-date').value;
        if (!date) return;

        // Loading state
        document.getElementById('empty-state').style.display = 'none';
        document.getElementById('preview-area').style.display = 'none';
        document.getElementById('banner-preview-wrap').innerHTML = '<div class="text-center p-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Memuat…</div>';
        document.getElementById('preview-area').style.display = '';

        fetch(`${PREVIEW_URL}?date=${date}`, {
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => renderResult(data))
        .catch(() => {
            document.getElementById('banner-preview-wrap').innerHTML =
                '<div class="alert alert-danger m-3">Gagal memuat pratinjau. Silakan coba lagi.</div>';
        });
    }

    function renderResult(data) {
        const months = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
        const t = data.active_theme;
        const allThemes = data.all_matching;

        // Banner Preview
        let bannerHtml = '';
        if (t) {
            bannerHtml = `
                <div class="d-flex align-items-center gap-3 p-3 fw-semibold"
                     style="background:linear-gradient(90deg,${t.primary_color},${t.secondary_color});color:${t.accent_color};min-height:72px;">
                    <span style="font-size:2rem">${t.emoji || '🎨'}</span>
                    <div>
                        <div class="fw-bold">${escHtml(t.banner_text || t.name)}</div>
                        ${t.banner_subtext ? `<div class="small opacity-80">${escHtml(t.banner_subtext)}</div>` : ''}
                    </div>
                </div>`;
        } else {
            bannerHtml = `<div class="p-4 text-center text-muted"><i class="bi bi-slash-circle me-2"></i>Tidak ada banner — tampilan normal STEMAN Alumni</div>`;
        }
        document.getElementById('banner-preview-wrap').innerHTML = bannerHtml;

        // Theme Info Card
        let infoHtml = '';
        if (t) {
            infoHtml = `
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="fs-2">${t.emoji || '🎨'}</div>
                    <div>
                        <div class="fw-bold fs-5" style="color:${t.primary_color}">${escHtml(t.name)}</div>
                        <div class="text-muted small">${escHtml(t.description || '')}</div>
                    </div>
                    <span class="badge ms-auto rounded-pill px-3 py-2 text-white" style="background:${t.primary_color}">P=${t.priority}</span>
                </div>
                <div class="row g-2">
                    <div class="col-auto"><span class="badge bg-light text-dark border rounded-pill px-3"><i class="bi bi-calendar3 me-1"></i>${months[t.start_month]} ${t.start_day} – ${months[t.end_month]} ${t.end_day}</span></div>
                    ${t.is_islamic ? '<div class="col-auto"><span class="badge rounded-pill px-3 text-white" style="background:#1a3a5c"><i class="bi bi-moon-fill me-1"></i>Kalender Hijriah – Otomatis</span></div>' : ''}
                    <div class="col-auto"><span class="badge bg-light text-dark border rounded-pill px-3">CSS: ${escHtml(t.css_class)}</span></div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <div class="rounded-3 p-3 text-center flex-fill border"><div class="small text-muted">Warna Utama</div><div style="width:36px;height:36px;background:${t.primary_color};border-radius:8px;margin:4px auto 0"></div><code class="small">${t.primary_color}</code></div>
                    <div class="rounded-3 p-3 text-center flex-fill border"><div class="small text-muted">Warna Sekunder</div><div style="width:36px;height:36px;background:${t.secondary_color};border-radius:8px;margin:4px auto 0"></div><code class="small">${t.secondary_color}</code></div>
                    <div class="rounded-3 p-3 text-center flex-fill border"><div class="small text-muted">Warna Aksen</div><div style="width:36px;height:36px;background:${t.accent_color};border-radius:8px;margin:4px auto 0"></div><code class="small">${t.accent_color}</code></div>
                </div>
            </div>`;
        } else {
            infoHtml = `<div class="card-body p-4 text-center text-muted"><i class="bi bi-info-circle me-2"></i>Tidak ada tema aktif pada <strong>${escHtml(data.date_label)}</strong> — website tampil dengan desain normal.</div>`;
        }
        document.getElementById('theme-info-card').innerHTML = infoHtml;

        // All Matching Themes List
        let listHtml = '';
        if (allThemes && allThemes.length > 0) {
            allThemes.forEach((th, i) => {
                const isWinner = t && th.id === t.id;
                listHtml += `
                <div class="list-group-item px-4 py-3 d-flex align-items-center gap-3 ${isWinner ? 'bg-warning-subtle' : ''}">
                    <span class="fs-5">${th.emoji || '🎨'}</span>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">${escHtml(th.name)} ${isWinner ? '<span class="badge bg-warning text-dark ms-1">✓ Menang</span>' : ''}</div>
                        <small class="text-muted">${months[th.start_month]} ${th.start_day} – ${months[th.end_month]} ${th.end_day}</small>
                    </div>
                    <span class="badge rounded-pill border" style="color:${th.primary_color};border-color:${th.primary_color}!important">P=${th.priority}</span>
                </div>`;
            });
        } else {
            listHtml = '<div class="list-group-item px-4 py-3 text-muted">Tidak ada tema yang cocok dengan tanggal ini.</div>';
        }
        document.getElementById('all-themes-list').innerHTML = listHtml;
        document.getElementById('preview-area').style.display = '';
        document.getElementById('empty-state').style.display = 'none';
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }

    // Auto-run on page load with today's date
    document.addEventListener('DOMContentLoaded', setToday);
</script>
@endpush
