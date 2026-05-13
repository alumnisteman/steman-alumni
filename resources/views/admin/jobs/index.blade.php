@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="section-title mb-0">Manajemen Lowongan Kerja</h2>
            <p class="text-muted small">Kelola info loker internal dan eksternal</p>
        </div>
        <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Tambah Loker
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Lowongan</th>
                        <th>Perusahaan</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                @if($job->image)
                                    <img src="{{ $job->image }}" class="rounded-3 me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                        <i class="bi bi-briefcase-fill"></i>
                                    </div>
                                @endif
                                <span class="fw-bold">{{ $job->title }}</span>
                            </div>
                        </td>
                        <td>{{ $job->company }}</td>
                        <td>{{ $job->location }}</td>
                        <td>
                            <span class="badge rounded-pill {{ $job->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <form id="delete-job-{{ $job->id }}" action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="window.Guardian.confirmDelete('delete-job-{{ $job->id }}')">
                                    <i class="bi bi-trash me-1" style="pointer-events: none;"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada lowongan yang ditambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jobs->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

