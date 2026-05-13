import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

sftp = ssh.open_sftp()

try:
    sftp.put(r'd:\THOY STEMAN FILE\steman-alumni-v4.1\steman-alumni\scripts\steman-autoheal.sh', '/var/www/steman-alumni/scripts/steman-autoheal.sh')
    print("Uploaded steman-autoheal.sh to /var/www/steman-alumni/scripts/")
except Exception as e:
    print("Error uploading:", e)

try:
    sftp.put(r'd:\THOY STEMAN FILE\steman-alumni-v4.1\steman-alumni\scripts\system_optimize.sh', '/var/www/steman-alumni/scripts/system_optimize.sh')
    print("Uploaded system_optimize.sh to /var/www/steman-alumni/scripts/")
except Exception as e:
    print("Error uploading:", e)

sftp.close()

# Let's also find if there are any cronjobs that need to be restarted, or if these are copied to /root
commands = [
    "cp /var/www/steman-alumni/scripts/steman-autoheal.sh /root/steman-autoheal.sh 2>/dev/null",
    "cp /var/www/steman-alumni/scripts/system_optimize.sh /root/system_optimize.sh 2>/dev/null",
    "cp /var/www/steman-alumni/scripts/steman-autoheal.sh /root/scripts/steman-autoheal.sh 2>/dev/null",
    "cp /var/www/steman-alumni/scripts/system_optimize.sh /root/scripts/system_optimize.sh 2>/dev/null",
    "chmod +x /var/www/steman-alumni/scripts/*.sh",
    "chmod +x /root/*.sh 2>/dev/null",
    "chmod +x /root/scripts/*.sh 2>/dev/null"
]

for cmd in commands:
    print(f"Executing: {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    stdout.channel.recv_exit_status()

print("Done.")
ssh.close()
