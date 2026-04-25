@extends('layouts.admin')

@section('admin-content')
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
                            <div class="d-flex justify-content-end gap-2">
                                <form action="{{ route('admin.news.toggle', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light border shadow-sm rounded-pill px-3" 
                                            title="{{ $item->status === 'published' ? 'Kembalikan ke Draft' : 'Terbitkan' }}">
                                        @if($item->status === 'published')
                                            <i class="bi bi-eye-slash text-secondary me-1"></i> Draft
                                        @else
                                            <i class="bi bi-cloud-upload text-success me-1"></i> Publish
                                        @endif
                                    </button>
                                </form>

                                <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-sm btn-light border shadow-sm rounded-circle" title="Edit">
                                    <i class="bi bi-pencil-square text-primary"></i>
                                </a>

                                <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus berita ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border shadow-sm rounded-circle" title="Hapus">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </form>

                                <a href="/news/{{ $item->slug }}" target="_blank" class="btn btn-sm btn-light border shadow-sm rounded-circle" title="Lihat">
                                    <i class="bi bi-box-arrow-up-right text-dark"></i>
                                </a>
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

