import paramiko
import time

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Wait for port 3000 to be open
for _ in range(10):
    stdin, stdout, stderr = ssh.exec_command('docker exec steman_grafana curl -s -o /dev/null -w "%{http_code}" http://localhost:3000/login || echo "down"')
    res = stdout.read().decode().strip()
    if res == "200":
        print("GRAFANA IS UP AND SERVING ON PORT 3000!")
        break
    else:
        print("Waiting...", res)
        time.sleep(5)
        
ssh.close()
