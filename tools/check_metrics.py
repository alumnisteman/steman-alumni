import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check actual metrics from Laravel /metrics endpoint
stdin, stdout, stderr = ssh.exec_command(
    'docker exec steman_nginx curl -s http://steman_app:9000/metrics 2>/dev/null | head -n 60'
)
metrics_raw = stdout.read().decode('utf-8', errors='replace')
with open('laravel_metrics.txt', 'w', encoding='utf-8') as f:
    f.write(metrics_raw)
print("Metrics saved.")

# Also check via nginx proxy
stdin, stdout, stderr = ssh.exec_command(
    'docker exec steman_nginx curl -s http://localhost/metrics | head -n 60'
)
metrics_nginx = stdout.read().decode('utf-8', errors='replace')
with open('laravel_metrics_via_nginx.txt', 'w', encoding='utf-8') as f:
    f.write(metrics_nginx)
print("Metrics via nginx saved.")

# Check nginx_status output
stdin, stdout, stderr = ssh.exec_command(
    'docker exec steman_nginx curl -s http://localhost/nginx_status'
)
print("NGINX STATUS RAW:", stdout.read().decode())

# Check current Prometheus targets health
stdin, stdout, stderr = ssh.exec_command(
    'docker exec steman_prometheus wget -qO- "http://localhost:9090/api/v1/targets" 2>/dev/null | python3 -c "import sys,json; d=json.load(sys.stdin); [print(t[\'labels\'][\'job\'], t[\'health\'], t.get(\'lastError\',\'\')[:80]) for t in d[\'data\'][\'activeTargets\']]"'
)
print("TARGETS:\n", stdout.read().decode())

ssh.close()
