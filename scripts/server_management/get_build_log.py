import paramiko
import sys

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('103.175.219.57', username='root', password='M4ruw4h3@')

print("=== Running build and saving output to build.log ===")
stdin, stdout, stderr = client.exec_command('cd /var/www/steman-alumni && docker compose -f docker-compose.yml build app 2>&1')
output = stdout.read().decode('utf-8', errors='replace')

# Save to local file
with open('build.log', 'w', encoding='utf-8') as f:
    f.write(output)

print("=== Build finished. Saved logs to build.log ===")
# Print the last 40 lines of the build log
lines = output.splitlines()
print("\n=== Last 40 lines of build log ===")
for line in lines[-40:]:
    print(line)

client.close()
