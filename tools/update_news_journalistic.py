import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

html_content = """<p><strong>TERNATE, 4 MEI 2026</strong> &ndash; Panitia Reuni Akbar STEMAN Ternate membenarkan telah terjadi insiden pengrusakan terhadap sejumlah fasilitas acara pada Senin, 4 Mei 2026, yang berlokasi tepat di depan SMKN 2 Ternate.</p>

<p>Berdasarkan hasil peninjauan awal di lokasi kejadian, kerusakan teridentifikasi pada beberapa fasilitas pendukung kegiatan, di antaranya baliho dan spanduk (<em>banner</em>). Panitia memastikan bahwa tidak terdapat korban jiwa maupun luka-luka dalam insiden ini. Meskipun demikian, kejadian tersebut menyebabkan gangguan terhadap proses persiapan serta kenyamanan pelaksanaan acara.</p>

<p>Menyikapi hal tersebut, saat ini panitia telah melakukan langkah-langkah sebagai berikut:</p>

<ul class="mb-4">
	<li>Mengumpulkan bukti berupa dokumentasi visual dan keterangan dari para saksi mata di lokasi.</li>
	<li>Melakukan penelusuran internal terkait kemungkinan penyebab dan pihak-pihak yang terlibat.</li>
	<li>Melaporkan kejadian ini secara resmi kepada pihak kepolisian guna proses investigasi lebih lanjut.</li>
</ul>

<p>Panitia menegaskan bahwa proses investigasi akan dilakukan secara objektif, profesional, dan penuh tanggung jawab. Kami juga mengimbau seluruh pihak untuk tidak menyebarkan informasi atau spekulasi yang belum terverifikasi demi menjaga suasana yang tetap kondusif.</p>

<p>Apabila ditemukan adanya unsur pelanggaran hukum, maka kejadian ini akan ditindaklanjuti sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.</p>

<p>Panitia tetap berkomitmen kuat untuk memastikan bahwa Reuni Akbar STEMAN Ternate dapat berjalan dengan aman, tertib, dan penuh semangat kebersamaan. Kami mengajak seluruh peserta serta pihak terkait untuk bersama-sama menjaga integritas dan nilai positif dari kegiatan ini.</p>

<p>Perkembangan lebih lanjut akan kami sampaikan secara resmi melalui kanal komunikasi panitia.</p>

<div class="mt-5 pt-4 border-top">
    <p class="fw-bold text-dark mb-1">Hormat kami,</p>
    <p class="fw-bold text-dark">Panitia Reuni Akbar STEMAN Ternate</p>
</div>"""

# Escape for PHP string
escaped_html = html_content.replace("\\", "\\\\").replace("'", "\\'").replace('"', '\\"')

php_code = f"""
$content = <<<'EOD'
{html_content}
EOD;
$news = \\App\\Models\\News::where('slug', 'siaran-pers-terkait-kejadian-pengrusakan-fasilitas-acara-reuni-akbar-steman-ternate-oUmnG')->first();
if ($news) {{
    $news->update(['content' => $content]);
    echo "SUCCESS: Updated news ID " . $news->id;
}} else {{
    echo "ERROR: News not found";
}}
"""

# Execute via Tinker
channel = ssh.invoke_shell()
channel.send('docker exec -i app php artisan tinker\n')
import time
time.sleep(2)
channel.send(php_code + '\n')
time.sleep(2)
output = channel.recv(10000).decode()
print(output)

ssh.close()
