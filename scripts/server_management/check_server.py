import paramiko
import sys
import io
import os

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

host = os.environ.get('SERVER_HOST', '103.175.219.57')
user = os.environ.get('SERVER_USER', 'root')
password = os.environ.get('SERVER_PASSWORD', '')

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(host, username=user, password=password)

print("=== .dockerignore contents ===")
stdin, stdout, stderr = client.exec_command('cat /var/www/steman-alumni/.dockerignore 2>/dev/null || echo "FILE NOT FOUND"')
out = stdout.read().decode('utf-8', errors='replace')
print(out)

print("=== Latest git log ===")
stdin, stdout, stderr = client.exec_command('cd /var/www/steman-alumni && git log --oneline -5')
out = stdout.read().decode('utf-8', errors='replace')
print(out)

print("=== Full docker build error ===")
stdin, stdout, stderr = client.exec_command(
    'cd /var/www/steman-alumni && docker compose -f docker-compose.yml build app 2>&1 | tail -50'
)
out = stdout.read().decode('utf-8', errors='replace')
print(out)

client.close()
