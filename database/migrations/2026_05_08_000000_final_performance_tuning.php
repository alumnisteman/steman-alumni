<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Messages: Composite index for chat threads
        try {
            Schema::table('messages', function (Blueprint $table) {
                // Common query: WHERE (sender_id = ? AND receiver_id = ?) ORDER BY created_at DESC
                $table->index(['sender_id', 'receiver_id', 'created_at'], 'idx_msgs_sender_receiver_created');
            });
        } catch (\Exception $e) {}

        // 2. Stories: Composite index for active stories
        try {
            Schema::table('stories', function (Blueprint $table) {
                // Common query: WHERE user_id = ? AND expires_at > NOW()
                $table->index(['user_id', 'expires_at'], 'idx_stories_user_expires');
            });
        } catch (\Exception $e) {}

        // 3. Sessions: Speed up cleanup and lookup
        try {
            Schema::table('sessions', function (Blueprint $table) {
                $table->index(['user_id', 'last_activity'], 'idx_sessions_user_activity');
            });
        } catch (\Exception $e) {}
    }

    public function down(): void
    {
        try {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropIndex('idx_msgs_sender_receiver_created');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('stories', function (Blueprint $table) {
                $table->dropIndex('idx_stories_user_expires');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropIndex('idx_sessions_user_activity');
            });
        } catch (\Exception $e) {}
    }
};
