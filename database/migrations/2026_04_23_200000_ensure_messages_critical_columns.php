<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotent migration to ensure the messages table has all required columns.
 * This is a safety migration that can be run multiple times without error.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('messages')) {
            return; // Table doesn't exist, handled by the base migration
        }

        Schema::table('messages', function (Blueprint $table) {
            // Add is_read column if missing (was accidentally absent from production)
            if (!Schema::hasColumn('messages', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('message');
            }

            // Add parent_id for reply-to-message feature
            if (!Schema::hasColumn('messages', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('receiver_id');
                $table->foreign('parent_id')->references('id')->on('messages')->onDelete('set null');
            }
        });

        // Add index on is_read + receiver_id for faster unread count queries
        try {
            Schema::table('messages', function (Blueprint $table) {
                $table->index(['receiver_id', 'is_read'], 'messages_receiver_is_read_index');
            });
        } catch (\Exception $e) {
            // Index might already exist, ignore
        }
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            if (Schema::hasColumn('messages', 'is_read')) {
                $table->dropColumn('is_read');
            }
        });
    }
};
