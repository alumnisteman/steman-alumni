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
    
    print("Checking docker ps...")
    stdin, stdout, stderr = client.exec_command("cd /var/www/steman-alumni && docker ps")
    print(stdout.read().decode('utf-8', errors='replace'))
    
    # Run the final fix script just in case it was interrupted
    print("Running final db fix...")
    stdin, stdout, stderr = client.exec_command("cd /var/www/steman-alumni && bash scripts/db/final_prod_fix.sh")
    exit_status = stdout.channel.recv_exit_status()
    print(stdout.read().decode('utf-8', errors='replace'))
    print(stderr.read().decode('utf-8', errors='replace'))
    
    print("Testing connection to global network API...")
    stdin, stdout, stderr = client.exec_command("curl -s -o /dev/null -w '%{http_code}' https://alumni-steman.my.id/api/v1/alumni")
    print("HTTP Code:", stdout.read().decode('utf-8', errors='replace'))
    
    client.close()
    print("Done!")
except Exception as e:
    print(f"Error: {e}")
