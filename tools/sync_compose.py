import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Read docker-compose.prod.yml from server
stdin, stdout, stderr = ssh.exec_command('cat /var/www/steman-alumni/docker-compose.prod.yml')
content = stdout.read().decode('utf-8', errors='ignore')
with open('docker-compose-server.yml', 'w', encoding='utf-8') as f:
    f.write(content)

ssh.close()
