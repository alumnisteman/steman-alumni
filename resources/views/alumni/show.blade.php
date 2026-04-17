@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="bg-warning p-5 text-center">
                    <img src="{{ $user->profile_picture_url }}" 
                         class="rounded-circle border border-4 border-white shadow-sm mb-3" width="180" height="180" style="object-fit: cover;">
                    <h2 class="fw-bold mb-1">{{ $user->name }}</h2>
                    <p class="lead opacity-75 mb-2 text-uppercase small fw-bold">{{ $user->major }} | Angkatan {{ $user->graduation_year }}</p>
                    <div class="d-inline-flex align-items-center bg-dark text-white px-3 py-1 rounded-pill small shadow-sm">
                        <i class="bi bi-shield-check me-2" style="color: #ffcc00;"></i>
                        <span class="fw-bold" style="font-size: 0.75rem;">Verified Member</span>
                        <span class="ms-2 opacity-50" style="font-size: 0.7rem;">ILUNI-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    @if($user->is_mentor)
                    <div class="mt-3">
                        <span class="badge bg-success text-white rounded-pill px-4 py-2 shadow-sm fs-6">
                            <i class="bi bi-patch-check-fill me-1"></i> Mentor Aktif
                        </span>
                    </div>
                    @endif
                </div>
                
                <div class="card-body p-5">
                    {{-- Mentor Section --}}
                    @if($user->is_mentor)
                    <div class="alert bg-primary-subtle border-0 rounded-4 p-4 mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-mortarboard-fill me-2"></i>Program Mentoring
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted d-block small">Keahlian Utama</label>
                                <span class="badge bg-primary rounded-pill px-3 py-2 fw-bold">
                                    <i class="bi bi-award me-1"></i> {{ $user->mentor_expertise ?? 'General Mentor' }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted d-block small">Poin Kontribusi</label>
                                <span class="fw-bold text-dark">
                                    <i class="bi bi-star-fill text-warning me-1"></i> {{ $user->points }} Poin
                                </span>
                            </div>
                        </div>
                        @if($user->mentor_bio)
                        <div class="mt-3">
                            <label class="text-muted d-block small">Pesan Mentor</label>
                            <p class="text-dark mb-0" style="white-space: pre-line;">{{ $user->mentor_bio }}</p>
                        </div>
                        @endif
                        @if($user->phone_number)
                        <div class="mt-3">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone_number) }}" target="_blank" class="btn btn-success rounded-pill fw-bold px-4">
                                <i class="bi bi-whatsapp me-2"></i> Hubungi via WhatsApp
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Informasi Pekerjaan</h6>
                            <div class="mb-4">
                                <label class="text-muted d-block small">Pekerjaan Sekarang</label>
                                <span class="fw-bold text-dark">{{ $user->current_job ?? 'Belum ditentukan' }}</span>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted d-block small">Instansi / Perusahaan</label>
                                <span class="fw-bold text-dark text-uppercase small">{{ $user->company_university ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Kontak</h6>
                            <div class="mb-4">
                                <label class="text-muted d-block small">address</label>
                                <span class="text-dark">{{ $user->address ?? '-' }}</span>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted d-block small">Email</label>
                                <span class="text-dark">{{ $user->email }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Social Links --}}
                    @if($user->show_social && $user->socialLinks->count() > 0)
                    <div class="mt-2 mb-4">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Jejaring Sosial</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($user->socialLinks as $link)
                            <a href="{{ $link->url }}" target="_blank" class="btn btn-outline-{{ $link->platform == 'instagram' ? 'danger' : ($link->platform == 'facebook' ? 'primary' : 'dark') }} rounded-pill px-3 py-2 small shadow-sm">
                                <i class="{{ $link->icon }} me-1"></i> {{ ucfirst($link->platform) }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mt-4">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Tentang Saya</h6>
                        <p class="text-dark" style="white-space: pre-line;">{{ $user->bio ?? 'Belum ada informasi tambahan.' }}</p>
                    </div>

                    <div class="text-center mt-5 pt-4 border-top">
                        <a href="/alumni" class="btn btn-outline-dark rounded-pill px-5">Kembali ke Daftar Alumni</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
