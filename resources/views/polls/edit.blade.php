@extends('layouts.app')

@section('content')
<style>
.edit-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%);
    padding: 40px 0 30px;
}
.edit-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    padding: 2rem;
    margin-bottom: 2rem;
}
.vote-btn {
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    color: #fff;
    border: none;
    border-radius: 30px;
    padding: 10px 28px;
    font-weight: 700;
    font-size: 0.9rem;
    transition: all 0.2s;
}
.vote-btn:hover { transform: scale(1.03); box-shadow: 0 6px 20px rgba(124,58,237,0.35); color: #fff; }
.dark .edit-card { background: #1e293b; border-color: rgba(255,255,255,0.08); }
</style>

<section class="edit-hero">
    <div class="container text-white">
        <a href="{{ route('polls.index') }}" class="btn btn-light btn-sm rounded-pill mb-3" style="color:#7c3aed;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <h2 class="fw-black mb-0">✏️ Edit Polling</h2>
        <p class="opacity-75 mt-1 mb-0">Perbarui pertanyaan, opsi, atau status polling</p>
    </div>
</section>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            @if ($errors->any())
            <div class="alert alert-danger rounded-4 mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="edit-card">
                <form action="{{ route('polls.update', $poll) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Emoji</label>
                            <input type="text" name="emoji"
                                   class="form-control rounded-3 text-center fs-4 @error('emoji') is-invalid @enderror"
                                   value="{{ old('emoji', $poll->emoji ?? '🗳️') }}" maxlength="2">
                            @error('emoji')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-10">
                            <label class="form-label fw-semibold">Pertanyaan <span class="text-danger">*</span></label>
                            <input type="text" name="question"
                                   class="form-control rounded-3 @error('question') is-invalid @enderror"
                                   value="{{ old('question', $poll->question ?? $poll->title) }}"
                                   placeholder="Contoh: Guru paling galak di Steman?" required>
                            @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi (opsional)</label>
                            <input type="text" name="description"
                                   class="form-control rounded-3 @error('description') is-invalid @enderror"
                                   value="{{ old('description', $poll->description) }}"
                                   placeholder="Keterangan tambahan...">
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipe</label>
                            <select name="type" class="form-select rounded-3 @error('type') is-invalid @enderror">
                                <option value="single" {{ old('type', $poll->type ?? 'single') === 'single' ? 'selected' : '' }}>Pilihan Tunggal</option>
                                <option value="multiple" {{ old('type', $poll->type ?? 'single') === 'multiple' ? 'selected' : '' }}>Pilihan Ganda</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Berakhir (opsional)</label>
                            <input type="datetime-local" name="ends_at"
                                   class="form-control rounded-3 @error('ends_at') is-invalid @enderror"
                                   value="{{ old('ends_at', $poll->ends_at ? $poll->ends_at->format('Y-m-d\TH:i') : '') }}">
                            @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status & Visibilitas</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="is_active"
                                       id="editIsActive" value="1"
                                       {{ old('is_active', $poll->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="editIsActive">Polling Aktif</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_anonymous"
                                       id="editIsAnonymous" value="1"
                                       {{ old('is_anonymous', $poll->is_anonymous ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="editIsAnonymous">Anonim</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Pilihan Jawaban <span class="text-danger">*</span></label>
                            <div id="optionsContainer">
                                @php
                                    $existingOptions = $poll->options ?? collect([]);
                                    $optionsList = old('options')
                                        ? collect(old('options'))->zip(collect(old('option_emojis', [])))
                                              ->map(fn($pair) => (object)['option_text' => $pair[0], 'option_emoji' => $pair[1]])
                                        : $existingOptions;
                                @endphp
                                @foreach ($optionsList as $i => $opt)
                                <div class="d-flex gap-2 mb-2 option-row">
                                    <input type="text" name="option_emojis[]"
                                           class="form-control rounded-3 text-center"
                                           style="width: 60px; flex-shrink: 0;" placeholder="🔥" maxlength="2"
                                           value="{{ is_object($opt) ? ($opt->option_emoji ?? '') : '' }}">
                                    <input type="text" name="options[]"
                                           class="form-control rounded-3"
                                           placeholder="Opsi {{ $i + 1 }}..."
                                           value="{{ is_object($opt) ? ($opt->option_text ?? '') : (is_string($opt) ? $opt : '') }}"
                                           {{ $i < 2 ? 'required' : '' }}>
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-3"
                                            style="flex-shrink:0;"
                                            onclick="removeOption(this)">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill mt-1"
                                    onclick="addOption()">
                                <i class="bi bi-plus"></i> Tambah Opsi
                            </button>
                            <p class="text-muted small mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Jumlah vote yang sudah masuk akan tetap tersimpan untuk opsi yang tidak diubah.
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('polls.index') }}" class="btn btn-light rounded-pill px-4">
                            Batal
                        </a>
                        <button type="submit" class="vote-btn">
                            <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Danger Zone --}}
            <div class="edit-card border-danger">
                <h6 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>Zona Berbahaya</h6>
                <p class="text-muted small mb-3">Menghapus polling akan menghapus semua data suara secara permanen dan tidak dapat dikembalikan.</p>
                <form action="{{ route('polls.destroy', $poll) }}" method="POST" id="deleteFormPage">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4"
                            onclick="confirmDeletePage()">
                        <i class="bi bi-trash-fill me-2"></i>Hapus Polling Ini
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
let optCount = {{ $optionsList->count() }};

function addOption() {
    if (optCount >= 10) return;
    optCount++;
    document.getElementById('optionsContainer').insertAdjacentHTML('beforeend', `
        <div class="d-flex gap-2 mb-2 option-row">
            <input type="text" name="option_emojis[]" class="form-control rounded-3 text-center"
                   style="width:60px;flex-shrink:0;" placeholder="✨" maxlength="2">
            <input type="text" name="options[]" class="form-control rounded-3" placeholder="Opsi ${optCount}...">
            <button type="button" class="btn btn-outline-danger btn-sm rounded-3"
                    style="flex-shrink:0;" onclick="removeOption(this)">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    `);
}

function removeOption(btn) {
    const rows = document.querySelectorAll('.option-row');
    if (rows.length <= 2) {
        alert('Minimal harus ada 2 opsi jawaban.');
        return;
    }
    btn.closest('.option-row').remove();
    optCount--;
}

function confirmDeletePage() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Polling?',
            text: 'Polling dan semua data suara akan dihapus permanen.',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('deleteFormPage').submit();
            }
        });
    } else {
        if (confirm('Hapus polling ini? Tindakan ini tidak dapat dibatalkan.')) {
            document.getElementById('deleteFormPage').submit();
        }
    }
}
</script>
@endpush
@endsection
