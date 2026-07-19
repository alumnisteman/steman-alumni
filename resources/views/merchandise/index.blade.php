@extends('layouts.app')

@section('content')
{{-- Hero --}}
<div class="merch-hero py-5 mb-0" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height: 320px; display:flex; align-items:center;">
    <div class="container py-4 text-center">
        <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-15 rounded-circle mb-3" style="width:72px;height:72px;">
            <i class="bi bi-bag-heart-fill fs-2 text-warning"></i>
        </div>
        <h1 class="display-5 fw-black text-white mb-2">MERCHANDISE RESMI</h1>
        <p class="lead text-white-50 mb-0">Koleksi eksklusif Alumni <strong class="text-warning">STEMAN</strong> — Bangga Memakai, Bangga Jadi Alumni</p>
        <div class="mt-3 d-flex flex-wrap gap-2 justify-content-center">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold fs-6">
                <i class="bi bi-clock-history me-1"></i>Pre-Order Tersedia
            </span>
            <span class="badge bg-white bg-opacity-10 text-white px-3 py-2 rounded-pill fw-semibold fs-6">
                <i class="bi bi-shield-check me-1"></i>Produk Resmi
            </span>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="bg-white dark:bg-dark border-bottom shadow-sm sticky-top" style="top:56px; z-index:100;">
    <div class="container py-2">
        <form method="GET" action="{{ route('merchandise.index') }}" class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('merchandise.index') }}" class="btn btn-sm rounded-pill {{ !request('category') && !request('type') ? 'btn-dark' : 'btn-outline-secondary' }}">
                    <i class="bi bi-grid me-1"></i>Semua
                </a>
                @foreach($categories as $key => $cat)
                    <a href="{{ route('merchandise.index', ['category' => $key]) }}" class="btn btn-sm rounded-pill {{ request('category') === $key ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' }}">
                        <i class="bi {{ $cat['icon'] }} me-1"></i>{{ $cat['label'] }}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('merchandise.index', ['type' => 'pre_order']) }}" class="btn btn-sm rounded-pill {{ request('type') === 'pre_order' ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning' }}">
                <i class="bi bi-clock me-1"></i>Pre-Order
            </a>
        </form>
    </div>
</div>

{{-- Products Grid --}}
<div class="container py-5">
    @if($merchandise->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-bag-x display-1 text-muted opacity-30"></i>
            <h4 class="mt-3 text-muted">Belum ada produk tersedia</h4>
            <p class="text-muted small">Segera hadir. Pantau terus halaman ini!</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($merchandise as $item)
            <div class="col-lg-3 col-md-4 col-6">
                <a href="{{ route('merchandise.show', $item->slug) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 merch-card" style="border-radius: 16px; overflow:hidden;">
                        {{-- Image --}}
                        <div class="position-relative" style="height:200px; background:#f8f9fa;">
                            @if($item->image)
                                <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-100 h-100" style="object-fit:cover;">
                            @else
                                @php $cat = $categories[$item->category] ?? ['icon'=>'bi-bag','label'=>$item->category]; @endphp
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="background: linear-gradient(135deg, #1a1a2e, #0f3460);">
                                    <i class="bi {{ $cat['icon'] }} text-warning" style="font-size: 3.5rem;"></i>
                                    <span class="text-white-50 small mt-1">{{ $cat['label'] }}</span>
                                </div>
                            @endif
                            {{-- Badges --}}
                            <div class="position-absolute top-0 start-0 p-2 d-flex flex-column gap-1">
                                @if($item->is_pre_order)
                                    <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded-pill shadow-sm">
                                        <i class="bi bi-clock-history me-1"></i>PRE-ORDER
                                    </span>
                                @endif
                                @if($item->price_member)
                                    <span class="badge bg-success fw-bold px-2 py-1 rounded-pill shadow-sm">
                                        <i class="bi bi-star-fill me-1"></i>Harga Alumni
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <span class="badge bg-warning bg-opacity-15 text-warning small rounded-pill mb-1">
                                {{ $categories[$item->category]['label'] ?? $item->category }}
                            </span>
                            <h6 class="fw-bold text-dark mb-1 mt-1" style="line-height:1.3;">{{ $item->name }}</h6>
                            <div class="mt-2">
                                <span class="fw-black text-dark" style="font-size:1.05rem;">{{ $item->formattedPrice() }}</span>
                                @if($item->price_member)
                                    <br><span class="text-success small fw-semibold"><i class="bi bi-tag-fill me-1"></i>{{ $item->formattedPriceMember() }} (alumni)</span>
                                @endif
                            </div>
                            @if($item->is_pre_order && $item->pre_order_close_at)
                                <div class="mt-2 text-muted small">
                                    <i class="bi bi-calendar-x me-1 text-danger"></i>Tutup: {{ $item->pre_order_close_at->format('d M Y') }}
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent border-0 px-3 pb-3 pt-0">
                            <div class="btn btn-dark w-100 rounded-pill btn-sm fw-semibold">
                                {{ $item->is_pre_order ? 'Pre-Order Sekarang' : 'Pesan Sekarang' }}
                                <i class="bi bi-arrow-right ms-1"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Info Banner --}}
<div class="bg-warning bg-opacity-10 border-top border-warning border-opacity-25 py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <i class="bi bi-shield-check-fill text-warning fs-2 mb-2 d-block"></i>
                <h6 class="fw-bold">Produk Resmi</h6>
                <p class="text-muted small mb-0">Bergaransi keaslian dari pengurus resmi Alumni STEMAN</p>
            </div>
            <div class="col-md-3 col-6">
                <i class="bi bi-clock-history text-warning fs-2 mb-2 d-block"></i>
                <h6 class="fw-bold">Pre-Order Aman</h6>
                <p class="text-muted small mb-0">Sistem pre-order terstruktur dengan konfirmasi langsung</p>
            </div>
            <div class="col-md-3 col-6">
                <i class="bi bi-truck text-warning fs-2 mb-2 d-block"></i>
                <h6 class="fw-bold">Pengiriman ke Seluruh Indonesia</h6>
                <p class="text-muted small mb-0">Ongkir menyesuaikan lokasi pengiriman</p>
            </div>
            <div class="col-md-3 col-6">
                <i class="bi bi-whatsapp text-warning fs-2 mb-2 d-block"></i>
                <h6 class="fw-bold">CS via WhatsApp</h6>
                <p class="text-muted small mb-0">Konfirmasi & info pesanan langsung via WhatsApp</p>
            </div>
        </div>
    </div>
</div>

<style>
.merch-card {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    cursor: pointer;
}
.merch-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 32px rgba(0,0,0,.12) !important;
}
[data-bs-theme="dark"] .merch-card { background: #1e1e2e !important; }
[data-bs-theme="dark"] h6.text-dark { color: #fff !important; }
[data-bs-theme="dark"] span.fw-black.text-dark { color: #ffd700 !important; }
</style>
@endsection
