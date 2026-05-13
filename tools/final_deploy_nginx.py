import paramiko
import time

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Upload to host path (volume source) - this is NOT read-only on host
sftp = ssh.open_sftp()
sftp.put(
    'd:/THOY STEMAN FILE/steman-alumni-v4.1/steman-alumni/docker/nginx/conf.d/services.conf',
    '/var/www/steman-alumni/docker/nginx/conf.d/services.conf'
)
sftp.close()
print("Config uploaded to host path")

# Test + reload
stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx nginx -t')
out = stdout.read().decode() + stderr.read().decode()
print("NGINX TEST:", out.strip())

if 'successful' in out:
    stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx nginx -s reload')
    print("RELOAD:", stderr.read().decode().strip())
    time.sleep(3)

# Final external HTTP check
stdin, stdout, stderr = ssh.exec_command('curl -sk -o /dev/null -w "%{http_code}" https://admin.alumni-steman.my.id/grafana/api/health')
print("EXTERNAL HEALTH:", stdout.read().decode().strip())

stdin, stdout, stderr = ssh.exec_command('curl -sk https://admin.alumni-steman.my.id/grafana/api/health')
print("HEALTH BODY:", stdout.read().decode().strip())

ssh.close()
