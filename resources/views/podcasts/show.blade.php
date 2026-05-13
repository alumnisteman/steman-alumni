@extends('layouts.app')

@section('content')
<div class="landing-bento min-vh-100 py-5">
    <div class="container py-5">
        <div class="mt-4 gsap-fade-up mb-4">
            <a href="{{ route('podcasts.index') }}" class="text-neon-cyan text-decoration-none small fw-bold tracking-widest"><i class="bi bi-arrow-left me-1"></i> BACK TO TERMINAL</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-8 gsap-fade-up">
                <div class="bento-card p-0 overflow-hidden">
                    <div class="position-relative" style="height: 400px;">
                        <img src="{{ $podcast->thumbnail_link }}" class="w-100 h-100 object-fit-cover" alt="{{ $podcast->title }}">
                        <div class="position-absolute inset-0 bg-black bg-opacity-60 d-flex flex-column align-items-center justify-content-center">
                            <button class="play-btn-neon scale-150 mb-4" onclick="playAudio('{{ $podcast->audio_link }}', '{{ $podcast->title }}')" style="width: 80px; height: 80px; font-size: 2rem;">
                                <i class="bi bi-play-fill"></i>
                            </button>
                            <h2 class="text-white fw-black text-center px-4 display-6">{{ $podcast->title }}</h2>
                            <p class="text-neon-cyan tracking-widest uppercase small mt-2">LINKING TRANSMISSION...</p>
                        </div>
                    </div>
                    <div class="p-4 p-md-5">
                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <span class="badge-neon">{{ strtoupper($podcast->category) }}</span>
                            <span class="text-white-50 small"><i class="bi bi-clock me-1"></i>{{ $podcast->duration }}</span>
                            <span class="text-white-50 small"><i class="bi bi-calendar-event me-1"></i>{{ $podcast->created_at->format('M d, Y') }}</span>
                        </div>

                        <h4 class="text-white fw-bold mb-3 tracking-tight">Transmission Overview</h4>
                        <div class="text-white-50 lh-lg">
                            {!! nl2br(e($podcast->description)) !!}
                        </div>

                        <div class="mt-5 p-4 border border-neon-cyan border-opacity-20" style="background: rgba(0, 255, 255, 0.02);">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-info-circle text-neon-cyan fs-4"></i>
                                <p class="text-white-50 small m-0">This audio signal is part of the **Steman Connect Legacy Project**. Share this transmission to inspire other alumni.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 gsap-fade-up">
                <div class="bento-card mb-4">
                    <h6 class="text-white fw-bold mb-4 small tracking-widest text-uppercase">Guest Profile</h6>
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-neon-cyan p-1" style="clip-path: polygon(10% 0, 100% 0, 100% 90%, 90% 100%, 0 100%, 0 10%);">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($podcast->guest_name) }}&background=000&color=0ff" class="rounded-0" width="60" height="60">
                        </div>
                        <div>
                            <h6 class="text-white fw-bold mb-0">{{ $podcast->guest_name }}</h6>
                            <p class="text-white-50 extra-small mb-0">Alumni Contributor</p>
                        </div>
                    </div>
                </div>

                <div class="bento-card">
                    <h6 class="text-white fw-bold mb-4 small tracking-widest text-uppercase">System Status</h6>
                    <ul class="list-unstyled extra-small text-white-50 d-flex flex-column gap-3">
                        <li class="d-flex justify-content-between">
                            <span>SIGNAL STRENGTH</span>
                            <span class="text-neon-cyan">98% OPTIMAL</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>ENCRYPTION</span>
                            <span class="text-success">AES-256 ACTIVE</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>TRANSCODE</span>
                            <span>MP3 / 320KBPS</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@include('podcasts.player_script')

<style>
    .inset-0 { top: 0; left: 0; bottom: 0; right: 0; }
    .display-6 { font-size: 2.5rem; }
    @media (max-width: 768px) {
        .display-6 { font-size: 1.8rem; }
    }
</style>
@endsection
