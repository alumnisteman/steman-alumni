import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check grafana logs to find admin password
stdin, stdout, stderr = ssh.exec_command('docker logs steman_grafana --tail 30 2>&1')
print("LOGS:", stdout.read().decode())

# Also check env vars
stdin, stdout, stderr = ssh.exec_command('docker inspect steman_grafana | grep -A5 "GF_SECURITY"')
print("ENV:", stdout.read().decode())

ssh.close()
