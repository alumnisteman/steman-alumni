import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check datasources directory
stdin, stdout, stderr = ssh.exec_command('cat /var/www/steman-alumni/docker/grafana/provisioning/datasources/prometheus.yml || cat /etc/grafana/provisioning/datasources/prometheus.yml')
print("STDOUT:", stdout.read().decode())

ssh.close()
