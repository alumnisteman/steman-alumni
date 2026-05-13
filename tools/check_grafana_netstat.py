import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check if port 3000 is listening in the container
stdin, stdout, stderr = ssh.exec_command('docker exec steman_grafana netstat -tunlp | grep 3000')
print("GRAFANA NETSTAT:\n", stdout.read().decode())

# Check Nginx error logs for grafana
stdin, stdout, stderr = ssh.exec_command('docker logs --tail 50 steman_nginx')
print("NGINX LOGS:\n", stdout.read().decode())
print("NGINX ERRORS:\n", stderr.read().decode())

ssh.close()
