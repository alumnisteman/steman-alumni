@extends('layouts.app')

@section('title', 'Alumni Radar - STEMAN Networking')

@push('styles')
<style>
    .radar-container {
        position: relative;
        width: 300px;
        height: 300px;
        margin: 40px auto;
        border-radius: 50%;
        background: rgba(67, 97, 238, 0.05);
        border: 1px solid rgba(67, 97, 238, 0.2);
        overflow: hidden;
    }

    .radar-pulse {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 2px solid #4361ee;
        animation: radar-pulse 3s infinite linear;
        opacity: 0;
    }

    .radar-pulse:nth-child(2) { animation-delay: 1s; }
    .radar-pulse:nth-child(3) { animation-delay: 2s; }

    @keyframes radar-pulse {
        0% { width: 0; height: 0; opacity: 1; }
        100% { width: 100%; height: 100%; opacity: 0; }
    }

    .radar-sweep {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 150px;
        height: 150px;
        background: linear-gradient(45deg, rgba(67, 97, 238, 0.4) 0%, transparent 50%);
        transform-origin: top left;
        animation: radar-sweep 4s infinite linear;
    }

    @keyframes radar-sweep {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .alumni-card-nearby {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .alumni-card-nearby:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.1);
        border-color: #4361ee;
    }

    .distance-badge {
        background: linear-gradient(135deg, #4361ee, #4cc9f0);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 10px;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-black text-white display-5 mb-2">ALUMNI RADAR</h1>
        <p class="text-muted">Menemukan koneksi alumni di sekitar lokasi Anda</p>
    </div>

    {{-- Radar Search State --}}
    <div id="radar-search" class="text-center py-5">
        <div class="radar-container">
            <div class="radar-pulse"></div>
            <div class="radar-pulse"></div>
            <div class="radar-pulse"></div>
            <div class="radar-sweep"></div>
            <div class="position-absolute top-50 start-50 translate-middle">
                <i class="bi bi-geo-alt-fill text-primary fs-1"></i>
            </div>
        </div>
        <h4 class="text-white mt-4 fw-bold" id="radar-status">Meminta Akses Lokasi...</h4>
        <p class="text-muted small">Harap izinkan akses lokasi pada browser Anda.</p>
    </div>

    {{-- Error State --}}
    <div id="radar-error" class="text-center py-5 d-none">
        <div class="mb-4">
            <i class="bi bi-exclamation-triangle text-warning display-1"></i>
        </div>
        <h3 class="text-white fw-bold" id="error-title">Gagal Mendeteksi Lokasi</h3>
        <p class="text-muted" id="error-message">Kami memerlukan izin lokasi untuk fitur ini.</p>
        <button class="btn btn-primary rounded-pill px-5 py-3 mt-3" onclick="location.reload()">
            Coba Lagi
        </button>
    </div>

    {{-- Results State --}}
    <div id="radar-results" class="d-none">
        <div class="row g-4" id="alumni-list">
            {{-- Dynamically populated --}}
        </div>

        <div id="no-results" class="text-center py-5 d-none">
            <i class="bi bi-person-x text-muted display-1 mb-3"></i>
            <h4 class="text-white">Tidak Ada Alumni di Sekitar</h4>
            <p class="text-muted">Jadilah yang pertama di daerah Anda!</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    fetchNearbyAlumni(lat, lng);
                },
                function(error) {
                    showError("Izin Lokasi Ditolak", "Harap izinkan akses lokasi untuk melihat alumni di sekitar Anda.");
                }
            );
        } else {
            showError("Tidak Didukung", "Browser Anda tidak mendukung fitur lokasi.");
        }

        function fetchNearbyAlumni(lat, lng) {
            document.getElementById('radar-status').innerText = 'Memindai Alumni...';
            
            fetch(`{{ route('alumni.networking.nearby') }}?lat=${lat}&lng=${lng}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayResults(data.recommendations);
                } else {
                    showError("Gagal Memindai", data.message);
                }
            })
            .catch(err => {
                showError("Error Server", "Gagal menghubungi server. Silakan coba lagi.");
            });
        }

        function displayResults(alumni) {
            document.getElementById('radar-search').classList.add('d-none');
            const resultsContainer = document.getElementById('radar-results');
            const listContainer = document.getElementById('alumni-list');
            
            resultsContainer.classList.remove('d-none');
            listContainer.innerHTML = '';

            if (alumni.length === 0) {
                document.getElementById('no-results').classList.remove('d-none');
                return;
            }

            alumni.forEach(person => {
                const card = `
                    <div class="col-md-6 col-lg-4">
                        <div class="alumni-card-nearby p-4 d-flex align-items-center gap-3">
                            <img src="${person.profile_picture}" class="rounded-circle shadow-sm" style="width: 70px; height: 70px; object-fit: cover; border: 3px solid rgba(67,97,238,0.3);">
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-white fw-bold mb-1 text-truncate">${person.name}</h5>
                                <div class="small text-muted mb-2">Angkatan ${person.graduation_year}</div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="distance-badge"><i class="bi bi-geo-alt-fill me-1"></i>${person.distance} km</span>
                                    <a href="/alumni/${person.id}" class="btn btn-link text-primary p-0 small fw-bold text-decoration-none">Lihat Profil <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                listContainer.innerHTML += card;
            });
        }

        function showError(title, message) {
            document.getElementById('radar-search').classList.add('d-none');
            document.getElementById('radar-error').classList.remove('d-none');
            document.getElementById('error-title').innerText = title;
            document.getElementById('error-message').innerText = message;
        }
    });
</script>
@endpush
