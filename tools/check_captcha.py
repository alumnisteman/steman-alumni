import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

sftp = ssh.open_sftp()
file_obj = sftp.file('/var/www/steman-alumni/app/Http/Controllers/AuthController.php', 'r')
content = file_obj.read().decode('utf-8')
file_obj.close()
sftp.close()

if "!session()->has('captcha_answer')" in content:
    print("MATCH EXACT")
elif "captcha_answer" in content:
    print("captcha_answer FOUND BUT NO EXACT MATCH")
    lines = content.split('\n')
    for i, line in enumerate(lines):
        if "captcha_answer" in line:
            print(f"Line {i+1}: {line.strip()}")
else:
    print("NO CAPTCHA_ANSWER FOUND AT ALL")

ssh.close()
