import paramiko
import time

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('103.175.219.57', username='root', password='M4ruw4h3@')

print("=== [1/3] Pulling latest fix from GitHub ===")
stdin, stdout, stderr = client.exec_command(
    'cd /var/www/steman-alumni && git fetch origin && git reset --hard origin/main'
)
print(stdout.read().decode('utf-8', errors='replace').strip())
print(stderr.read().decode('utf-8', errors='replace').strip())

print("\n=== [2/3] Rebuilding app image (--no-scripts fix applied) ===")
print("    This will take 5-8 minutes, please wait...")
transport = client.get_transport()
channel = transport.open_session()
channel.get_pty()
channel.exec_command(
    'cd /var/www/steman-alumni && docker compose -f docker-compose.yml build app 2>&1'
)

last_output = ""
while not channel.exit_status_ready():
    if channel.recv_ready():
        chunk = channel.recv(4096).decode('utf-8', errors='replace')
        for line in chunk.splitlines():
            line = line.strip()
            if line and line != last_output:
                # Only print meaningful progress lines
                if any(k in line for k in ['#', 'DONE', 'ERROR', 'Step', 'Successfully', 'COPY', 'RUN', 'FROM', 'FAIL', 'npm', 'composer', 'vite']):
                    print(line)
                    last_output = line
    time.sleep(3)

exit_status = channel.recv_exit_status()
print(f"\nBuild exit code: {exit_status}")

if exit_status == 0:
    print("\n=== [3/3] Starting ALL services including Grafana ===")
    stdin, stdout, stderr = client.exec_command(
        'cd /var/www/steman-alumni && docker compose -f docker-compose.yml up -d 2>&1'
    )
    print(stdout.read().decode('utf-8', errors='replace').strip())

    print("\n=== Container Status ===")
    stdin, stdout, stderr = client.exec_command('docker ps --format "{{.Names}}\t{{.Status}}"')
    print(stdout.read().decode('utf-8', errors='replace').strip())
else:
    print("\n[FAILED] Build failed!")
    # Show last error
    stdin, stdout, stderr = client.exec_command(
        'cd /var/www/steman-alumni && docker compose -f docker-compose.yml build app 2>&1 | tail -30'
    )
    print(stdout.read().decode('utf-8', errors='replace').strip())

client.close()
print("\n=== Deploy Done ===")
