import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

commands = [
    # Replace App/Models with app/Models
    "find /var/www -name 'system_optimize.sh' -exec sed -i 's/App\\/Models/app\\/Models/g' {} +",
    "find /root -name 'system_optimize.sh' -exec sed -i 's/App\\/Models/app\\/Models/g' {} +",
    "find /etc/cron* -type f -exec sed -i 's/App\\/Models/app\\/Models/g' {} + 2>/dev/null",
    
    # Replace the format=json block
    "find /var/www -name 'steman-autoheal.sh' -exec sed -i 's/--format=json | grep -oP \\'\"jobs\":\\\\s*\\\\K\\\\d+\\'/| grep -oP \\'\\\\d+\\' | head -n 1/g' {} +",
    "find /root -name 'steman-autoheal.sh' -exec sed -i 's/--format=json | grep -oP \\'\"jobs\":\\\\s*\\\\K\\\\d+\\'/| grep -oP \\'\\\\d+\\' | head -n 1/g' {} +",
]

for cmd in commands:
    print(f"Executing: {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    print("STDOUT:", stdout.read().decode())
    print("STDERR:", stderr.read().decode())

ssh.close()
