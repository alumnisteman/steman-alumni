@php $isMobile = $isMobile ?? false; @endphp

@if($isMobile)
{{-- ===== VERSI MOBILE (di dalam Offcanvas) ===== --}}
<nav class="admin-sidebar-mobile w-100 h-100">
    <div class="p-3 border-bottom bg-dark text-white text-center">
        <img src="{{ asset('images/logo.jpg') }}" height="45" class="mb-2 rounded-pill bg-white p-1" alt="Logo">
        <h6 class="fw-black mb-0 text-uppercase">{{ auth()->user()->role }} PANEL</h6>
        <small class="text-warning opacity-75">STEMAN ALUMNI v4.2-ADS</small>
    </div>
    <div class="p-3 pb-0">
        <a href="{{ config('app.url') }}" class="btn btn-primary w-100 fw-bold shadow-sm rounded-3">
            <i class="bi bi-globe me-2"></i> Lihat Website
        </a>
    </div>

    {{-- ===== SEARCH BAR MOBILE ===== --}}
    <div class="px-3 pt-3 pb-1">
        <div class="input-group input-group-sm rounded-3 overflow-hidden shadow-sm border">
            <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="mobileMenuSearch" class="form-control border-0 ps-0" placeholder="Cari menu..." autocomplete="off">
            <button class="btn btn-sm btn-white border-0 text-muted d-none" id="mobileSearchClear" onclick="clearMobileSearch()" type="button"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>
    <div id="mobileSearchEmpty" class="text-center text-muted small py-4 d-none">
        <i class="bi bi-emoji-frown d-block fs-3 mb-2 opacity-50"></i>
        Menu tidak ditemukan
    </div>

    <div class="sidebar-nav p-3 mt-0" style="overflow-y: auto; max-height: calc(100vh - 210px);" id="mobileNavContainer">

        <p class="text-muted small fw-bold mb-2 ps-2 opacity-50 mt-2 nav-section-label">UTAMA</p>
        <a href="{{ route('admin.dashboard') }}" class="nav-link-mobile {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-3"></i> Dashboard
        </a>

        <p class="text-muted small fw-bold mb-2 ps-2 opacity-50 mt-3 nav-section-label">MARKETPLACE</p>
        <a href="{{ route('admin.business.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.business.*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
            <span><i class="bi bi-shop me-3"></i> Bisnis Alumni</span>
            @php $pendingCount = \App\Models\Business::where('status', 'pending')->count(); @endphp
            @if($pendingCount > 0)<span class="badge bg-danger rounded-pill">{{ $pendingCount }}</span>@endif
        </a>

        <p class="text-muted small fw-bold mb-2 ps-2 opacity-50 mt-3 nav-section-label">MANAJEMEN KONTEN</p>
        <a href="{{ route('admin.news.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
            <i class="bi bi-newspaper me-3 text-primary"></i> Berita & Info
        </a>
        <a href="{{ route('admin.ads.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.ads.*') ? 'active' : '' }}">
            <i class="bi bi-megaphone me-3 text-danger"></i> Manajemen Iklan
        </a>
        <a href="{{ route('admin.event-themes.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.event-themes.*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar-event-fill me-3 text-warning"></i> Tema Event Otomatis</span>
            <span class="badge bg-warning text-dark rounded-pill" style="font-size:0.6rem">AUTO</span>
        </a>
        <a href="{{ route('admin.gallery.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
            <i class="bi bi-images me-3 text-success"></i> Galeri Foto
        </a>
        <a href="{{ route('admin.jobs.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}">
            <i class="bi bi-briefcase me-3 text-info"></i> Lowongan Kerja
        </a>
        <a href="{{ route('admin.programs.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
            <i class="bi bi-mortarboard me-3 text-warning"></i> Program Alumni
        </a>
        <a href="{{ route('admin.podcasts.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.podcasts.*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
            <span><i class="bi bi-mic me-3 text-danger"></i> Alumni Podcast</span>
            <span class="badge bg-danger rounded-pill" style="font-size:0.6rem">HOT</span>
        </a>
        <a href="{{ route('admin.registrations.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.registrations.*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clipboard-check me-3 text-primary"></i> Registrasi Masuk</span>
            @php $pendingRegCount = \App\Models\ProgramRegistration::where('status', 'pending')->count(); @endphp
            @if($pendingRegCount > 0)<span class="badge bg-danger rounded-pill">{{ $pendingRegCount }}</span>@endif
        </a>
        <a href="{{ route('admin.donations.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.donations.*') || request()->routeIs('admin.campaigns.*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
            <span><i class="bi bi-piggy-bank me-3 text-success"></i> Donasi & Dana</span>
            @php $pendingDonationCount = \App\Models\Donation::where('status', 'pending')->count(); @endphp
            @if($pendingDonationCount > 0)<span class="badge bg-danger rounded-pill">{{ $pendingDonationCount }}</span>@endif
        </a>

        <p class="text-muted small fw-bold mb-2 ps-2 opacity-50 mt-3 nav-section-label">SISTEM & CMS</p>
        <a href="{{ route('admin.chairman.edit') }}" class="nav-link-mobile {{ request()->routeIs('admin.chairman.*') ? 'active' : '' }}">
            <i class="bi bi-person-video3 me-3 text-warning"></i> Sambutan & Foto
        </a>
        <a href="{{ route('admin.hero.edit') }}" class="nav-link-mobile {{ request()->routeIs('admin.hero.*') ? 'active' : '' }}">
            <i class="bi bi-image me-3 text-info"></i> Tampilan Beranda
        </a>
        <a href="{{ route('admin.success-stories.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.success-stories.*') ? 'active' : '' }}">
            <i class="bi bi-trophy-fill me-3 text-success"></i> Jejak Sukses Alumni
        </a>
        <a href="{{ route('admin.settings.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
            <i class="bi bi-gear-fill me-3 text-secondary"></i> Pengaturan Umum
        </a>
        <a href="{{ route('admin.contact.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.contact.index') ? 'active' : '' }}">
            <i class="bi bi-telephone-inbound me-3 text-primary"></i> Kontak & Alamat
        </a>
        <a href="{{ route('admin.users.index') }}" class="nav-link-mobile {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
            <i class="bi bi-people me-3"></i> Database Alumni
        </a>
        <a href="{{ route('admin.users.verification') }}" class="nav-link-mobile {{ request()->routeIs('admin.users.verification') ? 'active' : '' }}">
            <i class="bi bi-patch-check me-3 text-success"></i> Verifikasi Data
        </a>

        <p class="text-muted small fw-bold mb-2 ps-2 opacity-50 mt-3 nav-section-label">MONITORING</p>
        <a href="{{ route('admin.ai.dashboard') }}" class="nav-link-mobile {{ request()->routeIs('admin.ai.*') ? 'active' : '' }}">
            <i class="bi bi-robot me-3 text-danger"></i> Monitoring AI
        </a>
        <a href="{{ route('admin.system.logs') }}" class="nav-link-mobile {{ request()->routeIs('admin.system.logs') ? 'active' : '' }}">
            <i class="bi bi-terminal me-3 text-dark"></i> Log Sistem
        </a>
        <a href="{{ route('admin.system.pulse') }}" class="nav-link-mobile {{ request()->routeIs('admin.system.pulse') ? 'active' : '' }} d-flex justify-content-between align-items-center">
            <span><i class="bi bi-activity me-3 text-info"></i> System Pulse</span>
            <span class="badge bg-info rounded-pill" style="font-size:0.6rem">NEW</span>
        </a>
        <a href="{{ route('admin.guard.dashboard') }}" class="nav-link-mobile {{ request()->routeIs('admin.guard.*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
            <span><i class="bi bi-shield-check me-3 text-primary"></i> System Guard</span>
            <span class="badge bg-primary rounded-pill" style="font-size:0.6rem">LIVE</span>
        </a>

        <div class="mt-4 pb-5">
            <a href="/logout" class="btn btn-outline-danger w-100 rounded-pill fw-bold shadow-sm" onclick="return confirm('Yakin ingin keluar?')">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar Sesi
            </a>
        </div>
    </div>
