import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check steman-laravel job scrape status
stdin, stdout, stderr = ssh.exec_command('docker exec steman_prometheus wget -qO- "http://localhost:9090/api/v1/targets" 2>/dev/null | python3 -c "import sys,json; data=json.load(sys.stdin); [print(t[\'labels\'][\'job\'], t[\'health\'], t.get(\'lastError\',\'\')) for t in data[\'data\'][\'activeTargets\']]"')
print("TARGETS:", stdout.read().decode())

# Try to access /metrics directly on nginx
stdin, stdout, stderr = ssh.exec_command('docker exec steman_nginx curl -s http://localhost/metrics | head -20')
print("NGINX /metrics:", stdout.read().decode())
print("NGINX /metrics STDERR:", stderr.read().decode())

ssh.close()
