@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="{{ route('alumni.business.index') }}" class="btn btn-light rounded-circle shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="fw-black text-uppercase tracking-wider mb-0" style="color: #0f172a;">DAFTARKAN USAHA 🏢</h2>
                    <p class="text-muted mb-0">Promosikan produk dan jasa Anda ke seluruh jaringan alumni.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 25px;">
                <form action="{{ route('alumni.business.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Logo Upload -->
                        <div class="col-12 text-center mb-4">
                            <div class="position-relative d-inline-block mx-auto">
                                <div id="logo-preview" class="bg-light rounded-4 d-flex align-items-center justify-content-center overflow-hidden shadow-sm" style="width: 150px; height: 150px; border: 3px dashed #ddd;">
                                    <i class="bi bi-image display-4 text-muted"></i>
                                </div>
                                <label for="logo" class="btn btn-warning btn-sm rounded-circle position-absolute bottom-0 end-0 shadow-sm" style="margin-bottom: -10px; margin-right: -10px;">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                                <input type="file" name="logo" id="logo" class="d-none" accept="image/*" onchange="previewLogo(this)">
                            </div>
                            <p class="small text-muted mt-3">Upload Logo Usaha (Opsional, Max 2MB)</p>
                            @error('logo') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-2">Nama Usaha/Brand <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="Contoh: Bengkel Steman Modif" value="{{ old('name') }}" required>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-2">Kategori <span class="text-danger">*</span></label>
                            <select name="category" class="form-select bg-light border-0 py-3 px-4 rounded-3" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark mb-2">Alamat / Lokasi Usaha <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="Contoh: Kel. Bastiong, Ternate Selatan" value="{{ old('location') }}" required>
                            @error('location') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark mb-2">Nomor WhatsApp <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white border-0 px-3 fw-bold">+62</span>
                                <input type="number" name="whatsapp" class="form-control bg-light border-0 py-3 px-4 rounded-end-3" placeholder="8123456789 (Tanpa angka 0 di depan)" value="{{ old('whatsapp') }}" required>
                            </div>
                            <small class="text-muted">Gunakan nomor aktif untuk dihubungi pembeli secara langsung.</small>
                            @error('whatsapp') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <!-- Gallery Photos -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-images me-2 text-primary"></i>Galeri Foto Produk / Jasa (Opsional)</label>
                            <input type="file" name="photos[]" class="form-control bg-light border-0 py-3 px-4 rounded-3" multiple accept="image/*">
                            <small class="text-muted">Anda bisa memilih lebih dari satu foto sekaligus untuk dipajang sebagai etalase.</small>
                            @error('photos.*') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark mb-2">Tentang Usaha / Deskripsi Produk <span class="text-danger">*</span></label>
                            <textarea name="description" rows="4" class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="Jelaskan produk atau jasa yang Anda tawarkan..." required>{{ old('description') }}</textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-tag-fill me-2 text-danger"></i>Info Diskon (Opsional)</label>
                            <input type="text" name="discount_info" class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="Contoh: Diskon 10% Member" value="{{ old('discount_info') }}">
                            <small class="text-muted">Akan muncul sebagai label merah di kartu usaha.</small>
                            @error('discount_info') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-globe me-2 text-primary"></i>Website / Link Eksternal (Opsional)</label>
                            <input type="url" name="website_url" class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="https://tokoku.com" value="{{ old('website_url') }}">
                            <small class="text-muted">Link ke website resmi atau marketplace.</small>
                            @error('website_url') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12 mt-5">
                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-lg">
                                DAFTARKAN SEKARANG 🚀
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="alert alert-info border-0 shadow-sm mt-4 p-4" style="border-radius: 20px; background: rgba(13, 110, 253, 0.05);">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-info-circle-fill text-primary mt-1"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Penting:</h6>
                        <p class="small mb-0 text-muted">Pastikan data yang Anda masukkan benar. Profil usaha Anda akan langsung terlihat oleh ribuan alumni yang aktif di portal ini.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logo-preview').innerHTML = '<img src="' + e.target.result + '" class="w-100 h-100" style="object-fit: cover;">';
            document.getElementById('logo-preview').style.border = '3px solid #ffcc00';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
