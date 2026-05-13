import paramiko
import json

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Get grafana container IP
stdin, stdout, stderr = ssh.exec_command("docker inspect steman_grafana --format '{{.NetworkSettings.Networks.steman-alumni_steman-network.IPAddress}}'")
grafana_ip = stdout.read().decode().strip()
print("Grafana IP:", grafana_ip)

# Call grafana API from host using the container network
stdin, stdout, stderr = ssh.exec_command(f'curl -s -u admin:StemanGrafana2026! http://{grafana_ip}:3000/api/datasources')
raw = stdout.read().decode()
print("DATASOURCES:", raw[:3000])

ssh.close()
