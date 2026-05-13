import paramiko
import re

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Read current file
stdin, stdout, stderr = ssh.exec_command('cat /var/www/steman-alumni/docker-compose.prod.yml')
content = stdout.read().decode()

# Find grafana block and replace memory
# We use regex to replace only the memory limit under grafana
grafana_pattern = r'(  grafana:.*?limits:\n\s*cpus:.*?memory:\s*)256M(.*)'
# Using a positive lookbehind isn't supported for variable length, so we match the block

def repl(m):
    return m.group(1) + '400M'

# We can just split the file by '  grafana:' to safely only modify the grafana block
parts = content.split('  grafana:\n')
if len(parts) == 2:
    new_grafana_block = re.sub(r'(limits:\s*\n\s*cpus:.*?memory:\s*)256M', r'\g<1>400M', parts[1], count=1, flags=re.DOTALL)
    new_content = parts[0] + '  grafana:\n' + new_grafana_block
    
    # Upload new content
    sftp = ssh.open_sftp()
    with sftp.open('/var/www/steman-alumni/docker-compose.prod.yml', 'w') as f:
        f.write(new_content)
    sftp.close()
    
    # Recreate grafana container
    print("Recreating grafana container...")
    stdin, stdout, stderr = ssh.exec_command('cd /var/www/steman-alumni && docker-compose -f docker-compose.prod.yml up -d grafana')
    print("STDOUT:", stdout.read().decode())
    print("STDERR:", stderr.read().decode())
else:
    print("Could not parse grafana block")

ssh.close()
