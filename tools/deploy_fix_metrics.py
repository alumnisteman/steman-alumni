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
    'd:/THOY STEMAN FILE/steman-alumni-v4.1/steman-alumni/docker/prometheus/prometheus.yml',
    '/var/www/steman-alumni/docker/prometheus/prometheus.yml'
)
sftp.put(
    'd:/THOY STEMAN FILE/steman-alumni-v4.1/steman-alumni/docker/grafana/provisioning/dashboards/steman-overview.json',
    '/var/www/steman-alumni/docker/grafana/provisioning/dashboards/steman-overview.json'
)
sftp.close()

# Fix ownership for the dashboard
ssh.exec_command('chown 472:472 /var/www/steman-alumni/docker/grafana/provisioning/dashboards/steman-overview.json')

# Start the new exporter and restart prometheus/grafana
print("Restarting services...")
stdin, stdout, stderr = ssh.exec_command(
    'cd /var/www/steman-alumni && docker compose -f docker-compose-server.yml up -d nginx-exporter prometheus grafana'
)
print("DEPLOY OUTPUT:", stdout.read().decode())
print("DEPLOY ERRORS:", stderr.read().decode())

# Wait for services to stabilize
time.sleep(10)

# Check if Nginx exporter is UP in Prometheus
stdin, stdout, stderr = ssh.exec_command(
    'docker exec steman_prometheus wget -qO- "http://localhost:9090/api/v1/targets?state=active"'
)
print("TARGETS STATUS:", stdout.read().decode())

ssh.close()
