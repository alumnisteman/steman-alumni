<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Portal Alumni STEMAN</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f0f4f8; padding: 30px 16px; }
        .wrapper { max-width: 580px; margin: 0 auto; }
        .card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #003366 0%, #0055a5 100%); padding: 40px 36px; text-align: center; }
        .header img { width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,0.15); padding: 12px; margin-bottom: 16px; }
        .logo-icon { font-size: 48px; margin-bottom: 12px; display: block; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 800; letter-spacing: -0.3px; margin-bottom: 6px; }
        .header p { color: rgba(255,255,255,0.75); font-size: 13px; }
        .badge { display: inline-block; background: #ffcc00; color: #003366; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 20px; letter-spacing: 0.5px; margin-top: 12px; text-transform: uppercase; }
        .body { padding: 36px; }
        .greeting { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 12px; }
        .text { color: #555e6d; font-size: 14.5px; line-height: 1.75; margin-bottom: 16px; }
        .highlight-box { background: #f0f7ff; border-left: 4px solid #003366; border-radius: 0 10px 10px 0; padding: 16px 20px; margin: 24px 0; }
        .highlight-box p { color: #003366; font-size: 13.5px; font-weight: 600; margin-bottom: 6px; }
        .highlight-box ul { list-style: none; padding: 0; }
        .highlight-box ul li { color: #444; font-size: 13.5px; padding: 4px 0; }
        .highlight-box ul li::before { content: '✅ '; }
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #003366, #0055a5); color: #ffffff !important; text-decoration: none; padding: 14px 36px; border-radius: 50px; font-size: 15px; font-weight: 700; letter-spacing: 0.3px; }
        .divider { border: none; border-top: 1px solid #eef2f7; margin: 28px 0; }
        .info-row { display: flex; gap: 8px; align-items: flex-start; margin-bottom: 10px; }
        .info-label { font-size: 12px; color: #888; min-width: 90px; padding-top: 1px; }
        .info-value { font-size: 13.5px; color: #333; font-weight: 600; }
        .footer { background: #f8fafc; padding: 24px 36px; text-align: center; border-top: 1px solid #eef2f7; }
        .footer p { font-size: 12px; color: #aaa; line-height: 1.7; }
        .footer a { color: #003366; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <!-- Header -->
        <div class="header">
            <span class="logo-icon">🎓</span>
            <h1>Portal Alumni STEMAN</h1>
            <p>Sistem Manajemen Alumni Digital</p>
            <span class="badge">✅ Akun Aktif</span>
        </div>

        <!-- Body -->
        <div class="body">
            <p class="greeting">Halo, {{ $user->name }}! 👋</p>

            <p class="text">
                Selamat! Akun Anda di <strong>Portal Alumni STEMAN</strong> sudah aktif dan siap digunakan.
                Anda kini bisa langsung login dan menikmati semua fitur yang tersedia untuk alumni.
            </p>

            <div class="highlight-box">
                <p>Yang bisa Anda lakukan setelah login:</p>
                <ul>
                    <li>Lengkapi profil alumni Anda</li>
                    <li>Terhubung dengan sesama alumni</li>
                    <li>Cari & posting lowongan pekerjaan</li>
                    <li>Ikuti program & event alumni</li>
                    <li>Akses peta jaringan alumni STEMAN</li>
                </ul>
            </div>

            <div class="btn-wrap">
                <a href="{{ config('app.url') }}/login" class="btn">
                    🚀 Login Sekarang
                </a>
            </div>

            <hr class="divider">

            <div class="info-row">
                <span class="info-label">Nama</span>
                <span class="info-value">{{ $user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $user->email }}</span>
            </div>
            @if($user->graduation_year)
            <div class="info-row">
                <span class="info-label">Angkatan</span>
                <span class="info-value">{{ $user->graduation_year }}</span>
            </div>
            @endif
            @if($user->major)
            <div class="info-row">
                <span class="info-label">Jurusan</span>
                <span class="info-value">{{ $user->major }}</span>
            </div>
            @endif

            <hr class="divider">

            <p class="text" style="font-size:13px; color:#888;">
                Jika ada pertanyaan, hubungi kami di
                <a href="mailto:{{ config('mail.from.address') }}" style="color:#003366;font-weight:600;">{{ config('mail.from.address') }}</a>.
                Jangan balas email ini jika Anda tidak merasa mendaftar.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                © {{ date('Y') }} <strong>Alumni STEMAN</strong> &mdash; Portal Resmi Alumni<br>
                <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
