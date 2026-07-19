@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-black mb-1"><i class="bi bi-bag-heart-fill text-warning me-2"></i>Merchandise Resmi</h2>
            <p class="text-muted mb-0">Kelola produk merchandise Alumni STEMAN</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ \Illuminate\Support\Facades\URL::route('admin.merchandise.orders') }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-list-check me-2"></i>Kelola Pesanan
            </a>
            <a href="{{ \Illuminate\Support\Facades\URL::route('admin.merchandise.create') }}" class="btn btn-warning fw-bold rounded-pill px-4">
                <i class="bi bi-plus-lg me-2"></i>Tambah Produk
            </a>
        </div>
    </div>

    @if(\Illuminate\Support\Facades\Session::has('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ \Illuminate\Support\Facades\Session::get('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:20px; overflow:hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">PRODUK</th>
                        <th class="py-3">KATEGORI</th>
                        <th class="py-3">HARGA</th>
                        <th class="py-3">TIPE</th>
                        <th class="py-3">STATUS</th>
                        <th class="py-3 text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($merchandise as $item)
                    @php $categories = \App\Models\Merchandise::getCategories(); @endphp
                    <tr class="{{ $item->trashed() ? 'opacity-50' : '' }}">
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                @if($item->image)
                                    <img src="{{ $item->image }}" alt="" class="rounded-3" style="width:48px;height:48px;object-fit:cover;">
                                @else
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning bg-opacity-10" style="width:48px;height:48px;">
                                        <i class="bi {{ $categories[$item->category]['icon'] ?? 'bi-bag' }} text-warning fs-5"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold">{{ $item->name }}</div>
                                    <code class="small text-muted">{{ $item->slug }}</code>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-warning bg-opacity-15 text-warning rounded-pill px-2">
                                {{ $categories[$item->category]['label'] ?? $item->category }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                            @if($item->price_member)
                                <div class="text-success small">Alumni: Rp {{ number_format($item->price_member, 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <td>
                            @if($item->is_pre_order)
                                <span class="badge bg-warning text-dark rounded-pill px-2">
                                    <i class="bi bi-clock-history me-1"></i>Pre-Order
                                </span>
                            @else
                                <span class="badge bg-success rounded-pill px-2">Ready Stock</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill px-2 {{ $item->trashed() ? 'bg-secondary' : ($item->is_active ? 'bg-success' : 'bg-danger') }}">
                                {{ $item->trashed() ? 'Dihapus' : ($item->is_active ? 'Aktif' : 'Nonaktif') }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            @if(!$item->trashed())
                                <a href="{{ \Illuminate\Support\Facades\URL::route('admin.merchandise.edit', $item) }}" class="btn btn-sm btn-light rounded-pill px-3 me-1">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form id="del-merch-{{ $item->id }}" action="{{ \Illuminate\Support\Facades\URL::route('admin.merchandise.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                        onclick="window.Guardian?.confirmDelete('del-merch-{{ $item->id }}') ?? (confirm('Hapus produk ini?') && document.getElementById('del-merch-{{ $item->id }}').submit())">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-bag-x display-4 d-block mb-2 opacity-30"></i>
                            Belum ada produk merchandise
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
