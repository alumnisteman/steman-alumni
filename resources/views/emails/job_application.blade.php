<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lamaran Pekerjaan Baru</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { background-color: #ffffff; max-width: 600px; margin: 0 auto; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h2 { color: #003366; border-bottom: 2px solid #ffcc00; padding-bottom: 10px; margin-top: 0; }
        p { color: #555555; line-height: 1.6; }
        .details { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .details table { width: 100%; border-collapse: collapse; }
        .details th { text-align: left; padding: 8px 0; color: #333; width: 40%; }
        .details td { padding: 8px 0; color: #666; }
        .btn { display: inline-block; background-color: #003366; color: #ffffff !important; text-decoration: none; padding: 10px 20px; border-radius: 5px; margin-top: 20px; font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Lamaran Pekerjaan Baru</h2>
        <p>Halo,</p>
        <p>Anda menerima lamaran baru untuk posisi <strong>{{ $job->title }}</strong> melalui Portal Alumni STEMAN.</p>
        
        @if($coverLetter)
        <div style="background-color: #fff9e6; padding: 15px; border-left: 4px solid #ffcc00; margin: 20px 0; border-radius: 4px;">
            <strong>Pesan Pengantar (Cover Letter):</strong><br><br>
            {!! nl2br(e($coverLetter)) !!}
        </div>
        @endif

        <div class="details">
            <h3>Profil Pelamar (Resume Otomatis)</h3>
            <table>
                <tr>
                    <th>Nama Lengkap</th>
                    <td>{{ $applicant->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $applicant->email }}</td>
                </tr>
                <tr>
                    <th>No. Telepon / WhatsApp</th>
                    <td>{{ $applicant->phone_number ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Jurusan di STEMAN</th>
                    <td>{{ $applicant->major ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tahun Lulus</th>
                    <td>{{ $applicant->graduation_year ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Pekerjaan Saat Ini</th>
                    <td>{{ $applicant->current_job ?? 'Belum Bekerja / Mencari Peluang' }}</td>
                </tr>
                <tr>
                    <th>Instansi / Perusahaan</th>
                    <td>{{ $applicant->company_university ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Lokasi (address)</th>
                    <td>{{ $applicant->address ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <p style="text-align: center;">
            <a href="{{ url('/alumni/' . ($applicant->username ?? $applicant->id)) }}" class="btn">Lihat Profil Lengkap Pelamar</a>
        </p>
    </div>
    <div class="footer">
        Email ini dikirim secara otomatis oleh Sistem Portal Alumni STEMAN.<br>
        &copy; {{ date('Y') }} Ikatan Alumni STEMAN. All rights reserved.
    </div>
</body>
</html>
