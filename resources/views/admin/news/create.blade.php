@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <a href="/admin/news" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
                <h2 class="section-title mt-2">Buat Berita Baru</h2>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <form action="/admin/news" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold">Judul Berita</label>
                    <input type="text" name="title" class="form-control" required placeholder="Masukkan judul..." maxlength="255">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Kategori</label>
                        <select name="category" class="form-select">
                            <option>Berita</option>
                            <option>Cerita Alumni</option>
                            <option>Pengumuman</option>
                            <option>Kegiatan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Thumbnail (Opsional)</label>
                        <input type="file" name="thumbnail" class="form-control">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Konten</label>
                    <textarea name="content" class="form-control" rows="15" required placeholder="Tulis isi berita di sini..."></textarea>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="status" value="published" checked id="pubSwitch">
                    <label class="form-check-label fw-bold small" for="pubSwitch">Terbitkan Sekarang</label>
                </div>

                <button type="submit" class="btn btn-alumni_smkn2 w-100 py-3 rounded-pill shadow">Simpan & Terbitkan</button>
            </form>
        </div>
    </div>
</div>
@endsection
