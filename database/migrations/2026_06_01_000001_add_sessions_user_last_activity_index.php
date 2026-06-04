<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add a composite index on the `sessions` table.
 * Improves the query used in the admin dashboard to count online users.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            // Composite index for faster distinct user count within recent activity window
            $table->index(['user_id', 'last_activity'], 'sessions_user_last_activity_index');
        });
    }

    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex('sessions_user_last_activity_index');
        });
    }
};
