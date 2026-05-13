import paramiko
import sys

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check logs and write to local file
stdin, stdout, stderr = ssh.exec_command('docker logs --tail 300 steman_grafana')
logs = stdout.read().decode('utf-8', errors='ignore')
with open('grafana_debug.log', 'w', encoding='utf-8') as f:
    f.write(logs)

# Check Nginx config and write to local file
stdin, stdout, stderr = ssh.exec_command('cat /var/www/steman-alumni/docker/nginx/conf.d/app.conf')
nginx_conf = stdout.read().decode('utf-8', errors='ignore')
with open('nginx_debug.conf', 'w', encoding='utf-8') as f:
    f.write(nginx_conf)

ssh.close()
