@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <div>
            <h2 class="fw-black text-uppercase tracking-wider mb-1" style="color: #0f172a;">STEMAN MARKETPLACE 🏪</h2>
            <p class="text-muted mb-0">Dukung sesama alumni dengan menggunakan produk & jasa mereka.</p>
        </div>
        <div>
            <a href="{{ route('alumni.business.create') }}" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                <i class="bi bi-plus-circle me-2"></i> DAFTARKAN USAHA SAYA
            </a>
        </div>
    </div>

    <!-- Stats & Highlight -->
    <div class="row g-4 mb-5">
        <div class="col-md-12">
            <div class="p-4 rounded-4 border-0 shadow-sm glass-card overflow-hidden position-relative" style="background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%); color: white;">
                <div class="position-relative z-1">
                    <h5 class="fw-bold mb-2">Mengapa Berbelanja di Sini?</h5>
                    <p class="opacity-75 mb-0 small" style="max-width: 600px;">Setiap transaksi yang Anda lakukan membantu memperkuat ekonomi keluarga besar alumni SMKN 2 Tetap. Mari kita saling dukung dan maju bersama!</p>
                </div>
                <i class="bi bi-shop position-absolute end-0 top-50 translate-middle-y opacity-10" style="font-size: 15rem; margin-right: -2rem;"></i>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm p-3 mb-5" style="border-radius: 20px;">
        <form action="{{ route('alumni.business.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <select name="category" class="form-select border-0 bg-light rounded-pill px-4" onchange="this.form.submit()">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-7">
                <div class="input-group bg-light rounded-pill px-3">
                    <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-transparent border-0 py-2" placeholder="Cari nama usaha atau produk..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold">CARI</button>
            </div>
        </form>
    </div>

    <!-- Business Grid -->
    <div class="row g-4">
        @forelse($businesses as $biz)
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-lg overflow-hidden transition-all hover-translate-y position-relative" style="border-radius: 25px; background: white;">
                <!-- Discount Badge -->
                @if($biz->discount_info)
                <div class="position-absolute start-0 top-0 m-3 z-3">
                    <div class="bg-danger text-white px-3 py-1 rounded-pill fw-black small shadow-lg animate-pulse">
                        <i class="bi bi-tag-fill me-1"></i> {{ strtoupper($biz->discount_info) }}
                    </div>
                </div>
                @endif

                <div class="position-relative">
                    @if($biz->logo_url)
                        <img src="{{ $biz->logo_url }}" class="card-img-top" alt="{{ $biz->name }}" style="height: 240px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="height: 240px;">
                            <i class="bi bi-shop display-1 opacity-25"></i>
                        </div>
                    @endif
                    <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                        <span class="badge bg-primary rounded-pill px-3 py-2 mb-2" style="font-size: 0.65rem; border: 1px solid rgba(255,255,255,0.2);">{{ strtoupper($biz->category) }}</span>
                        <h4 class="fw-black text-white mb-0">{{ $biz->name }}</h4>
                    </div>
                    
                    @if(auth()->id() == $biz->user_id)
                    <div class="position-absolute top-0 end-0 p-3">
                        <a href="{{ route('alumni.business.edit', $biz->id) }}" class="btn btn-warning btn-sm rounded-circle shadow-sm" title="Edit Usaha">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                    </div>
                    @endif
                </div>

                <div class="card-body p-4">
                    <p class="text-muted small mb-4 line-clamp-3" style="min-height: 60px;">{{ $biz->description }}</p>
                    
                    <div class="d-flex align-items-center mb-4 gap-2">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-circle">
                            <i class="bi bi-geo-alt-fill text-danger fs-5"></i>
                        </div>
                        <span class="small text-dark fw-bold">{{ $biz->location }}</span>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('alumni.business.show', $biz->id) }}" class="btn btn-light w-100 rounded-pill fw-bold btn-sm py-2">DETAIL</a>
                        </div>
                        <div class="col-6">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $biz->whatsapp) }}" target="_blank" class="btn btn-success w-100 rounded-pill fw-bold btn-sm py-2">
                                <i class="bi bi-whatsapp"></i> CHAT
                            </a>
                        </div>
                    </div>
                    
                    @if($biz->website_url)
                    <div class="mt-2">
                        <a href="{{ $biz->website_url }}" target="_blank" class="btn btn-outline-primary w-100 rounded-pill fw-bold btn-sm py-2 border-2">
                            <i class="bi bi-globe me-2"></i> KUNJUNGI WEBSITE
                        </a>
                    </div>
                    @endif
                </div>
                
                <div class="card-footer bg-light border-0 px-4 py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $biz->owner->profile_picture ? asset('storage/'.$biz->owner->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($biz->owner->name) }}" class="rounded-circle border border-2 border-white shadow-sm" width="30" height="30">
                            <div class="lh-1">
                                <div class="text-muted" style="font-size: 0.6rem;">PEMILIK</div>
                                <div class="fw-bold small text-dark">{{ explode(' ', $biz->owner->name)[0] }}</div>
                            </div>
                        </div>
                        <i class="bi bi-patch-check-fill text-primary ms-auto" title="Verified Alumni Business"></i>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5 text-center">
            <div class="bg-white p-5 rounded-5 shadow-sm d-inline-block border-2 border-dashed">
                <i class="bi bi-shop-window display-1 text-muted opacity-10 d-block mb-3"></i>
                <h4 class="fw-black text-dark">WAKTUNYA BERBISNIS!</h4>
                <p class="text-muted mb-4 px-lg-5">Belum ada usaha alumni di kategori ini. Jadilah pelopor dan daftarkan usaha Anda sekarang juga.</p>
                <a href="{{ route('alumni.business.create') }}" class="btn btn-primary rounded-pill px-5 fw-bold">MULAI SEKARANG</a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-5 d-flex justify-content-center">
        {{ $businesses->links() }}
    </div>
</div>

<style>
.glass-card {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;  
    overflow: hidden;
}
.hover-translate-y:hover {
    transform: translateY(-10px);
}
</style>
@endsection
