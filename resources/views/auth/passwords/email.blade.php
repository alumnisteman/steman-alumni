@extends('layouts.app')

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-black position-relative overflow-hidden" style="background: radial-gradient(circle at 0% 0%, rgba(6, 182, 212, 0.15) 0%, transparent 50%), radial-gradient(circle at 100% 100%, rgba(99, 102, 241, 0.15) 0%, transparent 50%);">
    
    <!-- Decorative Elements -->
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-20" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 40px 40px;"></div>

    <div class="row justify-content-center w-100 position-relative z-1">
        <div class="col-md-5 col-lg-4">
            <div class="bento-card p-0 overflow-hidden animate-reveal border border-white border-opacity-10 shadow-2xl" style="background: rgba(15, 15, 15, 0.8); backdrop-filter: blur(20px);">
                <div class="p-4 p-md-5">
                    <div class="text-center mb-5">
                        <div class="d-inline-flex p-3 rounded-4 bg-cyan bg-opacity-10 mb-4 border border-cyan border-opacity-20 shadow-glow">
                            <i class="bi bi-shield-lock text-cyan display-5"></i>
                        </div>
                        <h2 class="fw-black text-white tracking-tighter mb-2">RECOVERY</h2>
                        <p class="text-white-50 small">Masukkan email untuk memulihkan akses portal Anda.</p>
                    </div>

                    @if (session('success'))
                        <div class="alert bg-success bg-opacity-10 border border-success border-opacity-20 text-success small mb-4 rounded-4 animate-pulse">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert bg-danger bg-opacity-10 border border-danger border-opacity-20 text-danger small mb-4 rounded-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="text-white-50 extra-small fw-bold tracking-widest text-uppercase mb-2 d-block">Identitas Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-5 border-white border-opacity-10 text-white-50"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" class="form-control bg-white bg-opacity-5 border-white border-opacity-10 text-white py-3 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="nama@steman.id">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block extra-small mt-2" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-cyan btn-lg py-3 fw-black text-dark rounded-pill magnetic-el shadow-glow-sm">
                                KIRIM LINK PEMULIHAN
                            </button>
                            <a href="{{ route('login') }}" class="btn btn-link text-decoration-none text-white-50 small mt-2 magnetic-el">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Bottom accent bar -->
                <div class="h-1 w-100 bg-gradient-cyan"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-cyan { background: linear-gradient(90deg, #06b6d4, #6366f1); }
    .shadow-glow { box-shadow: 0 0 20px rgba(6, 182, 212, 0.3); }
    .shadow-glow-sm { box-shadow: 0 0 15px rgba(6, 182, 212, 0.2); }
    .extra-small { font-size: 0.7rem; }
    .tracking-widest { letter-spacing: 0.2em; }
    
    @keyframes reveal {
        from { opacity: 0; transform: translateY(40px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-reveal { animation: reveal 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    .animate-pulse { animation: pulse 2s infinite; }

    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.08) !important;
        border-color: rgba(6, 182, 212, 0.5) !important;
        color: white !important;
        box-shadow: 0 0 0 0.25rem rgba(6, 182, 212, 0.1);
    }
</style>
@endsection
