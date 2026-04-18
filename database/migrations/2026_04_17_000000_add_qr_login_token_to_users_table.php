<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('qr_login_token')->nullable()->unique()->after('remember_token');
        });

        // Populate existing users with UUIDs
        $users = DB::table('users')->get(['id']);
        foreach ($users as $user) {
            DB::table('users')->where('id', $user->id)->update([
                'qr_login_token' => (string) Str::uuid()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('qr_login_token');
        });
    }
};
