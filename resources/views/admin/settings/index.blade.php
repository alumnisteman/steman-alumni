@extends('layouts.admin')

@section('admin-content')
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
                            @elseif($group == 'profile') <span class="bg-dark text-white p-2 rounded-3 me-3"><i class="bi bi-eye-fill"></i></span>Profil & Visi Misi Organisasi
                            @elseif($group == 'hero') <span class="bg-warning text-dark p-2 rounded-3 me-3"><i class="bi bi-image"></i></span>Banner Utama (Hero)
                            @elseif($group == 'contact') <span class="bg-success text-white p-2 rounded-3 me-3"><i class="bi bi-telephone-fill"></i></span>Informasi Kontak & Sekretariat
                            @elseif($group == 'chairman') <span class="bg-info text-white p-2 rounded-3 me-3"><i class="bi bi-person-workspace"></i></span>Sambutan Ketua Umum
                            @elseif($group == 'event_chairman') <span class="bg-danger text-white p-2 rounded-3 me-3"><i class="bi bi-megaphone-fill"></i></span>Sambutan Ketua Panitia
                            @elseif($group == 'secretary') <span class="bg-success text-white p-2 rounded-3 me-3"><i class="bi bi-person-fill-check"></i></span>Sambutan Sekretaris Panitia
                            @elseif($group == 'ai') <span class="bg-dark text-white p-2 rounded-3 me-3"><i class="bi bi-robot"></i></span>Integrasi & API AI
                            @else <span class="bg-secondary text-white p-2 rounded-3 me-3"><i class="bi bi-grid-fill"></i></span>Konfigurasi Lanjutan @endif
                        </h4>
                        <hr class="mb-4 opacity-10">
                    
                    @foreach($items as $item)
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark">{{ $item->label }}</label>
                            @php
                                $key = (string)$item->key;
                                $isLongText = (strpos($key, 'message') !== false) || 
                                              (strpos($key, 'speech') !== false) || 
                                              (strpos($key, 'description') !== false) || 
                                              (strpos($key, 'program') !== false) || 
                                              (strpos($key, 'vision') !== false) || 
                                              (strpos($key, 'mission') !== false) || 
                                              (strpos($key, 'running_text') !== false) || 
                                              ($key == 'hero_title') || 
                                              ($key == 'contact_address');
                                
                                $isImage = (strpos($key, 'photo') !== false) || 
                                           (strpos($key, 'logo') !== false) || 
                                           (strpos($key, 'background') !== false);
                            @endphp

                            @if($isLongText)
                                <textarea name="{{ $item->key }}" class="form-control shadow-sm" rows="4" style="border-radius: 10px;">{{ $item->value }}</textarea>
                            @elseif($isImage)
                                <div class="p-3 bg-light border border-light-subtle rounded-3 mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item->value)
                                            <div class="position-relative">
                                                <img src="{{ $item->value }}" alt="Preview" class="rounded-3 shadow-sm border border-2 border-white" 
                                                    style="height: 80px; object-fit: cover; {{ strpos($item->key, 'background') !== false ? 'width: 160px;' : 'width: 80px;' }}">
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <label class="form-label x-small fw-bold text-primary mb-1">GANTI FOTO / UPLOAD :</label>
                                            <input type="file" name="{{ $item->key }}" class="form-control form-control-sm shadow-sm" style="border-radius: 8px;">
                                        </div>
                                    </div>
                                    <div class="mt-2 x-small text-muted">
                                        URL Saat Ini: <code class="bg-white px-1">{{ $item->value }}</code>
                                        @if(strpos($item->key, 'background') !== false)
                                            <div class="mt-1 text-success"><i class="bi bi-check-circle-fill me-1"></i> Rekomendasi resolusi: <b>1920 &times; 800 px</b> (sweet spot paling pas)</div>
                                        @elseif(strpos($item->key, 'photo') !== false)
                                            <div class="mt-1"><i class="bi bi-info-circle me-1"></i> Rekomendasi: Gunakan rasio pas foto / portrait.</div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                @if(strpos($item->key, 'api_key') !== false)
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;"><i class="bi bi-key-fill text-muted"></i></span>
                                        <input type="password" name="{{ $item->key }}" class="form-control border-start-0 shadow-sm" value="{{ $item->value }}" style="border-radius: 0 10px 10px 0;" placeholder="Masukkan kunci rahasia...">
                                    </div>
                                @else
                                    <input type="text" name="{{ $item->key }}" class="form-control shadow-sm" value="{{ $item->value }}" style="border-radius: 10px;">
                                @endif
                            @endif
                            <div class="form-text small opacity-50">Kunci: <code>{{ $item->key }}</code></div>
                        </div>
                    @endforeach
                    </div>
                @endforeach

                <div class="mt-4">
            <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill shadow-lg fw-bold overflow-hidden position-relative border-0" style="background: linear-gradient(135deg, #1e293b, #334155);">
                <span class="position-relative z-1"><i class="bi bi-cloud-upload-fill me-2"></i>SIMPAN & UNGGAH PERUBAHAN</span>
            </button>
        </div>    </form>
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
