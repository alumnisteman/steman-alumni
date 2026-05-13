@extends('layouts.admin')

@section('admin-content')
    <div class="mb-4">
        <h2 class="section-title">Edit Podcast</h2>
        <p class="text-muted small">Memperbarui data transmisi podcast.</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.podcasts.update', $podcast->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">JUDUL PODCAST</label>
                            <input type="text" name="title" class="form-control rounded-0" value="{{ $podcast->title }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">DESKRIPSI / OVERVIEW</label>
                            <textarea name="description" class="form-control rounded-0" rows="5" required>{{ $podcast->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">NAMA TAMU (ALUMNI)</label>
                            <input type="text" name="guest_name" class="form-control rounded-0" value="{{ $podcast->guest_name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">KATEGORI</label>
                            <select name="category" class="form-select rounded-0" required>
                                <option value="career" {{ $podcast->category == 'career' ? 'selected' : '' }}>Career Protocol</option>
                                <option value="overseas" {{ $podcast->category == 'overseas' ? 'selected' : '' }}>Global Uplink</option>
                                <option value="startup" {{ $podcast->category == 'startup' ? 'selected' : '' }}>Startup Matrix</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">DURASI (MM:SS)</label>
                            <input type="text" name="duration" class="form-control rounded-0" value="{{ $podcast->duration }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">URL AUDIO (MP3/Spotify Embed)</label>
                            <input type="url" name="audio_link" class="form-control rounded-0" value="{{ $podcast->audio_link }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">URL THUMBNAIL IMAGE</label>
                            <input type="url" name="thumbnail_link" class="form-control rounded-0" value="{{ $podcast->thumbnail_link }}" required>
                        </div>
                    </div>

                    <div class="col-12 border-top pt-3 mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_published" id="is_published" {{ $podcast->is_published ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="is_published">TERBITKAN</label>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.podcasts.index') }}" class="btn btn-light border rounded-0 px-4">BATAL</a>
                                <button type="submit" class="btn btn-primary rounded-0 px-5 shadow-sm">SIMPAN PERUBAHAN</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
