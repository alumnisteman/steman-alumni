@extends('layouts.admin')

@section('admin-content')
    <div class="mb-4">
        <h2 class="section-title">Tambah Podcast Baru</h2>
        <p class="text-muted small">Hubungkan cerita alumni dalam format audio.</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form action="{{ \Illuminate\Support\Facades\URL::route('admin.podcasts.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">JUDUL PODCAST</label>
                            <input type="text" name="title" class="form-control rounded-0 @error('title') is-invalid @enderror" placeholder="Contoh: Menembus Google dari Ternate" value="{{ \Illuminate\Support\Facades\Request::old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">DESKRIPSI / OVERVIEW</label>
                            <textarea name="description" class="form-control rounded-0 @error('description') is-invalid @enderror" rows="5" placeholder="Ringkasan isi podcast..." required>{{ \Illuminate\Support\Facades\Request::old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">NAMA TAMU (ALUMNI)</label>
                            <input type="text" name="guest_name" class="form-control rounded-0 @error('guest_name') is-invalid @enderror" placeholder="Nama Alumni" value="{{ \Illuminate\Support\Facades\Request::old('guest_name') }}" required>
                            @error('guest_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">KATEGORI</label>
                            <select name="category" class="form-select rounded-0 @error('category') is-invalid @enderror" required>
                                <option value="career" {{ \Illuminate\Support\Facades\Request::old('category') == 'career' ? 'selected' : '' }}>Career Protocol</option>
                                <option value="overseas" {{ \Illuminate\Support\Facades\Request::old('category') == 'overseas' ? 'selected' : '' }}>Global Uplink</option>
                                <option value="startup" {{ \Illuminate\Support\Facades\Request::old('category') == 'startup' ? 'selected' : '' }}>Startup Matrix</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">DURASI (MM:SS)</label>
                            <input type="text" name="duration" class="form-control rounded-0 @error('duration') is-invalid @enderror" placeholder="12:45" value="{{ \Illuminate\Support\Facades\Request::old('duration') }}" required>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">URL AUDIO (MP3/Spotify Embed)</label>
                            <input type="url" name="audio_url" class="form-control rounded-0 @error('audio_url') is-invalid @enderror" placeholder="https://domain.com/audio.mp3" value="{{ \Illuminate\Support\Facades\Request::old('audio_url') }}" required>
                            @error('audio_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text extra-small text-muted">Gunakan direct link file .mp3 atau link stream publik.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">URL THUMBNAIL IMAGE</label>
                            <input type="url" name="thumbnail_url" class="form-control rounded-0 @error('thumbnail_url') is-invalid @enderror" placeholder="https://domain.com/image.jpg" value="{{ \Illuminate\Support\Facades\Request::old('thumbnail_url') }}" required>
                            @error('thumbnail_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 border-top pt-3 mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_published" id="is_published" checked>
                                <label class="form-check-label small fw-bold" for="is_published">TERBITKAN SEKARANG</label>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ \Illuminate\Support\Facades\URL::route('admin.podcasts.index') }}" class="btn btn-light border rounded-0 px-4">BATAL</a>
                                <button type="submit" class="btn btn-alumni_smkn2 rounded-0 px-5 shadow-sm">SIMPAN PODCAST</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
