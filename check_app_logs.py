import paramiko
import sys
import io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('103.175.219.57', username='root', password='M4ruw4h3@')

print("=== [steman_app] Latest Logs ===")
stdin, stdout, stderr = client.exec_command('docker logs steman_app --tail 40')
out = stdout.read().decode('utf-8', errors='replace')
print(out)
err = stderr.read().decode('utf-8', errors='replace')
if err: print("STDERR:", err)

client.close()
