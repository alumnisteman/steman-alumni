@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid px-4 py-4">
    
    <!-- Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black text-dark mb-1 text-uppercase">MODERASI DIGITAL MUSEUM</h2>
            <p class="text-muted">Review, setujui, atau hapus kiriman arsip bersejarah dari alumni.</p>
        </div>
        <div>
            <a href="{{ route('museum.index') }}" class="btn btn-warning fw-bold rounded-pill px-4" target="_blank">
                <i class="bi bi-eye-fill me-2"></i>Lihat Museum Publik
            </a>
        </div>
    </div>

    <!-- Alert status -->
    @if(session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Card & Table -->
    <div class="card border-0 shadow-lg rounded-5 bg-white p-4">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">Pratinjau</th>
                        <th>Judul Arsip</th>
                        <th>Kategori</th>
                        <th>Era</th>
                        <th>Disumbang Oleh</th>
                        <th>Pengirim</th>
                        <th>Status</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                @if($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="Preview" class="rounded border" style="width: 60px; height: 45px; object-fit: cover;">
                                @elseif($item->youtube_embed_id)
                                    <div class="position-relative" style="width: 60px; height: 45px;">
                                        <img src="https://img.youtube.com/vi/{{ $item->youtube_embed_id }}/mqdefault.jpg" alt="YT Preview" class="rounded border w-100 h-100" style="object-fit: cover;">
                                        <div class="position-absolute top-50 start-50 translate-middle text-danger small"><i class="bi bi-play-btn-fill"></i></div>
                                    </div>
                                @else
                                    <div class="bg-light rounded border text-center fs-4" style="width: 60px; height: 45px; line-height: 40px;">
                                        {{ $item->category_icon }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->title }}</div>
                                <div class="text-muted small text-truncate" style="max-width: 300px;" title="{{ $item->description }}">
                                    {{ $item->description }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $item->category_icon }} {{ $item->category_label }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold">{{ $item->era_year ?: '-' }}</span>
                            </td>
                            <td>
                                <span class="text-primary">{{ $item->donated_by ?: '-' }}</span>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $item->uploader->name }}</div>
                                <div class="x-small text-muted">{{ $item->created_at->format('d M Y') }}</div>
                            </td>
                            <td>
                                @if($item->status === 'pending')
                                    <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill">PENDING</span>
                                @elseif($item->status === 'approved')
                                    <span class="badge bg-success px-3 py-1.5 rounded-pill">APPROVED</span>
                                @else
                                    <span class="badge bg-danger px-3 py-1.5 rounded-pill">REJECTED</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    @if($item->status === 'pending')
                                        <form action="{{ route('admin.museum.approve', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm rounded-circle" title="Setujui" style="width: 32px; height: 32px; padding: 0;">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.museum.reject', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm rounded-circle" title="Tolak" style="width: 32px; height: 32px; padding: 0;">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <button class="btn btn-outline-danger btn-sm rounded-circle" title="Hapus Permanen" style="width: 32px; height: 32px; padding: 0;"
                                            onclick="confirmDelete({{ $item->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('admin.museum.destroy', $item) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="display-6">🏛️</div>
                                <h5 class="mt-3 text-muted">Belum ada kiriman arsip di sistem</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus arsip ini?',
        text: "Arsip dan berkas medianya akan dihapus secara permanen dari server!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#475569',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endsection
