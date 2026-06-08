@php
    $theme    = $eventTheme ?? null;
    $months   = ['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei',
                 '6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September',
                 '10'=>'Oktober','11'=>'November','12'=>'Desember'];
@endphp

@if($errors->any())
<div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Terdapat {{ $errors->count() }} kesalahan:</strong>
    <ul class="mb-0 mt-2 ps-3">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Preview Banner --}}
<div class="mb-4">
    <label class="form-label fw-bold small text-uppercase text-muted">Preview Banner</label>
    <div id="banner-preview" class="rounded-3 px-4 py-3 d-flex align-items-center gap-3 fw-bold"
         style="background: linear-gradient(90deg, {{ old('primary_color', $theme->primary_color ?? '#1d4ed8') }}, {{ old('secondary_color', $theme->secondary_color ?? '#1e3a8a') }});
                color: {{ old('accent_color', $theme->accent_color ?? '#ffffff') }}; font-size:0.9rem; min-height: 52px;">
        <i id="preview-icon" class="{{ old('banner_icon', $theme->banner_icon ?? 'bi bi-stars') }} fs-5"></i>
        <span id="preview-text">{{ old('banner_text', $theme->banner_text ?? 'Teks banner akan muncul di sini...') }}</span>
    </div>
</div>

<div class="row g-4">
    {{-- Nama & Deskripsi --}}
    <div class="col-md-8">
        <label class="form-label fw-semibold">Nama Tema <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror"
               value="{{ old('name', $theme->name ?? '') }}" placeholder="Contoh: HUT Kemerdekaan RI" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Emoji</label>
        <input type="text" name="emoji" class="form-control rounded-3 @error('emoji') is-invalid @enderror"
               value="{{ old('emoji', $theme->emoji ?? '') }}" placeholder="🎉" maxlength="10">
        @error('emoji')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Deskripsi Singkat</label>
        <input type="text" name="description" class="form-control rounded-3"
               value="{{ old('description', $theme->description ?? '') }}" placeholder="Keterangan singkat tema ini">
    </div>

    {{-- Periode --}}
    <div class="col-12">
        <hr class="opacity-15 my-1"><label class="form-label fw-bold text-uppercase small text-muted">📅 Periode Aktif (Tanggal Mulai & Selesai)</label>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold small">Bulan Mulai <span class="text-danger">*</span></label>
        <select name="start_month" class="form-select rounded-3 @error('start_month') is-invalid @enderror" required>
            @foreach($months as $num => $name)
                <option value="{{ $num }}" {{ old('start_month', $theme->start_month ?? '') == $num ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        @error('start_month')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold small">Tanggal Mulai <span class="text-danger">*</span></label>
        <input type="number" name="start_day" min="1" max="31" class="form-control rounded-3 @error('start_day') is-invalid @enderror"
               value="{{ old('start_day', $theme->start_day ?? '') }}" required>
        @error('start_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold small">Bulan Selesai <span class="text-danger">*</span></label>
        <select name="end_month" class="form-select rounded-3 @error('end_month') is-invalid @enderror" required>
            @foreach($months as $num => $name)
                <option value="{{ $num }}" {{ old('end_month', $theme->end_month ?? '') == $num ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        @error('end_month')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold small">Tanggal Selesai <span class="text-danger">*</span></label>
        <input type="number" name="end_day" min="1" max="31" class="form-control rounded-3 @error('end_day') is-invalid @enderror"
               value="{{ old('end_day', $theme->end_day ?? '') }}" required>
        @error('end_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Warna --}}
    <div class="col-12">
        <hr class="opacity-15 my-1"><label class="form-label fw-bold text-uppercase small text-muted">🎨 Warna Tema</label>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold small">Warna Utama <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="color" name="primary_color" class="form-control form-control-color rounded-start-3 flex-shrink-0"
                   value="{{ old('primary_color', $theme->primary_color ?? '#1d4ed8') }}" id="inp-primary" title="Pilih warna utama">
            <input type="text" class="form-control rounded-end-3" id="txt-primary"
                   value="{{ old('primary_color', $theme->primary_color ?? '#1d4ed8') }}" readonly>
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold small">Warna Sekunder <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="color" name="secondary_color" class="form-control form-control-color rounded-start-3 flex-shrink-0"
                   value="{{ old('secondary_color', $theme->secondary_color ?? '#1e3a8a') }}" id="inp-secondary">
            <input type="text" class="form-control rounded-end-3" id="txt-secondary"
                   value="{{ old('secondary_color', $theme->secondary_color ?? '#1e3a8a') }}" readonly>
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold small">Warna Aksen / Teks <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="color" name="accent_color" class="form-control form-control-color rounded-start-3 flex-shrink-0"
                   value="{{ old('accent_color', $theme->accent_color ?? '#ffffff') }}" id="inp-accent">
            <input type="text" class="form-control rounded-end-3" id="txt-accent"
                   value="{{ old('accent_color', $theme->accent_color ?? '#ffffff') }}" readonly>
        </div>
    </div>

    {{-- Banner --}}
    <div class="col-12">
        <hr class="opacity-15 my-1"><label class="form-label fw-bold text-uppercase small text-muted">📢 Konten Banner</label>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold small">CSS Class <span class="text-danger">*</span></label>
        <input type="text" name="css_class" class="form-control rounded-3 font-monospace @error('css_class') is-invalid @enderror"
               value="{{ old('css_class', $theme->css_class ?? '') }}" placeholder="theme-kemerdekaan" {{ $theme ? 'readonly' : '' }}>
        <div class="form-text">Gunakan huruf kecil dan tanda hubung. Tidak bisa diubah setelah disimpan.</div>
        @error('css_class')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold small">Icon Bootstrap <small class="text-muted">(opsional)</small></label>
        <input type="text" name="banner_icon" id="inp-banner-icon" class="form-control rounded-3"
               value="{{ old('banner_icon', $theme->banner_icon ?? '') }}" placeholder="bi bi-flag-fill">
        <div class="form-text"><a href="https://icons.getbootstrap.com" target="_blank" class="text-primary">Lihat daftar icon</a></div>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold small">Prioritas <span class="text-danger">*</span></label>
        <input type="number" name="priority" min="0" max="999" class="form-control rounded-3 @error('priority') is-invalid @enderror"
               value="{{ old('priority', $theme->priority ?? 50) }}">
        <div class="form-text">Nilai lebih tinggi = tema ini dipilih duluan jika ada tumpukan event.</div>
        @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-12">
        <label class="form-label fw-semibold small">Teks Banner Utama</label>
        <input type="text" name="banner_text" id="inp-banner-text" class="form-control rounded-3"
               value="{{ old('banner_text', $theme->banner_text ?? '') }}" placeholder="🇮🇩 Dirgahayu Republik Indonesia!">
    </div>
    <div class="col-md-12">
        <label class="form-label fw-semibold small">Sub-teks Banner <small class="text-muted">(tampil di layar besar)</small></label>
        <input type="text" name="banner_subtext" class="form-control rounded-3"
               value="{{ old('banner_subtext', $theme->banner_subtext ?? '') }}" placeholder="Keterangan tambahan di sebelah teks utama">
    </div>

    {{-- Countdown --}}
    <div class="col-12">
        <hr class="opacity-15 my-1">
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="show_countdown" id="chk-countdown" value="1"
                   {{ old('show_countdown', $theme->show_countdown ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="chk-countdown">
                ⏳ Tampilkan Countdown di Banner
            </label>
        </div>
    </div>
    <div id="countdown-fields" class="{{ old('show_countdown', $theme->show_countdown ?? false) ? '' : 'd-none' }}">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Bulan Target Countdown</label>
                <select name="countdown_month" class="form-select rounded-3">
                    <option value="">-- Pilih Bulan --</option>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ old('countdown_month', $theme->countdown_month ?? '') == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Tanggal Target Countdown</label>
                <input type="number" name="countdown_day" min="1" max="31" class="form-control rounded-3"
                       value="{{ old('countdown_day', $theme->countdown_day ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Label Countdown</label>
                <input type="text" name="countdown_label" class="form-control rounded-3"
                       value="{{ old('countdown_label', $theme->countdown_label ?? '') }}" placeholder="Menuju Hari H">
            </div>
        </div>
    </div>

    {{-- Status --}}
    <div class="col-12">
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="is_active" id="chk-active" value="1"
                   {{ old('is_active', $theme->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="chk-active">Aktifkan Tema Ini</label>
        </div>
        <div class="form-text">Hanya tema yang diaktifkan yang bisa tampil di website.</div>
    </div>
</div>

<script>
(function () {
    // Sinkronisasi color picker → text box & preview
    function syncColor(inputId, textId) {
        var inp = document.getElementById(inputId);
        var txt = document.getElementById(textId);
        if (!inp || !txt) return;
        inp.addEventListener('input', function () {
            txt.value = inp.value;
            updatePreview();
        });
    }
    syncColor('inp-primary',   'txt-primary');
    syncColor('inp-secondary', 'txt-secondary');
    syncColor('inp-accent',    'txt-accent');

    function updatePreview() {
        var primary   = document.getElementById('inp-primary')   ? document.getElementById('inp-primary').value   : '#1d4ed8';
        var secondary = document.getElementById('inp-secondary') ? document.getElementById('inp-secondary').value : '#1e3a8a';
        var accent    = document.getElementById('inp-accent')    ? document.getElementById('inp-accent').value    : '#ffffff';
        var text      = document.getElementById('inp-banner-text')  ? document.getElementById('inp-banner-text').value  : '';
        var icon      = document.getElementById('inp-banner-icon')  ? document.getElementById('inp-banner-icon').value  : '';

        var preview  = document.getElementById('banner-preview');
        var prevIcon = document.getElementById('preview-icon');
        var prevText = document.getElementById('preview-text');

        if (preview)  { preview.style.background = 'linear-gradient(90deg,' + primary + ',' + secondary + ')'; preview.style.color = accent; }
        if (prevIcon) { prevIcon.className = (icon || 'bi bi-stars') + ' fs-5'; }
        if (prevText) { prevText.textContent = text || 'Teks banner akan muncul di sini...'; }
    }

    var bannerTextInput = document.getElementById('inp-banner-text');
    var bannerIconInput = document.getElementById('inp-banner-icon');
    if (bannerTextInput) bannerTextInput.addEventListener('input', updatePreview);
    if (bannerIconInput) bannerIconInput.addEventListener('input', updatePreview);

    // Toggle countdown fields
    var chk = document.getElementById('chk-countdown');
    var fields = document.getElementById('countdown-fields');
    if (chk && fields) {
        chk.addEventListener('change', function () {
            fields.classList.toggle('d-none', !chk.checked);
        });
    }
})();
</script>
