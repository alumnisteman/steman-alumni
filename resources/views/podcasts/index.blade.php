@extends('layouts.app')

@section('content')
<div class="landing-bento min-vh-100 py-5">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5 mt-4 gsap-fade-up">
            <div>
                <h1 class="fw-black text-white display-4 tracking-tighter mb-0">AUDIO TERMINAL</h1>
                <p class="text-neon-cyan fw-bold tracking-widest uppercase">Podcast Transmissions</p>
            </div>
            <div class="d-none d-md-block">
                <div class="badge-neon">ENCRYPTED STREAM</div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Filter Sidebar -->
            <div class="col-lg-3 gsap-fade-up">
                <div class="bento-card p-4">
                    <h6 class="text-white fw-bold mb-4 small tracking-widest text-uppercase">Categories</h6>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ \Illuminate\Support\Facades\URL::route('podcasts.index') }}" class="btn {{ !request('category') ? 'btn-neon-violet' : 'btn-outline-light' }} btn-sm text-start">ALL TRANSMISSIONS</a>
                        <a href="{{ \Illuminate\Support\Facades\URL::route('podcasts.index', ['category' => 'career']) }}" class="btn {{ request('category') == 'career' ? 'btn-neon-violet' : 'btn-outline-light' }} btn-sm text-start">CAREER PROTOCOL</a>
                        <a href="{{ \Illuminate\Support\Facades\URL::route('podcasts.index', ['category' => 'overseas']) }}" class="btn {{ request('category') == 'overseas' ? 'btn-neon-violet' : 'btn-outline-light' }} btn-sm text-start">GLOBAL UPLINK</a>
                        <a href="{{ \Illuminate\Support\Facades\URL::route('podcasts.index', ['category' => 'startup']) }}" class="btn {{ request('category') == 'startup' ? 'btn-neon-violet' : 'btn-outline-light' }} btn-sm text-start">STARTUP MATRIX</a>
                    </div>
                </div>
            </div>

            <!-- Podcast Grid -->
            <div class="col-lg-9">
                <div class="row g-4">
                    @forelse($podcasts as $podcast)
                        <div class="col-md-6 gsap-fade-up">
                            <div class="bento-card h-100 p-0 overflow-hidden">
                                <div class="position-relative" style="height: 200px;">
                                    <img src="{{ $podcast->thumbnail_link }}" class="w-100 h-100 object-fit-cover" alt="{{ $podcast->title }}">
                                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-black bg-opacity-40 d-flex align-items-center justify-content-center opacity-0 hover-opacity-100 transition-all">
                                        <button class="play-btn-neon scale-150" onclick="playAudio('{{ $podcast->audio_link }}', '{{ $podcast->title }}')">
                                            <i class="bi bi-play-fill"></i>
                                        </button>
                                    </div>
                                    <div class="position-absolute bottom-0 start-0 p-3 w-100 bg-gradient-dark">
                                        <span class="badge-neon py-1 px-2" style="font-size: 0.6rem;">{{ strtoupper($podcast->category) }}</span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h5 class="text-white fw-bold mb-1">{{ $podcast->title }}</h5>
                                    <p class="text-white-50 small mb-3">Host: {{ $podcast->guest_name }}</p>
                                    <p class="text-white-50 extra-small line-clamp-2 mb-4">{{ $podcast->description }}</p>
                                    
                                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-white border-opacity-10">
                                        <span class="text-neon-cyan fw-bold extra-small"><i class="bi bi-clock me-1"></i>{{ $podcast->duration }}</span>
                                        <a href="{{ \Illuminate\Support\Facades\URL::route('podcasts.show', $podcast->slug) }}" class="text-white-50 text-decoration-none extra-small hover-cyan transition-all">DETAILS <i class="bi bi-arrow-right ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="bento-card py-5">
                                <i class="bi bi-broadcast text-white-50 display-4 mb-3"></i>
                                <h4 class="text-white">NO TRANSMISSIONS DETECTED</h4>
                                <p class="text-white-50 small">Wait for the next signal or check other categories.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    {{ $podcasts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@include('podcasts.player_script')

<style>
    .hover-opacity-100:hover { opacity: 1 !important; }
    .hover-cyan:hover { color: #0ff !important; }
    .bg-gradient-dark { background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); }
    .scale-150 { transform: scale(1.5); }
    .scale-150:hover { transform: scale(1.7); }
</style>
@endsection
