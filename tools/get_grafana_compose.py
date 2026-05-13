import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

stdin, stdout, stderr = ssh.exec_command('grep -n -A10 "deploy:" /var/www/steman-alumni/docker-compose.prod.yml | grep -B20 "grafana_data"')
print("Server docker-compose.prod.yml limits:\n", stdout.read().decode())

# Actually let's just get the full grafana block
stdin, stdout, stderr = ssh.exec_command('sed -n "/^  grafana:/,/^  [a-z]/p" /var/www/steman-alumni/docker-compose.prod.yml')
print("Grafana block:\n", stdout.read().decode())

ssh.close()
