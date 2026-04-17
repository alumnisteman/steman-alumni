@extends('layouts.app')

@section('content')
<style>
    .store-banner {
        height: 250px;
        background-color: #f1f5f9;
        background-image: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        background-size: cover;
        background-position: center;
        border-radius: 0 0 25px 25px;
    }
    .store-logo-wrapper {
        margin-top: -60px;
        position: relative;
        z-index: 10;
    }
    .store-logo {
        width: 120px;
        height: 120px;
        border-radius: 20px;
        padding: 5px;
        background: white;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border: 4px solid #fff;
    }
    .status-badge {
        background: #e2e8f0;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 50px;
    }
    .nav-tabs-premium {
        border-bottom: 2px solid #f1f5f9;
    }
    .nav-tabs-premium .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        padding: 15px 25px;
        position: relative;
    }
    .nav-tabs-premium .nav-link.active {
        color: #0f172a;
        background: transparent;
    }
    .nav-tabs-premium .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 20%;
        right: 20%;
        height: 3px;
        background: #ffcc00;
        border-radius: 10px;
    }
    .product-card {
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
        border-radius: 15px;
        overflow: hidden;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        border-color: #ffcc00;
    }
    .btn-wa-premium {
        background-color: #25d366;
        color: white;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(37, 211, 102, 0.2);
        transition: all 0.3s ease;
    }
    .btn-wa-premium:hover {
        background-color: #128c7e;
        color: white;
        transform: scale(1.05);
    }
</style>

<div class="store-banner" style="@if($business->photos->count() > 0) background-image: url('{{ $business->photos->first()->photo_url }}'); @endif">
    <div class="container h-100 position-relative">
        <a href="{{ route('alumni.business.index') }}" class="btn btn-white btn-sm rounded-pill shadow-sm position-absolute top-0 start-0 m-4 fw-bold">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Marketplace
        </a>
    </div>
</div>

