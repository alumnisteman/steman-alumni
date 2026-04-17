<aside class="admin-sidebar bg-white shadow-sm d-none d-lg-block" style="width: 280px; z-index: 1000; height: 100vh; position: sticky; top: 0; overflow-y: auto;">
    <div class="p-4 border-bottom bg-dark text-white text-center">
        <h5 class="fw-black mb-0 tracking-wider">ADMIN PANEL</h5>
        <small class="text-warning opacity-75">STEMAN ALUMNI v4.2-ADS</small>
    </div>
    <div class="p-3 pb-0">
        <a href="/" class="btn btn-primary w-100 fw-bold shadow-sm rounded-3">
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
        <a href="{{ route('admin.gallery.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.gallery.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-images me-3 text-success"></i> Galeri Foto
        </a>
        <a href="{{ route('admin.jobs.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.jobs.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-briefcase me-3 text-info"></i> Lowongan Kerja
        </a>
        <a href="{{ route('admin.programs.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.programs.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }}">
            <i class="bi bi-mortarboard me-3 text-warning"></i> Program Alumni
        </a>
        <a href="{{ route('admin.registrations.index') }}" class="nav-link py-3 px-4 rounded-3 mb-2 {{ request()->routeIs('admin.registrations.*') ? 'active bg-warning bg-opacity-10 text-dark fw-bold' : 'text-dark' }} d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clipboard-check me-3 text-primary"></i> Registrasi Masuk</span>
            @php $pendingRegCount = \App\Models\ProgramRegistration::where('status', 'pending')->count(); @endphp
            @if($pendingRegCount > 0)
                <span class="badge bg-danger rounded-pill">{{ $pendingRegCount }}</span>
            @endif
        </a>

        <p class="text-muted small fw-bold mb-2 ps-3 opacity-50 mt-4">PENGGUNA</p>
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

        <div class="mt-5 pt-5 pb-4 ps-3">
             <a href="/" class="text-muted small text-decoration-none">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Situs
             </a>
        </div>
    </div>
</aside>

<style>
    .admin-sidebar .nav-link { transition: all 0.2s ease; border-radius: 12px !important; margin-bottom: 4px !important; }
    .admin-sidebar .nav-link:hover:not(.active) { background: rgba(0,0,0,0.05); transform: translateX(5px); }
    .fw-black { font-weight: 900 !important; }
</style>
