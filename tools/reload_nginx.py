import paramiko
import time

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Get current Grafana IP
stdin, stdout, stderr = ssh.exec_command('docker inspect steman_grafana | grep -m1 "IPAddress"')
print("GRAFANA IP:", stdout.read().decode().strip())

# Test from within Nginx container using the hostname
stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx curl -s -o /dev/null -w "%{http_code}" --max-time 5 http://steman_grafana:3000/api/health')
print("NGINX->GRAFANA:", stdout.read().decode().strip())

# Reload Nginx to flush DNS cache
stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx nginx -s reload')
print("NGINX RELOAD:", stdout.read().decode().strip(), stderr.read().decode().strip())
time.sleep(3)

# Test again after reload
stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx curl -s -o /dev/null -w "%{http_code}" --max-time 5 http://steman_grafana:3000/api/health')
print("NGINX->GRAFANA (after reload):", stdout.read().decode().strip())

ssh.close()
