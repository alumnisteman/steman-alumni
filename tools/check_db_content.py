import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Check raw content in DB
stdin, stdout, stderr = ssh.exec_command('docker exec -i app php artisan tinker --execute="echo \\App\\Models\\News::where(\'slug\', \'siaran-pers-terkait-kejadian-pengrusakan-fasilitas-acara-reuni-akbar-steman-ternate-oUmnG\')->first()->content;"')
print("DB CONTENT:\n", stdout.read().decode())

ssh.close()
