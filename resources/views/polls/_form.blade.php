@csrf

<div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $poll->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description (optional)</label>
    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $poll->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Options</label>
    <div id="options-wrapper">
        @php
            $options = old('options', $poll->options ?? ['']);
        @endphp
        @foreach($options as $opt)
            <div class="input-group mb-2 option-item">
                <input type="text" class="form-control" name="options[]" value="{{ $opt }}" required>
                <button type="button" class="btn btn-outline-danger remove-option">&times;</button>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn btn-outline-primary" id="add-option">Add Option</button>
</div>

<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $poll->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<button type="submit" class="btn btn-success">Save Poll</button>

@push('scripts')
<script>
document.getElementById('add-option').addEventListener('click', function () {
    const wrapper = document.getElementById('options-wrapper');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 option-item';
    div.innerHTML = `
        <input type="text" class="form-control" name="options[]" required>
        <button type="button" class="btn btn-outline-danger remove-option">&times;</button>
    `;
    wrapper.appendChild(div);
});

document.getElementById('options-wrapper').addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('remove-option')) {
        e.target.closest('.option-item').remove();
    }
});
</script>
@endpush
