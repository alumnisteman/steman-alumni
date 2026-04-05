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
        // 1. Add User IDs and Constraints to tables missing relations
        Schema::table('job_vacancies', function (Blueprint $table) {
            if (!Schema::hasColumn('job_vacancies', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            $table->index('status');
            $table->softDeletes();
        });

        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            $table->index('status');
            $table->softDeletes();
        });

        // 2. Add SoftDeletes & Explicit Indexes to Users
        Schema::table('users', function (Blueprint $table) {
            // Note: email is already unique() which acts as an index, but we ensure role is indexed
            $table->index('role');
            $table->softDeletes();
        });

        // 3. Add SoftDeletes to other relational tables
        $tablesWithSoftDeletes = [
            'news',
            'galleries',
            'forums',
            'comments',
            'messages'
        ];

        foreach ($tablesWithSoftDeletes as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_vacancies', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropIndex(['status']);
            $table->dropSoftDeletes();
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropIndex(['status']);
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropSoftDeletes();
        });

        $tablesWithSoftDeletes = [
            'news',
            'galleries',
            'forums',
            'comments',
            'messages'
        ];

        foreach ($tablesWithSoftDeletes as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
