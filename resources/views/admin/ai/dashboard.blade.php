@extends('layouts.app')

@section('title', '🤖 AI Control Panel')

@section('content')
<div class="row mb-5 py-4 px-2" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-radius: 20px; color: white;">
    <div class="col-md-8">
        <h1 class="fw-bold mb-2">🤖 AI Control Panel</h1>
        <p class="lead opacity-75">Manajemen otomatisasi portal alumni menggunakan Google Gemini AI.</p>
    </div>
    <div class="col-md-4 text-end d-flex align-items-center justify-content-end">
        <form action="{{ route('admin.ai.generate') }}" method="POST">
            @csrf
            <button class="btn btn-light btn-lg rounded-pill shadow px-4 py-2 hover-up">
                <i class="fas fa-magic me-2"></i> Generate Berita Sekarang
            </button>
        </form>
    </div>
</div>

<!-- Session Alerts -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Stats Section -->
<div class="row mb-5">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white text-dark overflow-hidden position-relative">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-primary bg-opacity-10 rounded-3 text-primary me-3">
                        <i class="fas fa-microchip fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-bold mb-0">AI Requests</h6>
                        <h2 class="fw-bold mb-0">{{ $stats['total_requests'] }}</h2>
                    </div>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" style="width: 45%;"></div>
                </div>
                <small class="text-muted mt-2 d-block">Quota harian: 10/min</small>
            </div>
            <i class="fas fa-network-wired position-absolute top-0 end-0 opacity-10 fa-5x me-n3 mt-n3" style="transform: rotate(15deg);"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white text-dark overflow-hidden position-relative">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-danger bg-opacity-10 rounded-3 text-danger me-3">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-bold mb-0">Spam Prevented</h6>
                        <h2 class="fw-bold mb-0">{{ $stats['spam_prevented'] }}</h2>
                    </div>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-danger" style="width: 72%;"></div>
                </div>
                <small class="text-muted mt-2 d-block">Automated Moderation Active</small>
            </div>
            <i class="fas fa-robot position-absolute top-0 end-0 opacity-10 fa-5x me-n3 mt-n3" style="transform: rotate(-10deg);"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white text-dark overflow-hidden position-relative">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-success bg-opacity-10 rounded-3 text-success me-3">
                        <i class="fas fa-newspaper fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-bold mb-0">Total AI Drafts</h6>
                        <h2 class="fw-bold mb-0">{{ $stats['news_drafts'] }}</h2>
                    </div>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: 30%;"></div>
                </div>
                <small class="text-muted mt-2 d-block">Waiting for review</small>
            </div>
            <i class="fas fa-pen-nib position-absolute top-0 end-0 opacity-10 fa-5x me-n3 mt-n3"></i>
        </div>
    </div>
</div>

<!-- Draft Review Table -->
<div class="card border-0 shadow-sm rounded-4 mb-5">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="fas fa-list-ul me-2"></i> Draft Berita AI (Perlu Review)</h5>
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">{{ count($aiDrafts) }} Draft</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4">Judul Berita</th>
                        <th>Kategori</th>
                        <th>Waktu Generate</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aiDrafts as $draft)
                    <tr>
                        <td class="ps-4 py-3">
                            <span class="fw-bold d-block">{{ $draft->title }}</span>
                            <small class="text-muted">{{ Str::limit(strip_tags($draft->content), 80) }}</small>
                        </td>
                        <td><span class="badge bg-info bg-opacity-10 text-info px-3 py-1">{{ $draft->category }}</span></td>
                        <td>{{ $draft->created_at->format('d M Y H:i') }}</td>
                        <td class="text-end pe-4">
                            <form action="{{ route('admin.ai.publish', $draft) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm rounded-3 shadow-none px-3">
                                    <i class="fas fa-check me-1"></i> Terbitkan
                                </button>
                            </form>
                            <a href="{{ url('/news/' . $draft->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-3 px-3 me-1">
                                <i class="fas fa-eye me-1"></i> Lihat
                            </a>
                            <a href="{{ route('admin.news.edit', $draft->id) }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <img src="https://img.icons8.com/clouds/200/robot-3.png" class="mb-3">
                            <p class="text-muted fs-5">Belum ada draft berita yang di-generate AI.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .hover-up:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
    }
</style>
@endsection
