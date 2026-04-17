@extends('layouts.admin')

@section('admin-content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-lg" style="border-radius: 20px;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary">
                        <i class="bi bi-megaphone-fill fs-4"></i>
                    </div>
                    <h3 class="fw-bold mb-0">Tambah Iklan Baru</h3>
                </div>

                <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    @include('admin.ads.form')

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 shadow-sm rounded-3 text-uppercase">
                            <i class="bi bi-save me-2"></i> SIMPAN IKLAN
                        </button>
                        <a href="{{ route('admin.ads.index') }}" class="btn btn-link text-muted mt-2">Batal dan Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
