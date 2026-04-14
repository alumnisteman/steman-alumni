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
            <div class="card h-100 border-0 shadow-sm overflow-hidden transition-all hover-translate-y" style="border-radius: 20px; background: white;">
                <div class="position-relative">
                    @if($biz->logo_url)
                        <img src="{{ $biz->logo_url }}" class="card-img-top" alt="{{ $biz->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="height: 200px;">
                            <i class="bi bi-shop display-4"></i>
                        </div>
                    @endif
                    <div class="position-absolute top-0 end-0 p-3 d-flex flex-column gap-2">
                        <span class="badge bg-white text-primary rounded-pill fw-bold shadow-sm" style="font-size: 0.7rem;">{{ $biz->category }}</span>
                        @if(auth()->id() == $biz->user_id)
                            <a href="{{ route('alumni.business.edit', $biz->id) }}" class="badge bg-warning text-dark rounded-pill fw-bold shadow-sm text-decoration-none border-0">
                                <i class="bi bi-pencil-square me-1"></i> KELOLA
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-2 text-dark">{{ $biz->name }}</h5>
                    <p class="text-muted small mb-4 line-clamp-2" style="min-height: 40px;">{{ $biz->description }}</p>
                    
                    <div class="d-flex align-items-center mb-4 gap-2">
                        <i class="bi bi-geo-alt-fill text-danger"></i>
                        <span class="small text-muted fw-medium">{{ $biz->location }}</span>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('alumni.business.show', $biz->id) }}" class="btn btn-outline-dark rounded-pill fw-bold btn-sm py-2">DETAIL USAHA</a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $biz->whatsapp) }}?text={{ urlencode('Halo, saya melihat usaha Anda di Portal Alumni Steman. Saya tertarik dengan...') }}" 
                           target="_blank" class="btn btn-success rounded-pill fw-bold btn-sm py-2 shadow-sm">
                            <i class="bi bi-whatsapp me-2"></i> HUBUNGI PEMILIK
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-0">
                    <hr class="opacity-10 mt-0">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ $biz->owner->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($biz->owner->name) }}" class="rounded-circle" width="24" height="24">
                        <span class="text-muted" style="font-size: 0.7rem;">Pemilik: <b class="text-dark">{{ $biz->owner->name }}</b></span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5 text-center">
            <div class="bg-light p-5 rounded-4 d-inline-block border-2 border-dashed border-muted">
                <i class="bi bi-inbox display-1 text-muted opacity-25 d-block mb-3"></i>
                <h4 class="fw-bold text-muted">Belum ada usaha terdaftar</h4>
                <p class="text-muted mb-0">Jadilah yang pertama mempromosikan usaha Anda!</p>
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
