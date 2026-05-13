import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Curl Grafana IP from Nginx
stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx curl -v http://steman_grafana:3000/login 2>&1')
print("Direct curl from nginx:", stdout.read().decode())

ssh.close()
