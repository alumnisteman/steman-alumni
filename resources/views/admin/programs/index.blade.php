@extends('layouts.admin')

@section('admin-content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Manajemen Program</h2>
            <p class="text-muted">Kelola program beasiswa, mentoring, dan aksi sosial.</p>
        </div>
        <a href="{{ \Illuminate\Support\Facades\URL::route('admin.programs.create') }}" class="btn btn-warning fw-bold px-4 rounded-pill shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>TAMBAH PROGRAM
        </a>
    </div>

    @if(\Illuminate\Support\Facades\Session::has('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            {{ \Illuminate\Support\Facades\Session::get('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">PROGRAM</th>
                        <th class="py-3">SLUG</th>
                        <th class="py-3">STATUS</th>
                        <th class="py-3 text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $program)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3 text-warning">
                                    <i class="bi {{ $program->icon }} fs-4"></i>
                                </div>
                                <div class="fw-bold text-dark">{{ $program->title }}</div>
                            </div>
                        </td>
                        <td><code>{{ $program->slug }}</code></td>
                        <td>
                            <span class="badge bg-{{ $program->status == 'active' ? 'success' : 'secondary' }} rounded-pill px-3">
                                {{ strtoupper($program->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ \Illuminate\Support\Facades\URL::route('admin.programs.edit', $program) }}" class="btn btn-sm btn-light rounded-pill px-3 me-2">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </a>
                             <form id="delete-program-{{ $program->id }}" action="{{ \Illuminate\Support\Facades\URL::route('admin.programs.destroy', $program) }}" method="POST" class="d-inline">
                                 @csrf
                                 @method('DELETE')
                                 <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="window.Guardian.confirmDelete('delete-program-{{ $program->id }}')">
                                     <i class="bi bi-trash me-1" style="pointer-events: none;"></i> Hapus
                                 </button>
                             </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Belum ada program yang ditambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

