import paramiko
import json

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Get actual datasource UID from Grafana API
stdin, stdout, stderr = ssh.exec_command('docker exec steman_grafana wget -qO- --header="Content-Type: application/json" http://admin:admin@localhost:3000/api/datasources')
raw = stdout.read().decode()
print("DATASOURCES:", raw)

ssh.close()
