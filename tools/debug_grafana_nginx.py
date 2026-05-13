import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check logs with UTF-8 encoding
stdin, stdout, stderr = ssh.exec_command('docker logs --tail 200 steman_grafana')
logs = stdout.read().decode('utf-8', errors='ignore')
print("GRAFANA LOGS START")
print(logs)
print("GRAFANA LOGS END")

# Check Nginx config
stdin, stdout, stderr = ssh.exec_command('cat /var/www/steman-alumni/docker/nginx/conf.d/app.conf')
print("NGINX CONFIG START")
print(stdout.read().decode('utf-8', errors='ignore'))
print("NGINX CONFIG END")

ssh.close()
