import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Create a clean provider.yml
content = """apiVersion: 1
providers:
  - name: 'Steman Dashboards'
    orgId: 1
    folder: 'Steman Alumni'
    type: file
    disableDeletion: false
    updateIntervalSeconds: 10
    options:
      path: /etc/grafana/provisioning/dashboards
"""

# Write it
ssh.exec_command(f'echo "{content}" > /var/www/steman-alumni/docker/grafana/provisioning/dashboards/provider.yml')
ssh.exec_command('chown 472:472 /var/www/steman-alumni/docker/grafana/provisioning/dashboards/provider.yml')

# Restart Grafana
ssh.exec_command('docker restart steman_grafana')

ssh.close()
