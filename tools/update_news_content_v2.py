import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

content = """Ternate, 4 Mei 2026

Panitia Reuni Akbar STEMAN Ternate menyampaikan bahwa telah terjadi insiden pengrusakan terhadap sebagian fasilitas acara pada Senin, 4 Mei 2026, yang berlokasi di depan SMKN 2 Ternate.

Berdasarkan hasil peninjauan awal, kerusakan meliputi beberapa fasilitas, di antaranya baliho dan banner. Tidak terdapat korban jiwa dalam kejadian ini. Namun demikian, insiden tersebut menyebabkan gangguan terhadap persiapan serta kenyamanan pelaksanaan acara.

Saat ini, panitia telah melakukan langkah-langkah sebagai berikut:

- Mengumpulkan bukti berupa dokumentasi visual dan keterangan saksi  
- Melakukan penelusuran internal terkait kemungkinan penyebab dan pihak yang terlibat  
- Melaporkan kejadian ini kepada pihak kepolisian guna proses investigasi lebih lanjut  

Panitia menegaskan bahwa proses investigasi dilakukan secara objektif, profesional, dan bertanggung jawab. Kami juga mengimbau seluruh pihak untuk tidak menyebarkan informasi atau spekulasi yang belum terverifikasi, demi menjaga suasana yang kondusif.

Apabila ditemukan adanya unsur pelanggaran hukum, maka kejadian ini akan ditindaklanjuti sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.

Panitia tetap berkomitmen untuk memastikan bahwa Reuni Akbar STEMAN Ternate dapat berjalan dengan aman, tertib, dan penuh semangat kebersamaan. Kami mengajak seluruh peserta serta pihak terkait untuk bersama-sama menjaga integritas dan nilai positif dari kegiatan ini.

Perkembangan lebih lanjut akan kami sampaikan secara resmi melalui kanal komunikasi panitia.

Hormat kami,  

Panitia Reuni Akbar STEMAN Ternate"""

# We'll use a PHP tinker command to update the news content
# We need to escape the content for PHP
escaped_content = content.replace("\\", "\\\\").replace("'", "\\'").replace('"', '\\"')

# We'll use a Heredoc in Tinker to avoid most escaping issues
php_code = f"""
$content = <<<'EOD'
{content}
EOD;
$news = \\App\\Models\\News::where('slug', 'siaran-pers-terkait-kejadian-pengrusakan-fasilitas-acara-reuni-akbar-steman-ternate-oUmnG')->first();
if ($news) {{
    $news->update(['content' => $content]);
    echo "SUCCESS: Updated news ID " . $news->id;
}} else {{
    echo "ERROR: News not found";
}}
"""

# Open a channel for interactive session
channel = ssh.invoke_shell()
channel.send('docker exec -i app php artisan tinker\n')
import time
time.sleep(2)
channel.send(php_code + '\n')
time.sleep(2)
output = channel.recv(10000).decode()
print(output)

ssh.close()
