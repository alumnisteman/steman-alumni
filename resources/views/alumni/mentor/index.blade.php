@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden" style="min-height: 600px;">
                <div class="row g-0 h-100">
                    <!-- Left Panel: Assistant -->
                    <div class="col-md-4 bg-dark p-5 text-white d-flex flex-column justify-content-between">
                        <div>
                            <div class="bg-primary bg-opacity-20 p-3 rounded-circle d-inline-block mb-4">
                                <i class="bi bi-robot fs-1 text-primary"></i>
                            </div>
                            <h2 class="fw-black mb-3">AI CAREER MENTOR</h2>
                            <p class="text-white-50 leading-relaxed mb-4">
                                Ceritakan impian karir Anda, dan asisten AI kami akan mencarikan alumni terbaik yang bisa membantu Anda mencapainya.
                            </p>
                            <div class="badge bg-primary rounded-pill animate-pulse">POWERED BY GEMINI</div>
                        </div>
                        
                        <div class="p-4 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-10">
                            <h6 class="fw-bold mb-2 small"><i class="bi bi-lightbulb-fill text-warning me-2"></i>TIPS:</h6>
                            <p class="small text-white-50 mb-0">Tuliskan tujuan karir secara spesifik, misal: "Saya ingin jadi Cloud Engineer di luar negeri".</p>
                        </div>
                    </div>

                    <!-- Right Panel: Interaction -->
                    <div class="col-md-8 p-5 bg-white">
                        <div id="setupView">
                            <h4 class="fw-bold mb-4">Apa tujuan karir Anda?</h4>
                            <div class="mb-4">
                                <textarea id="goalInput" class="form-control border-0 bg-light p-4 rounded-4 fw-medium" rows="4" placeholder="Contoh: Saya ingin bekerja sebagai Software Architect atau membangun startup sendiri..."></textarea>
                            </div>
                            <button onclick="findMatches()" id="findBtn" class="btn btn-primary btn-lg rounded-pill px-5 fw-black shadow-lg">
                                CARI REKOMENDASI MENTOR <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>

                        <!-- Results View -->
                        <div id="resultsView" class="d-none">
                            <h4 class="fw-bold mb-4">Rekomendasi Networking Untuk Anda</h4>
                            <div id="mentorList" class="d-flex flex-column gap-3">
                                <!-- Mentors will appear here -->
                            </div>
                            <div class="mt-5 pt-4 border-top">
                                <button onclick="resetView()" class="btn btn-link text-decoration-none text-muted p-0 fw-bold">
                                    <i class="bi bi-arrow-left me-2"></i> Cari tujuan lain
                                </button>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div id="loadingView" class="d-none text-center py-5">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h5 class="fw-bold">AI sedang berpikir...</h5>
                            <p class="text-muted">Menganalisa ribuan profil alumni STEMAN</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mentor Registration Section -->
            @if(!auth()->user()->is_mentor)
            <div class="card border-0 shadow-sm mt-4 rounded-4 bg-primary text-white text-center p-4">
                <h5 class="fw-bold mb-2">Ingin Berbagi Pengalaman?</h5>
                <p class="mb-3 opacity-75">Bantu alumni lain mencapai impian mereka dengan menjadi Mentor.</p>
                <button type="button" class="btn btn-light rounded-pill fw-bold px-4 mx-auto" data-bs-toggle="modal" data-bs-target="#mentorRegisterModal">
                    Daftar Jadi Mentor
                </button>
            </div>
            @else
            <div class="card border-0 shadow-sm mt-4 rounded-4 bg-success text-white text-center p-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-patch-check-fill me-2"></i> Anda terdaftar sebagai Mentor Aktif</h5>
            </div>
            @endif

        </div>
    </div>
</div>

<!-- Mentor Registration Modal -->
<div class="modal fade" id="mentorRegisterModal" tabindex="-1" aria-labelledby="mentorRegisterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold" id="mentorRegisterModalLabel">Pendaftaran Mentor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('alumni.mentor.register') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Keahlian Utama (Expertise)</label>
                        <input type="text" name="mentor_expertise" class="form-control" placeholder="Contoh: Web Development, Project Management..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pesan Mentor (Bio)</label>
                        <textarea name="mentor_bio" class="form-control" rows="3" placeholder="Pesan singkat untuk mentee Anda..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Daftar Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function findMatches() {
        const goal = document.getElementById('goalInput').value;
        if (!goal) {
            alert('Silakan tuliskan tujuan karir Anda terlebih dahulu.');
            return;
        }

        document.getElementById('setupView').classList.add('d-none');
        document.getElementById('loadingView').classList.remove('d-none');

        fetch('{{ route("alumni.mentor.find") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ goal: goal })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingView').classList.add('d-none');
            document.getElementById('resultsView').classList.remove('d-none');
            
            const list = document.getElementById('mentorList');
            list.innerHTML = '';

            data.mentors.forEach(m => {
                const card = `
                    <div class="p-4 rounded-5 border-0 shadow-sm transition-all hover-up-small bg-light animate-reveal">
                        <div class="d-flex align-items-center gap-4">
                            <img src="${m.user.profile_picture ? '/storage/' + m.user.profile_picture : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(m.user.name)}" 
                                 class="rounded-circle border border-3 border-white shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="fw-black mb-1">${m.user.name}</h6>
                                <div class="badge bg-white text-dark border small mb-2">${m.user.current_job}</div>
                                <p class="small text-muted mb-0"><i class="bi bi-stars text-primary me-2"></i>${m.reason}</p>
                            </div>
                            <a href="/alumni/${m.user.id}" class="btn btn-dark rounded-pill px-4 btn-sm">Profil</a>
                        </div>
                    </div>
                `;
                list.innerHTML += card;
            });
        });
    }

    function resetView() {
        document.getElementById('resultsView').classList.add('d-none');
        document.getElementById('setupView').classList.remove('d-none');
        document.getElementById('goalInput').value = '';
    }
</script>
<style>
    .animate-reveal { animation: reveal 0.5s ease forwards; }
    @keyframes reveal { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush
@endsection
