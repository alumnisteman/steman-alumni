@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-black text-dark mb-1">REGISTRASI PROGRAM MASUK</h2>
            <p class="text-muted mb-0">Kelola pendaftaran alumni untuk berbagai program institusi.</p>
        </div>
        <div class="d-flex gap-2">
             <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill border d-flex align-items-center">
                <i class="bi bi-people-fill text-primary me-2"></i> {{ $registrations->total() }} Pendaftar
            </span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <form action="{{ route('admin.registrations.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, email, atau program..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 rounded-3">Filter</button>
            </div>
            @if(request()->anyFilled(['search', 'status']))
            <div class="col-md-2">
                <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-secondary w-100 rounded-3">Reset</a>
            </div>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Alumni</th>
                        <th>Program</th>
                        <th>Kontak</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $reg)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white fw-bold rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        {{ substr($reg->user?->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $reg->user?->name ?? 'User Terhapus' }}</div>
                                        <div class="text-muted small">{{ $reg->user?->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $reg->program?->title ?? 'Program Terhapus' }}</span>
                            </td>
                            <td>{{ $reg->phone_number }}</td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 bg-{{ $reg->status === 'approved' ? 'success' : ($reg->status === 'rejected' ? 'danger' : 'warning text-dark') }}">
                                    {{ strtoupper($reg->status) }}
                                </span>
                            </td>
                            <td>{{ optional($reg->created_at)->format('d/m/Y') ?? '-' }}</td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modal-{{ $reg->id }}">
                                    Review
                                </button>
                            </td>
                        </tr>

                        <!-- Review Modal -->
                        <div class="modal fade" id="modal-{{ $reg->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <div class="modal-header border-0 p-4">
                                        <h5 class="fw-bold mb-0">Review Pendaftaran: {{ $reg->user?->name ?? 'User Terhapus' }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4 pt-0">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <p class="text-muted small fw-bold mb-1">PROGRAM</p>
                                                <h6 class="fw-bold">{{ $reg->program?->title ?? 'Program Terhapus' }}</h6>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <p class="text-muted small fw-bold mb-1">TANGGAL DAFTAR</p>
                                                <h6>{{ optional($reg->created_at)->format('d F Y H:i') ?? '-' }}</h6>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-3 bg-light rounded-4">
                                                    <p class="text-muted small fw-bold mb-2">MOTIVASI & ALASAN</p>
                                                    <p class="mb-0 leading-relaxed text-dark">{{ $reg->motivation }}</p>
                                                </div>
                                            </div>
                                            @if($reg->attachment_path)
                                                <div class="col-12">
                                                    <a href="{{ asset('storage/' . $reg->attachment_path) }}" target="_blank" class="btn btn-outline-dark w-100 rounded-pill">
                                                        <i class="bi bi-file-earmark-pdf me-2"></i> LIHAT LAMPIRAN BERKAS
                                                    </a>
                                                </div>
                                            @endif
                                            
                                            <hr>

                                            <form action="{{ route('admin.registrations.updateStatus', $reg->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Update Status</label>
                                                    <select name="status" class="form-select rounded-3">
                                                        <option value="pending" {{ $reg->status === 'pending' ? 'selected' : '' }}>PENDING</option>
                                                        <option value="approved" {{ $reg->status === 'approved' ? 'selected' : '' }}>APPROVE (SETUJUI)</option>
                                                        <option value="rejected" {{ $reg->status === 'rejected' ? 'selected' : '' }}>REJECT (TOLAK)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label fw-bold">Catatan Admin (Opsional)</label>
                                                    <textarea name="admin_notes" class="form-control rounded-3" rows="3" placeholder="Berikan feedback atau instruksi untuk alumni...">{{ $reg->admin_notes }}</textarea>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary flex-grow-1 py-3 rounded-pill fw-bold">SIMPAN PERUBAHAN</button>
                                                    <button type="button" class="btn btn-light py-3 px-4 rounded-pill fw-bold" data-bs-dismiss="modal">BATAL</button>
                                                </div>
                                            </form>
                                            
                                            <div class="col-12 mt-4 text-center">
                                                <form action="{{ route('admin.registrations.destroy', $reg->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pendaftaran ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger text-decoration-none small">
                                                        <i class="bi bi-trash me-1"></i> Hapus Permanen Data Ini
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="opacity-25 mb-3">
                                    <i class="bi bi-clipboard-x fs-1"></i>
                                </div>
                                <h6 class="text-muted fw-bold">Belum ada pendaftaran masuk</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($registrations->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

