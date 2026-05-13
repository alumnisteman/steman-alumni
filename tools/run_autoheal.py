import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Execute steman-autoheal.sh
stdin, stdout, stderr = ssh.exec_command('/var/www/steman-alumni/scripts/steman-autoheal.sh')
print("STDOUT:", stdout.read().decode())
print("STDERR:", stderr.read().decode())

ssh.close()
