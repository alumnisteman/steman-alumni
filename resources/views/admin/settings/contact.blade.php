@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title text-uppercase">KONTAK & SEKRETARIAT</h2>
                <p class="text-muted fw-bold">Perbarui address, Email dan Nomor Telepon resmi di sini.</p>
            </div>
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addFieldModal">
                <i class="bi bi-plus-lg me-2"></i>TAMBAH FIELD BARU
            </button>
        </div>
    </div>

    @if(session('success')) 
        <div class="alert alert-success border-0 shadow-sm rounded-pill px-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div> 
    @endif

    <div class="row">
        <div class="col-lg-8">
            <form action="/admin/settings" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm p-5" style="border-radius: 20px;">
                @csrf
                @method('PUT')

                @foreach($settings as $item)
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark">{{ $item->label }}</label>
                        @if($item->key == 'contact_address' || $item->key == 'site_description')
                            <textarea name="{{ $item->key }}" class="form-control form-control-lg" rows="4">{{ $item->value }}</textarea>
                        @else
                            <input type="text" name="{{ $item->key }}" class="form-control form-control-lg" value="{{ $item->value }}">
                        @endif
                        <div class="form-text small opacity-50">Tampil di: Footer & Halaman Kontak</div>
                    </div>
                @endforeach

                <button type="submit" class="btn btn-success w-100 py-3 rounded-pill shadow-sm fw-bold">
                    <i class="bi bi-save me-2"></i>SIMPAN KONTAK
                </button>
            </form>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 bg-success text-white" style="border-radius: 20px;">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>IDENTITAS RESMI</h6>
                <p class="small opacity-90">Informasi ini adalah data publik yang akan dilihat oleh seluruh alumni dan calon pendaftar. Pastikan data sudah valid dan aktif.</p>
            </div>
        </div>
    </div>
</div>
@endsection
