import paramiko
import json

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

grafana_ip = "172.18.0.11"

# Call grafana API using its container IP
stdin, stdout, stderr = ssh.exec_command(f'curl -s -u admin:StemanGrafana2026! http://{grafana_ip}:3000/api/datasources')
raw = stdout.read().decode()
print("DATASOURCES:", raw[:4000])

ssh.close()
