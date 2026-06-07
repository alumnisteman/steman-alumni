import paramiko
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
        "docker exec steman_app php artisan migrate --force",
        "docker exec steman_app php artisan optimize:clear"
    ]

    for cmd in commands:
        print(f"\nExecuting: {cmd}")
        stdin, stdout, stderr = client.exec_command(cmd)

        while True:
            line = stdout.readline()
            if not line:
                break
            print(line.strip())

        err = stderr.read().decode().strip()
        if err:
            print("Error/Warning output:")
            print(err)

except Exception as e:
    print(f"An error occurred: {e}")
finally:
    client.close()
    print("\nMigration finished.")
