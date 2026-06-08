@extends('layouts.admin')

@section('admin-content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="section-title mb-0"><i class="bi bi-calendar-event-fill me-2 text-warning"></i>Tema Event Otomatis</h2>
        <p class="text-muted small mb-0">Banner & tema tampilan website berdasarkan kalender — berubah otomatis setiap hari</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.event-themes.simulator') }}" class="btn btn-outline-warning rounded-pill px-4 fw-semibold">
            <i class="bi bi-play-circle-fill me-2"></i>Simulator
        </a>
        <a href="{{ route('admin.event-themes.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Tambah Tema
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Tema Aktif Hari Ini --}}
<div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
    <div class="card-body p-4 d-flex align-items-center gap-4"
         style="background: {{ $active ? 'linear-gradient(135deg,'.$active->primary_color.'22,'.$active->accent_color.'11)' : '#f8fafc' }}; border-left: 5px solid {{ $active ? $active->primary_color : '#e2e8f0' }};">
        <div class="fs-1">{{ $active ? ($active->emoji ?? '🎨') : '😴' }}</div>
        <div>
            <div class="fw-bold fs-6 text-dark">Tema Aktif Hari Ini</div>
            @if($active)
                <div class="fw-black fs-5" style="color:{{ $active->primary_color }}">{{ $active->name }}</div>
                <div class="small text-muted">{{ $active->banner_text }}</div>
            @else
                <div class="text-muted fw-semibold">Tidak ada event aktif — tampilan STEMAN Normal</div>
                <div class="small text-muted">Tambahkan tema atau aktifkan tema yang ada di bawah</div>
            @endif
        </div>
    </div>
</div>

