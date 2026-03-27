@extends('layouts.app')
@section('content')
<div class="container mt-4"><div class="mb-4"><a href="/" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a></div>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card border-0 shadow-lg" style="border-radius: 15px;">
            <div class="card-body p-4 p-md-5">
                <h3 class="text-center fw-bold mb-4">Login Alumni</h3>
                <form action="/login" method="POST">
                    @csrf
                    <div style="display:none !important;">
                        <input type="text" name="hp_field" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-4 bg-light p-3 rounded">
                        <label class="form-label">Keamanan: Berapa hasil dari <span class="fw-bold text-primary">{{ $captcha_question ?? '5 + 5' }}</span> ?</label>
                        <input type="number" name="captcha" class="form-control form-control-lg @error('captcha') is-invalid @enderror" required>
                        @error('captcha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">Login Sekarang</button>
                    
                    <div class="divider d-flex align-items-center my-4">
                        <p class="text-center fw-bold mx-3 mb-0 text-muted">ATAU</p>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('social.redirect', 'google') }}" class="btn btn-outline-danger btn-lg rounded-pill">
                            <i class="bi bi-google me-2"></i> Login with Google
                        </a>
                        <a href="{{ route('social.redirect', 'linkedin') }}" class="btn btn-outline-primary btn-lg rounded-pill">
                            <i class="bi bi-linkedin me-2"></i> Login with LinkedIn
                        </a>
                    </div>

                    <p class="text-center text-muted mt-4">Belum punya akun? <a href="/register">Daftar disini</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection