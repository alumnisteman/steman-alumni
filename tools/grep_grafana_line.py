import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Grep with line numbers
stdin, stdout, stderr = ssh.exec_command('grep -n "grafana" /var/www/steman-alumni/docker-compose.prod.yml')
print("GREP RESULTS:\n", stdout.read().decode())

ssh.close()
