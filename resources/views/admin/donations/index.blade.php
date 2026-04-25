@extends('layouts.admin')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-black text-uppercase mb-0">💰 Verifikasi Donasi</h2>
        <p class="text-muted small mb-0">Kelola dan verifikasi semua donasi masuk</p>
    </div>
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-dark rounded-pill px-4 fw-bold">
        <i class="bi bi-bank me-2"></i> Kelola Dana & Fund
    </a>
</div>

<!-- Fund Summary Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10 border-start border-4 border-warning">
            <div class="card-body p-4">
                <p class="small text-muted mb-1 fw-bold text-uppercase">⏳ Menunggu Verifikasi</p>
                <h3 class="fw-black text-warning mb-0">{{ $stats['pending'] }}</h3>
                <p class="small text-muted mb-0">Donasi pending</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
            <div class="card-body p-4">
                <p class="small text-muted mb-1 fw-bold text-uppercase">💰 Dana Yayasan</p>
                <h3 class="fw-black text-primary mb-0">Rp {{ number_format($stats['foundation'], 0, ',', '.') }}</h3>
                <p class="small text-muted mb-0">Total terverifikasi</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10 border-start border-4 border-success">
            <div class="card-body p-4">
                <p class="small text-muted mb-1 fw-bold text-uppercase">🎉 Dana Reuni</p>
                <h3 class="fw-black text-success mb-0">Rp {{ number_format($stats['event'], 0, ',', '.') }}</h3>
                <p class="small text-muted mb-0">Total terverifikasi</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold small">Jenis Dana</label>
                <select name="type" class="form-select rounded-3 border-0 bg-light">
                    <option value="">Semua Jenis</option>
                    <option value="foundation" {{ request('type') === 'foundation' ? 'selected' : '' }}>💰 Dana Yayasan</option>
                    <option value="event" {{ request('type') === 'event' ? 'selected' : '' }}>🎉 Dana Reuni</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">Status</label>
                <select name="status" class="form-select rounded-3 border-0 bg-light">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold w-100">
                    <i class="bi bi-funnel me-2"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 rounded-4 mb-4">{{ session('success') }}</div>
@endif

<!-- Donations Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Alumni</th>
                    <th>Fund / Campaign</th>
                    <th>Jenis</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Bukti</th>
                    <th class="pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donations as $donation)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold">{{ $donation->user->name ?? 'N/A' }}</div>
                        <div class="small text-muted">{{ $donation->is_anonymous ? '(Anonim di Publik)' : 'Nama ditampilkan' }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold small">{{ $donation->campaign->title ?? '—' }}</div>
                    </td>
                    <td>
                        @if(($donation->campaign->type ?? '') === 'foundation')
                            <span class="badge bg-primary bg-opacity-15 text-primary rounded-pill px-3">💰 Yayasan</span>
                        @elseif(($donation->campaign->type ?? '') === 'event')
                            <span class="badge bg-warning bg-opacity-15 text-warning rounded-pill px-3">🎉 Reuni</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-15 text-secondary rounded-pill px-3">—</span>
                        @endif
                    </td>
                    <td class="fw-bold">Rp {{ number_format($donation->amount, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge rounded-pill px-3 py-2 
                            @if($donation->status == 'pending') bg-warning text-dark
                            @elseif($donation->status == 'verified') bg-success
                            @else bg-danger @endif">
                            {{ strtoupper($donation->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ asset('storage/' . $donation->proof_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-eye me-1"></i> Lihat
                        </a>
                    </td>
                    <td class="pe-4">
                        @if($donation->status == 'pending')
                        <button class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $donation->id }}">
                            <i class="bi bi-check-circle me-1"></i> Verifikasi
                        </button>
                        @else
                        <span class="text-muted small">Selesai</span>
                        @endif
                    </td>
                </tr>

                <!-- Verification Modal -->
                <div class="modal fade" id="verifyModal{{ $donation->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">Update Status Donasi #{{ $donation->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.donations.verify', $donation->id) }}" method="POST">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="alert alert-light border rounded-3 small mb-3">
                                        <strong>Alumni:</strong> {{ $donation->user->name ?? 'N/A' }}<br>
                                        <strong>Fund:</strong> {{ $donation->campaign->title ?? '—' }} ({{ $donation->campaign->type === 'foundation' ? 'Yayasan' : 'Reuni' }})<br>
                                        <strong>Nominal:</strong> Rp {{ number_format($donation->amount, 0, ',', '.') }}
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Keputusan</label>
                                        <select name="status" class="form-select rounded-3">
                                            <option value="verified">✅ Verified (Uang Sudah Masuk)</option>
                                            <option value="rejected">❌ Rejected (Bukti Tidak Valid)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Catatan Admin (Opsional)</label>
                                        <textarea name="admin_notes" class="form-control rounded-3" rows="3" placeholder="Catatan untuk donor..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Simpan Keputusan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-4 d-block mb-2 opacity-25"></i>
                        Tidak ada donasi ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white p-4">
        {{ $donations->links() }}
    </div>
</div>
@endsection
