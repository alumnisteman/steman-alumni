import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Update docker-compose-server.yml and prometheus.yml on server
# We will use SFTP to sync the files we just edited locally
sftp = ssh.open_sftp()
sftp.put('d:/THOY STEMAN FILE/steman-alumni-v4.1/steman-alumni/docker-compose-server.yml', '/var/www/steman-alumni/docker-compose-server.yml')
sftp.put('d:/THOY STEMAN FILE/steman-alumni-v4.1/steman-alumni/docker/prometheus/prometheus.yml', '/var/www/steman-alumni/docker/prometheus/prometheus.yml')
sftp.close()

# Restart monitoring services
stdin, stdout, stderr = ssh.exec_command('cd /var/www/steman-alumni && docker compose -f docker-compose-server.yml up -d node-exporter prometheus grafana')
print("RESTART OUTPUT:\n", stdout.read().decode())
print("RESTART ERRORS:\n", stderr.read().decode())

ssh.close()
