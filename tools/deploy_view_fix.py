import paramiko
import os

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')
sftp = ssh.open_sftp()

files_to_deploy = [
    'resources/views/welcome.blade.php',
    'resources/views/layouts/app.blade.php'
]

for file_path in files_to_deploy:
    local_path = f"d:/THOY STEMAN FILE/steman-alumni-v4.1/steman-alumni/{file_path}"
    remote_path = f"/var/www/steman-alumni/{file_path}"
    
    print(f"Uploading {file_path}...")
    sftp.put(local_path, remote_path)

sftp.close()

# Clear view cache
print("\nClearing view cache...")
stdin, stdout, stderr = ssh.exec_command('docker exec steman-alumni-app-1 php artisan view:clear')
print(stdout.read().decode())
print(stderr.read().decode())

ssh.close()
