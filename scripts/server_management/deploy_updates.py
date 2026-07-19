import paramiko

host = '103.175.219.57'
user = 'root'
password = 'M4ruw4h3@'

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    print(f"Connecting to {host}...")
    client.connect(hostname=host, username=user, password=password, timeout=10)
    print("Connected successfully!")
    
    commands = [
        "cd /var/www/steman-alumni && git fetch origin main && git reset --hard origin/main",
        "cd /var/www/steman-alumni && docker compose -f docker-compose.prod.yml build app",
        "cd /var/www/steman-alumni && docker compose -f docker-compose.prod.yml up -d",
        "docker exec steman_app php artisan config:cache",
        "docker exec steman_app php artisan route:cache",
        "docker exec steman_app php artisan view:cache",
        # CATATAN: db:seed DIHAPUS. SettingSeeder memakai firstOrCreate — tidak perlu dijalankan saat deploy.
        "docker exec steman_app php artisan migrate --force"
    ]
    
    for cmd in commands:
        print(f"\nExecuting: {cmd}")
        stdin, stdout, stderr = client.exec_command(cmd)
        
        # We read incrementally to avoid hanging on long commands like docker build
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
    print("\nDeployment script finished.")
