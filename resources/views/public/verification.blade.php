<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Alumni Resmi - SMK N 2 Ternate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;900&family=Playfair+Display:ital,wght@0,700;1,700&display=swap');

        body {
            background-color: #f8fafc;
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .cert-container {
            max-width: 800px;
            width: 100%;
            background: white;
            border: 20px solid #f1f5f9;
            position: relative;
            box-shadow: 0 40px 100px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .cert-inner {
            border: 2px solid #e2e8f0;
            margin: 10px;
            padding: 60px 40px;
            text-align: center;
            position: relative;
        }

        .cert-inner::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
            opacity: 0.03;
            pointer-events: none;
        }

        .school-seal {
            width: 120px;
            margin-bottom: 30px;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1));
        }

        .cert-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .cert-subtitle {
            text-transform: uppercase;
            letter-spacing: 5px;
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 40px;
            font-weight: 300;
        }

        .verification-badge {
            display: inline-flex;
            align-items: center;
            background: #f0fdf4;
            color: #16a34a;
            padding: 8px 24px;
            border-radius: 100px;
            font-weight: 800;
            font-size: 0.8rem;
            border: 1px solid #bbf7d0;
            margin-bottom: 40px;
        }

        .alumni-name {
            font-size: 2.2rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }

        .alumni-meta {
            font-size: 1.1rem;
            color: #475569;
            margin-bottom: 50px;
        }

        .cert-footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            text-align: left;
        }

        .signature-box {
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
            width: 200px;
        }

        .hologram {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #fef9c3 0%, #fde047 50%, #eab308 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            bottom: 40px;
            right: 40px;
            box-shadow: 0 0 30px rgba(234, 179, 8, 0.3);
            opacity: 0.8;
        }

        .hologram i {
            font-size: 3rem;
            color: rgba(255,255,255,0.8);
        }

        @media (max-width: 600px) {
            .cert-inner { padding: 40px 20px; }
            .cert-title { font-size: 1.8rem; }
            .alumni-name { font-size: 1.5rem; }
            .cert-footer { flex-direction: column; align-items: center; text-align: center; gap: 40px; }
            .signature-box { width: 100%; }
            .hologram { position: static; margin: 20px auto; }
        }
    </style>
</head>
<body>

    <div class="cert-container animate-reveal">
        <div class="cert-inner">
            <img src="{{ asset('images/logo.jpg') }}" class="school-seal" onerror="this.src='https://ui-avatars.com/api/?name=STEMAN&background=0f172a&color=fff'">
            
            <h1 class="cert-title">Surat Verifikasi Digital</h1>
            <p class="cert-subtitle">Jaringan Alumni SMK Negeri 2 Ternate</p>

            <div class="verification-badge">
                <i class="bi bi-patch-check-fill me-2"></i> STATUS: VERIFIED GRADUATE
            </div>

            <p class="mb-1 text-muted small text-uppercase letter-spacing-2">Dengan ini menerangkan bahwa:</p>
            <h2 class="alumni-name">{{ strtoupper($user->name) }}</h2>
            
            <p class="alumni-meta">
                Lulusan Program Studi <strong>{{ $user->major }}</strong><br>
                Tahun Kelulusan Angkatan <strong>{{ $user->graduation_year }}</strong>
            </p>

            <div class="p-4 bg-light rounded-4 mb-5 mx-auto" style="max-width: 500px; border-left: 5px solid #1e293b;">
                <p class="small text-muted mb-0 text-start leading-relaxed">
                    Data ini dihasilkan secara otomatis oleh <strong>Portal Alumni STEMAN</strong> dan bersifat valid. 
                    Verifikasi ini mengonfirmasi status keanggotaan alumni yang bersangkutan dalam database resmi sekolah per tanggal <strong>{{ now()->translatedFormat('d F Y') }}</strong>.
                </p>
            </div>

            <div class="cert-footer">
                <div class="signature-box">
                    <p class="mb-0 small fw-bold text-dark">Kepala Sekolah</p>
                    <p class="mb-0 small text-muted">SMK Negeri 2 Ternate</p>
                    <div class="mt-4 pt-2 border-top">
                        <p class="mb-0 small font-monospace opacity-50">DIGITAL_ID: {{ strtoupper(substr($user->qr_login_token, 0, 8)) }}</p>
                    </div>

                    @if(!auth()->check())
                    <div class="mt-3">
                        <a href="{{ route('auth.qr-login', $user->qr_login_token) }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold" style="font-size: 0.7rem;">
                            <i class="bi bi-key-fill me-1"></i> LOGIN SEBAGAI PEMILIK
                        </a>
                    </div>
                    @else
                    <div class="mt-3 text-start">
                        <span class="badge bg-primary rounded-pill small">ANDA ADALAH PEMILIK</span>
                    </div>
                    @endif
                </div>

                <div class="hologram">
                    <i class="bi bi-shield-lock"></i>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
