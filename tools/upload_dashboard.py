import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

sftp = ssh.open_sftp()
local_path = r'd:\THOY STEMAN FILE\steman-alumni-v4.1\steman-alumni\updated_dashboard_pulse.json'
remote_path = '/var/www/steman-alumni/docker/grafana/provisioning/dashboards/steman-overview.json'

print("Uploading updated_dashboard_pulse.json to steman-overview.json...")
sftp.put(local_path, remote_path)
sftp.close()

# Restart grafana container
print("Restarting steman_grafana...")
stdin, stdout, stderr = ssh.exec_command('docker restart steman_grafana')
print("STDOUT:", stdout.read().decode())

ssh.close()
print("Done!")
