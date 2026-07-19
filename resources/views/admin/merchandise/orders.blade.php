@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-black mb-1"><i class="bi bi-list-check text-warning me-2"></i>Pesanan Merchandise</h2>
            <p class="text-muted mb-0">Kelola semua pre-order dan pesanan merchandise</p>
        </div>
        <a href="{{ \Illuminate\Support\Facades\URL::route('admin.merchandise.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Produk
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Cari</label>
                <input type="text" name="search" class="form-control rounded-3" value="{{ request('search') }}" placeholder="Kode, nama, atau nomor HP...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select rounded-3">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark rounded-pill w-100">Filter</button>
            </div>
        </div>
    </form>

    @if(\Illuminate\Support\Facades\Session::has('success'))
        <div class="alert alert-success border-0 rounded-3 mb-4">{{ \Illuminate\Support\Facades\Session::get('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:20px; overflow:hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">KODE PESANAN</th>
                        <th class="py-3">PEMESAN</th>
                        <th class="py-3">PRODUK</th>
                        <th class="py-3">QTY</th>
                        <th class="py-3">TOTAL</th>
                        <th class="py-3">STATUS</th>
                        <th class="py-3">TANGGAL</th>
                        <th class="py-3 text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="ps-4">
                            <code class="fw-bold text-dark">{{ $order->order_code }}</code>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $order->buyer_name }}</div>
                            <div class="small text-muted">
                                <i class="bi bi-whatsapp text-success me-1"></i>{{ $order->buyer_phone }}
                            </div>
                        </td>
                        <td>
                            <div class="small">{{ $order->merchandise->name ?? '-' }}</div>
                            @if($order->size || $order->color)
                                <div class="small text-muted">{{ implode(' / ', array_filter([$order->size, $order->color])) }}</div>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary rounded-pill">{{ $order->quantity }}x</span></td>
                        <td class="fw-bold">{{ $order->formattedTotal() }}</td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $order->status_badge }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="text-end pe-4">
                            {{-- Update Status Modal trigger --}}
                            <button class="btn btn-sm btn-light rounded-pill px-3" type="button"
                                data-bs-toggle="modal" data-bs-target="#modal-order-{{ $order->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- Status Update Modal --}}
                    <div class="modal fade" id="modal-order-{{ $order->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 shadow">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">Update Pesanan #{{ $order->order_code }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ \Illuminate\Support\Facades\URL::route('admin.merchandise.orders.update', $order) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <div class="modal-body">
                                        <div class="mb-3 p-3 bg-light rounded-3 small">
                                            <strong>{{ $order->buyer_name }}</strong><br>
                                            {{ $order->merchandise->name ?? '-' }} &times; {{ $order->quantity }}<br>
                                            <span class="text-success fw-bold">{{ $order->formattedTotal() }}</span><br>
                                            <i class="bi bi-whatsapp text-success"></i> {{ $order->buyer_phone }}<br>
                                            {{ $order->buyer_address }}
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status</label>
                                            <select name="status" class="form-select rounded-3">
                                                @foreach($statuses as $s)
                                                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                                        {{ ucfirst($s) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label fw-semibold">Catatan Admin</label>
                                            <textarea name="admin_note" class="form-control rounded-3" rows="2"
                                                placeholder="Nomor resi, info pembayaran, dll...">{{ $order->admin_note }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-4 d-block mb-2 opacity-30"></i>
                            Belum ada pesanan masuk
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="p-4">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection
