import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check for hidden files
stdin, stdout, stderr = ssh.exec_command('ls -laR /var/www/steman-alumni/docker/grafana/provisioning')
print("PROVISIONING LA:\n", stdout.read().decode())

ssh.close()
