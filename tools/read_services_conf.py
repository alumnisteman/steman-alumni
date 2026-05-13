import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Read services.conf
stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx cat /etc/nginx/conf.d/services.conf')
print("SERVICES CONF START")
print(stdout.read().decode('utf-8', errors='ignore'))
print("SERVICES CONF END")

ssh.close()
