@extends('layouts.app')
@section('content')
<div class="card border-0 shadow">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Daftar Alumni {{ setting('site_name', 'IKATAN ALUMNI SMKN 2') }}</h4>
            <form action="/alumni" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari nama/jurusan..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light text-nowrap">
                    <tr>
                        <th>NAMA</th>
                        <th>JURUSAN</th>
                        <th>ANGKATAN</th>
                        <th>PEKERJAAN</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alumni as $user)
                    <tr>
                        <td class="fw-bold"><a href="/alumni/{{ $user->id }}" class="text-dark text-decoration-none">{{ $user->name }}</a></td>
                        <td>{{ $user->jurusan ?? '-' }}</td>
                        <td>{{ $user->tahun_lulus ?? '-' }}</td>
                        <td>{{ $user->pekerjaan_sekarang ?? 'Belum terisi' }}</td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                <i class="bi bi-patch-check-fill me-1"></i> Verified
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-person-x d-block fs-3 mb-2"></i> Belum ada alumni terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $alumni->links() }}
        </div>
    </div>
</div>
@endsection