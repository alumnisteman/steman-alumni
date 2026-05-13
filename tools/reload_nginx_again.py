import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Reload Nginx to clear upstream IP cache
stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx nginx -s reload')
print("NGINX RELOAD:", stdout.read().decode())
print("NGINX RELOAD STDERR:", stderr.read().decode())

ssh.close()
