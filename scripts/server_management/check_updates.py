import paramiko
import sys
import os

host = os.environ.get('SERVER_HOST', '103.175.219.57')
user = os.environ.get('SERVER_USER', 'root')
password = os.environ.get('SERVER_PASSWORD', '')

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    print(f"Connecting to {host}...")
    client.connect(hostname=host, username=user, password=password, timeout=10)
    print("Connected successfully!")

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
