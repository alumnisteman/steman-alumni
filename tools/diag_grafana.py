import paramiko
import time

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Step 1: Check exact state of Grafana right now
stdin, stdout, stderr = ssh.exec_command('docker ps --filter name=steman_grafana --format "{{.Status}}"')
print("GRAFANA STATUS:", stdout.read().decode().strip())

# Step 2: Check what's in provisioning directory
stdin, stdout, stderr = ssh.exec_command('ls -la /var/www/steman-alumni/docker/grafana/provisioning/datasources/')
print("DATASOURCES DIR:", stdout.read().decode())

stdin, stdout, stderr = ssh.exec_command('ls -la /var/www/steman-alumni/docker/grafana/provisioning/dashboards/')
print("DASHBOARDS DIR:", stdout.read().decode())

# Step 3: Read current datasources.yaml
stdin, stdout, stderr = ssh.exec_command('cat /var/www/steman-alumni/docker/grafana/provisioning/datasources/datasources.yaml')
print("DATASOURCES.YAML:", stdout.read().decode())

# Step 4: Read provider.yml
stdin, stdout, stderr = ssh.exec_command('cat /var/www/steman-alumni/docker/grafana/provisioning/dashboards/provider.yml')
print("PROVIDER.YML:", stdout.read().decode())

ssh.close()
