import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check if port 3000 is open in steman_grafana
stdin, stdout, stderr = ssh.exec_command('docker exec steman_grafana netstat -tunlp | grep 3000')
print("GRAFANA NETSTAT:\n", stdout.read().decode())

# Check Prometheus targets
stdin, stdout, stderr = ssh.exec_command('docker exec steman_prometheus wget -qO- http://localhost:9090/api/v1/targets')
print("PROMETHEUS TARGETS:\n", stdout.read().decode())

ssh.close()
