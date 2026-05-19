import paramiko
client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('103.175.219.57', username='root', password='M4ruw4h3@')
stdin, stdout, stderr = client.exec_command('docker exec steman_app php artisan package:discover -v')
print("STDOUT:")
for line in stdout:
    print(line.strip())
err = stderr.read().decode().strip()
if err: print("STDERR:", err)
client.close()
