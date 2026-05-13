import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

datasource_content = """apiVersion: 1

datasources:
  - name: Prometheus
    type: prometheus
    uid: prometheus
    access: proxy
    url: http://steman_prometheus:9090
    isDefault: true
    editable: true
"""

# Upload new datasource yaml using sftp correctly
sftp = ssh.open_sftp()
with sftp.open('/var/www/steman-alumni/docker/grafana/provisioning/datasources/prometheus.yml', 'w') as remote_file:
    remote_file.write(datasource_content)
sftp.close()

# Restart Grafana
stdin, stdout, stderr = ssh.exec_command('docker restart steman_grafana')
print("STDOUT:", stdout.read().decode())

ssh.close()
print("Done!")
