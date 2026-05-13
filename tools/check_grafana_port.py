import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check listening ports inside grafana container
stdin, stdout, stderr = ssh.exec_command('docker exec steman_grafana netstat -tulpn || docker exec steman_grafana ss -tulpn')
print("Ports:", stdout.read().decode())

ssh.close()
