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
        try {
            Schema::table('users', function (Blueprint $table) {
                // For spatial lookup performance in Steman Earth
                if (!Schema::hasIndex('users', ['latitude', 'longitude'])) {
                    $table->index(['latitude', 'longitude'], 'idx_user_coords');
                }
                
                // For city-based filtering
                if (!Schema::hasIndex('users', ['city_name'])) {
                    $table->index('city_name', 'idx_user_city');
                }

                // For online status tracking (lightweight access)
                if (!Schema::hasIndex('users', ['last_active_at'])) {
                    $table->index('last_active_at', 'idx_user_activity');
                }
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Migration: Steman Earth indexes partially failed: ' . $e->getMessage());
        }

        try {
            Schema::table('activity_logs', function (Blueprint $table) {
                // Ensure fast pruning
                if (!Schema::hasIndex('activity_logs', ['created_at'])) {
                    $table->index('created_at', 'idx_activity_logs_date');
                }
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('idx_user_coords');
                $table->dropIndex('idx_user_city');
                $table->dropIndex('idx_user_activity');
            });
        } catch (\Exception $e) {}
        
        try {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropIndex('idx_activity_logs_date');
            });
        } catch (\Exception $e) {}
    }
};
