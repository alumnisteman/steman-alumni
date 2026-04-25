@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-black text-uppercase mb-1">Moderasi Marketplace</h2>
            <p class="text-muted">Kelola pendaftaran usaha alumni Steman agar tetap berkualitas.</p>
        </div>
        <a href="{{ route('alumni.business.index') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
            <i class="bi bi-shop me-2"></i> Lihat Marketplace
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <!-- Pending Section -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-warning py-3 border-0">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-hourglass-split me-2"></i> Menunggu Persetujuan ({{ $pendingBusinesses->count() }})</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Usaha</th>
                                <th>Pemilik</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingBusinesses as $biz)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $biz->logo_url ?? 'https://dummyimage.com/40' }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold">{{ $biz->name }}</div>
                                                <div class="text-muted small">{{ $biz->location }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $biz->owner->name }}</div>
                                        <div class="text-muted small">Angkatan {{ $biz->owner->graduation_year ?? '-' }}</div>
                                    </td>
                                    <td><span class="badge bg-secondary rounded-pill fw-medium">{{ $biz->category }}</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('alumni.business.show', $biz->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">Review</a>
                                            <form action="{{ route('admin.business.approve', $biz->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">Setujui</button>
                                            </form>
                                            <form action="{{ route('admin.business.reject', $biz->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak pendaftaran ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">Tolak</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-check-circle display-4 d-block mb-3"></i>
                                        Antrean bersih! Tidak ada usaha yang menunggu moderasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Approved Section -->
        <div class="col-lg-12 mt-5">
            <h5 class="fw-bold mb-3">Bisnis Terverifikasi ({{ $approvedBusinesses->total() }})</h5>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Usaha</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approvedBusinesses as $biz)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $biz->logo_url ?? 'https://dummyimage.com/40' }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                            <div class="fw-bold">{{ $biz->name }}</div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success rounded-pill">Tayang</span></td>
                                    <td>
                                        <form action="{{ route('admin.business.reject', $biz->id) }}" method="POST" onsubmit="return confirm('Tarik usaha ini dari publik?')">
                                            @csrf
                                            <button type="submit" class="btn btn-link text-danger p-0">Tarik dari Publik</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3">
                {{ $approvedBusinesses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

