import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check logs for Grafana and Node Exporter
stdin, stdout, stderr = ssh.exec_command('docker logs --tail 50 steman_grafana')
print("GRAFANA LOGS:\n", stdout.read().decode('utf-8', errors='ignore'))

stdin, stdout, stderr = ssh.exec_command('docker logs --tail 20 steman_node_exporter')
print("NODE EXPORTER LOGS:\n", stdout.read().decode('utf-8', errors='ignore'))

# Check container status
stdin, stdout, stderr = ssh.exec_command('docker ps --filter name=steman')
print("CONTAINER STATUS:\n", stdout.read().decode())

ssh.close()
