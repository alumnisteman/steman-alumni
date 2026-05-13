import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check memory limits of grafana container
stdin, stdout, stderr = ssh.exec_command("docker inspect steman_grafana --format '{{.HostConfig.Memory}}'")
print("Memory Limit (bytes):", stdout.read().decode())

ssh.close()
