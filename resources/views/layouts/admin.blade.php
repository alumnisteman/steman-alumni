<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADMIN PANEL - {{ setting('site_name', 'STEMAN ALUMNI') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/modern-v5.css') }}">

    <script>
        window.Guardian = {
            log: function(data) {
                if (window.fetch) {
                    fetch('/api/v1/guardian/log-error', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(data)
                    }).catch(() => {});
                }
            },
            safe: function(fn, context = 'Admin') {
                try { return fn(); } 
                catch (e) { console.error(`Guardian suppressed error in ${context}:`, e.message); this.log({ message: e.message, context }); return null; }
            },
            cleanupModals: function() {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 1) {
                    for (let i = 0; i < backdrops.length - 1; i++) backdrops[i].remove();
                    document.body.classList.add('modal-open');
                }
            }
        };
        const observer = new MutationObserver(() => window.Guardian.cleanupModals());
        observer.observe(document.body, { childList: true });
    </script>

    <style>
        :root {
            --admin-primary: #0f172a;
            --admin-accent: #ffcc00;
            --admin-bg: #f8fafc;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--admin-bg); color: #1e293b; overflow-x: hidden; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-main-content { flex-grow: 1; padding: 2rem; overflow-x: hidden; }
        .fw-black { font-weight: 900 !important; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .transition-all { transition: all 0.3s ease; }
        .hover-up-small:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important; }
        @media (max-width: 991px) { .admin-sidebar { display: none; } .admin-main-content { padding: 1.5rem 1rem; } }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        @include('components.admin-sidebar')
        <main class="admin-main-content">
            <div class="d-lg-none mb-4 d-flex justify-content-between align-items-center bg-white p-3 rounded-4 shadow-sm">
                <h5 class="fw-black mb-0">ADMIN PANEL</h5>
                <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileMenu">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            @yield('admin-content')
        </main>
    </div>

    {{-- Offcanvas for Mobile Admin Menu --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="adminMobileMenu">
        <div class="offcanvas-header bg-dark text-white">
            <h5 class="offcanvas-title fw-black">ADMIN MENU</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
             @include('components.admin-sidebar', ['isMobile' => true])
        </div>
    </div>

    @include('components.global-modals')
    @include('components.ai-chat-bubble')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Emergency audit for admin UI
        window.addEventListener('load', function() {
            if (typeof bootstrap === 'undefined') {
                console.error('Guardian: Bootstrap JS failed to load in Admin panel!');
            } else {
                console.log('Guardian: Bootstrap JS loaded successfully.');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
