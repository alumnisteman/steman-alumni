@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-0">Manajemen Iklan</h2>
            <p class="text-muted small">Kelola banner dan posisi iklan di website</p>
        </div>
        <a href="{{ route('admin.ads.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Tambah Iklan
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
                        <th class="ps-4">Preview</th>
                        <th>Judul Iklan</th>
                        <th>Total Klik</th>
                        <th>Posisi</th>
                        <th>Masa Tayang</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ads as $ad)
                    <tr>
                        <td class="ps-4">
                            <img src="{{ $ad->image_desktop }}" class="rounded-3 shadow-sm" style="width: 80px; height: 45px; object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold">{{ $ad->title }}</div>
                            <small class="text-muted d-block text-truncate" style="max-width: 200px;">{{ $ad->link }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">
                                <i class="bi bi-mouse me-1"></i> {{ number_format($ad->click) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info fw-bold text-uppercase px-3 rounded-pill">
                                {{ $ad->position }}
                            </span>
                        </td>
                        <td>
                            @if($ad->start_date || $ad->end_date)
                                <div class="small">
                                    {{ $ad->start_date ? $ad->start_date->format('d/m/Y') : '∞' }} - 
                                    {{ $ad->end_date ? $ad->end_date->format('d/m/Y') : '∞' }}
                                </div>
                            @else
                                <span class="text-muted small">Selamanya</span>
                            @endif
                        </td>
                        <td>
                            @if($ad->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Aktif</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Non-aktif</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.ads.edit', $ad) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Hapus iklan ini?')">
                                    <i class="bi bi-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada iklan yang ditambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ads->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $ads->links() }}
            </div>
        @endif
    </div>
@endsection
