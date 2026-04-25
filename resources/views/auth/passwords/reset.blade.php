@extends('layouts.app')

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-black position-relative overflow-hidden" style="background: radial-gradient(circle at 100% 0%, rgba(99, 102, 241, 0.15) 0%, transparent 50%), radial-gradient(circle at 0% 100%, rgba(6, 182, 212, 0.15) 0%, transparent 50%);">
    
    <!-- Decorative Elements -->
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background-image: linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 50px 50px;"></div>

    <div class="row justify-content-center w-100 position-relative z-1">
        <div class="col-md-5 col-lg-4">
            <div class="bento-card p-0 overflow-hidden animate-reveal border border-white border-opacity-10 shadow-2xl" style="background: rgba(15, 15, 15, 0.85); backdrop-filter: blur(25px);">
                <div class="p-4 p-md-5">
                    <div class="text-center mb-5">
                        <div class="d-inline-flex p-3 rounded-4 bg-indigo bg-opacity-10 mb-4 border border-indigo border-opacity-20 shadow-glow-indigo">
                            <i class="bi bi-shield-check text-indigo display-5"></i>
                        </div>
                        <h2 class="fw-black text-white tracking-tighter mb-2">NEW ACCESS</h2>
                        <p class="text-white-50 small">Buat password baru yang tidak mudah ditebak.</p>
                    </div>

                    @if (session('error'))
                        <div class="alert bg-danger bg-opacity-10 border border-danger border-opacity-20 text-danger small mb-4 rounded-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-4">
                            <label for="email" class="text-white-50 extra-small fw-bold tracking-widest text-uppercase mb-2 d-block">Konfirmasi Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-5 border-white border-opacity-10 text-white-50"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" class="form-control bg-white bg-opacity-5 border-white border-opacity-10 text-white py-3 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="email@steman.id">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block extra-small mt-2" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="text-white-50 extra-small fw-bold tracking-widest text-uppercase mb-2 d-block">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-5 border-white border-opacity-10 text-white-50"><i class="bi bi-key"></i></span>
                                <input id="password" type="password" class="form-control bg-white bg-opacity-5 border-white border-opacity-10 text-white py-3 @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="••••••••">
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block extra-small mt-2" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label for="password-confirm" class="text-white-50 extra-small fw-bold tracking-widest text-uppercase mb-2 d-block">Ulangi Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-5 border-white border-opacity-10 text-white-50"><i class="bi bi-shield-lock"></i></span>
                                <input id="password-confirm" type="password" class="form-control bg-white bg-opacity-5 border-white border-opacity-10 text-white py-3" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-indigo btn-lg py-3 fw-black text-white rounded-pill magnetic-el shadow-glow-indigo-sm">
                                UPDATE ACCESS
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Progress Accent -->
                <div class="h-1 w-100 bg-gradient-indigo"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-indigo { color: #6366f1 !important; }
    .bg-indigo { background-color: #6366f1 !important; }
    .btn-indigo { background: #6366f1; border: none; }
    .btn-indigo:hover { background: #4f46e5; color: white; }
    .border-indigo { border-color: #6366f1 !important; }
    .bg-gradient-indigo { background: linear-gradient(90deg, #6366f1, #a855f7); }
    .shadow-glow-indigo { box-shadow: 0 0 20px rgba(99, 102, 241, 0.3); }
    .shadow-glow-indigo-sm { box-shadow: 0 0 15px rgba(99, 102, 241, 0.2); }
    .extra-small { font-size: 0.7rem; }
    .tracking-widest { letter-spacing: 0.2em; }
    
    @keyframes reveal {
        from { opacity: 0; transform: translateY(40px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-reveal { animation: reveal 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.08) !important;
        border-color: rgba(99, 102, 241, 0.5) !important;
        color: white !important;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
    }
</style>
@endsection
