@extends('layouts.app')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden animate-reveal">
                <div class="card-header bg-dark text-white p-4 text-center border-0">
                    <div class="bg-primary bg-opacity-20 d-inline-flex p-3 rounded-circle mb-3">
                        <i class="bi bi-key-fill text-primary fs-2"></i>
                    </div>
                    <h4 class="fw-black mb-1">Lupa Password?</h4>
                    <p class="text-white-50 small mb-0">Jangan khawatir, masukkan email Anda untuk menerima link reset.</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    @if (session('success'))
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="form-label small fw-bold text-uppercase">Email Terdaftar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-envelope-at text-muted"></i></span>
                                <input id="email" type="email" class="form-control bg-light border-0 py-3 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="nama@email.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3 fw-bold shadow-sm rounded-pill">
                                <i class="bi bi-send-fill me-2"></i> Kirim Link Reset
                            </button>
                            <a href="{{ route('login') }}" class="btn btn-link text-decoration-none text-muted small mt-2">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes reveal {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-reveal { animation: reveal 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards; }
    .form-control:focus { background-color: #fff !important; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1); }
</style>
@endsection
