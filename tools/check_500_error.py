import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check laravel log error message
stdin, stdout, stderr = ssh.exec_command(
    'grep -m 2 -A 5 "production.ERROR" $(ls -t /var/www/steman-alumni/storage/logs/laravel* | head -n 1)'
)
print(stdout.read().decode('utf-8'))

ssh.close()
