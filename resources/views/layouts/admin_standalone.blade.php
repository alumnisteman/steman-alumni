<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADMIN PANEL - {{ setting('site_name', 'STEMAN ALUMNI') }}</title>
    
    <!-- Premium Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --admin-primary: #0f172a;
            --admin-accent: #ffcc00;
            --admin-bg: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--admin-bg);
            color: #1e293b;
        }

        .fw-black { font-weight: 900 !important; }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .admin-main-content {
            flex-grow: 1;
            padding: 2rem;
            overflow-x: hidden;
        }

        /* Sidebar Customization for Standalone */
        .admin-sidebar {
            background: #ffffff;
            border-right: 1px solid rgba(0,0,0,0.05);
            width: 280px;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .hover-up-small {
            transition: all 0.3s ease;
        }

        .hover-up-small:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }

        .badge-admin {
            padding: 0.5em 1em;
            border-radius: 8px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
        }

        /* Chart/Table Container */
        .admin-table-container {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 991px) {
            .admin-sidebar {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        @include('components.admin-sidebar')

        <!-- Content -->
        <main class="admin-main-content">
            @yield('admin-content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>
