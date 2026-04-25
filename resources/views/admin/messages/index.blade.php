@extends('layouts.admin')

@section('admin-content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title text-uppercase">PESAN MASUK</h2>
            <p class="text-muted fw-bold">Pesan yang dikirim dari halaman KONTAK.</p>
        </div>
        @if($unreadCount > 0)
            <span class="badge bg-danger fs-6 px-4 py-2 rounded-pill">{{ $unreadCount }} Belum Dibaca</span>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 py-3 px-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4" style="width: 40px;">#</th>
                        <th class="py-3">PENGIRIM</th>
                        <th class="py-3">SUBJEK</th>
                        <th class="py-3 text-center">STATUS</th>
                        <th class="py-3">WAKTU</th>
                        <th class="py-3 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $msg)
                    <tr class="{{ !$msg->is_read ? 'table-warning' : '' }}">
                        <td class="ps-4 fw-bold text-muted small">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold">{{ $msg->name }}</div>
                            <div class="small text-muted">{{ $msg->email }}</div>
                        </td>
                        <td>
                            <span class="{{ !$msg->is_read ? 'fw-bold' : 'text-muted' }}">{{ $msg->subject }}</span>
                        </td>
                        <td class="text-center">
                            @if(!$msg->is_read)
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">BARU</span>
                            @else
                                <span class="badge bg-light text-muted px-3 py-2 rounded-pill border">DIBACA</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $msg->created_at->diffForHumans() }}</td>
                        <td class="text-center">
                            <a href="/admin/messages/{{ $msg->id }}" class="btn btn-sm btn-primary rounded-pill px-3 me-1">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.messages.destroy', $msg->id) }}" class="d-inline" onsubmit="return confirm('Hapus pesan ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                            Belum ada pesan masuk.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $messages->links() }}
    </div>
</div>
@endsection

