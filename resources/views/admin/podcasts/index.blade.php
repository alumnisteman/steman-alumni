@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Kelola Podcast Alumni</h2>
        <a href="{{ route('admin.podcasts.create') }}" class="btn btn-alumni_smkn2 shadow-sm rounded-0 px-4">
            <i class="bi bi-plus-lg me-2"></i>Tambah Podcast
        </a>
    </div>

    @if(session('success')) <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div> @endif

    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4">TANGGAL</th>
                        <th>JUDUL</th>
                        <th>TAMU</th>
                        <th>KATEGORI</th>
                        <th>DURASI</th>
                        <th>STATUS</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($podcasts as $item)
                    <tr>
                        <td class="ps-4 small text-muted">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td><span class="fw-bold text-dark">{{ Str::limit($item->title, 50) }}</span></td>
                        <td>{{ $item->guest_name }}</td>
                        <td><span class="badge bg-light text-dark shadow-sm px-3 rounded-pill">{{ strtoupper($item->category) }}</span></td>
                        <td>{{ $item->duration }}</td>
                        <td>
                            @if($item->is_published)
                                <span class="badge bg-success rounded-pill px-3">Published</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3">Draft</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.podcasts.edit', $item->id) }}" class="btn btn-sm btn-light border shadow-sm rounded-circle" title="Edit">
                                    <i class="bi bi-pencil-square text-primary"></i>
                                </a>

                                <form id="delete-podcast-{{ $item->id }}" action="{{ route('admin.podcasts.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-light border shadow-sm rounded-circle" title="Hapus" 
                                            onclick="window.Guardian.confirmDelete('delete-podcast-{{ $item->id }}')"
                                            style="position: relative; z-index: 5;">
                                        <i class="bi bi-trash text-danger" style="pointer-events: none;"></i>
                                    </button>
                                </form>

                                <a href="{{ route('podcasts.show', $item->slug) }}" target="_blank" class="btn btn-sm btn-light border shadow-sm rounded-circle" title="Lihat">
                                    <i class="bi bi-box-arrow-up-right text-dark"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-mic d-block fs-3 mb-2"></i> Belum ada podcast.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($podcasts->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $podcasts->links() }}
    </div>
    @endif
</div>
@endsection
