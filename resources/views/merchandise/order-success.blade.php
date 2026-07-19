@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card border-0 shadow rounded-4 text-center p-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mx-auto mb-4" style="width:90px;height:90px;">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:3rem;"></i>
                </div>
                <h2 class="fw-black mb-2">Pre-Order Berhasil! 🎉</h2>
                <p class="text-muted mb-4">Terima kasih, pesanan Anda telah kami terima. Tim kami akan segera menghubungi Anda untuk konfirmasi.</p>

                <div class="bg-warning bg-opacity-10 rounded-3 p-4 mb-4 text-start">
                    <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-2 text-warning"></i>Detail Pesanan</h6>
                    <table class="table table-sm table-borderless mb-0 small">
                        <tr>
                            <td class="text-muted py-1" style="width:40%">Kode Pesanan</td>
                            <td class="fw-bold text-dark py-1">{{ $order->order_code }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Produk</td>
                            <td class="fw-semibold py-1">{{ $order->merchandise->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Nama</td>
                            <td class="py-1">{{ $order->buyer_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">No. WhatsApp</td>
                            <td class="py-1">{{ $order->buyer_phone }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Jumlah</td>
                            <td class="py-1">{{ $order->quantity }} pcs</td>
                        </tr>
                        @if($order->size)<tr><td class="text-muted py-1">Ukuran</td><td class="py-1">{{ $order->size }}</td></tr>@endif
                        @if($order->color)<tr><td class="text-muted py-1">Warna</td><td class="py-1">{{ $order->color }}</td></tr>@endif
                        <tr>
                            <td class="text-muted py-1">Total</td>
                            <td class="fw-black text-dark py-1 fs-5">{{ $order->formattedTotal() }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Status</td>
                            <td class="py-1"><span class="badge bg-warning text-dark rounded-pill">Menunggu Konfirmasi</span></td>
                        </tr>
                    </table>
                </div>

                <div class="alert alert-info border-0 rounded-3 text-start small mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Langkah Selanjutnya:</strong><br>
                    Tim Alumni STEMAN akan menghubungi Anda via WhatsApp ke nomor <strong>{{ $order->buyer_phone }}</strong>
                    untuk konfirmasi pesanan dan informasi pembayaran.
                </div>

                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="{{ route('merchandise.index') }}" class="btn btn-dark rounded-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i>Lihat Produk Lain
                    </a>
                    @if($order->merchandise?->whatsapp_contact)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->merchandise->whatsapp_contact) }}?text={{ urlencode('Halo, saya baru melakukan pre-order dengan kode ' . $order->order_code . '. Mohon konfirmasinya.') }}"
                       target="_blank" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-whatsapp me-2"></i>Konfirmasi via WA
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
