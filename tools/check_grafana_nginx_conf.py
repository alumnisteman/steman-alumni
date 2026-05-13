import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check where /grafana is configured in nginx
stdin, stdout, stderr = ssh.exec_command('grep -rn "grafana" /var/www/steman-alumni/docker/nginx/conf.d/')
print("NGINX CONF:", stdout.read().decode())

ssh.close()
