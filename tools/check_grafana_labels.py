import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check labels to see if it was started by docker-compose
stdin, stdout, stderr = ssh.exec_command('docker inspect steman_grafana | grep -A20 "Labels"')
print("Labels:", stdout.read().decode())

ssh.close()
