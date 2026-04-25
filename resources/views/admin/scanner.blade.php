@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid px-4 py-4">
    <div class="mb-5">
        <h2 class="fw-black text-dark mb-1"><i class="bi bi-qr-code-scan me-3 text-primary"></i>QR VERIFICATION SCANNER</h2>
        <p class="text-muted">Gunakan kamera untuk men-scan kartu digital alumni dan melakukan verifikasi instan.</p>
    </div>

    <div class="row g-4">
        <!-- Scanner Column -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden bg-dark">
                <div class="card-header bg-dark border-bottom border-secondary border-opacity-25 py-3 d-flex justify-content-between align-items-center">
                    <span class="text-white fw-bold small"><i class="bi bi-camera-fill me-2 text-primary"></i>LIVE CAMERA FEED</span>
                    <span class="badge bg-primary animate-pulse" id="scannerStatus">ACTIVE</span>
                </div>
                <div class="card-body p-0 position-relative" style="min-height: 400px; background: #000;">
                    <div id="reader" style="width: 100%;"></div>
                    <div id="scannerLine" class="position-absolute w-100 bg-primary opacity-50" style="height: 2px; top: 0; z-index: 10; display:none;"></div>
                </div>
                <div class="card-footer bg-dark border-0 p-4 text-center">
                    <button class="btn btn-outline-primary rounded-pill px-4" onclick="restartScanner()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Restart Scanner
                    </button>
                </div>
            </div>
        </div>

        <!-- Result Column -->
        <div class="col-lg-6">
            <div id="resultPlaceholder" class="card border-0 shadow-sm rounded-5 h-100 d-flex align-items-center justify-content-center p-5 bg-white border-2 border-dashed">
                <div class="text-center opacity-25">
                    <i class="bi bi-person-bounding-box display-1 mb-3"></i>
                    <h5 class="fw-bold">Menunggu Scan...</h5>
                    <p class="small">Arahkan QR Code kartu alumni ke kamera</p>
                </div>
            </div>

            <div id="resultCard" class="card border-0 shadow-lg rounded-5 h-100 p-0 overflow-hidden d-none">
                <div class="bg-primary p-4 text-white">
                    <div class="d-flex align-items-center gap-4">
                        <img id="resImage" src="" class="rounded-circle border border-4 border-white border-opacity-25 shadow" style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <h4 id="resName" class="fw-black mb-0"></h4>
                            <p id="resMajor" class="opacity-75 mb-0"></p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4">
                                <label class="small text-muted d-block mb-1">GRADUATION</label>
                                <span id="resYear" class="fw-bold fs-5"></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4">
                                <label class="small text-muted d-block mb-1">POINTS</label>
                                <span id="resPoints" class="fw-bold fs-5 text-primary"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small text-muted d-block mb-1">CURRENT JOB</label>
                        <div id="resJob" class="p-3 bg-light rounded-4 fw-bold"></div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="border-top pt-4">
                        <h6 class="fw-bold mb-3">QUICK ACTIONS</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button onclick="awardPoints(10)" class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-plus-circle me-2"></i>+10 Poin
                            </button>
                            <button onclick="awardPoints(50)" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-star-fill me-2"></i>+50 Poin
                            </button>
                            <a id="resProfileLink" href="#" class="btn btn-dark rounded-pill px-4">
                                <i class="bi bi-person-fill me-2"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                <div id="resMessage" class="card-footer bg-light border-0 p-3 text-center d-none">
                    <span class="text-success small fw-bold"></span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrCode;
    let currentUserId = null;

    function onScanSuccess(decodedText, decodedResult) {
        // Pause scanner but keep camera on if possible, or stop and restart later
        // We'll stop to avoid multiple hits
        html5QrCode.stop().then(() => {
            document.getElementById('scannerStatus').textContent = 'PROCESSING...';
            document.getElementById('scannerStatus').className = 'badge bg-warning';
            verifyToken(decodedText);
        });
    }

    function verifyToken(token) {
        fetch('{{ route("admin.scanner.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ token: token })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showResult(data.user);
            } else {
                alert(data.message);
                restartScanner();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            restartScanner();
        });
    }

    function showResult(user) {
        currentUserId = user.id;
        document.getElementById('resultPlaceholder').classList.add('d-none');
        document.getElementById('resultCard').classList.remove('d-none');
        
        document.getElementById('resName').textContent = user.name;
        document.getElementById('resMajor').textContent = user.major;
        document.getElementById('resYear').textContent = user.graduation_year;
        document.getElementById('resPoints').textContent = user.points;
        document.getElementById('resJob').textContent = user.current_job;
        document.getElementById('resProfileLink').href = '/admin/users/' + user.id + '/edit';
        
        if (user.profile_picture) {
            document.getElementById('resImage').src = '/storage/' + user.profile_picture;
        } else {
            document.getElementById('resImage').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name);
        }

        document.getElementById('scannerStatus').textContent = 'VERIFIED';
        document.getElementById('scannerStatus').className = 'badge bg-success';
    }

    function awardPoints(amount) {
        if (!currentUserId) return;

        fetch('{{ route("admin.scanner.award") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ user_id: currentUserId, amount: amount })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('resPoints').textContent = data.new_points;
                const msg = document.getElementById('resMessage');
                msg.classList.remove('d-none');
                msg.querySelector('span').textContent = data.message;
                
                // Shake effect on points
                const p = document.getElementById('resPoints');
                p.style.transition = '0.1s';
                p.style.transform = 'scale(1.5)';
                setTimeout(() => p.style.transform = 'scale(1)', 200);
            }
        });
    }

    function restartScanner() {
        document.getElementById('resultPlaceholder').classList.remove('d-none');
        document.getElementById('resultCard').classList.add('d-none');
        document.getElementById('resMessage').classList.add('d-none');
        currentUserId = null;
        
        startScanner();
    }

    function startScanner() {
        document.getElementById('scannerStatus').textContent = 'ACTIVE';
        document.getElementById('scannerStatus').className = 'badge bg-primary animate-pulse';
        
        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "environment" }, 
            {
                fps: 20,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess
        ).catch(err => {
            console.error("Scanner failed: ", err);
        });
    }

    window.onload = startScanner;
</script>

<style>
    .animate-pulse { animation: pulse-scanner 2s infinite ease-in-out; }
    @keyframes pulse-scanner {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    .animate-reveal { 
        animation: reveal 0.5s cubic-bezier(0.23, 1, 0.32, 1) forwards; 
    }
    @keyframes reveal {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    #reader video {
        border-radius: 0;
        object-fit: cover;
    }
</style>
@endpush
@endsection

