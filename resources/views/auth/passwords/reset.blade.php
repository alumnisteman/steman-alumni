@extends('layouts.app')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden animate-reveal">
                <div class="card-header bg-dark text-white p-4 text-center border-0">
                    <div class="bg-warning bg-opacity-20 d-inline-flex p-3 rounded-circle mb-3">
                        <i class="bi bi-shield-lock-fill text-warning fs-2"></i>
                    </div>
                    <h4 class="fw-black mb-1">Update Password</h4>
                    <p class="text-white-50 small mb-0">Silakan buat password baru yang kuat untuk akun Anda.</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    @if (session('error'))
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold text-uppercase">Konfirmasi Email</label>
                            <input id="email" type="email" class="form-control bg-light border-0 py-3 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com">
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label small fw-bold text-uppercase">Password Baru</label>
                            <input id="password" type="password" class="form-control bg-light border-0 py-3 @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Min. 4 karakter">
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label small fw-bold text-uppercase">Ulangi Password</label>
                            <input id="password-confirm" type="password" class="form-control bg-light border-0 py-3" name="password_confirmation" required autocomplete="new-password" placeholder="Ketik ulang password">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning py-3 fw-bold shadow-sm rounded-pill text-dark">
                                <i class="bi bi-check-circle-fill me-2"></i> Perbarui Password
                            </button>
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
</style>
@endsection
