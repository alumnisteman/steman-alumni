<div class="col-md-6 col-lg-4">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 hover-lift">
        <a href="{{ route('donations.show', $campaign->slug) }}" class="text-decoration-none text-body">
            <div class="position-relative">
                <img src="{{ $campaign->image ? asset('storage/' . $campaign->image) : 'https://dummyimage.com/600x400/0f172a/ffffff&text=Alumni+Fund' }}"
                     class="card-img-top" style="height:180px;object-fit:cover;
                     {{ $campaign->status === 'completed' ? 'filter:brightness(.85)' : '' }}">
                <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-dark bg-opacity-50">
                    <span class="badge bg-{{ $color }} rounded-pill px-3 py-2">
                        {{ $campaign->type === 'foundation' ? 'Dana Abadi' : 'Target Event' }}
                    </span>
                    @if($campaign->status === 'completed')
                    <span class="badge bg-success rounded-pill px-3 py-2 ms-1">
                        <i class="bi bi-check-circle-fill me-1"></i>Selesai
                    </span>
                    @endif
                </div>
            </div>
            <div class="card-body p-4">
                <h5 class="fw-bold mb-2">{{ $campaign->title }}</h5>
                <p class="text-muted small mb-3 text-truncate-3" style="min-height:4.5em;">{{ $campaign->description }}</p>

                {{-- Stats row --}}
                <div class="row g-2 mb-3">
                    <div class="col-4 text-center p-2 rounded-3" style="background:var(--bs-tertiary-bg,#f8fafc);">
                        <div style="font-size:.55rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Terkumpul</div>
                        <div style="font-size:.78rem;font-weight:900;color:#059669;">Rp {{ number_format($campaign->current_amount/1e6,1) }}jt</div>
                    </div>
                    <div class="col-4 text-center p-2 rounded-3" style="background:var(--bs-tertiary-bg,#f8fafc);">
                        <div style="font-size:.55rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Donatur</div>
                        <div style="font-size:.78rem;font-weight:900;color:#2563eb;">
                            {{ ($campaign->donations()->where('status','verified')->distinct('user_id')->count('user_id') ?: ($campaign->manual_donor_count ?? 0)) }}
                        </div>
                    </div>
                    <div class="col-4 text-center p-2 rounded-3" style="background:var(--bs-tertiary-bg,#f8fafc);">
                        <div style="font-size:.55rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;">Progress</div>
                        <div style="font-size:.78rem;font-weight:900;color:#d97706;">{{ number_format($campaign->progress,0) }}%</div>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="progress rounded-pill mb-3" style="height:7px;">
                    <div class="progress-bar bg-{{ $campaign->status === 'completed' ? 'success' : $color }}"
                         style="width:{{ min($campaign->progress,100) }}%"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div style="font-size:.72rem;color:#64748b;">
                        Target: <strong>Rp {{ number_format($campaign->goal_amount/1e6,0) }}jt</strong>
                    </div>
                    <span class="small d-flex align-items-center gap-1"
                          style="color:{{ $campaign->status==='completed' ? '#059669' : '#64748b' }};font-weight:700;font-size:.72rem;">
                        {{ $campaign->status === 'completed' ? 'Lihat LPJ' : 'Lihat Laporan' }}
                        <i class="bi bi-arrow-right-short fs-5"></i>
                    </span>
                </div>
            </div>
        </a>

        <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4">
            @if($campaign->status === 'completed')
                <a href="{{ route('donations.show', $campaign->slug) }}"
                   class="btn btn-success rounded-pill w-100 fw-bold">
                    <i class="bi bi-file-earmark-text me-2"></i>Baca Laporan LPJ
                </a>
            @elseif(auth()->check())
                <a href="{{ route('donations.donate', $campaign->id) }}"
                   class="btn btn-dark rounded-pill w-100 fw-bold shadow-sm">
                    <i class="bi bi-heart-fill me-2 text-danger"></i>Donasi Sekarang
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="btn btn-outline-dark rounded-pill w-100 fw-bold">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login untuk Donasi
                </a>
            @endif
        </div>
    </div>
</div>
