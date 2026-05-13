import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx curl -s http://localhost/metrics || docker exec steman_nginx curl -s http://localhost/api/metrics || docker exec steman_nginx curl -s http://localhost/admin/metrics')
print("STDOUT:", stdout.read().decode())

ssh.close()
