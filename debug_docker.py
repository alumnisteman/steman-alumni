import paramiko
client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('103.175.219.57', username='root', password='M4ruw4h3@')
cmd = "docker run --rm -v /var/www/steman-alumni:/var/www -w /var/www -e APP_ENV=local composer:latest sh -c 'composer dump-autoload --optimize && php artisan package:discover -v'"
stdin, stdout, stderr = client.exec_command(cmd)
print("STDOUT:")
for line in stdout:
    print(line.strip())
err = stderr.read().decode().strip()
if err: print("STDERR:", err)
client.close()
