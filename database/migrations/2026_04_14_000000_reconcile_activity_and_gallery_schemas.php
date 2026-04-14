<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Reconcile activity_logs
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('activity_logs', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
                }
                if (!Schema::hasColumn('activity_logs', 'user_agent')) {
                    $table->text('user_agent')->nullable()->after('ip_address');
                }
            });
        }

        // 2. Reconcile galleries
        if (Schema::hasTable('galleries')) {
            Schema::table('galleries', function (Blueprint $table) {
                // Ensure user_id is nullable (to match local migration intentions and prevent strict crash)
                $table->foreignId('user_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (Schema::hasColumn('activity_logs', 'user_agent')) {
                    $table->dropColumn('user_agent');
                }
            });
        }
        
        if (Schema::hasTable('galleries')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable(false)->change();
            });
        }
    }
};
