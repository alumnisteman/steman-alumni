import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check OOM killer
stdin, stdout, stderr = ssh.exec_command('dmesg -T | grep -i oom | tail -20')
print("OOM:", stdout.read().decode())

ssh.close()
