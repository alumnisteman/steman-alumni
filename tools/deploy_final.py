import paramiko
import time

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')
sftp = ssh.open_sftp()

# Upload updated files
sftp.put(
    'd:/THOY STEMAN FILE/steman-alumni-v4.1/steman-alumni/docker-compose-server.yml',
    '/var/www/steman-alumni/docker-compose-server.yml'
)
sftp.put(
    'd:/THOY STEMAN FILE/steman-alumni-v4.1/steman-alumni/docker/grafana/provisioning/dashboards/steman-overview.json',
    '/var/www/steman-alumni/docker/grafana/provisioning/dashboards/steman-overview.json'
)
sftp.close()

# Fix ownership
ssh.exec_command('chown 472:472 /var/www/steman-alumni/docker/grafana/provisioning/dashboards/steman-overview.json')

# Apply Grafana with new resource limits
stdin, stdout, stderr = ssh.exec_command(
    'cd /var/www/steman-alumni && docker compose -f docker-compose-server.yml up -d grafana'
)
print("DEPLOY OUTPUT:", stdout.read().decode())
print("DEPLOY ERRORS:", stderr.read().decode())

# Wait for Grafana to start
time.sleep(25)

# Verify port is open
stdin, stdout, stderr = ssh.exec_command('docker exec steman_grafana netstat -tunlp | grep 3000')
print("PORT STATUS:", stdout.read().decode().strip())

# Check if node-exporter target is UP
stdin, stdout, stderr = ssh.exec_command(
    'docker exec steman_prometheus wget -qO- "http://localhost:9090/api/v1/query?query=up{job=\'steman-node-exporter\'}"'
)
print("NODE EXPORTER UP:", stdout.read().decode())

ssh.close()
