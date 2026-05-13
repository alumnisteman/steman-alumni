import paramiko
import json

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

grafana_ip = "172.18.0.11"

# Try with grafana subpath prefix
for path in ['/api/datasources', '/grafana/api/datasources']:
    stdin, stdout, stderr = ssh.exec_command(f'curl -s -u admin:StemanGrafana2026! http://{grafana_ip}:3000{path}')
    raw = stdout.read().decode()
    if raw:
        print(f"Success with path {path}:", raw[:3000])
        break
    else:
        print(f"Empty response from {path}")

# Check current prometheus.yml content on server
stdin, stdout, stderr = ssh.exec_command('cat /var/www/steman-alumni/docker/grafana/provisioning/datasources/prometheus.yml')
print("\nCurrent prometheus.yml:", stdout.read().decode())

ssh.close()
