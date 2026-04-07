import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('103.175.219.57', username='root', password='M4ruw4h3@')

print("Enabling APP_DEBUG=true in production .env...")
# Replace APP_DEBUG=false with APP_DEBUG=true
cmd = "sed -i 's/APP_DEBUG=false/APP_DEBUG=true/g' /var/www/steman-alumni/.env"
client.exec_command(cmd)

print("Clearing config cache on all app replicas...")
stdin, stdout, stderr = client.exec_command('docker ps --filter name=steman-alumni-app --format "{{.Names}}"')
container_names = stdout.read().decode('utf-8').strip().split('\n')

for container in container_names:
    if container:
        print(f"Clearing cache on {container}...")
        client.exec_command(f"docker exec {container} php artisan config:clear")

client.close()
print("Debug Mode is now ENABLED. Please refresh /nostalgia and share the error message.")
