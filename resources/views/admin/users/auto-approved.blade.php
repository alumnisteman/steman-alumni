@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black mb-1"><i class="bi bi-person-check-fill text-success me-2"></i>Alumni Auto-Approved</h2>
            <p class="text-muted">Daftar alumni yang langsung aktif saat mendaftar (tanpa perlu persetujuan manual).</p>
        </div>
        <div class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fs-6">
            <i class="bi bi-lightning-fill me-1"></i> {{ $total }} Alumni
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Alumni</th>
                        <th class="py-3">Data Akademik</th>
                        <th class="py-3">Kota</th>
                        <th class="py-3">Waktu Daftar</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="text-end pe-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=10b981&color=fff' }}"
                                         class="rounded-circle me-3" style="width: 44px; height: 44px; object-fit: cover;" loading="lazy">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-1 d-block" style="width: fit-content;">{{ $user->major ?? '-' }}</span>
                                <div class="small text-muted">
                                    NISN: {{ $user->nisn ?? '-' }} &bull; Lulus: {{ $user->graduation_year ?? '-' }}
                                </div>
                            </td>
                            <td class="small text-muted">{{ $user->city_name ?? '-' }}</td>
                            <td>
                                <div class="text-dark small">{{ $user->created_at->format('d M Y, H:i') }}</div>
                                <div class="text-muted x-small">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill {{ $user->is_active ? 'bg-success' : 'bg-secondary' }} px-3">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('alumni.show', $user->id) }}" target="_blank"
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i>Profil
                                    </a>
                                    <form action="{{ route('admin.users.toggleActive', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                                class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-pill px-3">
                                            <i class="bi {{ $user->is_active ? 'bi-pause-fill' : 'bi-play-fill' }} me-1"></i>
                                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-person-plus display-4 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">Belum ada alumni yang mendaftar secara otomatis.</h5>
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

    <div class="alert alert-info border-0 rounded-4 mt-4 d-flex align-items-start gap-3">
        <i class="bi bi-info-circle-fill fs-5 mt-1 flex-shrink-0"></i>
        <div>
            <strong>Tentang Auto-Approve:</strong><br>
            Sejak fitur ini diaktifkan, setiap alumni baru yang mendaftar langsung mendapat status <strong>Aktif</strong> dan bisa login tanpa menunggu persetujuan manual admin.
            Anda masih bisa menonaktifkan akun individual jika diperlukan.
        </div>
    </div>
@endsection

@push('styles')
<style>.x-small { font-size: 0.72rem; }</style>
@endpush
