<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>419 – Sesi Formulir Kedaluwarsa | STEMAN Alumni</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; }
        .card-419 { background: rgba(255,255,255,.05); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,.1); border-radius: 24px; padding: 3rem 2.5rem; max-width: 480px; width: 100%; text-align: center; }
        .icon-ring { width: 96px; height: 96px; border-radius: 50%; background: linear-gradient(135deg, #f59e0b22, #ef444422); border: 2px solid #f59e0b55; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.05)} }
        .countdown-bar { height: 6px; background: #334155; border-radius: 3px; overflow: hidden; margin: 1.5rem 0; }
        .countdown-fill { height: 100%; background: linear-gradient(90deg, #f59e0b, #ef4444); border-radius: 3px; transition: width 1s linear; }
        .btn-retry { background: linear-gradient(135deg, #f59e0b, #d97706); border: none; border-radius: 50px; padding: .75rem 2rem; color: #000; font-weight: 700; }
        .btn-home { background: transparent; border: 1px solid rgba(255,255,255,.2); border-radius: 50px; padding: .75rem 2rem; color: rgba(255,255,255,.7); }
    </style>
</head>
<body>
<div class="card-419">
    <div class="icon-ring">
        <i class="bi bi-clock-history" style="font-size:2.5rem;color:#f59e0b;"></i>
    </div>
    <h2 class="text-white fw-bold mb-1">Sesi Formulir Kedaluwarsa</h2>
    <p class="text-white-50 mb-1" style="font-size:.9rem;">Token keamanan halaman ini sudah tidak berlaku.</p>
    <p class="text-white-50" style="font-size:.85rem;">Ini terjadi bila Anda membiarkan halaman terlalu lama. Klik tombol di bawah untuk kembali dan coba lagi.</p>

    <div class="countdown-bar">
        <div class="countdown-fill" id="bar" style="width:100%"></div>
    </div>
    <p class="text-white-50 mb-3" style="font-size:.8rem;">Kembali otomatis dalam <strong id="sec" class="text-warning">10</strong> detik…</p>

    <div class="d-flex gap-3 justify-content-center flex-wrap">
        <button class="btn btn-retry" onclick="goBack()">
            <i class="bi bi-arrow-counterclockwise me-2"></i>Kembali & Coba Lagi
        </button>
        <a href="/" class="btn btn-home">
            <i class="bi bi-house-fill me-2"></i>Beranda
        </a>
    </div>
</div>

<script>
    let t = 10;
    const bar = document.getElementById('bar');
    const sec = document.getElementById('sec');
    const iv = setInterval(() => {
        t--;
        sec.textContent = t;
        bar.style.width = (t / 10 * 100) + '%';
        if (t <= 0) { clearInterval(iv); goBack(); }
    }, 1000);

    function goBack() {
        // Refresh CSRF token then go back
        fetch('/csrf-refresh', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(() => { history.length > 1 ? history.back() : (window.location.href = '/'); })
            .catch(() => { history.length > 1 ? history.back() : (window.location.href = '/'); });
    }
</script>
</body>
</html>
