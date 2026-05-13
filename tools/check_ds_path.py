import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check where the file actually is
stdin, stdout, stderr = ssh.exec_command('ls -la /var/www/steman-alumni/docker/grafana/provisioning/datasources/')
print("STDOUT 1:", stdout.read().decode())

stdin, stdout, stderr = ssh.exec_command('ls -la /etc/grafana/provisioning/datasources/')
print("STDOUT 2:", stdout.read().decode())

ssh.close()
