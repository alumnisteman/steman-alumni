@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h2 class="section-title">Konfigurasi & Branding Situs</h2>
        <p class="text-muted">Atur nama organisasi, identitas sekolah, dan deskripsi program di sini.</p>
    </div>

    @if(session('success')) 
        <div class="alert alert-success border-0 shadow-sm rounded-pill px-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div> 
    @endif

    <div class="row">
        <div class="col-lg-8">
            <form action="/admin/settings" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                @csrf
                @method('PUT')

                @foreach($settings as $group => $items)
                    <div id="group-{{ $group }}" class="mb-5 p-4 bg-white border border-light rounded-4 shadow-sm scroll-margin-top">
                        <h4 class="fw-bold mb-4 text-dark d-flex align-items-center">
                            @if($group == 'general') <span class="bg-primary text-white p-2 rounded-3 me-3"><i class="bi bi-gear-fill"></i></span>Identitas & Branding
                            @elseif($group == 'hero') <span class="bg-warning text-dark p-2 rounded-3 me-3"><i class="bi bi-image"></i></span>Banner Utama (Hero)
                            @elseif($group == 'contact') <span class="bg-success text-white p-2 rounded-3 me-3"><i class="bi bi-telephone-fill"></i></span>Informasi Kontak & Sekretariat
                            @elseif($group == 'chairman') <span class="bg-info text-white p-2 rounded-3 me-3"><i class="bi bi-person-workspace"></i></span>Sambutan Ketua Umum
                            @elseif($group == 'event_chairman') <span class="bg-danger text-white p-2 rounded-3 me-3"><i class="bi bi-megaphone-fill"></i></span>Sambutan Ketua Panitia
                            @else <span class="bg-secondary text-white p-2 rounded-3 me-3"><i class="bi bi-grid-fill"></i></span>Konfigurasi Lanjutan @endif
                        </h4>
                        <hr class="mb-4 opacity-10">
                    
                    @foreach($items as $item)
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark">{{ $item->label }}</label>
                            @if(str_contains($item->key, 'program') || $item->key == 'hero_title' || $item->key == 'contact_address')
                                <textarea name="{{ $item->key }}" class="form-control" rows="3">{{ $item->value }}</textarea>
                            @elseif($item->key == 'hero_background')
                                <div class="mb-2">
                                    <img src="{{ $item->value }}" alt="Hero Background Preview" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                                <input type="file" name="{{ $item->key }}" class="form-control">
                            @else
                                <input type="text" name="{{ $item->key }}" class="form-control" value="{{ $item->value }}">
                            @endif
                            <div class="form-text small opacity-50">Kunci: <code>{{ $item->key }}</code></div>
                        </div>
                    @endforeach
                    </div>
                @endforeach

                <button type="submit" class="btn btn-steman w-100 py-3 rounded-pill shadow-sm fw-bold">
                    <i class="bi bi-save me-2"></i>SIMPAN PERUBAHAN
                </button>
            </form>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 bg-dark text-white" style="border-radius: 15px;">
                <h6 class="fw-bold mb-3" style="color: #ffcc00;">PANDUAN CMS</h6>
                <p class="small opacity-75">Perubahan yang Anda buat di sini akan langsung berdampak pada seluruh halaman website:</p>
                <ul class="small opacity-75 list-unstyled">
                    <li class="mb-2"><i class="bi bi-check2-circle me-2 text-warning"></i>Nama Organisasi muncul di Navbar dan Footer.</li>
                    <li class="mb-2"><i class="bi bi-check2-circle me-2 text-warning"></i>Nama Sekolah digunakan di halaman pendaftaran.</li>
                    <li class="mb-2"><i class="bi bi-check2-circle me-2 text-warning"></i>Hero section menyambut pengunjung di halaman utama.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
