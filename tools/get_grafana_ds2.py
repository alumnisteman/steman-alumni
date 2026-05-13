import paramiko
import json

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Try with curl instead
stdin, stdout, stderr = ssh.exec_command('docker exec steman_grafana curl -s -u admin:admin http://localhost:3000/api/datasources')
raw = stdout.read().decode()
err = stderr.read().decode()
print("DATASOURCES:", raw[:2000])
print("STDERR:", err[:500])

ssh.close()
