import paramiko
import sys

def upload_file(host, port, username, password, local_path, remote_path):
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try:
        client.connect(host, port=port, username=username, password=password, timeout=10)
        sftp = client.open_sftp()
        sftp.put(local_path, remote_path)
        sftp.close()
        print(f"Successfully uploaded {local_path} to {remote_path}")
    except Exception as e:
        print(f"Error: {e}")
    finally:
        client.close()

if __name__ == "__main__":
    upload_file(
        "103.175.219.57", 
        22, 
        "root", 
        "M4ruw4h3@", 
        "d:\\THOY STEMAN FILE\\steman-alumni-v4.1\\steman-alumni\\resources\\views\\admin\\gallery.blade.php",
        "/var/www/steman-alumni/resources/views/admin/gallery.blade.php"
    )
