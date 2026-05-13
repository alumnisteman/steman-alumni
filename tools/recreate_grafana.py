import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Recreate grafana container with docker compose
print("Recreating grafana container with docker compose...")
stdin, stdout, stderr = ssh.exec_command('cd /var/www/steman-alumni && docker compose -f docker-compose.prod.yml up -d grafana')
print("STDOUT:", stdout.read().decode())
print("STDERR:", stderr.read().decode())

ssh.close()
