import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check if Nginx returns 200 for grafana login page now
stdin, stdout, stderr = ssh.exec_command('curl -s -o /dev/null -w "%{http_code}" https://admin.alumni-steman.my.id/grafana/login')
print("HTTP Status via Nginx:", stdout.read().decode())

ssh.close()
