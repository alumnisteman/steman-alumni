import paramiko
import json

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect('103.175.219.57', username='root', password='M4ruw4h3@')

# Strategy: rebuild dashboard to use datasource name instead of UID
# This way it matches by name regardless of UID
stdin, stdout, stderr = ssh.exec_command('cat /var/www/steman-alumni/docker/grafana/provisioning/dashboards/steman-overview.json')
raw = stdout.read()

dashboard = json.loads(raw)

# Replace all datasource references to use name-based lookup
ds_ref = {"type": "prometheus", "uid": "${DS_PROMETHEUS}"}

for panel in dashboard.get('panels', []):
    panel['datasource'] = ds_ref
    for target in panel.get('targets', []):
        target['datasource'] = ds_ref

# Also add __inputs for template variable resolution
dashboard['__inputs'] = [
    {
        "name": "DS_PROMETHEUS",
        "label": "Prometheus",
        "description": "",
        "type": "datasource",
        "pluginId": "prometheus",
        "pluginName": "Prometheus"
    }
]
dashboard['__requires'] = [
    {"type": "datasource", "id": "prometheus", "name": "Prometheus", "version": "1.0.0"}
]

# Upload the updated dashboard
import io
content = json.dumps(dashboard, indent=2).encode('utf-8')
sftp = ssh.open_sftp()
with sftp.open('/var/www/steman-alumni/docker/grafana/provisioning/dashboards/steman-overview.json', 'w') as f:
    f.write(json.dumps(dashboard, indent=2))
sftp.close()

print("Dashboard updated with variable datasource reference")

# Now restart grafana to pick up both the new datasource UID and new dashboard
stdin, stdout, stderr = ssh.exec_command('docker restart steman_grafana')
print("Restart:", stdout.read().decode().strip())

ssh.close()
