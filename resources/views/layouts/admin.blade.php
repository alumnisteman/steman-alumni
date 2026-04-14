@extends('layouts.app')

@section('content')
<div class="admin-wrapper d-flex min-vh-100 bg-light">
    <!-- REUSABLE MODULAR SIDEBAR -->
    @include('components.admin-sidebar')

    <!-- MAIN CONTENT AREA -->
    <main class="flex-grow-1 p-4 p-md-5 overflow-auto">
        @yield('admin-content')
    </main>
</div>

@push('styles')
<style>
    body { background-color: #f8fafc !important; }
    .navbar { display: none !important; } /* Unified Admin: Hide Site Navbar */
    .admin-wrapper { font-family: 'Inter', sans-serif; }
    .fw-black { font-weight: 900 !important; }
    
    /* Global Admin Utilities */
    .glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border-radius: 20px; }
    .hover-up-small:hover { transform: translateY(-5px); transition: transform 0.3s ease; }
    
    /* Ensure charts and tables are responsive */
    .table-responsive { border-radius: 12px; }
</style>
@endpush
@endsection

