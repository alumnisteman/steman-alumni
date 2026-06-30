<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Selamat Datang di Alumni STEMAN</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 30px 15px; color: #333; }
    .wrap { max-width: 600px; margin: 0 auto; }
    .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

    /* Header */
    .header { background: linear-gradient(135deg, #1a3a5c 0%, #0f6b44 100%); padding: 40px 30px; text-align: center; }
    .logo-ring { width: 72px; height: 72px; background: rgba(255,255,255,0.15); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; font-size: 32px; }
    .header h1 { color: #fff; font-size: 22px; font-weight: 800; letter-spacing: -0.3px; }
    .header p { color: rgba(255,255,255,0.75); font-size: 13px; margin-top: 6px; }
    .badge-aktif { display: inline-block; background: #10b981; color: #fff; font-size: 11px; font-weight: 700; padding: 4px 14px; border-radius: 20px; letter-spacing: 0.5px; margin-top: 14px; }

    /* Body */
    .body { padding: 36px 30px; }
    .greeting { font-size: 18px; font-weight: 700; color: #1a3a5c; margin-bottom: 12px; }
    .intro { font-size: 14px; line-height: 1.7; color: #555; margin-bottom: 24px; }

    /* Info box */
    .info-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 12px; padding: 20px 22px; margin-bottom: 28px; }
    .info-box .row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid #dcfce7; font-size: 13px; }
    .info-box .row:last-child { border-bottom: none; }
    .info-box .label { color: #6b7280; font-weight: 600; }
    .info-box .val { color: #065f46; font-weight: 700; }

    /* CTA button */
    .cta-wrap { text-align: center; margin: 28px 0; }
    .cta-btn { display: inline-block; background: linear-gradient(135deg, #0f6b44, #10b981); color: #fff; text-decoration: none; padding: 14px 36px; border-radius: 50px; font-size: 15px; font-weight: 700; letter-spacing: 0.3px; box-shadow: 0 4px 14px rgba(16,185,129,0.35); }

    /* Steps */
    .steps-title { font-size: 13px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 14px; }
    .step { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 14px; }
    .step-num { flex-shrink: 0; width: 28px; height: 28px; background: #1a3a5c; color: #fff; border-radius: 50%; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
    .step-text { font-size: 13px; color: #555; line-height: 1.6; padding-top: 4px; }
    .step-text strong { color: #1a3a5c; }

    /* Footer */
    .footer { background: #f8fafc; border-top: 1px solid #e5e7eb; padding: 22px 30px; text-align: center; }
    .footer p { font-size: 11.5px; color: #9ca3af; line-height: 1.7; }
    .footer a { color: #0f6b44; text-decoration: none; font-weight: 600; }
    .footer .brand { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 6px; }
</style>
</head>
<body>
<div class="wrap">
<div class="card">

    <div class="header">
        <div class="logo-ring">🎓</div>
        <h1>Forum Alumni STEMAN</h1>
        <p>SMKN 2 Ternate — Koneksi Abadi, Kontribusi Tanpa Henti</p>
        <div class="badge-aktif">✓ AKUN AKTIF</div>
    </div>

    <div class="body">
        <div class="greeting">Halo, {{ $user->name }}! 👋</div>
        <p class="intro">
            Selamat bergabung! Akun Anda di <strong>Forum Alumni STEMAN</strong> telah aktif dan siap digunakan.
            Anda bisa langsung login tanpa menunggu verifikasi manual.
        </p>

        <div class="info-box">
            <div class="row">
                <span class="label">Nama Lengkap</span>
                <span class="val">{{ $user->name }}</span>
            </div>
            <div class="row">
                <span class="label">Email Login</span>
                <span class="val">{{ $user->email }}</span>
            </div>
            <div class="row">
                <span class="label">Status Akun</span>
                <span class="val">✅ Aktif &amp; Terverifikasi</span>
            </div>
            <div class="row">
                <span class="label">Terdaftar</span>
                <span class="val">{{ $user->created_at->format('d M Y, H:i') }} WIT</span>
            </div>
        </div>

        <div class="cta-wrap">
            <a href="{{ url('/profile') }}" class="cta-btn">Lengkapi Profil Saya →</a>
        </div>

        <div class="steps-title">Langkah Berikutnya</div>

        <div class="step">
            <div class="step-num">1</div>
            <div class="step-text"><strong>Lengkapi profil</strong> — Upload foto, isi angkatan, jurusan, dan pekerjaan saat ini agar alumni lain bisa mengenali Anda.</div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <div class="step-text"><strong>Jelajahi forum</strong> — Ikut diskusi, bagikan pengalaman, dan terhubung dengan sesama alumni dari berbagai angkatan.</div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-text"><strong>Bergabung ke komunitas</strong> — Lihat lowongan kerja, event reuni, dan info beasiswa yang dibagikan antar alumni.</div>
        </div>
    </div>

    <div class="footer">
        <p class="brand">Forum Silaturahmi Alumni STEMAN</p>
        <p>
            Email ini dikirim otomatis karena Anda baru saja mendaftar di
            <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>.<br>
            Jika Anda tidak merasa mendaftar, abaikan email ini.
        </p>
        <p style="margin-top: 10px;">
            &copy; {{ now()->year }} Alumni STEMAN — SMKN 2 Ternate
        </p>
    </div>

</div>
</div>
</body>
</html>
