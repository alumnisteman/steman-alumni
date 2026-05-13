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

# Upload new datasource yaml
sftp = ssh.open_sftp()
sftp.putfo(paramiko.sftp_file.SFTPFile(sftp, '/var/www/steman-alumni/docker/grafana/provisioning/datasources/prometheus.yml', 'w'), datasource_content.encode())
sftp.close()

# Also write using shell just to be sure if sftp mapping fails
stdin, stdout, stderr = ssh.exec_command('echo "apiVersion: 1\n\ndatasources:\n  - name: Prometheus\n    type: prometheus\n    uid: prometheus\n    access: proxy\n    url: http://steman_prometheus:9090\n    isDefault: true\n    editable: true" > /var/www/steman-alumni/docker/grafana/provisioning/datasources/prometheus.yml')
stdout.read()

# Restart Grafana
stdin, stdout, stderr = ssh.exec_command('docker restart steman_grafana')
print("STDOUT:", stdout.read().decode())

ssh.close()
print("Done!")
