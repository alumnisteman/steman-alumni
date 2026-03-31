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
        Schema::table('users', function (Blueprint $table) {
            // Index for status filtering (very common in directory)
            if (!Schema::hasIndex('users', 'users_status_index')) {
                $table->index('status');
            }
            // Index for role filtering
            if (!Schema::hasIndex('users', 'users_role_index')) {
                $table->index('role');
            }
            // Ensure email is unique (usually is, but ensuring index exists)
            if (!Schema::hasIndex('users', 'users_email_unique')) {
                $table->unique('email');
            }
        });

        Schema::table('job_vacancies', function (Blueprint $table) {
            if (!Schema::hasIndex('job_vacancies', 'job_vacancies_status_index')) {
                $table->index('status');
            }
        });

        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasIndex('programs', 'programs_status_index')) {
                $table->index('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['role']);
            $table->dropUnique(['email']);
        });

        Schema::table('job_vacancies', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
