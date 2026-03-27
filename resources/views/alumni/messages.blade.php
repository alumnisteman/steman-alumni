@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-1">Pesan Saya</h2>
            <p class="text-muted mb-0">Riwayat pesan yang Anda kirimkan melalui halaman kontak dan balasannya.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('alumni.dashboard') }}" class="btn btn-light rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>

    @if($messages->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="bi bi-inbox text-muted opacity-25" style="font-size: 4rem;"></i>
                <h5 class="mt-3 fw-bold text-muted">Belum ada pesan</h5>
                <p class="text-muted mb-4">Anda belum mengirimkan pesan kontak apapun.</p>
                <a href="{{ route('kontak') }}" class="btn btn-primary rounded-pill px-4">Kirim Pesan Sekarang</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($messages as $msg)
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $msg->subject }}</h5>
                                    <span class="badge {{ $msg->reply_content ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-3">
                                        {{ $msg->reply_content ? 'Dibalas' : 'Menunggu Balasan' }}
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-clock me-1"></i>{{ $msg->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                            
                            <div class="bg-light p-3 rounded-4 mb-3">
                                <span class="d-block small text-muted text-uppercase fw-bold mb-2">Pesan Anda:</span>
                                <p class="mb-0 text-dark">{{ $msg->message }}</p>
                            </div>

                            @if($msg->reply_content)
                                <div class="bg-primary bg-opacity-10 border-start border-primary border-4 p-3 rounded-end-4">
                                    <span class="d-block small text-primary text-uppercase fw-bold mb-2">Balasan Admin ({{ \Carbon\Carbon::parse($msg->replied_at)->format('d M Y, H:i') }}):</span>
                                    <p class="mb-0 text-dark">{{ $msg->reply_content }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $messages->links() }}
        </div>
    @endif
</div>
@endsection
