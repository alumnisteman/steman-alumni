@extends('layouts.admin')
@section('admin-content')
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


            <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
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
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small fw-bold mb-0">Konten</label>
                        <button type="button" id="btn-ai-news" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-stars me-1"></i> AI Generate
                        </button>
                    </div>
                    <textarea name="content" id="news-content" class="form-control" rows="15" required placeholder="Tulis isi berita di sini..."></textarea>
                    <div id="ai-news-status" class="mt-1 small text-muted d-none">AI sedang menulis...</div>
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

@push('scripts')
<script>
    document.getElementById('btn-ai-news').addEventListener('click', function() {
        const title = document.querySelector('input[name="title"]').value;
        const status = document.getElementById('ai-news-status');
        const contentArea = document.getElementById('news-content');
        const btn = this;

        if (!title || title.length < 5) {
            Swal.fire({
                icon: 'warning',
                title: 'Judul Terlalu Pendek',
                text: 'Silakan masukkan judul yang lebih jelas agar AI bisa menulis konten yang relevan.',
                confirmButtonColor: '#6366f1'
            });
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Berpikir...';
        status.classList.remove('d-none');

        fetch("{{ route('admin.ai.generate-content') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ type: 'news', input: title })
        })
        .then(response => response.json())
        .then(data => {
            if (data.content) {
                contentArea.value = data.content;
                Swal.fire({
                    icon: 'success',
                    title: 'Berita Berhasil Dibuat!',
                    text: 'AI telah menyusun draf berita berdasarkan judul Anda.',
                    confirmButtonColor: '#6366f1'
                });
            } else {
                throw new Error(data.error || 'Gagal menghasilkan konten');
            }
        })
        .catch(err => {
            Swal.fire('Error', err.message, 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-stars me-1"></i> AI Generate';
            status.classList.add('d-none');
        });
    });
</script>
@endpush
@endsection

