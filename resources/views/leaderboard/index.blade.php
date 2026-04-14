@extends('layouts.app')

@section('content')
<div class="leaderboard-header text-center py-5 mb-5" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white;">
    <div class="container py-4">
        <h6 class="text-warning fw-bold text-uppercase mb-2">Hall of Fame</h6>
        <h1 class="display-4 fw-black mb-3">PAHLAWAN ALUMNI</h1>
        <p class="lead opacity-75 mx-auto" style="max-width: 700px;">Apresiasi untuk alumni paling aktif yang berkontribusi dalam diskusi dan kemajuan komunitas Steman.</p>
    </div>
</div>

<div class="container mb-5">
    {{-- Podium Section --}}
    <div class="row align-items-end justify-content-center mb-5 gx-4 podium-section">
        {{-- Rank 2 --}}
        @if($podium->count() >= 2)
        <div class="col-md-3 text-center order-2 order-md-1">
            <div class="podium-card silver p-4 mb-3 border-0 shadow-lg position-relative">
                <div class="rank-badge silver">2</div>
                <img src="{{ $podium[1]->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($podium[1]->name).'&background=cbd5e1&color=0f172a' }}" 
                     class="rounded-circle border border-4 border-white shadow-sm mb-3" width="80" height="80" style="object-fit: cover;">
                <h5 class="fw-bold mb-1">{{ $podium[1]->name }}</h5>
                <p class="text-muted small mb-3">{{ $podium[1]->major }} '{{ $podium[1]->graduation_year }}</p>
                <div class="points-badge py-1 px-3 rounded-pill bg-light fw-bold text-secondary">{{ number_format($podium[1]->points) }} PTS</div>
            </div>
            <div class="podium-step silver" style="height: 120px;"></div>
        </div>
        @endif

        {{-- Rank 1 --}}
        @if($podium->count() >= 1)
        <div class="col-md-4 text-center order-1 order-md-2 mb-4 mb-md-0">
            <div class="podium-card gold p-4 mb-3 border-0 shadow-lg position-relative">
                <div class="rank-badge gold"><i class="bi bi-trophy-fill"></i></div>
                <div class="crown-icon"><i class="bi bi-award-fill text-warning"></i></div>
                <img src="{{ $podium[0]->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($podium[0]->name).'&background=fef08a&color=a16207' }}" 
                     class="rounded-circle border border-5 border-warning shadow-sm mb-3" width="120" height="120" style="object-fit: cover;">
                <h4 class="fw-bold mb-1">{{ $podium[0]->name }}</h4>
                <p class="text-muted mb-3">{{ $podium[0]->major }} '{{ $podium[0]->graduation_year }}</p>
                <div class="points-badge py-2 px-4 rounded-pill bg-warning text-dark fw-bold shadow-sm" style="font-size: 1.1rem;">{{ number_format($podium[0]->points) }} PTS</div>
            </div>
            <div class="podium-step gold" style="height: 180px;"></div>
        </div>
        @endif

        {{-- Rank 3 --}}
        @if($podium->count() >= 3)
        <div class="col-md-3 text-center order-3 order-md-3">
            <div class="podium-card bronze p-4 mb-3 border-0 shadow-lg position-relative">
                <div class="rank-badge bronze">3</div>
                <img src="{{ $podium[2]->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($podium[2]->name).'&background=fed7aa&color=9a3412' }}" 
                     class="rounded-circle border border-4 border-white shadow-sm mb-3" width="80" height="80" style="object-fit: cover;">
                <h5 class="fw-bold mb-1">{{ $podium[2]->name }}</h5>
                <p class="text-muted small mb-3">{{ $podium[2]->major }} '{{ $podium[2]->graduation_year }}</p>
                <div class="points-badge py-1 px-3 rounded-pill bg-light fw-bold text-secondary">{{ number_format($podium[2]->points) }} PTS</div>
            </div>
            <div class="podium-step bronze" style="height: 80px;"></div>
        </div>
        @endif
    </div>

    {{-- Others Table --}}
    @if($others->isNotEmpty())
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-5">
        <div class="card-header bg-white py-3 px-4">
            <h5 class="fw-bold mb-0">PERINGKAT SELANJUTNYA</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" width="80">RANK</th>
                        <th>ALUMNI</th>
                        <th>major</th>
                        <th class="text-end pe-4">SKOR POIN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($others as $index => $user)
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#{{ $index + 4 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" 
                                     class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                <div>
                                    <span class="fw-bold d-block">{{ $user->name }}</span>
                                    <small class="text-muted">Angkatan {{ $user->graduation_year }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">{{ $user->major }}</span></td>
                        <td class="text-end pe-4">
                            <span class="fw-black text-dark fs-5">{{ number_format($user->points) }}</span>
                            <small class="text-muted ms-1">PTS</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<style>
    .fw-black { font-weight: 900; }
    .podium-section { margin-top: -80px; }
    .podium-card { background: white; border-radius: 20px; transition: transform 0.3s ease; z-index: 2; }
    .podium-card:hover { transform: translateY(-5px); }
    
    .podium-step { border-radius: 15px 15px 0 0; opacity: 0.9; }
    .podium-step.gold { background: linear-gradient(to bottom, #fde047, #eab308); }
    .podium-step.silver { background: linear-gradient(to bottom, #cbd5e1, #94a3b8); }
    .podium-step.bronze { background: linear-gradient(to bottom, #fed7aa, #f97316); }

    .rank-badge {
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: 3px solid white;
    }
    .rank-badge.gold { background: #eab308; color: white; width: 50px; height: 50px; font-size: 1.5rem; top: -25px; }
    .rank-badge.silver { background: #94a3b8; color: white; }
    .rank-badge.bronze { background: #f97316; color: white; }

    .crown-icon {
        position: absolute;
        top: -45px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 2.5rem;
    }
</style>
@endsection
