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
        // 1. User Table Tuning for Alumni Search & Filtering
        try {
            Schema::table('users', function (Blueprint $table) {
                // Optimized for alumni listing and search filters
                if (!Schema::hasIndex('users', 'idx_user_search_basic')) {
                    $table->index(['role', 'status', 'graduation_year'], 'idx_user_search_basic');
                }
                
                if (!Schema::hasIndex('users', 'idx_user_search_major')) {
                    $table->index(['role', 'status', 'major'], 'idx_user_search_major');
                }

                if (!Schema::hasIndex('users', 'idx_user_activation_lookup')) {
                    $table->index(['is_active', 'status', 'role'], 'idx_user_activation_lookup');
                }
            });
        } catch (\Exception $e) {}

        // 2. Posts Table Tuning
        try {
            Schema::table('posts', function (Blueprint $table) {
                // For user profile feed performance
                if (!Schema::hasIndex('posts', 'idx_posts_user_created')) {
                    $table->index(['user_id', 'created_at'], 'idx_posts_user_created');
                }
            });
        } catch (\Exception $e) {}

        // 3. Job Vacancies Tuning
        try {
            Schema::table('job_vacancies', function (Blueprint $table) {
                if (!Schema::hasIndex('job_vacancies', 'idx_jobs_status_date')) {
                    $table->index(['status', 'created_at'], 'idx_jobs_status_date');
                }
            });
        } catch (\Exception $e) {}

        // 4. Activity Logs Performance
        try {
            Schema::table('activity_logs', function (Blueprint $table) {
                // Common query for user activity history
                if (!Schema::hasIndex('activity_logs', 'idx_logs_user_date')) {
                    $table->index(['user_id', 'created_at'], 'idx_logs_user_date');
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
                $table->dropIndex('idx_user_search_basic');
                $table->dropIndex('idx_user_search_major');
                $table->dropIndex('idx_user_activation_lookup');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropIndex('idx_posts_user_created');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('job_vacancies', function (Blueprint $table) {
                $table->dropIndex('idx_jobs_status_date');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropIndex('idx_logs_user_date');
            });
        } catch (\Exception $e) {}
    }
};
