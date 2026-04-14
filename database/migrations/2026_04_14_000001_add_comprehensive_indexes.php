<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- activity_logs: composite index for admin log viewer ---
        try {
            Schema::table('activity_logs', function (Blueprint $table) {
                // Most common query: WHERE user_id = ? ORDER BY created_at DESC
                $table->index(['user_id', 'created_at'], 'idx_activity_logs_user_created');
            });
        } catch (\Exception $e) {}

        // --- news: composite index for public listing ---
        try {
            Schema::table('news', function (Blueprint $table) {
                // WHERE status = 'published' ORDER BY created_at DESC
                $table->index(['status', 'created_at'], 'idx_news_status_created');
            });
        } catch (\Exception $e) {}

        // --- galleries: composite index for public gallery ---
        try {
            Schema::table('galleries', function (Blueprint $table) {
                // WHERE status = 'published' AND type = ? ORDER BY created_at DESC
                $table->index(['status', 'type', 'created_at'], 'idx_galleries_status_type_created');
            });
        } catch (\Exception $e) {}

        // --- users: composite index for alumni directory ---
        try {
            Schema::table('users', function (Blueprint $table) {
                // WHERE role = 'alumni' AND status = 'active'
                $table->index(['role', 'status'], 'idx_users_role_status');
            });
        } catch (\Exception $e) {}

        // --- forums: index for listing by creation date ---
        try {
            Schema::table('forums', function (Blueprint $table) {
                $table->index(['created_at'], 'idx_forums_created_at');
            });
        } catch (\Exception $e) {}

        // --- job_vacancies: status index for public listing ---
        try {
            Schema::table('job_vacancies', function (Blueprint $table) {
                $table->index(['status', 'created_at'], 'idx_jobs_status_created');
            });
        } catch (\Exception $e) {}

        // --- programs: composite index ---
        try {
            Schema::table('programs', function (Blueprint $table) {
                $table->index(['status', 'created_at'], 'idx_programs_status_created');
            });
        } catch (\Exception $e) {}
    }

    public function down(): void
    {
        $drops = [
            'activity_logs' => 'idx_activity_logs_user_created',
            'news'          => 'idx_news_status_created',
            'galleries'     => 'idx_galleries_status_type_created',
            'users'         => 'idx_users_role_status',
            'forums'        => 'idx_forums_created_at',
            'job_vacancies' => 'idx_jobs_status_created',
            'programs'      => 'idx_programs_status_created',
        ];

        foreach ($drops as $table => $index) {
            try {
                Schema::table($table, function (Blueprint $tbl) use ($index) {
                    $tbl->dropIndex($index);
                });
            } catch (\Exception $e) {}
        }
    }
};
