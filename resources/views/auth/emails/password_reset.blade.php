<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password - Portal Alumni Steman</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background-color: #1e293b; color: #ffffff; padding: 40px 20px; text-center: center; }
        .content { padding: 40px; color: #334155; line-height: 1.6; }
        .button { display: inline-block; padding: 15px 30px; background-color: #ffcc00; color: #1e293b !important; text-decoration: none; border-radius: 30px; font-weight: bold; margin-top: 25px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; }
        h2 { margin-top: 0; color: #1e293b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header" style="text-align: center;">
            <h1 style="margin: 0; font-size: 24px; letter-spacing: 2px;">STEMAN ALUMNI</h1>
        </div>
        <div class="content">
            <h2>Halo, {{ $user->name }}!</h2>
            <p>Kami menerima permintaan untuk mereset password akun Anda di Portal Alumni Steman.</p>
            <p>Silakan klik tombol di bawah ini untuk melanjutkan proses penggantian password. Link ini akan kadaluarsa dalam 60 menit.</p>
            
            <div style="text-align: center;">
                <a href="{{ url('password/reset/'.$token) }}?email={{ urlencode($user->email) }}" class="button">RESET PASSWORD SAYA</a>
            </div>
            
            <p style="margin-top: 30px; font-size: 14px; color: #64748b;">Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini. Keamanan akun Anda tetap terjaga.</p>
        </div>
        <div class="footer">
            &copy; 2026 Ikatan Alumni SMKN 2 Ternate. Seluruh hak cipta dilindungi.<br>
            Jangan membalas email otomatis ini.
        </div>
    </div>
</body>
</html>
