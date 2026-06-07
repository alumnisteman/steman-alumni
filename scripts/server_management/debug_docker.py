import paramiko
import os

host = os.environ.get('SERVER_HOST', '103.175.219.57')
user = os.environ.get('SERVER_USER', 'root')
password = os.environ.get('SERVER_PASSWORD', '')

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(host, username=user, password=password)
cmd = "docker run --rm -v /var/www/steman-alumni:/var/www -w /var/www -e APP_ENV=local composer:latest sh -c 'composer dump-autoload --optimize && php artisan package:discover -v'"
stdin, stdout, stderr = client.exec_command(cmd)
print("STDOUT:")
for line in stdout:
    print(line.strip())
err = stderr.read().decode().strip()
if err: print("STDERR:", err)
client.close()
