@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                <div class="bg-dark p-4 text-center text-white">
                    <h4 class="fw-black text-uppercase mb-0 tracking-wider">⚡ TICKET SCANNER</h4>
                    <p class="small opacity-50 mb-0">Event Staff Authority</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div id="reader" class="rounded-4 overflow-hidden border-0 bg-light" style="width: 100%;"></div>
                    
                    <div id="scan-result" class="mt-4" style="display: none;">
                        <div class="alert rounded-4 p-4 d-flex align-items-center gap-3 shadow-sm border-0" id="result-alert">
                            <div class="bg-white rounded-circle p-2 shadow-sm" id="result-icon-container">
                                <i class="bi fs-2" id="result-icon"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1" id="result-title"></h6>
                                <p class="small mb-0" id="result-message"></p>
                            </div>
                        </div>
                        <button onclick="restartScanner()" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-lg">SCAN LAGI</button>
                    </div>

                    <div id="instructions" class="text-center mt-4">
                        <i class="bi bi-camera display-4 text-muted d-block mb-2"></i>
                        <p class="text-muted small">Izinkan akses kamera dan arahkan ke QR Code pada tiket peserta.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    const html5QrCode = new Html5Qrcode("reader");
    const scanResult = document.getElementById('scan-result');
    const instructions = document.getElementById('instructions');
    const resultAlert = document.getElementById('result-alert');
    const resultIcon = document.getElementById('result-icon');
    const resultTitle = document.getElementById('result-title');
    const resultMessage = document.getElementById('result-message');

    const qrConfig = { fps: 10, qrbox: { width: 250, height: 250 } };

    const startScanner = () => {
        html5QrCode.start(
            { facingMode: "environment" }, 
            qrConfig,
            (decodedText, decodedResult) => {
                // Success Scan
                console.log(`Scan success: ${decodedText}`);
                processTicket(decodedText);
            },
            (errorMessage) => {
                // ignore
            }
        ).catch((err) => {
            alert("Gagal mengakses kamera: " + err);
        });
    }

    const processTicket = (ticketCode) => {
        html5QrCode.stop().then(() => {
            instructions.style.display = 'none';
            scanResult.style.display = 'block';

            // Show loading state
            resultAlert.className = 'alert alert-light border-0 rounded-4 p-4 d-flex align-items-center gap-3 shadow-sm';
            resultTitle.innerText = 'Memproses...';
            resultMessage.innerText = 'Menghubungkan ke server...';

            fetch("{{ route('events.scan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ ticket_code: ticketCode })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultAlert.className = 'alert alert-success border-0 rounded-4 p-4 d-flex align-items-center gap-3 shadow-sm';
                    resultIcon.className = 'bi bi-check-circle-fill text-success';
                    resultTitle.innerText = 'BERHASIL!';
                    resultMessage.innerText = `${data.user} telah check-in untuk ${data.program}`;
                } else {
                    resultAlert.className = 'alert alert-danger border-0 rounded-4 p-4 d-flex align-items-center gap-3 shadow-sm';
                    resultIcon.className = 'bi bi-x-circle-fill text-danger';
                    resultTitle.innerText = 'GAGAL!';
                    resultMessage.innerText = data.message;
                }
            })
            .catch(error => {
                resultAlert.className = 'alert alert-danger border-0 rounded-4 p-4 d-flex align-items-center gap-3 shadow-sm';
                resultTitle.innerText = 'ERROR!';
                resultMessage.innerText = 'Terjadi kesalahan sistem.';
            });
        });
    }

    const restartScanner = () => {
        scanResult.style.display = 'none';
        instructions.style.display = 'block';
        startScanner();
    }

    window.onload = startScanner;
</script>
@endsection
