@extends('layouts.admin')

@section('admin-content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-light rounded-pill px-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="fw-black text-uppercase mb-0">📊 Edit Laporan Keuangan</h2>
        <p class="text-muted small mb-0">{{ $campaign->title }}</p>
    </div>
    <div class="ms-auto">
        <a href="{{ route('donations.show', $campaign->slug) }}" target="_blank" class="btn btn-outline-dark rounded-pill px-4 fw-bold btn-sm">
            <i class="bi bi-eye me-2"></i> Lihat Halaman Publik
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success border-0 rounded-4 mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill text-success"></i> {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger border-0 rounded-4 mb-4">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('admin.campaigns.report.update', $campaign->id) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="row g-4">

        {{-- ── Ringkasan Keuangan ────────────────────── --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-black mb-4">💰 Ringkasan Keuangan</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Total Penerimaan (Otomatis)</label>
                            <div class="form-control bg-light border-0 rounded-3 py-3 text-success fw-bold">
                                Rp {{ number_format($campaign->current_amount, 0, ',', '.') }}
                            </div>
                            <small class="text-muted">Dihitung otomatis dari donasi terverifikasi.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Total Pengeluaran (IDR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 fw-bold">Rp</span>
                                <input type="number" name="total_expense" class="form-control bg-light border-0 rounded-end-3 py-3"
                                    value="{{ old('total_expense', $campaign->total_expense ?? 0) }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Jumlah Sponsor / Mitra</label>
                            <input type="number" name="sponsor_count" class="form-control bg-light border-0 rounded-3 py-3"
                                value="{{ old('sponsor_count', $campaign->sponsor_count ?? 0) }}" min="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Distribusi Pengeluaran ───────────────── --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="fw-black mb-0">📊 Distribusi Pengeluaran</h5>
                        <button type="button" id="addDistRow" class="btn btn-sm btn-dark rounded-pill px-3">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Item
                        </button>
                    </div>
                    <p class="text-muted small mb-3">Masukkan kategori pengeluaran beserta persentasenya. Total persentase harus = 100%.</p>

                    <div id="distRows">
                        @php
                            $defaultDist = $campaign->expense_distribution ?? [
                                ['label' => 'Konsumsi', 'percentage' => 35, 'color' => '#3B82F6'],
                                ['label' => 'Operasional', 'percentage' => 22, 'color' => '#10B981'],
                                ['label' => 'Dokumentasi', 'percentage' => 15, 'color' => '#F59E0B'],
                                ['label' => 'Bantuan Sosial', 'percentage' => 10, 'color' => '#EF4444'],
                                ['label' => 'Portal Alumni', 'percentage' => 8, 'color' => '#8B5CF6'],
                                ['label' => 'Merchandise', 'percentage' => 6, 'color' => '#EC4899'],
                                ['label' => 'Administrasi', 'percentage' => 4, 'color' => '#6366F1'],
                            ];
                        @endphp
                        @foreach($defaultDist as $i => $dist)
                        <div class="dist-row d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-4">
                            <input type="color" name="dist_color[]" value="{{ $dist['color'] ?? '#6366f1' }}" class="form-control form-control-color border-0 rounded-3" style="width:48px;height:48px;">
                            <input type="text" name="dist_label[]" placeholder="Nama kategori" value="{{ $dist['label'] ?? '' }}"
                                class="form-control border-0 bg-white rounded-3 py-2 flex-grow-1" required>
                            <div class="input-group" style="max-width:140px;">
                                <input type="number" name="dist_percentage[]" placeholder="%" value="{{ $dist['percentage'] ?? '' }}"
                                    class="form-control border-0 bg-white rounded-start-3 py-2" min="0" max="100" step="0.1" required>
                                <span class="input-group-text bg-white border-0">%</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill remove-dist" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-2">
                        <span class="small text-muted">Total:</span>
                        <span id="totalPct" class="fw-bold small text-primary">0%</span>
                        <span class="small text-muted">(harus 100%)</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Status & Pengesahan ──────────────────── --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-black mb-4">✅ Status & Pengesahan</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Keterangan Status Laporan</label>
                            <input type="text" name="report_status" class="form-control bg-light border-0 rounded-3 py-3"
                                value="{{ old('report_status', $campaign->report_status ?? '') }}"
                                placeholder="Contoh: Laporan telah disahkan pada Rapat Pembubaran Panitia">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Pengesahan</label>
                            <input type="date" name="report_verified_at" class="form-control bg-light border-0 rounded-3 py-3"
                                value="{{ old('report_verified_at', $campaign->report_verified_at?->format('Y-m-d') ?? '') }}">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="show_donor_list" id="show_donor_list" value="1"
                                    {{ ($campaign->show_donor_list ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="show_donor_list">
                                    Tampilkan daftar donatur di halaman publik
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Upload Dokumen PDF ───────────────────── --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-black mb-4">📄 Dokumen PDF</h5>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">LPJ Lengkap (PDF)</label>
                        @if($campaign->lpj_pdf_path)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $campaign->lpj_pdf_path) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Lihat PDF Saat Ini
                            </a>
                        </div>
                        @endif
                        <input type="file" name="lpj_pdf" class="form-control bg-light border-0 rounded-3" accept=".pdf">
                        <small class="text-muted">Format: PDF, maks. 10MB</small>
                    </div>

                    <div>
                        <label class="form-label fw-bold small">Rincian Keuangan (PDF)</label>
                        @if($campaign->finance_detail_pdf_path)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $campaign->finance_detail_pdf_path) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Lihat PDF Saat Ini
                            </a>
                        </div>
                        @endif
                        <input type="file" name="finance_detail_pdf" class="form-control bg-light border-0 rounded-3" accept=".pdf">
                        <small class="text-muted">Format: PDF, maks. 10MB</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Foto Dokumentasi ────────────────────── --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-black mb-4">📸 Foto Dokumentasi Kegiatan</h5>

                    @if($campaign->documentation_images && count($campaign->documentation_images) > 0)
                    <div class="row g-2 mb-4">
                        @foreach($campaign->documentation_images as $img)
                        <div class="col-4 col-md-2">
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $img) }}" class="w-100 rounded-3" style="aspect-ratio:1;object-fit:cover;">
                            </div>
                        </div>
                        @endforeach
                        <div class="col-12">
                            <small class="text-muted">Upload foto baru akan <b>mengganti</b> semua foto yang ada.</small>
                        </div>
                    </div>
                    @endif

                    <input type="file" name="documentation_images[]" class="form-control bg-light border-0 rounded-3" accept="image/*" multiple>
                    <small class="text-muted">Bisa pilih banyak foto sekaligus. Format: JPG/PNG/WEBP, maks. 2MB per foto.</small>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="col-12 d-flex gap-3 justify-content-end">
            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-light rounded-pill px-5 fw-bold">Batal</a>
            <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold shadow-sm">
                <i class="bi bi-save me-2"></i> Simpan Laporan
            </button>
        </div>

    </div>
</form>

@push('scripts')
<script>
// ── Distribusi rows ──────────────────────────────────────
function updateTotal() {
    const inputs = document.querySelectorAll('input[name="dist_percentage[]"]');
    let total = 0;
    inputs.forEach(i => total += parseFloat(i.value) || 0);
    const el = document.getElementById('totalPct');
    el.textContent = total.toFixed(1) + '%';
    el.style.color = Math.abs(total - 100) < 0.5 ? '#059669' : '#dc2626';
}

document.getElementById('addDistRow').addEventListener('click', function () {
    const colors = ['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#6366F1','#0EA5E9'];
    const color = colors[Math.floor(Math.random() * colors.length)];
    const row = document.createElement('div');
    row.className = 'dist-row d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-4';
    row.innerHTML = `
        <input type="color" name="dist_color[]" value="${color}" class="form-control form-control-color border-0 rounded-3" style="width:48px;height:48px;">
        <input type="text" name="dist_label[]" placeholder="Nama kategori" class="form-control border-0 bg-white rounded-3 py-2 flex-grow-1" required>
        <div class="input-group" style="max-width:140px;">
            <input type="number" name="dist_percentage[]" placeholder="%" class="form-control border-0 bg-white rounded-start-3 py-2" min="0" max="100" step="0.1" required>
            <span class="input-group-text bg-white border-0">%</span>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill remove-dist"><i class="bi bi-trash"></i></button>
    `;
    document.getElementById('distRows').appendChild(row);
    bindRemove(row.querySelector('.remove-dist'));
    row.querySelector('input[name="dist_percentage[]"]').addEventListener('input', updateTotal);
});

function bindRemove(btn) {
    btn.addEventListener('click', function () {
        this.closest('.dist-row').remove();
        updateTotal();
    });
}

document.querySelectorAll('.remove-dist').forEach(bindRemove);
document.querySelectorAll('input[name="dist_percentage[]"]').forEach(i => i.addEventListener('input', updateTotal));
updateTotal();
</script>
@endpush
@endsection
