@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="{{ route('alumni.business.show', $business->id) }}" class="btn btn-light rounded-circle shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="fw-black text-uppercase tracking-wider mb-0" style="color: #0f172a;">EDIT PROFIL BISNIS 🛠️</h2>
                    <p class="text-muted mb-0">Perbarui informasi atau tambah foto galeri produk Anda.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4 p-md-5 mb-4" style="border-radius: 25px;">
                        <form action="{{ route('alumni.business.update', $business->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-4">
                                <!-- Logo Edit -->
                                <div class="col-12 text-center mb-4">
                                    <div class="position-relative d-inline-block mx-auto">
                                        <div id="logo-preview" class="bg-light rounded-4 d-flex align-items-center justify-content-center overflow-hidden shadow-sm" style="width: 150px; height: 150px; border: 3px solid #ffcc00;">
                                            @if($business->logo_url)
                                                <img src="{{ $business->logo_url }}" class="w-100 h-100" style="object-fit: cover;">
                                            @else
                                                <i class="bi bi-image display-4 text-muted"></i>
                                            @endif
                                        </div>
                                        <label for="logo" class="btn btn-warning btn-sm rounded-circle position-absolute bottom-0 end-0 shadow-sm" style="margin-bottom: -10px; margin-right: -10px;">
                                            <i class="bi bi-camera-fill"></i>
                                        </label>
                                        <input type="file" name="logo" id="logo" class="d-none" accept="image/*" onchange="previewLogo(this)">
                                    </div>
                                    <p class="small text-muted mt-3">Ganti Logo Usaha</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-2 text-dark">Nama Usaha</label>
                                    <input type="text" name="name" class="form-control bg-light border-0 py-3 px-4 rounded-3" value="{{ old('name', $business->name) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-2 text-dark">Kategori</label>
                                    <select name="category" class="form-select bg-light border-0 py-3 px-4 rounded-3" required>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" {{ old('category', $business->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold mb-2 text-dark">Lokasi Usaha</label>
                                    <input type="text" name="location" class="form-control bg-light border-0 py-3 px-4 rounded-3" value="{{ old('location', $business->location) }}" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold mb-2 text-dark">WhatsApp (Tanpa angka 0 di depan)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white border-0 px-3 fw-bold">+62</span>
                                        <input type="number" name="whatsapp" class="form-control bg-light border-0 py-3 px-4 rounded-end-3" value="{{ old('whatsapp', $business->whatsapp) }}" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold mb-2 text-dark">Tentang Usaha</label>
                                    <textarea name="description" rows="5" class="form-control bg-light border-0 py-3 px-4 rounded-3" required>{{ old('description', $business->description) }}</textarea>
                                </div>

                                <!-- Add More Photos -->
                                <div class="col-12 mt-4">
                                    <label class="form-label fw-bold mb-2 text-dark"><i class="bi bi-images me-2 text-primary"></i>Tambah Foto Produk Baru</label>
                                    <input type="file" name="photos[]" class="form-control bg-light border-0 py-2 px-3 rounded-3" multiple accept="image/*">
                                    <small class="text-muted">Anda bisa memilih banyak foto sekaligus (Etalase Produk).</small>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-lg">SIMPAN PERUBAHAN</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Gallery Management Sidebar -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 25px;">
                        <h6 class="fw-bold mb-3">GALERI PRODUK SAAT INI</h6>
                        <div class="row g-2">
                            @forelse($business->photos as $photo)
                                <div class="col-6 position-relative mb-2">
                                    <div class="rounded-3 overflow-hidden shadow-sm shadow-sm" style="height: 100px;">
                                        <img src="{{ $photo->photo_url }}" class="w-100 h-100" style="object-fit: cover;">
                                    </div>
                                    <form action="{{ route('alumni.business.photo.delete', $photo->id) }}" method="POST" class="position-absolute top-0 end-0 m-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-circle p-1 lh-1 shadow-sm" onclick="return confirm('Hapus foto ini?')">
                                            <i class="bi bi-x-circle" style="font-size: 0.7rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="col-12 py-4 text-center">
                                    <p class="text-muted small mb-0">Belum ada foto galeri.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm p-4 border-top border-5 border-danger" style="border-radius: 25px;">
                        <h6 class="fw-bold text-danger mb-3">ZONE BERBAHAYA</h6>
                        <p class="small text-muted">Menghapus usaha Anda bersifat permanen dan tidak bisa dibatalkan.</p>
                        <form action="{{ route('alumni.business.destroy', $business->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus usaha ini dari direktori?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold">HAPUS USAHA</button>
                        </form>
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
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
