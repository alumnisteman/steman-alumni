@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('merchandise.index') }}" class="text-warning text-decoration-none"><i class="bi bi-bag-heart me-1"></i>Merchandise</a></li>
            <li class="breadcrumb-item active">{{ $item->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        {{-- Left: Gallery --}}
        <div class="col-lg-5">
            @php
                $cat    = $categories[$item->category] ?? ['icon' => 'bi-bag', 'label' => $item->category];
                $allPhotos = array_filter(array_merge(
                    $item->image ? [$item->image] : [],
                    $item->images ?? []
                ));
                $allPhotos = array_values($allPhotos);
            @endphp

            {{-- Main display --}}
            <div class="rounded-4 overflow-hidden shadow mb-3" style="aspect-ratio:1/1; background:#f8f9fa;">
                @if(count($allPhotos))
                    <img id="main-photo" src="{{ $allPhotos[0] }}" alt="{{ $item->name }}"
                         class="w-100 h-100" style="object-fit:cover; transition: opacity .25s;">
                @else
                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center"
                         style="background: linear-gradient(135deg,#1a1a2e,#0f3460); min-height:300px;">
                        <i class="bi {{ $cat['icon'] }} text-warning" style="font-size:5rem;"></i>
                        <span class="text-white-50 mt-2 fw-semibold">{{ $cat['label'] }}</span>
                    </div>
                @endif
            </div>

            {{-- Thumbnails --}}
            @if(count($allPhotos) > 1)
            <div class="d-flex gap-2 flex-wrap">
                @foreach($allPhotos as $i => $photo)
                    <div class="rounded-3 overflow-hidden shadow-sm thumb-btn {{ $i === 0 ? 'thumb-active' : '' }}"
                         style="width:72px;height:72px;cursor:pointer;flex-shrink:0;border:2px solid {{ $i === 0 ? '#ffc107' : 'transparent' }};"
                         data-src="{{ $photo }}" onclick="selectThumb(this)">
                        <img src="{{ $photo }}" class="w-100 h-100" style="object-fit:cover;">
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Status badges --}}
            <div class="d-flex gap-2 mt-3 flex-wrap">
                @if($item->is_pre_order)
                    <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill fs-6">
                        <i class="bi bi-clock-history me-1"></i>PRE-ORDER
                    </span>
                    @if($item->pre_order_close_at)
                        <span class="badge bg-danger text-white fw-semibold px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-calendar-x me-1"></i>Tutup {{ $item->pre_order_close_at->format('d M Y') }}
                        </span>
                    @endif
                @endif
                @if($item->estimated_delivery_at)
                    <span class="badge bg-info text-dark fw-semibold px-3 py-2 rounded-pill fs-6">
                        <i class="bi bi-truck me-1"></i>Est. {{ $item->estimated_delivery_at->format('M Y') }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Right: Info + Form --}}
        <div class="col-lg-7">
            <span class="badge bg-warning bg-opacity-15 text-warning rounded-pill px-3 py-2 mb-2 fw-semibold">
                <i class="bi {{ $cat['icon'] }} me-1"></i>{{ $cat['label'] }}
            </span>
            <h1 class="fw-black mb-2" style="font-size: clamp(1.5rem,4vw,2.2rem);">{{ $item->name }}</h1>

            {{-- Price --}}
            <div class="mb-3">
                <span class="fw-black text-dark" style="font-size:1.8rem;">{{ $item->formattedPrice() }}</span>
                @if($item->price_member)
                    <div class="text-success fw-semibold mt-1">
                        <i class="bi bi-star-fill me-1"></i>{{ $item->formattedPriceMember() }}
                        <span class="text-muted small fw-normal">untuk alumni terverifikasi</span>
                    </div>
                @endif
            </div>

            @if($item->description)
                <p class="text-muted lh-lg mb-4">{{ $item->description }}</p>
            @endif

            @if($item->isOrderable())
            {{-- Order Form --}}
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-cart-plus me-2 text-warning"></i>
                    {{ $item->is_pre_order ? 'Form Pre-Order' : 'Form Pemesanan' }}
                </h5>

                @if(session('success'))
                    <div class="alert alert-success rounded-3 border-0">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger rounded-3 border-0">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('merchandise.order', $item->slug) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="buyer_name" class="form-control rounded-3 @error('buyer_name') is-invalid @enderror"
                                value="{{ old('buyer_name', auth()->user()?->name) }}" placeholder="Nama Anda" required>
                            @error('buyer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. WhatsApp <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-whatsapp text-success"></i></span>
                                <input type="text" name="buyer_phone" class="form-control rounded-end-3 @error('buyer_phone') is-invalid @enderror"
                                    value="{{ old('buyer_phone') }}" placeholder="08xxxxxxxxxx" required>
                            </div>
                            @error('buyer_phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="buyer_email" class="form-control rounded-3 @error('buyer_email') is-invalid @enderror"
                                value="{{ old('buyer_email', auth()->user()?->email) }}" placeholder="email@contoh.com">
                            @error('buyer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat Pengiriman <span class="text-danger">*</span></label>
                            <textarea name="buyer_address" class="form-control rounded-3 @error('buyer_address') is-invalid @enderror"
                                rows="2" placeholder="Jalan, No. Rumah, Kelurahan, Kecamatan, Kota, Kode Pos" required>{{ old('buyer_address') }}</textarea>
                            @error('buyer_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @if(!empty($item->sizes))
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ukuran</label>
                            <select name="size" class="form-select rounded-3">
                                <option value="">-- Pilih Ukuran --</option>
                                @foreach($item->sizes as $size)
                                    <option value="{{ $size }}" {{ old('size') == $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        @if(!empty($item->colors))
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Warna</label>
                            <select name="color" class="form-select rounded-3">
                                <option value="">-- Pilih Warna --</option>
                                @foreach($item->colors as $color)
                                    <option value="{{ $color }}" {{ old('color') == $color ? 'selected' : '' }}>{{ $color }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="qty-input"
                                class="form-control rounded-3 @error('quantity') is-invalid @enderror"
                                value="{{ old('quantity', $item->min_order) }}" min="{{ $item->min_order }}" required>
                            @if($item->min_order > 1)
                                <div class="form-text">Minimal {{ $item->min_order }} pcs</div>
                            @endif
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan Tambahan</label>
                            <textarea name="custom_note" class="form-control rounded-3" rows="2"
                                placeholder="Permintaan khusus, nama untuk print, dll...">{{ old('custom_note') }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-warning border-0 rounded-3 mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold"><i class="bi bi-calculator me-2"></i>Estimasi Total</span>
                                    <span class="fw-black fs-5" id="price-preview">{{ $item->formattedPrice() }}</span>
                                </div>
                                <small class="text-muted d-block mt-1">Belum termasuk ongkos kirim</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold fs-6">
                                <i class="bi bi-send-fill me-2"></i>
                                {{ $item->is_pre_order ? 'Kirim Pre-Order' : 'Pesan Sekarang' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @else
                <div class="alert alert-secondary rounded-4 border-0 py-4 text-center">
                    <i class="bi bi-clock-history fs-1 text-muted d-block mb-2"></i>
                    <strong>Pemesanan Belum Dibuka</strong>
                    <p class="mb-0 text-muted small mt-1">
                        @if($item->pre_order_open_at && now()->lt($item->pre_order_open_at))
                            Pre-order akan dibuka pada {{ $item->pre_order_open_at->format('d M Y') }}
                        @elseif($item->pre_order_close_at && now()->gt($item->pre_order_close_at))
                            Periode pre-order telah berakhir
                        @else
                            Produk ini sedang tidak tersedia untuk dipesan
                        @endif
                    </p>
                </div>
            @endif

            @if($item->whatsapp_contact)
            <div class="mt-3 text-center">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->whatsapp_contact) }}?text={{ urlencode('Halo, saya ingin menanyakan produk ' . $item->name) }}"
                   target="_blank" class="btn btn-outline-success rounded-pill px-4">
                    <i class="bi bi-whatsapp me-2"></i>Tanya via WhatsApp
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Gallery thumbnail switcher
function selectThumb(el) {
    document.getElementById('main-photo').style.opacity = '0';
    setTimeout(() => {
        document.getElementById('main-photo').src = el.dataset.src;
        document.getElementById('main-photo').style.opacity = '1';
    }, 150);
    document.querySelectorAll('.thumb-btn').forEach(t => {
        t.style.borderColor = 'transparent';
        t.classList.remove('thumb-active');
    });
    el.style.borderColor = '#ffc107';
    el.classList.add('thumb-active');
}

// Price calculator
(function() {
    @php
        $isVerifiedAlumni = auth()->check()
            && auth()->user()->status === 'approved'
            && auth()->user()->role === 'alumni';
    @endphp
    const priceEff = {{ ($isVerifiedAlumni && $item->price_member) ? $item->price_member : $item->price }};
    const qtyInput = document.getElementById('qty-input');
    const preview  = document.getElementById('price-preview');
    if (!qtyInput || !preview) return;
    const fmt = n => 'Rp ' + n.toLocaleString('id-ID');
    qtyInput.addEventListener('input', () => {
        preview.textContent = fmt(priceEff * (parseInt(qtyInput.value) || 1));
    });
})();
</script>
@endsection
