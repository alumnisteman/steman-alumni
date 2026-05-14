<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Alumni Resmi - SMK N 2 Ternate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;900&family=Playfair+Display:ital,wght@0,700;1,900&display=swap');

        :root {
            --primary: #6366f1;
            --accent: #f43f5e;
            --dark: #0f172a;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: #0f172a; /* Solid fallback */
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(244, 63, 94, 0.15) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(99, 102, 241, 0.1) 0px, transparent 50%);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #f8fafc;
            overflow-x: hidden;
        }

        .cert-container {
            max-width: 850px;
            width: 100%;
            background: rgba(255, 255, 255, 0.05); /* Slightly more visible glass */
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 40px;
            position: relative;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
            padding: 4px;
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .cert-container:hover {
            transform: scale(1.01) translateY(-5px);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .cert-inner {
            background: #0f172a; /* Solid background */
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
            border-radius: 36px;
            padding: 80px 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cert-inner::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: conic-gradient(from 0deg, transparent, var(--primary), transparent, var(--accent), transparent);
            animation: rotate 10s linear infinite;
            opacity: 0.1;
            pointer-events: none;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .school-seal {
            width: 150px;
            height: auto;
            border-radius: 20px;
            margin-bottom: 40px;
            background: white;
            padding: 15px;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 2;
            border: 1px solid var(--glass-border);
        }

        .cert-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 900;
            background: linear-gradient(to right, #fff, #cbd5e1);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
            letter-spacing: -2px;
        }

        .cert-subtitle {
            text-transform: uppercase;
            letter-spacing: 4px;
            color: #94a3b8;
            font-size: 0.85rem;
            margin-bottom: 50px;
            font-weight: 600;
            opacity: 0.8;
        }

        .verification-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399 !important; /* Brighter green */
            padding: 10px 28px;
            border-radius: 100px;
            font-weight: 900;
            font-size: 0.75rem;
            border: 1px solid rgba(16, 185, 129, 0.3);
            margin-bottom: 50px;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
        }

        .alumni-name {
            font-size: 3rem;
            font-weight: 900;
            color: #fff;
            margin-bottom: 15px;
            letter-spacing: -2px;
            text-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .alumni-meta {
            font-size: 1.2rem;
            color: #94a3b8;
            margin-bottom: 60px;
        }

        .alumni-meta strong {
            color: #fff;
            font-weight: 600;
        }

        .info-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--glass-border);
            padding: 30px;
            border-radius: 24px;
            max-width: 600px;
            margin: 0 auto 60px;
            text-align: left;
            position: relative;
            z-index: 2;
        }

        .cert-footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .signature-box {
            text-align: left;
        }

        .hologram {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, var(--primary), var(--accent));
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.4);
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(5deg); }
        }

        .hologram i {
            font-size: 2.5rem;
            color: #fff;
        }

        .btn-genz {
            background: #fff;
            color: #000;
            border: none;
            padding: 12px 30px;
            border-radius: 100px;
            font-weight: 900;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-genz:hover {
            background: var(--primary);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.4);
        }

        @media (max-width: 768px) {
            .cert-inner { padding: 60px 30px; }
            .cert-title { font-size: 2.2rem; }
            .alumni-name { font-size: 2rem; }
            .cert-footer { flex-direction: column; gap: 40px; text-align: center; }
            .signature-box { text-align: center; }
            .hologram { margin: 0 auto; }
        }

        /* Animations */
        .animate-reveal {
            animation: reveal 1s cubic-bezier(0.19, 1, 0.22, 1) forwards;
            opacity: 1; /* Changed from 0 to 1 for immediate visibility */
            transform: translateY(0);
        }

        @keyframes reveal {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .text-muted {
            color: #94a3b8 !important;
        }

        /* Fallback for title if background-clip: text is not supported */
        .cert-title {
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="cert-container animate-reveal">
        <div class="cert-inner">
            <img src="{{ asset('images/logo.png') }}" class="school-seal" onerror="this.src='https://ui-avatars.com/api/?name=FORSA&background=6366f1&color=fff'">
            
            <h1 class="cert-title">Alumni Terverifikasi</h1>
            <p class="cert-subtitle">Jaringan Alumni STM Nasional Ternate - STM Negeri Ternate - SMK Negeri 2 Ternate</p>

            <div class="verification-badge">
                <i class="bi bi-patch-check-fill me-2"></i> Digital ID Terverifikasi
            </div>

            <p class="mb-2 text-muted small text-uppercase letter-spacing-2">Sertifikat Kelulusan Resmi</p>
            <h2 class="alumni-name">{{ strtoupper($user->name) }}</h2>
            
            <p class="alumni-meta">
                Jurusan <strong>{{ $user->major }}</strong><br>
                Tahun Kelulusan <strong>{{ $user->graduation_year }}</strong>
            </p>

            <div class="info-card">
                <p class="small text-muted mb-0 leading-relaxed">
                    Kredensial digital ini dihasilkan secara otomatis oleh <strong>Portal Alumni STEMAN</strong>. 
                    Ini berfungsi sebagai konfirmasi resmi status alumni dalam database sekolah yang terenkripsi per tanggal <strong>{{ now()->translatedFormat('d F Y') }}</strong>.
                </p>
            </div>

            <div class="cert-footer">
                <div class="signature-box">
                    <p class="mb-0 small fw-bold text-white">Verifikasi Kepala Sekolah</p>
                    <p class="mb-4 small text-muted">SMK Negeri 2 Ternate</p>
                    
                    <div class="d-flex align-items-center gap-3">
                        <div class="px-3 py-1 bg-white bg-opacity-5 rounded-pill border border-white border-opacity-10">
                            <span class="font-monospace text-muted x-small">ID: {{ strtoupper(substr($user->qr_login_token, 0, 8)) }}</span>
                        </div>
                        
                        @if(!auth()->check())
                            <a href="{{ route('auth.qr-login', $user->qr_login_token) }}" class="btn-genz">
                                <i class="bi bi-person-circle"></i> Klaim Profil
                            </a>
                        @else
                            <span class="badge bg-primary rounded-pill px-3 py-2">Milik Anda</span>
                        @endif
                    </div>
                </div>

                <div class="hologram">
                    <i class="bi bi-qr-code-scan"></i>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
