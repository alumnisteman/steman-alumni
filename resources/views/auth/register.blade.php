@extends('layouts.app')
@section('content')
<div class="container mt-4"><div class="mb-4"><a href="/" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a></div>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card border-0 shadow-lg" style="border-radius: 15px;">
            <div class="card-body p-5">
                <h3 class="text-center fw-bold mb-4">Pendaftaran Alumni</h3>
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div style="display:none !important;">
                        <input type="text" name="hp_field" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun Lulus</label>
                            <input type="number" name="graduation_year" class="form-control" value="{{ old('graduation_year') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">major</label>
                        <select name="major" class="form-select">
                            <option value="" selected disabled>Pilih major</option>
                            @foreach($majors as $major)
                                <option value="{{ $major->name }}">{{ $major->name }}</option>
                            @endforeach</select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-4 bg-light p-3 rounded">
                        <label class="form-label">Keamanan: Berapa hasil dari <span class="fw-bold text-primary">{{ $captcha_question ?? '5 + 5' }}</span> ?</label>
                        <input type="number" name="captcha" class="form-control @error('captcha') is-invalid @enderror" required>
                        @error('captcha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Daftar Akun</button>
                    <p class="text-center mt-3">Sudah punya akun? <a href="/login">Login di sini</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
