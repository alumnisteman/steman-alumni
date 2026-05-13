import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

stdin, stdout, stderr = ssh.exec_command('docker logs steman_grafana --tail 100 2>&1')
logs = stdout.read().decode('utf-8', errors='replace')
with open('grafana_fatal.txt', 'w', encoding='utf-8') as f:
    f.write(logs)

ssh.close()
