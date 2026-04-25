<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pemulihan Mandiri - STEMAN ALUMNI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .shield-card { max-width: 500px; width: 90%; background: #fff; border-radius: 24px; padding: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); text-align: center; border: 1px solid rgba(0,0,0,0.05); }
        .shield-icon { font-size: 4rem; color: #059669; margin-bottom: 20px; animation: pulse 2s infinite; }
        h4 { font-weight: 900; color: #1e293b; margin-bottom: 15px; }
        p { color: #64748b; line-height: 1.6; }
        .btn-home { background: #1e293b; color: #fff; border-radius: 12px; padding: 12px 30px; font-weight: 700; text-decoration: none; display: inline-block; margin-top: 20px; transition: all 0.3s; }
        .btn-home:hover { background: #000; transform: translateY(-2px); }
        @keyframes pulse { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.1); opacity: 0.8; } 100% { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body>
    <div class="shield-card">
        <div class="shield-icon">
            <i class="bi bi-shield-check"></i>
        </div>
        <h4>MODE PEMULIHAN AKTIF</h4>
        <p>{{ $message ?? 'Sistem mendeteksi adanya gangguan teknis kecil dan sedang melakukan pemulihan otomatis untuk menjaga keamanan data Anda.' }}</p>
        <p class="small text-muted">Tim teknis kami telah menerima laporan otomatis ini.</p>
        <a href="/" class="btn-home">Kembali ke Beranda</a>
    </div>
</body>
</html>
