import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Search for grafana in all files in the project root
stdin, stdout, stderr = ssh.exec_command('grep -r "grafana" /var/www/steman-alumni/ --exclude-dir=vendor --exclude-dir=storage --max-count=1')
print("GREP RESULTS:\n", stdout.read().decode())

ssh.close()
