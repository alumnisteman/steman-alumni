@extends('layouts.admin')

@section('admin-content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-0">Manajemen major</h2>
            <p class="text-muted small">Kelola daftar major dan kompetensi keahlian</p>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addMajorModal">
            <i class="bi bi-plus-lg me-2"></i>Tambah major
        </button>
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
                        <th class="ps-4">Nama major</th>
                        <th>Kelompok/Grup</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $currentGroup = ''; @endphp
                    @forelse($majors as $major)
                        @if($currentGroup != $major->group)
                            <tr class="bg-light bg-opacity-50">
                                <td colspan="4" class="ps-4 py-2 small fw-bold text-uppercase text-primary">{{ $major->group }}</td>
                            </tr>
                            @php $currentGroup = $major->group; @endphp
                        @endif
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $major->name }}</td>
                            <td>{{ $major->group }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $major->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($major->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2" 
                                        data-bs-toggle="modal" data-bs-target="#editMajorModal{{ $major->id }}">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </button>
                                <form id="delete-major-{{ $major->id }}" action="{{ route('admin.majors.destroy', $major) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="window.Guardian.confirmDelete('delete-major-{{ $major->id }}')">
                                        <i class="bi bi-trash me-1" style="pointer-events: none;"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Belum ada major yang ditambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals Section -->
@foreach($majors as $major)
<!-- Edit Modal -->
<div class="modal fade" id="editMajorModal{{ $major->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.majors.update', $major) }}" method="POST" class="modal-content border-0 shadow rounded-4">
            @csrf
            @method('PUT')
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Edit major</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama major</label>
                    <input type="text" name="name" class="form-control" value="{{ $major->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Kelompok/Grup</label>
                    <select name="group" class="form-select">
                        <option value="Modern" {{ $major->group == 'Modern' ? 'selected' : '' }}>Modern (Saat Ini)</option>
                        <option value="Legacy" {{ $major->group == 'Legacy' ? 'selected' : '' }}>Legacy (Lama)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ $major->status == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $major->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Add Modal -->
<div class="modal fade" id="addMajorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.majors.store') }}" method="POST" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah major Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama major</label>
                    <input type="text" name="name" class="form-control" required placeholder="Contoh: Teknik Mesin">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Kelompok/Grup</label>
                    <select name="group" class="form-select">
                        <option value="Modern">Modern (Saat Ini)</option>
                        <option value="Legacy">Legacy (Lama)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan major</button>
            </div>
        </form>
    </div>
</div>
@endsection

