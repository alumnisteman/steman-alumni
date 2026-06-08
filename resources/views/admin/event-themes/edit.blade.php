@extends('layouts.admin')

@section('admin-content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-lg" style="border-radius: 20px;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                             style="width:48px;height:48px;background:linear-gradient(135deg,{{ $eventTheme->primary_color }},{{ $eventTheme->accent_color }});font-size:1.5rem;">
                            {{ $eventTheme->emoji ?? '🎨' }}
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Edit: {{ $eventTheme->name }}</h3>
                            <p class="text-muted small mb-0 font-monospace">{{ $eventTheme->css_class }}</p>
                        </div>
                    </div>
                    <span class="badge {{ $eventTheme->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-2 fs-6">
                        {{ $eventTheme->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <form action="{{ route('admin.event-themes.update', $eventTheme) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.event-themes.form')
                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 shadow-sm rounded-3 text-uppercase">
                            <i class="bi bi-save me-2"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.event-themes.index') }}" class="btn btn-link text-muted mt-2 text-center">Batal dan Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
