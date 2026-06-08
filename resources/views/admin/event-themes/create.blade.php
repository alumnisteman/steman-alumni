@extends('layouts.admin')

@section('admin-content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-lg" style="border-radius: 20px;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-calendar-plus-fill fs-4 text-warning"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">Tambah Tema Event Baru</h3>
                        <p class="text-muted small mb-0">Tema akan aktif otomatis sesuai periode yang ditentukan</p>
                    </div>
                </div>

                <form action="{{ route('admin.event-themes.store') }}" method="POST">
                    @csrf
                    @include('admin.event-themes.form')
                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 shadow-sm rounded-3 text-uppercase">
                            <i class="bi bi-save me-2"></i> Simpan Tema Event
                        </button>
                        <a href="{{ route('admin.event-themes.index') }}" class="btn btn-link text-muted mt-2 text-center">Batal dan Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
