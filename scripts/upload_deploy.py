import paramiko
import base64

hostname = '103.175.219.57'
username = 'root'
password = 'YOUR_SSH_PASSWORD'

env_b64 = "QVBQX05BTUU9IkFsdW1uaSBTVEVNQU4iCkFQUF9FTlY9cHJvZHVjdGlvbgpBUFBfS0VZPWJhc2U2NDpFTUwySEoyQ284c2g1dlNndXR6TVZnc0lsaVJISWtRQWQzYzU0RDdUalFJPQpBUFBfREVCVUc9ZmFsc2UKQVBQX1VSTD1odHRwczovL2FsdW1uaS1zdGVtYW4ubXkuaWQKCkxPR19DSEFOTkVMPWRhaWx5CkxPR19ERVBSRUNBVElPTlNfQ0hBTk5FTD1udWxsCkxPR19MRVZFTD1kZWJ1ZwoKREJfQ09OTkVDVElPTj1teXNxbApEQl9IT1NUPWRiCkRCX1BPUlQ9MzMwNgpEQl9EQVRBQkFTRT1zdGVtYW5fYWx1bW5pCkRCX1VTRVJOQU1FPWFwcF91c2VyCkRCX1BBU1NXT1JEPXN0cm9uZ3Bhc3N3b3JkCgpCUk9BRENBU1RfQ09OTkVDVElPTj1yZXZlcmIKQ0FDSEVfU1RPUkU9ZmlsZQpGSUxFU1lTVEVNX0RJU0s9cHVibGljClFVRVVFX0NPTk5FQ1RJT049cmVkaXMKUkVESVNfSE9TVD1yZWRpcwpSRURJU19QQVNTV09SRD1udWxsClJFRElTX1BPUlQ9NjM3OQoKUkVWRVJCX0FQUF9JRD0xMjM0NTYKUkVWRVJCX0FQUF9LRVk9c3RlbWFuLXJldmVyYi1rZXkKUkVWRVJCX0FQUF9TRUNSRVQ9c3RlbWFuLXJldmVyYi1zZWNyZXQKUkVWRVJCX0hPU1Q9ImFsdW1uaS1zdGVtYW4ubXkuaWQiClJFVkVSQl9QT1JUPTQ0MwpSRVZFUkJfU0NIRU1FPWh0dHBzCgpWSVRFX1JFVkVSQl9BUFBfS0VZPSJzdGVtYW4tcmV2ZXJiLWtleSIKVklURV9SRVZFUkJfSE9TVD0iYWx1bW5pLXN0ZW1hbi5teS5pZCIKVklURV9SRVZFUkJfUE9SVD00NDMKVklURV9SRVZFUkJfU0NIRU1FPWh0dHBzCgpTRVNTSU9OX0RSSVZFUj1maWxlClNFU1NJT05fTElGRVRJTUU9MTIwCgpNRU1DQUNIRURfSE9TVD0xMjcuMC4wLjEKCk1BSUxfTUFJTEVSPXNtdHAKTUFJTF9IT1NUPXNtdHAuZ21haWwuY29tCk1BSUxfUE9SVD00NjUKTUFJTF9VU0VSTkFNRT1lbWFpbF9hbmRhQGdtYWlsLmNvbQpNQUlMX1BBU1NXT1JEPXBhc3N3b3JkX2FwcF9nb29nbGVfYW5kYQpNQUlMX0VOQ1JZUFRJT049dGxzCk1BSUxfRlJPTV9BRERSRVNTPWVtYWlsX2FuZGFAZ21haWwuY29tCk1BSUxfRlJPTV9OQU1FPSJBbHVtbmkgU1RFTUFOIgoKR09PR0xFX0NMSUVOVF9JRD0zNDE2MTIyNzExMTYtdHBtMW5yNG42aDh2YW8zMnFyNHVrMTBrdHJsMnFoOWEuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20KR09PR0xFX0NMSUVOVF9TRUNSRVQ9R09DU1BYLUc2Mm1LTHFsZ2lrU01UYzRhbXMxZk1KTjUwdDYKR09PR0xFX1JFRElSRUNUX1VSST1odHRwczovL2FsdW1uaS1zdGVtYW4ubXkuaWQvYXV0aC9nb29nbGUvY2FsbGJhY2sKCkxJTktFRElOX0NMSUVOVF9JRD15b3VyLWxpbmtlZGluLWNsaWVudC1pZApMSU5LRURJTl9DTElFTlRfU0VDUkVUPXJlbW92ZWQKTElOS0VESU5fUkVESVJFQ1RfVVJJPWh0dHBzOi8vYWx1bW5pLXN0ZW1hbi5teS5pZC9hdXRoL2xpbmtlZGluL2NhbGxiYWNrCg=="

try:
    print("Connecting via SSH...")
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(hostname, username=username, password=password)
    
    # Send the env file
    print("Writing .env file...")
    sftp = client.open_sftp()
    env_content = base64.b64decode(env_b64)
    with sftp.open('/var/www/steman-alumni/.env', 'w') as f:
        f.write(env_content)
    sftp.close()
    
    print("Restarting Docker containers...")
    stdin, stdout, stderr = client.exec_command("cd /var/www/steman-alumni && docker compose -f docker-compose.prod.yml up -d --build --remove-orphans && bash scripts/db/final_prod_fix.sh")
    exit_status = stdout.channel.recv_exit_status()  # Block until command finishes
    
    print("STDOUT:")
    for line in stdout:
        print(line.strip())
        
    print("STDERR:")
    for line in stderr:
        print(line.strip())
        
    client.close()
    print("Done!")
except Exception as e:
    print(f"Error: {e}")
