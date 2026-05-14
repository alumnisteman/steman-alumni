@extends('layouts.admin')

@section('admin-content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-light rounded-pill px-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="fw-black text-uppercase mb-0">✏️ Edit Fund</h2>
                <p class="text-muted small mb-0">{{ $campaign->title }}</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-4 mb-4">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5">
            <form action="{{ route('admin.campaigns.update', $campaign->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="row g-4">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Nama Fund <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control bg-light border-0 rounded-3 py-3" value="{{ old('title', $campaign->title) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Jenis Fund <span class="text-danger">*</span></label>
                        <select name="type" class="form-select bg-light border-0 rounded-3 py-3" required>
                            <option value="foundation" {{ $campaign->type == 'foundation' ? 'selected' : '' }}>💰 Dana Yayasan (Abadi)</option>
                            <option value="event" {{ $campaign->type == 'event' ? 'selected' : '' }}>🎉 Dana Reuni (Event)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Deskripsi Fund <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control bg-light border-0 rounded-3 py-3" rows="4" required>{{ old('description', $campaign->description) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Info Rekening Tujuan</label>
                        <textarea name="bank_info" class="form-control bg-light border-0 rounded-3 py-3" rows="3" placeholder="Contoh:&#10;Bank: BRI&#10;No. Rek: 0123-456-789&#10;Atas Nama: Forum Silaturahmi Alumni Steman Ternate">{{ old('bank_info', $campaign->bank_info) }}</textarea>
                        <small class="text-muted">Info ini akan ditampilkan di halaman form donasi alumni.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Target Dana (IDR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 fw-bold">Rp</span>
                            <input type="number" name="goal_amount" class="form-control bg-light border-0 rounded-end-3 py-3" value="{{ old('goal_amount', $campaign->goal_amount) }}" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Berakhir</label>
                        @php
                            $endDateVal = old('end_date');
                            if (!$endDateVal && $campaign->end_date) {
                                $endDateVal = is_string($campaign->end_date)
                                    ? $campaign->end_date
                                    : $campaign->end_date->format('Y-m-d');
                            }
                        @endphp
                        <input type="date" name="end_date" class="form-control bg-light border-0 rounded-3 py-3" value="{{ $endDateVal }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select bg-light border-0 rounded-3 py-3">
                            <option value="active" {{ $campaign->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ $campaign->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $campaign->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Gambar Cover</label>
                        @if($campaign->image)
                            <div class="mb-2"><img src="{{ asset('storage/' . $campaign->image) }}" class="rounded-3" style="height: 80px; object-fit: cover;"></div>
                        @endif
                        <input type="file" name="image" class="form-control bg-light border-0 rounded-3 py-3" accept="image/*">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ $campaign->is_featured ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_featured">Tampilkan sebagai Featured Fund</label>
                        </div>
                    </div>
                    
                    <!-- Fund Stats (read-only) -->
                    <div class="col-12">
                        <div class="bg-light rounded-4 p-3">
                            <div class="row g-3 text-center">
                                <div class="col">
                                    <div class="small text-muted">Terkumpul</div>
                                    <div class="fw-bold text-success">Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}</div>
                                </div>
                                <div class="col">
                                    <div class="small text-muted">Progress</div>
                                    <div class="fw-bold">{{ number_format($campaign->progress, 1) }}%</div>
                                </div>
                                <div class="col">
                                    <div class="small text-muted">Total Donatur</div>
                                    <div class="fw-bold">{{ $campaign->donations()->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-5">
                    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold">
                        <i class="bi bi-save me-2"></i> Perbarui Fund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
