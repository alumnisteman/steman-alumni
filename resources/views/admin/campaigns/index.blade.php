@extends('layouts.admin')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-black text-uppercase mb-0">🏦 Kelola Dana & Fund</h2>
        <p class="text-muted small mb-0">Buat dan atur program donasi Yayasan & Reuni</p>
    </div>
    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-dark rounded-pill px-4 fw-bold">
        <i class="bi bi-plus-circle me-2"></i> Buat Fund Baru
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 rounded-4 mb-4">{{ session('success') }}</div>
@endif

<!-- Foundation Funds -->
<div class="mb-5">
    <div class="d-flex align-items-center gap-3 mb-3">
        <h4 class="fw-black text-uppercase mb-0">💰 Dana Yayasan</h4>
        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Sosial & Abadi</span>
    </div>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Nama Fund</th>
                        <th>Target</th>
                        <th>Terkumpul</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Donasi</th>
                        <th class="pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($foundationCampaigns as $c)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold">{{ $c->title }}</div>
                            @if($c->is_featured) <span class="badge bg-warning text-dark rounded-pill small">Featured</span> @endif
                        </td>
                        <td class="small">Rp {{ number_format($c->goal_amount, 0, ',', '.') }}</td>
                        <td class="fw-bold text-primary">Rp {{ number_format($c->current_amount, 0, ',', '.') }}</td>
                        <td style="min-width: 120px;">
                            <div class="progress rounded-pill" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: {{ min($c->progress, 100) }}%"></div>
                            </div>
                            <small class="text-muted">{{ number_format($c->progress, 1) }}%</small>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 @if($c->status === 'active') bg-success @elseif($c->status === 'completed') bg-primary @else bg-secondary @endif">
                                {{ strtoupper($c->status) }}
                            </span>
                        </td>
                        <td><span class="badge bg-light text-dark rounded-pill">{{ $c->donations_count }} donasi</span></td>
                        <td class="pe-4">
                            <a href="{{ route('admin.campaigns.edit', $c->id) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.campaigns.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus fund ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted small">Belum ada dana yayasan. <a href="{{ route('admin.campaigns.create') }}">Buat sekarang</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Event / Reuni Funds -->
<div class="mb-5">
    <div class="d-flex align-items-center gap-3 mb-3">
        <h4 class="fw-black text-uppercase mb-0">🎉 Dana Reuni</h4>
        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Event-Based</span>
    </div>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Nama Fund</th>
                        <th>Target</th>
                        <th>Terkumpul</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Donasi</th>
                        <th class="pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventCampaigns as $c)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold">{{ $c->title }}</div>
                            @if($c->is_featured) <span class="badge bg-warning text-dark rounded-pill small">Featured</span> @endif
                            @if($c->end_date) <div class="small text-muted">Berakhir: {{ is_string($c->end_date) ? $c->end_date : $c->end_date->format('d M Y') }}</div> @endif
                        </td>
                        <td class="small">Rp {{ number_format($c->goal_amount, 0, ',', '.') }}</td>
                        <td class="fw-bold text-warning">Rp {{ number_format($c->current_amount, 0, ',', '.') }}</td>
                        <td style="min-width: 120px;">
                            <div class="progress rounded-pill" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: {{ min($c->progress, 100) }}%"></div>
                            </div>
                            <small class="text-muted">{{ number_format($c->progress, 1) }}%</small>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 @if($c->status === 'active') bg-success @elseif($c->status === 'completed') bg-primary @else bg-secondary @endif">
                                {{ strtoupper($c->status) }}
                            </span>
                        </td>
                        <td><span class="badge bg-light text-dark rounded-pill">{{ $c->donations_count }} donasi</span></td>
                        <td class="pe-4">
                            <a href="{{ route('admin.campaigns.edit', $c->id) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.campaigns.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus fund ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted small">Belum ada dana reuni. <a href="{{ route('admin.campaigns.create') }}">Buat sekarang</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
