import paramiko
import time

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Find correct nginx conf path
stdin, stdout, stderr = ssh.exec_command('docker inspect steman_nginx | grep -A3 "Binds"')
print("NGINX BINDS:", stdout.read().decode())

# Try docker cp approach
sftp = ssh.open_sftp()
sftp.put(
    'd:/THOY STEMAN FILE/steman-alumni-v4.1/steman-alumni/docker/nginx/conf.d/services.conf',
    '/tmp/services.conf'
)
sftp.close()

# Copy into nginx container
stdin, stdout, stderr = ssh.exec_command('docker cp /tmp/services.conf steman_nginx:/etc/nginx/conf.d/services.conf')
print("CP:", stderr.read().decode())

# Test config
stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx nginx -t')
out = stdout.read().decode() + stderr.read().decode()
print("NGINX TEST:", out.strip())

if 'successful' in out:
    stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx nginx -s reload')
    print("RELOAD:", stderr.read().decode().strip())

time.sleep(2)
stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx curl -s -o /dev/null -w "%{http_code}" --max-time 5 http://steman_grafana:3000/api/health')
print("HEALTH:", stdout.read().decode().strip())

ssh.close()
