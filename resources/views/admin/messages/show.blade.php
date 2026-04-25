@extends('layouts.admin')

@section('admin-content')
<div class="container py-4">
    <div class="mb-4">
        <a href="/admin/messages" class="btn btn-light rounded-pill px-4 mb-3">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Pesan Masuk
        </a>
    </div>

    <div class="card border-0 shadow-sm p-5" style="border-radius: 20px;">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="fw-black mb-1">{{ $message->subject }}</h4>
                <p class="text-muted mb-0 small">
                    Diterima: {{ $message->created_at->format('d M Y, H:i') }} &mdash; {{ $message->created_at->diffForHumans() }}
                </p>
            </div>
            <form method="POST" action="{{ route('admin.messages.destroy', $message->id) }}" onsubmit="return confirm('Hapus pesan ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger rounded-pill px-4">
                    <i class="bi bi-trash me-2"></i>Hapus
                </button>
            </form>
        </div>

        <hr>

        <!-- Sender Info -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase opacity-50">Nama Pengirim</label>
                <div class="fw-bold fs-5">{{ $message->name }}</div>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase opacity-50">Email Pengirim</label>
                <a href="mailto:{{ $message->email }}" class="fw-bold fs-5 text-decoration-none text-primary">
                    {{ $message->email }}
                </a>
            </div>
        </div>

        <!-- Message Body -->
        <div class="p-4 bg-light rounded-4 mb-4">
            <label class="form-label small fw-bold text-uppercase opacity-50 d-block mb-3">ISI PESAN</label>
            <p class="mb-0" style="white-space: pre-wrap; line-height: 1.8;">{{ $message->message }}</p>
        </div>

        <!-- AI Suggested Reply -->
        @if(!$message->reply_content && $message->is_ai_processed && $message->ai_suggested_reply)
            <div class="p-4 mb-4" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 20px; border: 1px solid #bae6fd;">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary">
                        <i class="bi bi-robot fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-primary">Saran Balasan AI (Google Gemini)</h6>
                        <small class="text-muted small">AI meninjau pesan ini dan menyarankan balasan berikut:</small>
                    </div>
                </div>
                <div class="p-3 bg-white bg-opacity-50 rounded-3 mb-3 border border-white">
                    <p class="mb-0 italic" id="ai-suggestion-text" style="white-space: pre-wrap;">{{ $message->ai_suggested_reply }}</p>
                </div>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-none" onclick="copyAiSuggestion()">
                    <i class="bi bi-magic me-2"></i> Gunakan Saran Ini
                </button>
            </div>

            <script>
                function copyAiSuggestion() {
                    const text = document.getElementById('ai-suggestion-text').innerText;
                    document.getElementById('reply-textarea').value = text;
                    document.getElementById('reply-textarea').focus();
                    
                    // Smooth scroll to reply box if needed
                    document.getElementById('reply-section').scrollIntoView({ behavior: 'smooth' });
                }
            </script>
        @elseif(!$message->reply_content && !$message->is_ai_processed)
            <div class="p-3 mb-4 bg-light bg-opacity-50 rounded-4 text-center border-dashed">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                <span class="text-muted small">AI sedang menyiapkan saran balasan...</span>
            </div>
        @endif

        <!-- Quick Reply -->
        <div class="mt-2" id="reply-section">
            @if(!$message->reply_content)
                <div class="p-4 bg-white border rounded-4 mt-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-reply-fill me-2"></i>Tulis Balasan Internal</h6>
                    <form method="POST" action="{{ route('admin.messages.reply', $message->id) }}">
                        @csrf
                        <div class="mb-3">
                            <textarea name="reply_content" id="reply-textarea" class="form-control" rows="5" placeholder="Ketik balasan untuk alumni..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-send-fill me-2"></i> Kirim Balasan
                        </button>
                    </form>
                </div>
            @else
                <div class="p-4 bg-primary bg-opacity-10 border-primary border-start border-4 rounded-end-4 mt-4">
                    <h6 class="fw-bold text-primary mb-2">Balasan Anda ({{ \Carbon\Carbon::parse($message->replied_at)->format('d M Y, H:i') }})</h6>
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $message->reply_content }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

