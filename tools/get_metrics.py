import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Curl the metrics endpoint inside the app container or from host (assuming port 80 or similar)
# Since Prometheus scrapes it, it should be exposed. Let's try to scrape it from the app container.
stdin, stdout, stderr = ssh.exec_command('docker exec app php -r "echo file_get_contents(\'http://localhost/api/metrics\');" 2>/dev/null || docker exec app php -r "echo file_get_contents(\'http://localhost/metrics\');"')
print("STDOUT:", stdout.read().decode())

ssh.close()
