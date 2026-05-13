import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# List files in project root
stdin, stdout, stderr = ssh.exec_command('ls -F /var/www/steman-alumni/')
print("FILES:\n", stdout.read().decode())

ssh.close()
