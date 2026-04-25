@extends('layouts.app')

@section('content')
<style>
    .login-wrapper {
        min-height: 90vh;
        display: flex;
        align-items: center;
        background: radial-gradient(circle at 10% 20%, #0f172a 0%, #1e293b 100%);
        position: relative;
        overflow: hidden;
        border-radius: 30px;
        margin: 20px 0;
    }

    /* Dekorasi Background - Lingkaran Cahaya */
    .login-wrapper::before {
        content: "";
        position: absolute;
        width: 400px;
        height: 400px;
        background: rgba(255, 204, 0, 0.05);
        filter: blur(80px);
        border-radius: 50%;
        top: -100px;
        right: -100px;
    }

    .glass-login-card {
        background: rgba(15, 23, 42, 0.85) !important; /* Dark background to contrast with white inputs */
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        box-shadow: 0 40px 80px rgba(0,0,0,0.5);
        color: white !important;
    }
    
    .glass-login-card h2, .glass-login-card p, .glass-login-card label, .glass-login-card div {
        color: white !important;
    }

    .form-control-custom {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: white !important;
        border-radius: 12px !important;
        padding: 12px 20px !important;
    }

    .form-control-custom:focus {
        background: rgba(255, 255, 255, 0.1) !important;
        border-color: #ffcc00 !important;
        box-shadow: 0 0 15px rgba(255, 204, 0, 0.2) !important;
    }

    .btn-login {
        background: #ffcc00 !important;
        color: #000 !important;
        font-weight: 800 !important;
        border-radius: 12px !important;
        padding: 14px !important;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(255, 204, 0, 0.3);
    }
</style>

<div class="login-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="mb-4 text-center text-md-start">
                    <a href="/" class="text-decoration-none text-warning fw-bold"><i class="bi bi-arrow-left"></i> KEMBALI KE BERANDA</a>
                </div>

                <div class="glass-login-card p-4 p-md-5">
                    <div class="text-center mb-5">
                        <div class="d-inline-block bg-warning bg-opacity-10 p-3 rounded-4 mb-3">
                            <i class="bi bi-shield-lock-fill text-warning fs-1"></i>
                        </div>
                        <h2 class="fw-black text-uppercase" style="letter-spacing: 2px;">LOGIN ALUMNI</h2>
                        <p class="opacity-50 small">Selamat datang kembali di Portal STEMAN</p>
                    </div>

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div style="display:none !important;">
                            <input type="text" name="hp_field" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label opacity-75">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-custom @error('email') is-invalid @enderror" placeholder="email@contoh.com" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label opacity-75 mb-0">Password</label>
                                <a href="{{ route('password.request') }}" class="small text-warning text-decoration-none">Lupa Password?</a>
                            </div>
                            <input type="password" name="password" class="form-control form-control-custom" placeholder="••••••••" required>
                        </div>

                        <div class="mb-4 p-3 rounded" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                            <label class="form-label opacity-75 small mb-3">Keamanan: Berapa hasil dari <span class="fw-bold text-warning">{{ $captcha_question ?? '5 + 5' }}</span> ?</label>
                            <input type="number" name="captcha" class="form-control form-control-custom @error('captcha') is-invalid @enderror" required>
                            @error('captcha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-login w-100 text-uppercase">MASUK KE DASHBOARD</button>

                        <p class="text-center mt-5 mb-0 opacity-75 small">
                            Belum punya akun? <a href="{{ route('register') }}" class="text-warning fw-bold text-decoration-none">Daftar sekarang</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
