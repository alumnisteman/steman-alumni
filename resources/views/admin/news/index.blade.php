@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Kelola Berita & Cerita</h2>
        <a href="/admin/news/create" class="btn btn-alumni_smkn2 shadow-sm rounded-0 px-4">
            <i class="bi bi-plus-lg me-2"></i>Tulis Berita
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
                        <th>KATEGORI</th>
                        <th>STATUS</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news as $item)
                    <tr>
                        <td class="ps-4 small text-muted">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td><span class="fw-bold text-dark">{{ Str::limit($item->title, 100) }}</span></td>
                        <td><span class="badge bg-light text-dark shadow-sm px-3 rounded-pill">{{ $item->category }}</span></td>
                        <td>
                            @if($item->status === 'published')
                                <span class="badge bg-success rounded-pill px-3">Published</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3">Draft</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-link link-dark shadow-none" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                    <li><a class="dropdown-item" href="/news/{{ $item->slug }}" target="_blank">Lihat</a></li>
                                    <li><a class="dropdown-item" href="/admin/news/{{ $item->id }}/edit">Edit</a></li>
                                    <li>
                                        <form action="/admin/news/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus berita ini?')">
                                            @csrf @method('DELETE')
                                            <button class="dropdown-item text-danger">Hapus</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x d-block fs-3 mb-2"></i> Belum ada berita/artikel.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($news->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $news->links() }}
    </div>
    @endif
</div>
@endsection
