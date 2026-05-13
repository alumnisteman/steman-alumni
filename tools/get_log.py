import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

stdin, stdout, stderr = ssh.exec_command('tail -n 100 /var/www/storage/logs/laravel.log')
log_content = stdout.read()
# save to a local file instead of printing to avoid encoding issues
with open('remote_laravel_log.txt', 'wb') as f:
    f.write(log_content)

ssh.close()
