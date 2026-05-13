import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check what the healthchecker is doing
stdin, stdout, stderr = ssh.exec_command(
    'docker exec steman-alumni-app-1 php artisan tinker --execute="echo str_contains(file_get_contents(app_path(\'Http/Controllers/AuthController.php\')), \\"!session()->has(\'captcha_answer\')\\") ? \'YES\' : \'NO\';"'
)
print(stdout.read().decode('utf-8'))

ssh.close()
