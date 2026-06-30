@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid px-4 py-4" style="background: #f0f2f5; min-height: 100vh;">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-black mb-0 text-dark" style="letter-spacing: -1px;">GEOCODING <span class="text-primary">STATUS</span></h1>
            <p class="text-muted mb-0 small">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                Monitor & kelola koordinat GPS alumni — otomatis retry setiap hari jam 03:00 & 10:00
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.guard.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                <i class="bi bi-shield-check me-1"></i> System Guard
            </a>
            @if($stats['pending'] > 0)
            <form method="POST" action="{{ route('admin.geocoding.retry-all') }}"
                  onsubmit="return confirm('Jadwalkan ulang geocoding untuk {{ $stats['pending'] }} alumni yang belum punya koordinat?')">
                @csrf
                <button type="submit" class="btn btn-warning rounded-pill px-3 shadow-sm fw-bold">
                    <i class="bi bi-arrow-repeat me-1"></i> Retry Semua ({{ $stats['pending'] }})
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Validasi gagal:</strong>
        <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Kartu Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.geocoding.index', ['filter' => 'all']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 h-100"
                     style="{{ $filter === 'all' ? 'border:2px solid #0d6efd!important' : '' }}">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Total Alumni</div>
                        <div class="fw-black fs-1 text-dark">{{ number_format($stats['total']) }}</div>
                        <div class="small text-muted">Punya alamat/kota</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.geocoding.index', ['filter' => 'success']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 h-100"
                     style="{{ $filter === 'success' ? 'border:2px solid #198754!important' : 'border-left:4px solid #198754' }}">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Berhasil</div>
                        <div class="fw-black fs-1 text-success">{{ number_format($stats['success']) }}</div>
                        <div class="small text-success">
                            {{ $stats['total'] > 0 ? round($stats['success'] / $stats['total'] * 100) : 0 }}% selesai
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.geocoding.index', ['filter' => 'missing']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 h-100"
                     style="{{ $filter === 'missing' ? 'border:2px solid #ffc107!important' : 'border-left:4px solid #ffc107' }}">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Belum Dicoba</div>
                        <div class="fw-black fs-1 text-warning">{{ number_format($stats['missing']) }}</div>
                        <div class="small text-warning">Perlu dijadwalkan</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.geocoding.index', ['filter' => 'failed']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 h-100"
                     style="{{ $filter === 'failed' ? 'border:2px solid #dc3545!important' : 'border-left:4px solid #dc3545' }}">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Gagal</div>
                        <div class="fw-black fs-1 text-danger">{{ number_format($stats['failed']) }}</div>
                        <div class="small text-danger">Alamat tidak dikenali</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Progress Bar --}}
    @if($stats['total'] > 0)
    @php
        $pct     = round($stats['success'] / $stats['total'] * 100);
        $pctFail = round($stats['failed']  / $stats['total'] * 100);
    @endphp
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold small text-dark">Progress Geocoding Keseluruhan</span>
                <span class="fw-bold small text-primary">{{ number_format($stats['success']) }} / {{ number_format($stats['total']) }} alumni</span>
            </div>
            <div class="progress rounded-pill" style="height: 14px;">
                <div class="progress-bar bg-success" style="width: {{ $pct }}%" title="Berhasil {{ $pct }}%"></div>
                <div class="progress-bar bg-danger"  style="width: {{ $pctFail }}%" title="Gagal {{ $pctFail }}%"></div>
            </div>
            <div class="d-flex gap-3 mt-2">
                <span class="small"><span class="badge bg-success me-1">&nbsp;</span> Berhasil {{ $pct }}%</span>
                <span class="small"><span class="badge bg-danger me-1">&nbsp;</span> Gagal {{ $pctFail }}%</span>
                <span class="small"><span class="badge bg-secondary me-1">&nbsp;</span> Belum {{ 100 - $pct - $pctFail }}%</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Info Otomatis --}}
    <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4 py-2 px-3" style="font-size:0.85rem">
        <i class="bi bi-clock-fill me-2"></i>
        <strong>Auto-retry aktif:</strong> Sistem otomatis mencoba geocoding setiap hari jam <strong>03:00</strong> dan <strong>10:00</strong>.
        Alumni yang gagal akan dicoba ulang setelah <strong>24 jam</strong> tanpa perlu tindakan manual.
        Gunakan tombol <strong>Edit Alamat</strong> jika penulisan alamat perlu diperbaiki agar API bisa mengenalinya.
    </div>

    {{-- Tabel Alumni --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="fw-bold text-dark">
                <i class="bi bi-table me-2 text-primary"></i>
                @if($filter === 'all')     Semua Alumni ({{ $users->total() }})
                @elseif($filter === 'success') Alumni Berhasil ({{ $users->total() }})
                @elseif($filter === 'missing') Belum Dicoba ({{ $users->total() }})
                @elseif($filter === 'failed')  Gagal Geocoding ({{ $users->total() }})
                @elseif($filter === 'pending') Belum Punya Koordinat ({{ $users->total() }})
                @endif
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @foreach(['all' => 'Semua', 'success' => 'Berhasil', 'missing' => 'Belum Dicoba', 'failed' => 'Gagal'] as $key => $label)
                <a href="{{ route('admin.geocoding.index', ['filter' => $key]) }}"
                   class="btn btn-sm rounded-pill {{ $filter === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th class="ps-3 py-3 text-muted small fw-bold">#</th>
                        <th class="py-3 text-muted small fw-bold">ALUMNI</th>
                        <th class="py-3 text-muted small fw-bold">ANGKATAN / JURUSAN</th>
                        <th class="py-3 text-muted small fw-bold">ALAMAT</th>
                        <th class="py-3 text-muted small fw-bold">KOORDINAT</th>
                        <th class="py-3 text-muted small fw-bold">STATUS</th>
                        <th class="py-3 text-muted small fw-bold">TERAKHIR DICOBA</th>
                        <th class="py-3 text-muted small fw-bold text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    @php
                        $hasCoords    = !empty($user->latitude) && !empty($user->longitude);
                        $hasAttempted = !empty($user->geocode_attempted_at);
                        if ($hasCoords) {
                            $statusBadge = '<span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Berhasil</span>';
                        } elseif ($hasAttempted) {
                            $statusBadge = '<span class="badge bg-danger rounded-pill"><i class="bi bi-x-circle me-1"></i>Gagal</span>';
                        } else {
                            $statusBadge = '<span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-clock me-1"></i>Belum Dicoba</span>';
                        }
                    @endphp
                    <tr>
                        <td class="ps-3 text-muted small">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                         class="rounded-circle flex-shrink-0" width="34" height="34"
                                         style="object-fit:cover"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <div class="rounded-circle bg-primary d-none align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                         style="width:34px;height:34px;font-size:14px">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @else
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                         style="width:34px;height:34px;font-size:14px">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold small text-dark" style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                        {{ $user->name }}
                                    </div>
                                    <div class="text-muted" style="font-size:0.7rem">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $user->graduation_year ?? '—' }}</div>
                            <div class="text-muted" style="font-size:0.7rem">{{ Str::limit($user->major ?? '—', 20) }}</div>
                        </td>
                        <td style="max-width:200px">
                            @if($user->address || $user->city_name)
                                <div class="small text-dark" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px"
                                     title="{{ $user->address }}">
                                    {{ $user->address ?? $user->city_name }}
                                </div>
                                @if($user->city_name && $user->address)
                                <div class="text-muted" style="font-size:0.7rem">{{ $user->city_name }}</div>
                                @endif
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($hasCoords)
                                <div class="small text-success fw-semibold">
                                    <i class="bi bi-geo-alt-fill me-1"></i>
                                    {{ number_format($user->latitude, 4) }},<br>
                                    {{ number_format($user->longitude, 4) }}
                                </div>
                            @else
                                <span class="text-muted small"><i class="bi bi-geo me-1"></i>Tidak ada</span>
                            @endif
                        </td>
                        <td>{!! $statusBadge !!}</td>
                        <td>
                            @if($hasAttempted)
                                <div class="small text-dark">{{ $user->geocode_attempted_at->format('d M Y') }}</div>
                                <div class="text-muted" style="font-size:0.7rem">{{ $user->geocode_attempted_at->diffForHumans() }}</div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center" style="min-width:130px">
                            @if(!$hasCoords)
                            <div class="d-flex gap-1 justify-content-center flex-wrap">
                                {{-- Tombol Retry --}}
                                <form method="POST" action="{{ route('admin.geocoding.retry', $user) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-2"
                                            title="Coba geocoding dengan alamat saat ini"
                                            onclick="return confirm('Retry geocoding untuk {{ addslashes($user->name) }}?')">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                                {{-- Tombol Edit Alamat --}}
                                <button type="button"
                                        class="btn btn-sm btn-outline-warning rounded-pill px-2"
                                        title="Edit alamat lalu retry"
                                        onclick="openEditModal(
                                            {{ $user->id }},
                                            '{{ addslashes($user->name) }}',
                                            '{{ addslashes($user->address ?? '') }}',
                                            '{{ addslashes($user->city_name ?? '') }}'
                                        )">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                            </div>
                            @else
                            <div class="d-flex gap-1 justify-content-center">
                                <span class="text-success small" title="Koordinat sudah tersedia">
                                    <i class="bi bi-check-circle-fill"></i>
                                </span>
                                {{-- Tetap izinkan edit alamat jika koordinat perlu diperbarui --}}
                                <button type="button"
                                        class="btn btn-sm btn-outline-secondary rounded-pill px-2"
                                        title="Edit & perbarui koordinat"
                                        onclick="openEditModal(
                                            {{ $user->id }},
                                            '{{ addslashes($user->name) }}',
                                            '{{ addslashes($user->address ?? '') }}',
                                            '{{ addslashes($user->city_name ?? '') }}'
                                        )">
                                    <i class="bi bi-pencil" style="font-size:0.75rem"></i>
                                </button>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-geo-alt display-4 d-block mb-3 opacity-25"></i>
                            @if($filter === 'success')
                                Belum ada alumni yang berhasil di-geocoding.
                            @elseif($filter === 'failed')
                                Tidak ada alumni yang gagal geocoding. 🎉
                            @elseif($filter === 'missing')
                                Semua alumni sudah pernah dicoba geocoding.
                            @else
                                Tidak ada alumni yang ditemukan.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} alumni
            </div>
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    {{-- Penjelasan Status --}}
    <div class="card border-0 shadow-sm rounded-3 mt-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i>Cara Kerja Geocoding Otomatis</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge bg-primary rounded-pill mt-1 flex-shrink-0">1</span>
                        <div>
                            <div class="fw-semibold small">Alumni simpan alamat</div>
                            <div class="text-muted small">Saat alumni update profil, sistem otomatis menjadwalkan geocoding ke background queue.</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge bg-warning text-dark rounded-pill mt-1 flex-shrink-0">2</span>
                        <div>
                            <div class="fw-semibold small">Cron harian 03:00 & 10:00</div>
                            <div class="text-muted small">Scheduler otomatis mencari alumni yang belum atau gagal geocoding, lalu dispatch ulang jobnya.</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge bg-success rounded-pill mt-1 flex-shrink-0">3</span>
                        <div>
                            <div class="fw-semibold small">Berhasil → Peta aktif</div>
                            <div class="text-muted small">Koordinat tersimpan. Alumni muncul di Peta Jaringan Global. System Guard tidak memunculkan alert.</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <span class="badge bg-danger rounded-pill mt-1 flex-shrink-0">!</span>
                        <div>
                            <div class="fw-semibold small">Gagal → Edit Alamat</div>
                            <div class="text-muted small">Jika alamat tidak dikenali API, klik ✏️ <strong>Edit Alamat</strong>, perbaiki penulisannya (lebih spesifik/standar), lalu sistem retry otomatis.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ===== MODAL EDIT ALAMAT ===== --}}
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" id="editAddressForm" action="">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold" id="editAddressModalLabel">
                            <i class="bi bi-pencil-fill text-warning me-2"></i>Edit Alamat Alumni
                        </h5>
                        <p class="text-muted small mb-0" id="editAddressSubtitle"></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="alert alert-warning border-0 rounded-3 py-2 px-3 mb-3" style="font-size:0.82rem">
                        <i class="bi bi-lightbulb-fill me-1"></i>
                        <strong>Tips:</strong> Tulis alamat lebih spesifik dan standar agar API geocoding bisa menemukannya.
                        Contoh: <em>"Kelurahan Kalumpang, Ternate Tengah, Kota Ternate, Maluku Utara"</em>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Alamat Lengkap</label>
                        <textarea name="address" id="editAddressField" class="form-control rounded-3"
                                  rows="3" placeholder="Contoh: Jl. Nuku No. 5, Kelurahan Salero, Ternate Utara, Kota Ternate, Maluku Utara"></textarea>
                        <div class="form-text">Kelurahan/Desa, Kecamatan, Kota, Provinsi — semakin lengkap semakin baik.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kota / Kabupaten</label>
                        <input type="text" name="city_name" id="editCityField" class="form-control rounded-3"
                               placeholder="Contoh: Kota Ternate">
                        <div class="form-text">Digunakan sebagai fallback jika alamat tidak ditemukan.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">
                        <i class="bi bi-geo-alt-fill me-1"></i> Simpan & Geocoding Ulang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(userId, name, address, cityName) {
    document.getElementById('editAddressModalLabel').innerHTML =
        '<i class="bi bi-pencil-fill text-warning me-2"></i>Edit Alamat Alumni';
    document.getElementById('editAddressSubtitle').textContent = name;
    document.getElementById('editAddressField').value = address;
    document.getElementById('editCityField').value    = cityName;

    var baseUrl = '{{ url("system/geocoding") }}';
    document.getElementById('editAddressForm').action = baseUrl + '/' + userId + '/update-address';

    var modal = new bootstrap.Modal(document.getElementById('editAddressModal'));
    modal.show();
}
</script>
@endsection