<div class="container mb-5">
    @if($business->status == 'pending')
    <div class="row mt-n4 mb-4">
        <div class="col-12">
            <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 d-flex align-items-center">
                <i class="bi bi-hourglass-split display-6 me-4"></i>
                <div>
                    <h5 class="fw-bold mb-1">USAHA SEDANG DITINJAU</h5>
                    <p class="mb-0 opacity-75">Ini adalah pratinjau. Profil usaha ini belum tayang untuk publik dan sedang menunggu persetujuan Admin.</p>
                </div>
                @if(auth()->user()->role == 'admin')
                    <form action="{{ route('admin.business.approve', $business->id) }}" method="POST" class="ms-auto">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">SETUJUI SEKARANG</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <!-- Store Header Info -->
            <div class="card border-0 shadow-sm p-4 pt-0 mb-4" style="border-radius: 0 0 25px 25px;">
                <div class="row align-items-end">
                    <div class="col-md-auto text-center text-md-start">
                        <div class="store-logo-wrapper">
                            @if($business->logo_url)
                                <img src="{{ $business->logo_url }}" class="store-logo" alt="{{ $business->name }}">
                            @else
                                <div class="store-logo d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-shop display-5"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md mt-3 mt-md-0">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <div>
                                <h1 class="fw-black text-uppercase tracking-wider mb-1" style="color: #0f172a; font-size: 1.75rem;">
                                    {{ $business->name }}
                                    <i class="bi bi-patch-check-fill text-primary ms-1" style="font-size: 1.2rem;" title="Alumni Terverifikasi"></i>
                                </h1>
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <span class="text-muted small fw-medium"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $business->location }}</span>
                                    <span class="status-badge"><i class="bi bi-tag-fill me-1"></i> {{ $business->category }}</span>
                                    <span class="text-success small fw-bold"><i class="bi bi-clock-fill me-1"></i> Buka Sekarang</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-3 mt-md-0">
                                @if(auth()->id() == $business->user_id)
                                    <a href="{{ route('alumni.business.edit', $business->id) }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
                                        <i class="bi bi-pencil-square me-2"></i> KELOLA TOKO
                                    </a>
                                @endif
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}?text={{ urlencode('Halo ' . $business->name . ', saya melihat produk Anda di Portal Alumni Steman...') }}" target="_blank" class="btn btn-wa-premium">
                                    <i class="bi bi-chat-dots-fill me-2"></i> CHAT PENJUAL
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs nav-tabs-premium mt-5" id="storeTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab">BERANDA</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="product-tab" data-bs-toggle="tab" data-bs-target="#product" type="button" role="tab">ETALASE PRODUK ({{ $business->photos->count() }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab">TENTANG TOKO</button>
                    </li>
                </ul>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="storeTabContent">
                <!-- Home Tab -->
                <div class="tab-pane fade show active" id="home" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                                <h6 class="fw-bold text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Info Pengusaha</h6>
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="rounded-circle overflow-hidden bg-light" style="width: 50px; height: 50px;">
                                        @if($business->owner?->avatar)
                                            <img src="{{ asset('storage/'.$business->owner->avatar) }}" class="w-100 h-100" style="object-fit: cover;">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted fw-bold">
                                                {{ substr($business->owner?->name ?? '?', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="color: #0f172a;">{{ $business->owner?->name ?? 'Pemilik Tidak Tersedia' }}</div>
                                        <div class="text-muted small">Alumni Angkatan {{ $business->owner?->graduation_year ?? '-' }}</div>
                                    </div>
                                </div>
                                <hr class="opacity-10 mb-4">
                                <h6 class="fw-bold text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Kontak Resmi</h6>
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <i class="bi bi-whatsapp text-success fs-5"></i>
                                    <span class="small">+62 {{ $business->whatsapp }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <!-- Featured Photos Slider -->
                            @if($business->photos->count() > 0)
                                <div id="featuredCarousel" class="carousel slide rounded-4 overflow-hidden shadow-sm mb-4" data-bs-ride="carousel">
                                    <div class="carousel-inner" style="height: 350px;">
                                        @foreach($business->photos as $key => $photo)
                                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }} h-100">
                                                <img src="{{ $photo->photo_url }}" class="d-block w-100 h-100" style="object-fit: cover;">
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    </button>
                                </div>
                            @endif

                            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                                <h5 class="fw-bold mb-3">Deskripsi Produk</h5>
                                <div class="text-muted" style="line-height: 1.8;">
                                    {!! nl2br(e($business->description)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Grid Tab -->
                <div class="tab-pane fade" id="product" role="tabpanel">
                    <div class="row g-3">
                        @forelse($business->photos as $photo)
                            <div class="col-6 col-md-3">
                                <div class="card product-card h-100">
                                    <div class="ratio ratio-1x1 bg-light">
                                        <img src="{{ $photo->photo_url }}" class="img-fluid" style="object-fit: cover;">
                                    </div>
                                    <div class="p-3 text-center">
                                        <div class="small fw-bold text-dark truncate-1">Poto Produk</div>
                                        <a href="{{ $photo->photo_url }}" target="_blank" class="btn btn-link btn-sm text-decoration-none p-0 mt-1">Lihat Detail</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 py-5 text-center">
                                <i class="bi bi-images display-4 text-muted mb-3 d-block"></i>
                                <p class="text-muted">Belum ada foto produk di etalase ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- About Tab -->
                <div class="tab-pane fade" id="about" role="tabpanel">
                    <div class="card border-0 shadow-sm p-5" style="border-radius: 25px;">
                        <div class="row align-items-center g-5">
                            <div class="col-md-6">
                                <h3 class="fw-bold mb-4">Tentang {{ $business->name }}</h3>
                                <p class="text-muted mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                                    {{ $business->description }}
                                </p>
                                <div class="alert alert-warning border-0 p-4" style="border-radius: 15px;">
                                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle-fill me-2"></i> Keamanan Transaksi</h6>
                                    <p class="small mb-0 opacity-75">Portal Alumni Steman hanya menyediakan direktori. Pastikan Anda bertransaksi dengan aman melalui fitur chat resmi yang tersedia.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="rounded-4 overflow-hidden shadow-lg">
                                    <img src="{{ $business->logo_url ?? 'https://via.placeholder.com/600x400' }}" class="w-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
