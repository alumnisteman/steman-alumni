import paramiko
import sys
import io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('103.175.219.57', username='root', password='M4ruw4h3@')

print("=== Checking Container Status ===")
stdin, stdout, stderr = client.exec_command('docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"')
out = stdout.read().decode('utf-8', errors='replace')
print(out)

print("=== Checking app logs (last 10 lines) ===")
stdin, stdout, stderr = client.exec_command('docker logs --tail 10 steman_app')
out = stdout.read().decode('utf-8', errors='replace')
print(out)

client.close()
