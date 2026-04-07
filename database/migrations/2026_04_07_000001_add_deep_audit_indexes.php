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
                $table->index(['role', 'status'], 'idx_user_role_status');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->index('user_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->index('type');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->index('created_at');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('job_vacancies', function (Blueprint $table) {
                $table->index('status');
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
                $table->dropIndex('idx_user_role_status');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropIndex(['type']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropIndex(['created_at']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('job_vacancies', function (Blueprint $table) {
                $table->dropIndex(['status']);
            });
        } catch (\Exception $e) {}
    }
};
