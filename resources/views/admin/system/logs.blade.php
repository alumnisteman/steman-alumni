@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black text-dark mb-1">SISTEM LOGS</h2>
            <p class="text-muted">Pantau aktivitas dan error sistem secara real-time tanpa SSH.</p>
        </div>
        <form action="{{ route('admin.system.logs.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua log?')">
            @csrf
            <button type="submit" class="btn btn-danger rounded-pill px-4">
                <i class="bi bi-trash3 me-2"></i> Bersihkan Log
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-pill px-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <!-- Emergency Logs -->
        @if($emergencyLogs)
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 border-start border-danger border-5">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> EMERGENCY FATAL LOGS</h5>
                    <span class="badge bg-danger rounded-pill">Critical</span>
                </div>
                <div class="card-body bg-dark text-warning p-0">
                    <pre class="m-0 p-4 small" style="max-height: 200px; overflow-y: auto; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;">{{ $emergencyLogs }}</pre>
                </div>
                <div class="card-footer bg-light border-0 py-2 small text-muted">
                    Path: <code class="text-danger">{{ $emergencyPath }}</code>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Laravel Logs -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-code me-2"></i> LARAVEL SYSTEM LOGS (Last 200 Lines)</h5>
                </div>
                <div class="card-body bg-dark text-light p-0">
                    <pre class="m-0 p-4 small" style="max-height: 600px; overflow-y: auto; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; line-height: 1.6;">{{ $logs }}</pre>
                </div>
                <div class="card-footer bg-light border-0 py-2 small text-muted d-flex justify-content-between">
                    <span>Path: <code class="text-primary">{{ $logPath }}</code></span>
                    <span>Tarik nafas, error adalah teman belajar. ☕</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .fw-black { font-weight: 900 !important; }
</style>
@endpush
@endsection
