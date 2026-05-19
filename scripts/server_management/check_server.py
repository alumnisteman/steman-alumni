import paramiko
import sys
import io

# Force UTF-8 output
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check .dockerignore
print("=== .dockerignore contents ===")
stdin, stdout, stderr = client.exec_command('cat /var/www/steman-alumni/.dockerignore 2>/dev/null || echo "FILE NOT FOUND"')
out = stdout.read().decode('utf-8', errors='replace')
print(out)

# Check git log
print("=== Latest git log ===")
stdin, stdout, stderr = client.exec_command('cd /var/www/steman-alumni && git log --oneline -5')
out = stdout.read().decode('utf-8', errors='replace')
print(out)

# Get full build error
print("=== Full docker build error ===")
stdin, stdout, stderr = client.exec_command(
    'cd /var/www/steman-alumni && docker compose -f docker-compose.yml build app 2>&1 | tail -50'
)
out = stdout.read().decode('utf-8', errors='replace')
print(out)

client.close()
