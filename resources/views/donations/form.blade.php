@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="mb-4">
                <a href="{{ route('donations.index') }}" class="text-decoration-none text-muted small mb-2 d-inline-block">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard Fund
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-5 p-4 p-md-5">
                <div class="text-center mb-5">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-inline-block mb-3">
                        <i class="bi bi-heart-fill fs-2"></i>
                    </div>
                    <h3 class="fw-black mb-1">BERI DONASI</h3>
                    <p class="text-muted">Campaign: {{ $campaign->title }}</p>
                </div>

                <div class="alert alert-{{ $campaign->type === 'foundation' ? 'primary' : 'warning' }} border-0 rounded-4 p-4 mb-4" style="background: rgba(13, 110, 253, 0.05);">
                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Instruksi Transfer:</h6>
                    <p class="small mb-1 text-dark">Silakan transfer donasi untuk <b>{{ $campaign->title }}</b> ke rekening berikut:</p>
                    <div class="bg-white p-3 rounded-4 border border-{{ $campaign->type === 'foundation' ? 'primary' : 'warning' }} border-opacity-25 mt-2">
                        @if($campaign->bank_info)
                            <div class="white-space-pre-wrap small">{!! nl2br(e($campaign->bank_info)) !!}</div>
                        @else
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small text-muted">Bank:</span>
                                <span class="fw-bold">BANK MANDIRI</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small text-muted">No. Rekening:</span>
                                <span class="fw-bold text-primary">123-456-7890-00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Atas Nama:</span>
                                <span class="fw-bold text-uppercase">Forum Silaturahmi Alumni Steman Ternate</span>
                            </div>
                        @endif
                    </div>
                </div>

                <form action="{{ route('donations.store', $campaign->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nominal Donasi (IDR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 py-3 px-4 rounded-start-4 fw-bold">Rp</span>
                            <input type="number" name="amount" class="form-control bg-light border-0 py-3 px-4 rounded-end-4" placeholder="Min. 1.000" min="1000" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload Bukti Transfer <span class="text-danger">*</span></label>
                        <input type="file" name="proof" class="form-control bg-light border-0 py-3 px-4 rounded-4" accept="image/*" required>
                        <small class="text-muted">Format: JPG, PNG, WEBP (Max 2MB)</small>
                    </div>

                    <div class="mb-5">
                        <div class="form-check form-switch p-0">
                            <label class="form-check-label fw-bold me-2" for="is_anonymous">Sembunyikan Nama (Anonim)</label>
                            <input class="form-check-input float-end" type="checkbox" name="is_anonymous" id="is_anonymous">
                        </div>
                        <small class="text-muted">Jika diaktifkan, nama Anda tidak akan muncul di daftar publik donatur.</small>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-lg py-3">
                        KONFIRMASI DONASI 🚀
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
