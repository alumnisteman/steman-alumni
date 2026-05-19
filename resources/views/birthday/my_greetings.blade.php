@extends('layouts.app')

@section('content')
<style>
.birthday-greetings-page {
    background: radial-gradient(circle at 50% 50%, #faf5ff 0%, #f3e8ff 100%);
    min-height: calc(100vh - 60px);
    padding: 3rem 0;
}
.dark .birthday-greetings-page {
    background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #0f0728 100%);
}

.greetings-header {
    background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
    border-radius: 24px;
    padding: 2.5rem;
    color: #fff;
    box-shadow: 0 10px 30px rgba(124, 58, 237, 0.15);
}

.greeting-card {
    background: #fff;
    border: 1px solid #e9d5ff;
    border-radius: 20px;
    padding: 1.5rem;
    transition: all 0.3s;
}
.dark .greeting-card {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.08);
}
.greeting-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(124, 58, 237, 0.08);
    border-color: #c084fc;
}
.greeting-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #f3e8ff;
}
.dark .greeting-avatar {
    border-color: rgba(255, 255, 255, 0.1);
}

.back-birthday-btn {
    color: #7c3aed;
    text-decoration: none;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: transform 0.2s;
}
.back-birthday-btn:hover {
    transform: translateX(-4px);
}
.dark .back-birthday-btn {
    color: #c084fc;
}
</style>

<div class="birthday-greetings-page">
    <div class="container">
        
        <!-- Back Link -->
        <div class="mb-4">
            <a href="{{ route('birthday.index') }}" class="back-birthday-btn">
                <i class="bi bi-arrow-left"></i> KEMBALI KE BIRTHDAY RADAR
            </a>
        </div>

        <!-- Header -->
        <div class="greetings-header text-center mb-5 position-relative overflow-hidden">
            <div style="font-size: 4rem; animation: pulse 2s infinite;" class="mb-2">📬</div>
            <h1 class="fw-black mb-1">Kotak Masuk Ucapan</h1>
            <p class="opacity-90 mb-0">Semua doa, harapan, dan ucapan hangat dari rekan-rekan alumni STEMAN untukmu! 🎂</p>
        </div>

        <!-- Greetings Feed -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @forelse ($greetings as $greet)
                <div class="greeting-card d-flex gap-4 align-items-start mb-3 shadow-sm">
                    <img src="{{ $greet->sender->profile_picture_url }}" class="greeting-avatar flex-shrink-0" alt="{{ $greet->sender->name }}">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark dark:text-white" style="color: #1e1b4b;">{{ $greet->sender->name }}</h6>
                                <span class="badge bg-purple-100 text-purple-800 rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.65rem; background: #faf5ff; color: #7c3aed; border: 1px solid rgba(124,58,237,0.2);">
                                    {{ $greet->sender->major }}
                                </span>
                            </div>
                            <span class="text-muted small" style="font-size: 0.75rem;">{{ $greet->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="mt-3 p-3 bg-light dark:bg-black dark:bg-opacity-20 rounded-3 text-dark dark:text-white" style="font-style: italic;">
                            <span class="fs-4 me-2">{{ $greet->emoji ?: '🎂' }}</span> "{{ $greet->message }}"
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 rounded-5 border border-dashed p-4" style="background: rgba(255,255,255,0.4);">
                    <div style="font-size: 4rem;">🎈</div>
                    <h5 class="mt-3 text-muted">Belum ada ucapan ulang tahun untukmu</h5>
                    <p class="text-muted small">Jangan khawatir, mari mulai dengan mengirimkan ucapan hangat kepada alumni lain yang merayakannya hari ini!</p>
                    <a href="{{ route('birthday.index') }}" class="btn btn-primary rounded-pill px-4" style="background: #7c3aed; border: none;">
                        Cari Alumni Ulang Tahun
                    </a>
                </div>
                @endforelse

                <!-- Pagination -->
                <div class="mt-4 d-flex justify-content-center">
                    {{ $greetings->links() }}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
