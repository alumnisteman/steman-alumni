import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# List files in nginx container's conf.d
stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx ls /etc/nginx/conf.d/')
print("NGINX CONF FILES:\n", stdout.read().decode())

# Read default.conf if it exists
stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx cat /etc/nginx/conf.d/default.conf')
print("DEFAULT CONF START")
print(stdout.read().decode('utf-8', errors='ignore'))
print("DEFAULT CONF END")

ssh.close()
