<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pesan Kontak Baru</title>
<style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; color: #333; }
    .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .header { background: #1a1a2e; color: white; padding: 30px; text-align: center; }
    .header h1 { margin: 0; font-size: 22px; }
    .header p { margin: 5px 0 0; opacity: 0.7; font-size: 13px; }
    .badge { display: inline-block; background: #e74c3c; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; margin-top: 10px; }
    .body { padding: 30px; }
    .field { margin-bottom: 20px; }
    .field label { display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #999; margin-bottom: 5px; letter-spacing: 1px; }
    .field .value { font-size: 15px; color: #333; font-weight: 500; }
    .message-box { background: #f8f9fa; border-left: 4px solid #1a1a2e; padding: 16px 20px; border-radius: 8px; line-height: 1.7; white-space: pre-wrap; }
    .reply-btn { display: inline-block; background: #1a1a2e; color: white; padding: 12px 28px; border-radius: 30px; text-decoration: none; font-weight: bold; margin-top: 24px; font-size: 14px; }
    .footer { background: #f8f8f8; padding: 20px 30px; text-align: center; font-size: 12px; color: #aaa; border-top: 1px solid #eee; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📬 PESAN KONTAK BARU</h1>
        <p>Alumni SMKN 2 Ternate</p>
        <span class="badge">PESAN MASUK</span>
    </div>
    <div class="body">
        <div class="field">
            <label>Nama Pengirim</label>
            <div class="value">{{ $senderName }}</div>
        </div>
        <div class="field">
            <label>Alamat Email</label>
            <div class="value">{{ $senderEmail }}</div>
        </div>
        <div class="field">
            <label>Subjek Pesan</label>
            <div class="value">{{ $messageSubject }}</div>
        </div>
        <div class="field">
            <label>Isi Pesan</label>
            <div class="message-box">{{ $body }}</div>
        </div>
        <a href="mailto:{{ $senderEmail }}?subject=Re: {{ $messageSubject }}" class="reply-btn">
            ↩ BALAS PESAN INI
        </a>
    </div>
    <div class="footer">
        Pesan ini dikirim otomatis dari halaman Kontak website Alumni STEMAN.<br>
        Diterima pada {{ now()->format('d M Y, H:i') }} WIT
    </div>
</div>
</body>
</html>
