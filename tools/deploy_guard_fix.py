import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')
sftp = ssh.open_sftp()

files_to_deploy = [
    'app/Console/Commands/SystemGuard.php',
    'app/Http/Controllers/AuthController.php'
]

for file_path in files_to_deploy:
    local_path = f"d:/THOY STEMAN FILE/steman-alumni-v4.1/steman-alumni/{file_path}"
    remote_path = f"/var/www/steman-alumni/{file_path}"
    
    print(f"Uploading {file_path}...")
    sftp.put(local_path, remote_path)

sftp.close()
ssh.close()

print("Deployed successfully.")
