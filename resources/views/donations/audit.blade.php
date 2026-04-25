@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-lg-8">
            <h1 class="fw-black text-uppercase tracking-wider mb-2">📜 PUBLIC AUDIT LOG</h1>
            <p class="text-muted lead">Sistem Transparansi Dana Alumni - Catatan transaksi bersifat immutable dan tidak dapat diubah.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="bg-dark text-white p-4 shadow-lg rounded-4 border-start border-5 border-primary">
                <p class="small opacity-50 mb-0 font-weight-bold">Status Audit</p>
                <h4 class="fw-black text-uppercase mb-0"><i class="bi bi-shield-check text-success me-2"></i> VERIFIED</h4>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-5 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-black opacity-50">Waktu</th>
                        <th class="py-3 text-uppercase small fw-black opacity-50">Aksi</th>
                        <th class="py-3 text-uppercase small fw-black opacity-50">Pelaku</th>
                        <th class="py-3 text-uppercase small fw-black opacity-50">Integrity Hash</th>
                        <th class="py-3 text-uppercase small fw-black opacity-50">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td class="ps-4">
                            <span class="small fw-bold d-block">{{ $log->created_at->format('d M Y') }}</span>
                            <span class="small text-muted">{{ $log->created_at->format('H:i:s') }} WIB</span>
                        </td>
                        <td>
                            <span class="badge bg-dark rounded-pill px-3 py-2 small text-uppercase">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span class="fw-bold small">{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td>
                            <code>{{ substr($log->hash, 0, 16) }}...</code>
                            <button class="btn btn-sm btn-link p-0 ms-1" onclick="alert('Full Hash: {{ $log->hash }}')" title="View Full Hash">
                                <i class="bi bi-info-circle"></i>
                            </button>
                        </td>
                        <td>
                            <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill me-1"></i> IMMUTABLE</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>

    <!-- Integrity Disclaimer -->
    <div class="mt-5 p-4 bg-light rounded-4 border-start border-5 border-info">
        <div class="d-flex gap-3">
            <i class="bi bi-patch-check-fill text-info display-6"></i>
            <div>
                <h6 class="fw-black text-uppercase">Integrity Check</h6>
                <p class="small text-muted mb-0">Setiap catatan audit dilengkapi dengan <b>SHA-256 Hashing Algorithm</b>. Jika ada data yang diubah secara ilegal di database, hash tidak akan sinkron dan sistem akan memberikan peringatan otomatis. Kami menjamin akurasi data 100%.</p>
            </div>
        </div>
    </div>
</div>
@endsection
