@extends('layouts.app')

@section('content')
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
            <form method="POST" action="/admin/messages/{{ $message->id }}" onsubmit="return confirm('Hapus pesan ini?')">
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

        <!-- Quick Reply -->
        <div class="mt-2">
            @if(!$message->reply_content)
                <div class="p-4 bg-white border rounded-4 mt-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-reply-fill me-2"></i>Tulis Balasan Internal</h6>
                    <form method="POST" action="{{ route('admin.messages.reply', $message->id) }}">
                        @csrf
                        <div class="mb-3">
                            <textarea name="reply_content" class="form-control" rows="4" placeholder="Ketik balasan untuk alumni..." required></textarea>
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
