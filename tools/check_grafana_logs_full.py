import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check logs again, but full output
stdin, stdout, stderr = ssh.exec_command('docker logs --tail 100 steman_grafana')
print("GRAFANA LOGS:\n", stdout.read().decode())
print("GRAFANA ERRORS:\n", stderr.read().decode())

ssh.close()
