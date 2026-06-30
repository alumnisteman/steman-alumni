<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Anggota Baru Mendaftar</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 30px 15px; color: #333; }
    .wrap { max-width: 600px; margin: 0 auto; }
    .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

    .header { background: linear-gradient(135deg, #1a3a5c 0%, #7c3aed 100%); padding: 32px 30px; text-align: center; }
    .header h1 { color: #fff; font-size: 20px; font-weight: 800; }
    .header p { color: rgba(255,255,255,0.75); font-size: 13px; margin-top: 6px; }
    .badge-new { display: inline-block; background: #f59e0b; color: #1a1a1a; font-size: 11px; font-weight: 800; padding: 4px 14px; border-radius: 20px; letter-spacing: 0.5px; margin-top: 12px; }

    .body { padding: 32px 30px; }
    .notice { background: #fef3c7; border: 1px solid #fbbf24; border-radius: 10px; padding: 14px 18px; font-size: 13.5px; color: #92400e; margin-bottom: 24px; line-height: 1.6; }

    .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 22px; margin-bottom: 24px; }
    .info-row { display: flex; padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #6b7280; font-weight: 600; width: 140px; flex-shrink: 0; }
    .info-val { color: #1e293b; font-weight: 500; }
    .badge-ok { display: inline-block; background: #10b981; color: #fff; font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 20px; }

    .cta-wrap { text-align: center; margin: 24px 0 8px; }
    .cta-btn { display: inline-block; background: linear-gradient(135deg, #1a3a5c, #7c3aed); color: #fff; text-decoration: none; padding: 13px 32px; border-radius: 50px; font-size: 14px; font-weight: 700; }

    .footer { background: #f8fafc; border-top: 1px solid #e5e7eb; padding: 20px 30px; text-align: center; }
    .footer p { font-size: 11.5px; color: #9ca3af; line-height: 1.7; }
    .footer .brand { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 6px; }
</style>
</head>
<body>
<div class="wrap">
<div class="card">

    <div class="header">
        <h1>👤 Anggota Baru Mendaftar</h1>
        <p>Forum Alumni STEMAN — Notifikasi Admin</p>
        <div class="badge-new">🔔 MEMBER BARU</div>
    </div>

    <div class="body">
        <div class="notice">
            Seorang alumni baru telah mendaftar dan akun mereka <strong>langsung aktif secara otomatis</strong>. Tidak diperlukan tindakan verifikasi manual.
        </div>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Nama Lengkap</span>
                <span class="info-val">{{ $newUser->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-val">{{ $newUser->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status Akun</span>
                <span class="info-val"><span class="badge-ok">✓ Auto-Approved</span></span>
            </div>
            <div class="info-row">
                <span class="info-label">Waktu Daftar</span>
                <span class="info-val">{{ $newUser->created_at ? $newUser->created_at->format('d M Y, H:i') . ' WIT' : now()->format('d M Y, H:i') . ' WIT' }}</span>
            </div>
            @if($newUser->phone)
            <div class="info-row">
                <span class="info-label">No. HP</span>
                <span class="info-val">{{ $newUser->phone }}</span>
            </div>
            @endif
        </div>

        <div class="cta-wrap">
            <a href="{{ url('/admin/users/' . $newUser->id) }}" class="cta-btn">Lihat Profil Member →</a>
        </div>
    </div>

    <div class="footer">
        <p class="brand">Admin Panel — Alumni STEMAN</p>
        <p>
            Email ini dikirim otomatis setiap kali ada anggota baru mendaftar.<br>
            Pantau semua member di: <a href="{{ url('/admin/users/auto-approved') }}" style="color:#7c3aed; font-weight:600;">Dashboard Auto-Approved</a>
        </p>
        <p style="margin-top: 8px;">&copy; {{ now()->year }} Alumni STEMAN — SMKN 2 Ternate</p>
    </div>

</div>
</div>
</body>
</html>
