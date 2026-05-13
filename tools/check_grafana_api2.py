import paramiko
import time

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Wait for grafana to start
time.sleep(10)

# Try Grafana API via the nginx proxy (it should be accessible via admin.alumni-steman.my.id)
stdin, stdout, stderr = ssh.exec_command('curl -s -u admin:StemanGrafana2026! https://admin.alumni-steman.my.id/grafana/api/datasources --insecure')
raw = stdout.read().decode()
print("VIA NGINX:", raw[:3000])

ssh.close()
