import paramiko
import sys

host = '103.175.219.57'
user = 'root'
password = 'M4ruw4h3@'

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    print(f"Connecting to {host}...")
    client.connect(hostname=host, username=user, password=password, timeout=10)
    print("Connected successfully!")
    
    # Run commands
    commands = [
        "cd /var/www/steman-alumni && git remote update && git status -uno"
    ]
    
    for cmd in commands:
        print(f"\nExecuting: {cmd}")
        stdin, stdout, stderr = client.exec_command(cmd)
        out = stdout.read().decode().strip()
        err = stderr.read().decode().strip()
        
        if out:
            print("Output:")
            print(out)
        if err:
            print("Error/Warning:")
            print(err)
            
except Exception as e:
    print(f"An error occurred: {e}")
finally:
    client.close()
