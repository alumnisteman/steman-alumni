import paramiko
import sys

def run_remote_command(host, port, username, password, command):
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try:
        client.connect(host, port=port, username=username, password=password, timeout=10)
        stdin, stdout, stderr = client.exec_command(command)
        out = stdout.read().decode('utf-8')
        err = stderr.read().decode('utf-8')
        exit_status = stdout.channel.recv_exit_status()
        
        sys.stdout.buffer.write(f"--- STDOUT ---\n{out}".encode('utf-8'))
        sys.stdout.buffer.write(f"\n--- STDERR ---\n{err}".encode('utf-8'))
        print(f"\n--- EXIT STATUS ---: {exit_status}")
    except Exception as e:
        print(f"Error: {e}")
    finally:
        client.close()

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python remote_cmd.py '<command>'")
        sys.exit(1)
        
    cmd = sys.argv[1]
    run_remote_command("103.175.219.57", 22, "root", "M4ruw4h3@", cmd)