</nav>

<style>
    .nav-link-mobile {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 4px;
        color: #1e293b;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    .nav-link-mobile:hover { background: rgba(0,0,0,0.05); transform: translateX(4px); color: #1e293b; }
    .nav-link-mobile.active { background: rgba(255, 204, 0, 0.15); font-weight: 700; color: #1e293b; }
    .nav-link-mobile .search-highlight { background: rgba(255,204,0,0.5); border-radius: 3px; padding: 0 2px; font-weight: 700; }
    #mobileMenuSearch:focus { box-shadow: none; outline: none; }
</style>

<script>
(function() {
    const input    = document.getElementById('mobileMenuSearch');
    const clear    = document.getElementById('mobileSearchClear');
    const empty    = document.getElementById('mobileSearchEmpty');
    const container = document.getElementById('mobileNavContainer');
    if (!input || !container) return;

    // Kumpulkan semua menu item dan label section
    const links    = Array.from(container.querySelectorAll('a.nav-link-mobile'));
    const sections = Array.from(container.querySelectorAll('p.nav-section-label'));

    // Simpan teks asli tiap link (tanpa icon, ambil text content)
    const linkTexts = links.map(el => el.textContent.trim().toLowerCase());

    function doSearch(q) {
        q = q.trim().toLowerCase();
        clear.classList.toggle('d-none', q === '');

        if (q === '') {
            // Reset semua
            links.forEach(el => { el.style.display = ''; el.querySelectorAll('.search-highlight').forEach(h => { h.outerHTML = h.textContent; }); });
            sections.forEach(el => el.style.display = '');
            empty.classList.add('d-none');
            return;
        }

        let visibleCount = 0;

        links.forEach((el, i) => {
            if (linkTexts[i].includes(q)) {
                el.style.display = '';
                // Highlight matching text in spans (cegah double highlight)
                highlightText(el, q);
                visibleCount++;
            } else {
                el.style.display = 'none';
            }
        });

        // Sembunyikan section label jika semua item-nya hidden
        sections.forEach(section => {
            let nextEl = section.nextElementSibling;
            let hasVisible = false;
            while (nextEl && !nextEl.classList.contains('nav-section-label')) {
                if (nextEl.classList.contains('nav-link-mobile') && nextEl.style.display !== 'none') {
                    hasVisible = true; break;
                }
                nextEl = nextEl.nextElementSibling;
            }
            section.style.display = hasVisible ? '' : 'none';
        });

        empty.classList.toggle('d-none', visibleCount > 0);
    }

    function highlightText(el, q) {
        // Hanya highlight pada text node langsung (bukan dalam badge)
        el.querySelectorAll('span:not(.badge)').forEach(span => {
            // Hapus highlight lama
            span.innerHTML = span.innerHTML.replace(/<mark class="search-highlight">(.*?)<\/mark>/gi, '$1');
            const re = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
            span.innerHTML = span.innerHTML.replace(re, '<mark class="search-highlight">$1</mark>');
        });
        // Juga cek text node langsung di <a> (tanpa nested span)
        el.childNodes.forEach(node => {
            if (node.nodeType === 3 && node.textContent.toLowerCase().includes(q)) {
                const span = document.createElement('span');
                const re = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
                span.innerHTML = node.textContent.replace(re, '<mark class="search-highlight">$1</mark>');
                node.replaceWith(...span.childNodes);
            }
        });
    }

    function clearMobileSearch() {
        input.value = '';
        doSearch('');
        input.focus();
    }
    window.clearMobileSearch = clearMobileSearch;

    input.addEventListener('input', () => doSearch(input.value));

    // Auto-fokus saat offcanvas dibuka
    const offcanvas = document.getElementById('adminMobileMenu');
    if (offcanvas) {
        offcanvas.addEventListener('shown.bs.offcanvas', () => {
            input.focus();
            input.select();
        });
    }
})();
</script>

@else
{{-- ===== VERSI DESKTOP (sticky sidebar) ===== --}}
<aside class="admin-sidebar bg-white shadow-sm" style="width: 280px; z-index: 1000; height: 100vh; position: sticky; top: 0; overflow-y: auto; flex-shrink: 0;">
    <div class="p-4 border-bottom bg-dark text-white text-center">
        <img src="{{ asset('images/logo.jpg') }}" height="50" class="mb-3 rounded-pill bg-white p-1" alt="Logo">
        <h5 class="fw-black mb-0 tracking-wider text-uppercase">{{ auth()->user()->role }} PANEL</h5>
        <small class="text-warning opacity-75">STEMAN ALUMNI v4.2-ADS</small>
    </div>
    <div class="p-3 pb-0">
        <a href="{{ config('app.url') }}" class="btn btn-primary w-100 fw-bold shadow-sm rounded-3">
            <i class="bi bi-globe me-2"></i> Lihat Website
        </a>
    </div>
    <div class="sidebar-nav p-3 mt-0">
        <p class="text-muted small fw-bold mb-2 ps-3 opacity-50">UTAMA</p>
        <a href="{{ route('admin.dashboard') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.dashboard') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-speedometer2 me-3"></i> Dashboard
        </a>
        
        <p class="text-muted small fw-bold mb-2 ps-3 opacity-50 mt-4">MARKETPLACE</p>
        <a href="{{ route('admin.business.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.business.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }} d-flex justify-content-between align-items-center">
            <span><i class="bi bi-shop me-3"></i> Bisnis Alumni</span>
            @if(auth()->check() && auth()->user()->role === 'admin')
                @php $pendingCount = \App\Models\Business::where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-danger rounded-pill">{{ $pendingCount }}</span>
                @endif
            @endif
        </a>
        <p class="text-muted small fw-bold mb-2 ps-3 opacity-50 mt-4">MANAJEMEN KONTEN</p>
        <a href="{{ route('admin.news.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.news.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-newspaper me-3 text-primary"></i> Berita & Info
        </a>
        <a href="{{ route('admin.ads.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.ads.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-megaphone me-3 text-danger"></i> Manajemen Iklan
        </a>
        <a href="{{ route('admin.event-themes.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 d-flex justify-content-between align-items-center {{ request()->routeIs('admin.event-themes.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <span><i class="bi bi-calendar-event-fill me-3 text-warning"></i> Tema Event Otomatis</span>
            <span class="badge bg-warning text-dark rounded-pill" style="font-size:0.6rem">AUTO</span>
        </a>
        <a href="{{ route('admin.gallery.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.gallery.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-images me-3 text-success"></i> Galeri Foto
        </a>
        <a href="{{ route('admin.jobs.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.jobs.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-briefcase me-3 text-info"></i> Lowongan Kerja
        </a>
        <a href="{{ route('admin.programs.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.programs.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-mortarboard me-3 text-warning"></i> Program Alumni
        </a>
        <a href="{{ route('admin.podcasts.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.podcasts.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-mic me-3 text-danger"></i> Alumni Podcast
            <span class="badge bg-danger rounded-pill ms-auto" style="font-size:0.6rem">HOT</span>
        </a>
        <a href="{{ route('admin.registrations.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.registrations.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }} d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clipboard-check me-3 text-primary"></i> Registrasi Masuk</span>
            @php $pendingRegCount = \App\Models\ProgramRegistration::where('status', 'pending')->count(); @endphp
            @if($pendingRegCount > 0)
                <span class="badge bg-danger rounded-pill">{{ $pendingRegCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.donations.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.donations.*') || request()->routeIs('admin.campaigns.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }} d-flex justify-content-between align-items-center">
            <span><i class="bi bi-piggy-bank me-3 text-success"></i> Donasi & Dana</span>
            @php $pendingDonationCount = \App\Models\Donation::where('status', 'pending')->count(); @endphp
            @if($pendingDonationCount > 0)
                <span class="badge bg-danger rounded-pill">{{ $pendingDonationCount }}</span>
            @endif
        </a>

        <div class="px-3 mt-4 mb-2">
            <small class="text-uppercase fw-bold text-muted opacity-50" style="font-size: 0.65rem; letter-spacing: 1px;">SYSTEM MANAGEMENT</small>
        </div>
        <a href="https://alumni-steman.my.id:9443" target="_blank" class="nav-link py-3 px-4 d-flex align-items-center text-dark border-start border-4 border-transparent hover-bg-light transition-all">
            <i class="bi bi-box-seam fs-5 me-3 text-primary"></i>
            <span class="fw-semibold">Portainer (Docker)</span>
            <i class="bi bi-box-arrow-up-right ms-auto small opacity-50"></i>
        </a>
        <a href="http://alumni-steman.my.id:7700" target="_blank" class="nav-link py-3 px-4 d-flex align-items-center text-dark border-start border-4 border-transparent hover-bg-light transition-all">
            <i class="bi bi-search fs-5 me-3 text-warning"></i>
            <span class="fw-semibold">Meilisearch</span>
            <i class="bi bi-box-arrow-up-right ms-auto small opacity-50"></i>
        </a>

        <div class="px-3 mt-4 mb-2">
            <small class="text-uppercase fw-bold text-muted opacity-50" style="font-size: 0.65rem; letter-spacing: 1px;">PENGATURAN</small>
        </div>
        <a href="{{ route('admin.users.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.users.index') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-people me-3"></i> Database Alumni
        </a>
        <a href="{{ route('admin.users.verification') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.users.verification') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-patch-check me-3 text-success"></i> Verifikasi Data
        </a>

        <p class="text-muted small fw-bold mb-2 ps-3 opacity-50 mt-4">SISTEM & CMS</p>
        <a href="{{ route('admin.chairman.edit') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.chairman.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-person-video3 me-3 text-warning"></i> Sambutan & Foto
        </a>
        <a href="{{ route('admin.hero.edit') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.hero.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-image me-3 text-info"></i> Tampilan Beranda
        </a>
        <a href="{{ route('admin.success-stories.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.success-stories.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-trophy-fill me-3 text-success"></i> Jejak Sukses Alumni
        </a>
        <a href="{{ route('admin.settings.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.settings.index') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-gear-fill me-3 text-secondary"></i> Pengaturan Umum
        </a>
        <a href="{{ route('admin.contact.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.contact.index') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-telephone-inbound me-3 text-primary"></i> Kontak & Alamat
        </a>
        <a href="{{ route('admin.ai.dashboard') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.ai.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-robot me-3 text-danger"></i> Monitoring AI
        </a>
        <a href="{{ route('admin.system.logs') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.system.logs') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-terminal me-3 text-dark"></i> Log Sistem
        </a>
        <a href="{{ route('admin.system.pulse') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.system.pulse') ? 'active bg-info bg-opacity-10 text-info fw-bold' : 'text-dark' }}">
            <i class="bi bi-activity me-3 text-info"></i> System Pulse
            <span class="badge bg-info rounded-pill ms-auto" style="font-size:0.6rem">NEW</span>
        </a>
        <a href="{{ route('admin.guard.dashboard') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.guard.*') ? 'active bg-primary bg-opacity-10 text-primary fw-bold' : 'text-dark' }}">
            <i class="bi bi-shield-check me-3 text-primary"></i> System Guard
            <span class="badge bg-primary rounded-pill ms-auto" style="font-size:0.6rem">LIVE</span>
        </a>

        <div class="mt-5 pt-3 pb-5 ps-3">
             <a href="/" class="text-muted small text-decoration-none d-block mb-3">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Situs
             </a>
             <a href="/logout" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold shadow-sm" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar Sesi
             </a>
        </div>
    </div>
</aside>

<style>
    .admin-sidebar .nav-link { transition: all 0.2s ease; border-radius: 12px !important; margin-bottom: 4px !important; }
    .admin-sidebar .nav-link:hover:not(.active) { background: rgba(0,0,0,0.05); transform: translateX(5px); }
    .fw-black { font-weight: 900 !important; }
</style>
@endif
