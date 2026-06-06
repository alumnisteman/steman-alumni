<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ResetPasswordCommand extends Command
{
    protected $signature = 'password:reset {email}';
    protected $description = 'Reset user password to password123';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error('User not found');
            return 1;
        }
        
        $user->password = bcrypt('password123');
        $user->save();
        
        $this->info("Password reset to 'password123' for {$email}");
        return 0;
    }
}
