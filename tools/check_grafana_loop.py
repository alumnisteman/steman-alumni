import paramiko
import time

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check grafana status multiple times to see if it's crash-looping
for _ in range(3):
    stdin, stdout, stderr = ssh.exec_command('docker ps | grep grafana || echo "Container not running"')
    print(stdout.read().decode().strip())
    time.sleep(3)

# If it crashed, get the last panic/fatal log
stdin, stdout, stderr = ssh.exec_command('docker logs steman_grafana 2>&1 | grep -iE "fatal|panic|error" | tail -10')
print("\nERRORS:\n", stdout.read().decode())

ssh.close()
