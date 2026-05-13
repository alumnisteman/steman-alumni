@extends('layouts.admin')
@section('admin-content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <a href="{{ route('admin.news.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
                <h2 class="section-title mt-2">Edit Berita</h2>
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

            <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label small fw-bold">Judul Berita</label>
                    <input type="text" name="title" class="form-control" required placeholder="Masukkan judul..." value="{{ old('title', $news->title) }}" maxlength="255">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Kategori</label>
                        <select name="category" class="form-select">
                            <option {{ old('category', $news->category) == 'Berita' ? 'selected' : '' }}>Berita</option>
                            <option {{ old('category', $news->category) == 'Cerita Alumni' ? 'selected' : '' }}>Cerita Alumni</option>
                            <option {{ old('category', $news->category) == 'Pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                            <option {{ old('category', $news->category) == 'Kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Thumbnail (Opsional)</label>
                        @if($news->thumbnail)
                            <div class="mb-2">
                                <img src="{{ $news->thumbnail }}" alt="Thumbnail" class="img-thumbnail" style="height: 100px;">
                            </div>
                        @endif
                        <input type="file" name="thumbnail" class="form-control">
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small fw-bold mb-0">Konten</label>
                        <button type="button" id="btn-ai-news" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-stars me-1"></i> AI Improve
                        </button>
                    </div>
                    <textarea name="content" id="news-content" class="form-control" rows="15" required placeholder="Tulis isi berita di sini...">{{ old('content', $news->content) }}</textarea>
                    <div id="ai-news-status" class="mt-1 small text-muted d-none">AI sedang memproses...</div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="status" value="published" {{ $news->status == 'published' ? 'checked' : '' }} id="pubSwitch">
                    <label class="form-check-label fw-bold small" for="pubSwitch">Terbitkan</label>
                </div>

                <button type="submit" class="btn btn-alumni_smkn2 w-100 py-3 rounded-pill shadow">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('btn-ai-news').addEventListener('click', function() {
        const title = document.querySelector('input[name="title"]').value;
        const currentContent = document.getElementById('news-content').value;
        const status = document.getElementById('ai-news-status');
        const contentArea = document.getElementById('news-content');
        const btn = this;

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
            body: JSON.stringify({ 
                type: 'news', 
                input: `Judul: ${title}\n\nKonten Saat Ini: ${currentContent}` 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.content) {
                contentArea.value = data.content;
                Swal.fire({
                    icon: 'success',
                    title: 'Berita Berhasil Diperbarui!',
                    text: 'AI telah mengoptimalkan konten berita Anda.',
                    confirmButtonColor: '#6366f1'
                });
            } else {
                throw new Error(data.error || 'Gagal memproses konten');
            }
        })
        .catch(err => {
            Swal.fire('Error', err.message, 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-stars me-1"></i> AI Improve';
            status.classList.add('d-none');
        });
    });
</script>
@endpush
@endsection
