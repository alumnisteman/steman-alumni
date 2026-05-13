import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Full logs for grafana
stdin, stdout, stderr = ssh.exec_command('docker logs --tail 200 steman_grafana')
print("GRAFANA LOGS (FULL):\n", stdout.read().decode('utf-8', errors='ignore'))

ssh.close()
