@extends('layouts.app')

@section('content')
<style>
.create-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%);
    padding: 40px 0 30px;
}
.create-card {
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
.dark .create-card { background: #1e293b; border-color: rgba(255,255,255,0.08); }
</style>

<section class="create-hero">
    <div class="container text-white">
        <a href="{{ route('polls.index') }}" class="btn btn-light btn-sm rounded-pill mb-3" style="color:#7c3aed;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <h2 class="fw-black mb-0">🗳️ Buat Polling Baru</h2>
        <p class="opacity-75 mt-1 mb-0">Buat pertanyaan seru untuk komunitas alumni</p>
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

            <div class="create-card">
                <form action="{{ route('polls.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Emoji</label>
                            <input type="text" name="emoji"
                                   class="form-control rounded-3 text-center fs-4 @error('emoji') is-invalid @enderror"
                                   value="{{ old('emoji', '🗳️') }}" maxlength="2">
                            @error('emoji')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-10">
                            <label class="form-label fw-semibold">Pertanyaan <span class="text-danger">*</span></label>
                            <input type="text" name="question"
                                   class="form-control rounded-3 @error('question') is-invalid @enderror"
                                   value="{{ old('question') }}"
                                   placeholder="Contoh: Guru paling berkesan di Steman?" required>
                            @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi (opsional)</label>
                            <input type="text" name="description"
                                   class="form-control rounded-3 @error('description') is-invalid @enderror"
                                   value="{{ old('description') }}"
                                   placeholder="Keterangan tambahan...">
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipe</label>
                            <select name="type" class="form-select rounded-3 @error('type') is-invalid @enderror">
                                <option value="single" {{ old('type', 'single') === 'single' ? 'selected' : '' }}>Pilihan Tunggal</option>
                                <option value="multiple" {{ old('type') === 'multiple' ? 'selected' : '' }}>Pilihan Ganda</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Berakhir (opsional)</label>
                            <input type="datetime-local" name="ends_at"
                                   class="form-control rounded-3 @error('ends_at') is-invalid @enderror"
                                   value="{{ old('ends_at') }}">
                            @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Opsi Tambahan</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="is_anonymous"
                                       id="createIsAnonymous" value="1"
                                       {{ old('is_anonymous') ? 'checked' : '' }}>
                                <label class="form-check-label" for="createIsAnonymous">Anonim</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Pilihan Jawaban <span class="text-danger">*</span></label>
                            <div id="optionsContainer">
                                @php $oldOptions = old('options', ['', '']); $oldEmojis = old('option_emojis', []); @endphp
                                @foreach ($oldOptions as $i => $opt)
                                <div class="d-flex gap-2 mb-2 option-row">
                                    <input type="text" name="option_emojis[]"
                                           class="form-control rounded-3 text-center"
                                           style="width: 60px; flex-shrink: 0;" placeholder="🔥" maxlength="2"
                                           value="{{ $oldEmojis[$i] ?? '' }}">
                                    <input type="text" name="options[]"
                                           class="form-control rounded-3"
                                           placeholder="Opsi {{ $i + 1 }}..."
                                           value="{{ $opt }}"
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
                                Minimal 2 opsi jawaban diperlukan.
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('polls.index') }}" class="btn btn-light rounded-pill px-4">
                            Batal
                        </a>
                        <button type="submit" class="vote-btn">
                            <i class="bi bi-send-fill me-2"></i>Buat Polling
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
let optCount = {{ count(old('options', ['', ''])) }};

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
</script>
@endpush
@endsection
