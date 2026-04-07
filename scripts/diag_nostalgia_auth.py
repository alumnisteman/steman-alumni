import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('103.175.219.57', username='root', password='M4ruw4h3@')

print("Simulating Authenticated Request to /nostalgia via Tinker...")

# 1. Get an Alumni User
# 2. Login as that user
# 3. Call the controller@index
# 4. Catch the error
tinker_script = """
try {
    $user = \App\Models\User::where('role', 'alumni')->first();
    if (!$user) {
        throw new Exception("No alumni user found in database.");
    }
    Auth::login($user);
    $request = request();
    $controller = new \App\Http\Controllers\PostController();
    $response = $controller->index($request);
    echo "STATUS: 200 SUCCESS";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . "\n";
    echo "LINE: " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
"""

# Avoid issues with multiple lines by joining them
tinker_script_flat = tinker_script.replace("\n", " ")
cmd = f"docker exec steman-alumni-app-1 php artisan tinker --execute='{tinker_script_flat}'"

stdin, stdout, stderr = client.exec_command(cmd)
print(stdout.read().decode('utf-8'))
print(stderr.read().decode('utf-8'))

client.close()
