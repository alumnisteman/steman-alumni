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

print("=== [1/2] Spinning up all containers ===")
stdin, stdout, stderr = client.exec_command('cd /var/www/steman-alumni && docker compose -f docker-compose.yml up -d')
out = stdout.read().decode('utf-8', errors='replace')
print(out)
err = stderr.read().decode('utf-8', errors='replace')
if err: print("STDERR:", err)

print("\n=== [2/2] Checking Running Containers ===")
stdin, stdout, stderr = client.exec_command('docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep -E "steman|Names"')
out = stdout.read().decode('utf-8', errors='replace')
print(out)

client.close()
print("\n=== All Done ===")
