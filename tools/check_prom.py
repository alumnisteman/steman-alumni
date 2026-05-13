import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check prometheus container and if it can scrape steman metrics
stdin, stdout, stderr = ssh.exec_command('docker exec steman_prometheus wget -qO- http://localhost:9090/api/v1/targets | head -200')
print("PROMETHEUS TARGETS:", stdout.read().decode()[:1000])

# Check if steman metrics endpoint is in prometheus config
stdin, stdout, stderr = ssh.exec_command('cat /var/www/steman-alumni/docker/prometheus/prometheus.yml')
print("PROMETHEUS CONFIG:", stdout.read().decode())

ssh.close()
