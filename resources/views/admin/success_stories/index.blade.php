@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-0">Manajemen Jejak Sukses Alumni</h2>
            <p class="text-muted">Kelola cerita inspiratif yang muncul di halaman utama.</p>
        </div>
        <a href="{{ route('admin.success-stories.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i>Tambah Kisah
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Foto</th>
                        <th>Nama & Gelar</th>
                        <th>major & Angkatan</th>
                        <th>Status</th>
                        <th>Urutan</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stories as $story)
                    <tr>
                        <td class="ps-4">
                            <img src="{{ $story->image_path ? asset('storage/'.$story->image_path) : 'https://ui-avatars.com/api/?name='.urlencode($story->name) }}" 
                                 class="rounded-3" width="50" height="50" style="object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold">{{ $story->name }}</div>
                            <small class="text-muted">{{ $story->title }}</small>
                        </td>
                        <td>{{ $story->major_year }}</td>
                        <td>
                            @if($story->is_published)
                                <span class="badge bg-success-subtle text-success px-3 rounded-pill">Published</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-3 rounded-pill">Draft</span>
                            @endif
                        </td>
                        <td>{{ $story->order }}</td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.success-stories.edit', $story->id) }}" class="btn btn-sm btn-outline-primary rounded-start-pill px-3">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form id="delete-story-{{ $story->id }}" action="{{ route('admin.success-stories.destroy', $story->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-end-pill px-3" onclick="window.Guardian.confirmDelete('delete-story-{{ $story->id }}')">
                                        <i class="bi bi-trash" style="pointer-events: none;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada kisah sukses yang ditambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

