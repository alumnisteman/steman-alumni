<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADMIN PANEL - {{ setting('site_name', 'STEMAN ALUMNI') }}</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">

    {{-- Bootstrap CSS (CDN – dimuat duluan sebagai fallback agar layout tidak rusak) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Vite: JS saja (Bootstrap CSS sudah di atas via CDN) --}}
    @vite(['resources/js/app.js'])

    {{-- Admin CSS khusus – menggantikan modern-v5.css yang hanya untuk portal publik --}}
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">

    {{-- JS Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" defer></script>
    <script src="{{ asset('assets/js/command-palette.js') }}" defer></script>

    {{-- Guardian Error Handler --}}
    <script>
        window.Guardian = {
            log: function(data) {
                if (window.fetch) {
                    fetch('/api/v1/guardian/log-error', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    }).catch(function() {});
                }
            },
            safe: function(fn, context) {
                context = context || 'Admin';
                try {
                    return fn();
                } catch (e) {
                    console.error('Guardian suppressed error in ' + context + ':', e.message);
                    this.log({ message: e.message, context: context });
                    return null;
                }
            },
            confirmDelete: function(formId) {
                Swal.fire({
                    title: 'Hapus data?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    background: '#ffffff',
                    borderRadius: '15px'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            }
        };
    </script>

    @stack('styles')
</head>
<body>

    <div class="admin-wrapper">

        {{-- Sidebar Desktop (hidden di mobile via admin.css) --}}
        @include('components.admin-sidebar')

        <main class="admin-main-content">

            {{-- Topbar Mobile --}}
            <div class="admin-mobile-topbar d-lg-none">
                <h5 class="fw-black mb-0">ADMIN PANEL</h5>
                <button class="btn btn-dark btn-sm px-3"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#adminMobileMenu"
                        aria-controls="adminMobileMenu">
                    <i class="bi bi-list fs-5"></i>
                </button>
            </div>

            @yield('admin-content')

        </main>
    </div>

    {{-- Offcanvas Mobile Admin Menu --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="adminMobileMenu" aria-labelledby="adminMobileMenuLabel">
        <div class="offcanvas-header bg-dark text-white">
            <h5 class="offcanvas-title fw-black" id="adminMobileMenuLabel">ADMIN MENU</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body p-0">
            @include('components.admin-sidebar', ['isMobile' => true])
        </div>
    </div>

    @include('components.global-modals')
    @include('components.ai-chat-bubble')

    {{-- Bootstrap JS Bundle (includes Popper) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.addEventListener('load', function() {
            if (typeof bootstrap === 'undefined') {
                console.error('Guardian: Bootstrap JS gagal dimuat di Admin panel!');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
