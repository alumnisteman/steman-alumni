import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check if grafana is up
stdin, stdout, stderr = ssh.exec_command('docker ps | grep grafana')
print("PS:", stdout.read().decode())

# Check OOM again
stdin, stdout, stderr = ssh.exec_command('dmesg -T | grep -i oom | tail -5')
print("OOM:", stdout.read().decode())

ssh.close()
