<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Verifikasi - Gagal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="text-center p-5 bg-white shadow-sm rounded-4" style="max-width: 400px;">
        <i class="bi bi-exclamation-triangle-fill text-danger display-1 mb-4"></i>
        <h4 class="fw-bold">Verifikasi Gagal</h4>
        <p class="text-muted">{{ $message }}</p>
        <hr>
        <a href="/" class="btn btn-dark rounded-pill px-4">Kembali ke Beranda</a>
    </div>
</body>
</html>
