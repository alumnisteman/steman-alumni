import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Get all network info for grafana
stdin, stdout, stderr = ssh.exec_command("docker inspect steman_grafana --format '{{json .NetworkSettings.Networks}}'")
print("NETWORKS:", stdout.read().decode()[:2000])

# Also check what port grafana is exposed on the host
stdin, stdout, stderr = ssh.exec_command("docker port steman_grafana")
print("PORTS:", stdout.read().decode())

ssh.close()
