import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

stdin, stdout, stderr = ssh.exec_command('docker ps --filter name=steman_grafana')
print("GRAFANA STATUS:\n", stdout.read().decode())

stdin, stdout, stderr = ssh.exec_command('docker exec steman_grafana netstat -tunlp | grep 3000')
port_out = stdout.read().decode().strip()
print("PORT 3000:", port_out if port_out else "NOT LISTENING")

# Check if Grafana is crash-looping (look at last error in logs)
stdin, stdout, stderr = ssh.exec_command('docker logs --tail 30 steman_grafana 2>&1 | grep -i "error\|Error\|fail"')
err_logs = stdout.read().decode('utf-8', errors='replace')
with open('grafana_errors.log', 'w', encoding='utf-8') as f:
    f.write(err_logs)
print("Error log saved to grafana_errors.log")

# Also test direct HTTP from inside server
stdin, stdout, stderr = ssh.exec_command('curl -s -o /dev/null -w "%{http_code}" http://steman_grafana:3000/api/health')
print("CURL HEALTH:", stdout.read().decode())

ssh.close()
