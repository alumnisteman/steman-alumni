<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change DB-level default from 'pending' to 'approved'
        // so any new user created via ANY path is approved by default.
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved'");

        // Fix any existing 'pending' alumni that slipped through
        DB::statement("UPDATE users SET status='approved', auto_approved=1 WHERE status='pending' AND role='alumni'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
};
