import sys
import paramiko

sys.stdout.reconfigure(encoding='utf-8')
hostname = '103.175.219.57'
username = 'root'
password = 'YOUR_SSH_PASSWORD'

try:
    print("Connecting via SSH...")
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(hostname, username=username, password=password)
    
    print("Checking docker ps -a...")
    stdin, stdout, stderr = client.exec_command("docker ps -a")
    print(stdout.read().decode('utf-8', errors='replace'))
    
    # Run docker-compose up for webserver specifically
    print("Starting nginx webserver...")
    stdin, stdout, stderr = client.exec_command("cd /var/www/steman-alumni && docker compose -f docker-compose.prod.yml up -d webserver")
    exit_status = stdout.channel.recv_exit_status()
    print(stdout.read().decode('utf-8', errors='replace'))
    print(stderr.read().decode('utf-8', errors='replace'))
    
    # Just in case, restart all to be sure everything is linked
    print("Starting all containers...")
    stdin, stdout, stderr = client.exec_command("cd /var/www/steman-alumni && docker compose -f docker-compose.prod.yml up -d")
    exit_status = stdout.channel.recv_exit_status()
    print(stdout.read().decode('utf-8', errors='replace'))
    print(stderr.read().decode('utf-8', errors='replace'))
    
    print("Checking docker ps...")
    stdin, stdout, stderr = client.exec_command("docker ps")
    print(stdout.read().decode('utf-8', errors='replace'))
    
    client.close()
    print("Done!")
except Exception as e:
    print(f"Error: {e}")
