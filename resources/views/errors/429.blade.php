<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 – Akses Dibatasi | STEMAN Alumni</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/fonts/bootstrap-icons.min.css">
    <style>
        @font-face {
            font-family: "bootstrap-icons";
            src: url("/fonts/bootstrap-icons.woff2") format("woff2"),
                 url("/fonts/bootstrap-icons.woff") format("woff");
            font-display: block;
        }
    </style>
    <style>
        body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; }
        .card-429 { background: rgba(255,255,255,.05); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,.1); border-radius: 24px; padding: 3rem 2.5rem; max-width: 500px; width: 100%; text-align: center; }
        .icon-ring { width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #ef444422, #b9111122); border: 2px solid #ef444455; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{transform:scale(1); box-shadow:0 0 0 0 rgba(239,68,68,.3);} 50%{transform:scale(1.05); box-shadow:0 0 0 12px rgba(239,68,68,0);} }
        .badge-error { background: rgba(239,68,68,.15); color: #ef4444; border: 1px solid rgba(239,68,68,.3); border-radius: 50px; padding: .35rem 1rem; font-size: .8rem; font-weight: 700; letter-spacing: 2px; display: inline-block; margin-bottom: 1rem; }
        .btn-home { background: linear-gradient(135deg, #4f46e5, #7c3aed); border: none; border-radius: 50px; padding: .75rem 2rem; color: #fff; font-weight: 700; text-decoration: none; display: inline-block; transition: all .3s; }
        .btn-home:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(79,70,229,.4); color: #fff; }
        .info-box { background: rgba(239,68,68,.08); border: 1px solid rgba(239,68,68,.2); border-radius: 12px; padding: 1rem 1.25rem; margin: 1.5rem 0; text-align: left; }
        .info-box li { color: rgba(255,255,255,.7); font-size: .875rem; margin-bottom: .4rem; }
    </style>
</head>
<body>
    <div class="card-429">
        <div class="icon-ring">
            <i class="bi bi-shield-lock-fill text-danger" style="font-size: 2.5rem;"></i>
        </div>

        <div class="badge-error">AKSES DIBATASI</div>

        <h2 class="text-white fw-bold mb-2">Terlalu Banyak Percobaan</h2>
        <p class="text-white-50 mb-0" style="font-size: .95rem;">
            IP Anda diblokir sementara karena terlalu banyak percobaan login yang gagal. Sistem kami mendeteksi aktivitas yang mencurigakan.
        </p>

        <div class="info-box">
            <ul class="list-unstyled mb-0">
                <li><i class="bi bi-clock me-2 text-warning"></i> Blokir otomatis aktif untuk melindungi akun alumni</li>
                <li><i class="bi bi-shield-check me-2 text-success"></i> Silakan coba lagi setelah beberapa menit</li>
                <li><i class="bi bi-envelope me-2 text-info"></i> Hubungi admin jika Anda merasa ini adalah kesalahan</li>
            </ul>
        </div>

        <a href="{{ url('/login') }}" class="btn-home">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Login
        </a>

        <p class="text-white-50 mt-4 mb-0" style="font-size: .8rem;">
            Kode Error: 429 Too Many Requests &nbsp;·&nbsp; STEMAN Alumni Portal
        </p>
    </div>
</body>
</html>
