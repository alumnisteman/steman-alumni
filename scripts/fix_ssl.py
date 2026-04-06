import sys
import paramiko
import time

sys.stdout.reconfigure(encoding='utf-8')
hostname = '103.175.219.57'
username = 'root'
password = 'YOUR_SSH_PASSWORD'

def run_cmd(client, cmd, timeout=120):
    print(f"\n>>> {cmd}")
    stdin, stdout, stderr = client.exec_command(cmd, timeout=timeout)
    exit_status = stdout.channel.recv_exit_status()
    out = stdout.read().decode('utf-8', errors='replace')
    err = stderr.read().decode('utf-8', errors='replace')
    if out.strip():
        print(out)
    if err.strip():
        print(f"STDERR: {err}")
    print(f"Exit: {exit_status}")
    return exit_status, out, err

try:
    print("Connecting via SSH...")
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(hostname, username=username, password=password)

    # Step 1: Stop nginx so port 80 is free for certbot standalone
    print("\n=== STEP 1: Stop nginx ===")
    run_cmd(client, "docker stop steman_nginx")

    # Step 2: Check if certbot_certs volume exists and list its contents
    print("\n=== STEP 2: Check certbot volume ===")
    run_cmd(client, "docker volume inspect steman-alumni_certbot_certs 2>/dev/null || echo 'Volume not found'")
    
    # Step 3: Run certbot standalone to get fresh SSL cert
    # We need port 80 free (nginx is stopped) and use the certbot_certs volume
    print("\n=== STEP 3: Run certbot standalone ===")
    run_cmd(client, 
        "docker run --rm -p 80:80 "
        "-v steman-alumni_certbot_certs:/etc/letsencrypt "
        "certbot/certbot certonly --standalone "
        "-d alumni-steman.my.id "
        "--non-interactive --agree-tos "
        "--email admin@steman-alumni.com "
        "--force-renewal",
        timeout=120
    )

    # Step 4: Verify the cert was created  
    print("\n=== STEP 4: Verify certificate ===")
    run_cmd(client, 
        "docker run --rm -v steman-alumni_certbot_certs:/etc/letsencrypt alpine "
        "ls -la /etc/letsencrypt/live/alumni-steman.my.id/"
    )

    # Step 5: Start nginx back
    print("\n=== STEP 5: Start nginx ===")
    run_cmd(client, "cd /var/www/steman-alumni && docker compose -f docker-compose.prod.yml up -d webserver")

    # Step 6: Wait and check status
    print("\n=== STEP 6: Check status ===")
    time.sleep(5)
    run_cmd(client, "docker ps --filter name=steman_nginx")
    run_cmd(client, "docker logs --tail 5 steman_nginx 2>&1")

    client.close()
    print("\n=== DONE ===")
except Exception as e:
    print(f"Error: {e}")
