@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black mb-1"> VERIFIKASI ALUMNI</h2>
            <p class="text-muted">Tinjau dan setujui pendaftaran alumni baru untuk memberikan akses penuh.</p>
        </div>
        <div class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
            <i class="bi bi-clock-history me-1"></i> {{ $users->total() }} Menunggu Persetujuan
        </div>
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
                        <th class="ps-4 py-3">Alumni</th>
                        <th class="py-3">Data Akademik</th>
                        <th class="py-3">Waktu Daftar</th>
                        <th class="text-end pe-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->foto_profil ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=6366f1&color=fff' }}" 
                                         class="rounded-circle me-3" style="width: 45px; height: 45px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="badge bg-primary bg-opacity-10 text-primary mb-1" style="width: fit-content;">{{ $user->jurusan ?? '-' }}</span>
                                    <div class="small">
                                        <span class="text-muted">NISN:</span> {{ $user->nisn ?? '-' }} 
                                        <span class="text-muted ms-2">Lulus:</span> {{ $user->tahun_lulus ?? '-' }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark small">{{ $user->created_at->format('d M Y') }}</div>
                                <div class="text-muted x-small">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <form action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-bold">
                                            <i class="bi bi-check-lg me-1"></i> Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-person-check display-4 text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">Antrean kosong. Semua alumni sudah terverifikasi!</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $users->links() }}
    </div>
</div>

<style>
.fw-black { font-weight: 900; letter-spacing: -1px; }
.x-small { font-size: 0.75rem; }
.hover-lift { transition: transform 0.2s; }
.hover-lift:hover { transform: translateY(-3px); }
</style>
@endsection
