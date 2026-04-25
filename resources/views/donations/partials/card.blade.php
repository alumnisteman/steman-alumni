<div class="col-md-6 col-lg-4">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 hover-lift">
        <div class="position-relative">
            <img src="{{ $campaign->image ? asset('storage/' . $campaign->image) : 'https://dummyimage.com/600x400/0f172a/ffffff&text=Alumni+Fund' }}" class="card-img-top" style="height: 180px; object-fit: cover;">
            <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-dark bg-opacity-50 backdrop-blur">
                <span class="badge bg-{{ $color }} rounded-pill px-3 py-2">{{ $campaign->type === 'foundation' ? 'Dana Abadi' : 'Target Event' }}</span>
            </div>
        </div>
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">{{ $campaign->title }}</h5>
            <p class="text-muted small mb-4 text-truncate-3" style="min-height: 4.5em;">{{ $campaign->description }}</p>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="small fw-bold text-{{ $color }}">Progress: {{ number_format($campaign->progress, 1) }}%</span>
                    <span class="small text-muted">Goal: Rp {{ number_format($campaign->goal_amount, 0, ',', '.') }}</span>
                </div>
                <div class="progress rounded-pill" style="height: 8px;">
                    <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $campaign->progress }}%"></div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="small text-muted mb-0">Terkumpul</p>
                    <h6 class="fw-bold mb-0">Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</h6>
                </div>
                <a href="{{ route('donations.donate', $campaign->id) }}" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">Donasi</a>
            </div>
        </div>
    </div>
</div>
