<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $user = User::where('email', 'namaskarabumi@gmail.com')->first();
        
        if ($user) {
            $user->update([
                'password' => Hash::make('M4ruw4h3'),
                'role' => 'admin',
                'status' => 'approved'
            ]);
        } else {
            // Jika user belum ada, buat baru sebagai Admin Utama
            User::create([
                'name' => 'Admin Utama',
                'email' => 'namaskarabumi@gmail.com',
                'password' => Hash::make('M4ruw4h3'),
                'role' => 'admin',
                'status' => 'approved',
                'graduation_year' => 2000,
                'major' => 'TKJ'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for emergency fix
    }
};