{{-- Tabel Semua Tema --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Tema</th>
                    <th>Periode</th>
                    <th>Preview Banner</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($themes as $theme)
                @php
                    $months = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
                    $isToday = ($active && $active->id === $theme->id);
                @endphp
                <tr class="{{ $isToday ? 'table-warning' : '' }}">
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm"
                                 style="width:44px;height:44px;background:linear-gradient(135deg,{{ $theme->primary_color }},{{ $theme->accent_color }});font-size:1.4rem;flex-shrink:0;">
                                {{ $theme->emoji ?? '🎨' }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $theme->name }}</div>
                                <small class="text-muted">{{ $theme->css_class }}</small>
                            </div>
                        </div>
                        @if($isToday)
                            <span class="badge bg-warning text-dark rounded-pill ms-1" style="font-size:0.65rem;">AKTIF HARI INI</span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-semibold text-dark">
                            {{ $months[$theme->start_month] }} {{ $theme->start_day }}
                        </span>
                        <span class="text-muted mx-1">—</span>
                        <span class="fw-semibold text-dark">
                            {{ $months[$theme->end_month] }} {{ $theme->end_day }}
                        </span>
                    </td>
                    <td style="max-width: 280px;">
                        <div class="rounded-3 px-3 py-2 small fw-bold d-flex align-items-center gap-2"
                             style="background:linear-gradient(90deg,{{ $theme->primary_color }},{{ $theme->secondary_color }});color:{{ $theme->accent_color }};font-size:0.75rem;">
                            @if($theme->banner_icon)
                                <i class="{{ $theme->banner_icon }}"></i>
                            @endif
                            <span class="text-truncate">{{ \Illuminate\Support\Str::limit($theme->banner_text ?? '', 40) }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge rounded-pill px-3"
                              style="background:{{ $theme->primary_color }}22;color:{{ $theme->primary_color }};font-size:0.78rem;font-weight:800;">
                            {{ $theme->priority }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('admin.event-themes.toggle', $theme) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm rounded-pill px-3 fw-bold border-0 {{ $theme->is_active ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}">
                                <i class="bi bi-{{ $theme->is_active ? 'check-circle-fill' : 'x-circle' }} me-1"></i>
                                {{ $theme->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-end pe-4">
                        {{-- Tombol Preview --}}
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary rounded-pill px-3 me-1"
                                onclick="previewTheme({{ json_encode([
                                    'name'           => $theme->name,
                                    'emoji'          => $theme->emoji ?? '🎨',
                                    'primary'        => $theme->primary_color,
                                    'secondary'      => $theme->secondary_color,
                                    'accent'         => $theme->accent_color,
                                    'banner_text'    => $theme->banner_text ?? $theme->name,
                                    'banner_subtext' => $theme->banner_subtext ?? '',
                                    'banner_icon'    => $theme->banner_icon ?? '',
                                    'css_class'      => $theme->css_class,
                                    'start'          => $months[$theme->start_month].' '.$theme->start_day,
                                    'end'            => $months[$theme->end_month].' '.$theme->end_day,
                                    'priority'       => $theme->priority,
                                ]) }})">
                            <i class="bi bi-eye me-1"></i>Preview
                        </button>
                        <a href="{{ route('admin.event-themes.edit', $theme) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <form id="del-theme-{{ $theme->id }}" action="{{ route('admin.event-themes.destroy', $theme) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                    onclick="window.Guardian.confirmDelete('del-theme-{{ $theme->id }}')">
                                <i class="bi bi-trash" style="pointer-events:none;"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x d-block fs-1 mb-3 opacity-25"></i>
                        Belum ada tema event. <a href="{{ route('admin.event-themes.create') }}">Tambah sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="alert alert-info border-0 rounded-4 shadow-sm mt-4 small" role="alert">
    <i class="bi bi-info-circle-fill me-2"></i>
    <strong>Cara Kerja:</strong> Sistem secara otomatis memilih tema dengan <strong>prioritas tertinggi</strong> yang periodenya mencakup tanggal hari ini.
    Ketika tidak ada event aktif, website menampilkan tampilan STEMAN normal. Timezone: <strong>Asia/Makassar (WIT)</strong>.
</div>

{{-- ============================================================
     MODAL PREVIEW TEMA
     ============================================================ --}}
<div class="modal fade" id="themePreviewModal" tabindex="-1" aria-labelledby="themePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            {{-- Modal Header --}}
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div>
                    <h5 class="modal-title fw-black mb-0" id="themePreviewLabel">
                        <i class="bi bi-eye me-2 text-primary"></i>Preview Tampilan Tema
                    </h5>
                    <p class="text-muted small mb-0 mt-1" id="previewThemeName">—</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body p-0">

                {{-- === SIMULASI PORTAL WEBSITE === --}}
                <div id="previewPortalWrapper" style="background:#f8fafc; font-family:'Inter',sans-serif;">

                    {{-- Event Banner Bar --}}
                    <div id="previewBannerBar" style="
                        display:flex; align-items:center; gap:12px;
                        padding:10px 20px; font-size:0.85rem; font-weight:700;
                        color:#fff; background:#1a3a5c; min-height:42px;
                        border-bottom:2px solid rgba(255,255,255,0.2);
                        box-shadow:0 2px 12px rgba(0,0,0,0.18);">
                        <span id="previewBannerIcon" style="font-size:1.3rem;"></span>
                        <span id="previewBannerText" style="flex:1;"></span>
                        <span style="font-size:0.72rem;opacity:0.8;background:rgba(0,0,0,0.2);padding:3px 10px;border-radius:999px;">
                            <i class="bi bi-calendar3"></i>
                            Berlaku: <span id="previewPeriode"></span>
                        </span>
                        <span style="background:rgba(0,0,0,0.2);border:none;color:rgba(255,255,255,0.8);
                              width:22px;height:22px;border-radius:50%;display:flex;align-items:center;
                              justify-content:center;cursor:default;font-size:0.9rem;">✕</span>
                    </div>

                    {{-- Simulasi Navbar --}}
                    <div id="previewNavbar" style="
                        background:#fff; border-bottom:3px solid #0d6efd;
                        padding:12px 24px; display:flex; align-items:center;
                        justify-content:space-between; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:#1a1a1a;display:flex;align-items:center;justify-content:center;">
                                <span style="font-size:0.7rem;color:#fff;font-weight:900;">S</span>
                            </div>
                            <span style="font-weight:900;font-size:0.9rem;color:#1e293b;letter-spacing:-0.5px;">STEMAN ALUMNI</span>
                        </div>
                        <div style="display:flex;gap:20px;">
                            <span style="font-size:0.8rem;color:#64748b;cursor:default;">Beranda</span>
                            <span style="font-size:0.8rem;color:#64748b;cursor:default;">Alumni</span>
                            <span style="font-size:0.8rem;color:#64748b;cursor:default;">Berita</span>
                            <span style="font-size:0.8rem;color:#64748b;cursor:default;">Bisnis</span>
                        </div>
                        <div id="previewNavbarBtn" style="
                            background:#0d6efd;color:#fff;font-size:0.75rem;
                            font-weight:700;padding:6px 16px;border-radius:999px;cursor:default;">
                            Masuk
                        </div>
                    </div>

                    {{-- Simulasi Hero Section --}}
                    <div id="previewHero" style="
                        background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%);
                        padding:40px 24px; text-align:center;">
                        <div style="font-size:2.5rem;margin-bottom:10px;" id="previewHeroEmoji"></div>
                        <h2 id="previewHeroTitle" style="font-size:1.5rem;font-weight:900;color:#1e293b;margin-bottom:8px;">
                            Selamat Datang di Portal Alumni STEMAN
                        </h2>
                        <p id="previewHeroSubtext" style="font-size:0.85rem;color:#64748b;margin:0;">
                            Jalin Silaturahmi, Bangun Kontribusi
                        </p>
                        <div style="margin-top:20px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                            <div id="previewHeroBtn1" style="
                                background:#0d6efd;color:#fff;
                                padding:10px 24px;border-radius:999px;font-size:0.82rem;font-weight:700;cursor:default;">
                                Bergabung Sekarang
                            </div>
                            <div style="
                                background:#fff;color:#1e293b;border:1.5px solid #e2e8f0;
                                padding:10px 24px;border-radius:999px;font-size:0.82rem;font-weight:700;cursor:default;">
                                Lihat Alumni
                            </div>
                        </div>
                    </div>

                    {{-- Label Preview --}}
                    <div style="text-align:center;padding:10px;background:#f1f5f9;">
                        <small style="color:#94a3b8;font-size:0.72rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Ini adalah simulasi tampilan — tampilan asli portal dapat sedikit berbeda
                        </small>
                    </div>
                </div>

                {{-- Info Tema --}}
                <div class="px-4 py-3 border-top" style="background:#f8fafc;">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <span class="text-muted small fw-semibold">Warna Tema:</span>
                                <div class="d-flex gap-2">
                                    <div id="previewColor1" style="width:24px;height:24px;border-radius:6px;border:1.5px solid #e2e8f0;" title="Primary"></div>
                                    <div id="previewColor2" style="width:24px;height:24px;border-radius:6px;border:1.5px solid #e2e8f0;" title="Secondary"></div>
                                    <div id="previewColor3" style="width:24px;height:24px;border-radius:6px;border:1.5px solid #e2e8f0;" title="Accent"></div>
                                </div>
                                <code id="previewCssClass" class="small bg-light px-2 py-1 rounded"></code>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>Aktif:
                                <strong id="previewPeriodeInfo"></strong>
                                &nbsp;·&nbsp;
                                <i class="bi bi-sort-down me-1"></i>Prioritas:
                                <strong id="previewPriorityInfo"></strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-2">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function previewTheme(t) {
    // Banner bar
    var bar = document.getElementById('previewBannerBar');
    bar.style.background = 'linear-gradient(90deg,' + t.primary + ',' + t.secondary + ')';
    bar.style.borderBottomColor = t.accent;

    // Banner icon & text
    var iconEl = document.getElementById('previewBannerIcon');
    iconEl.innerHTML = t.banner_icon
        ? '<i class="' + t.banner_icon + '"></i>'
        : (t.emoji || '🎉');
    document.getElementById('previewBannerText').innerHTML =
        '<strong>' + (t.banner_text || t.name) + '</strong>';

    // Periode
    document.getElementById('previewPeriode').textContent = t.start + ' – ' + t.end;

    // Navbar accent
    document.getElementById('previewNavbar').style.borderBottomColor = t.primary;
    document.getElementById('previewNavbarBtn').style.background = t.primary;

    // Hero
    document.getElementById('previewHeroEmoji').textContent = t.emoji || '🎉';
    document.getElementById('previewHeroTitle').style.color = '#1e293b';
    document.getElementById('previewHeroBtn1').style.background = t.primary;

    // Hero subtext from banner_subtext if available
    if (t.banner_subtext) {
        document.getElementById('previewHeroSubtext').textContent = t.banner_subtext;
    } else {
        document.getElementById('previewHeroSubtext').textContent = 'Jalin Silaturahmi, Bangun Kontribusi';
    }

    // Hero background - tint from primary
    document.getElementById('previewHero').style.background =
        'linear-gradient(135deg,' + t.primary + '11 0%,' + t.accent + '22 100%)';

    // Warna swatches
    document.getElementById('previewColor1').style.background = t.primary;
    document.getElementById('previewColor2').style.background = t.secondary;
    document.getElementById('previewColor3').style.background = t.accent;
    document.getElementById('previewCssClass').textContent = '.' + t.css_class;

    // Info text
    document.getElementById('previewThemeName').textContent = t.emoji + ' ' + t.name;
    document.getElementById('previewPeriodeInfo').textContent = t.start + ' – ' + t.end;
    document.getElementById('previewPriorityInfo').textContent = t.priority;

    // Open modal
    var modal = new bootstrap.Modal(document.getElementById('themePreviewModal'));
    modal.show();
}
</script>
@endpush
