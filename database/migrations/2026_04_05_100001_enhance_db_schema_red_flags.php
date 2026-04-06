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
        try {
            Schema::table('job_vacancies', function (Blueprint $table) {
                if (!Schema::hasColumn('job_vacancies', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                }
            });
        } catch (\Exception $e) {}

        try { Schema::table('job_vacancies', function (Blueprint $table) { $table->index('status'); }); } catch (\Exception $e) {}
        try { Schema::table('job_vacancies', function (Blueprint $table) { $table->softDeletes(); }); } catch (\Exception $e) {}

        try {
            Schema::table('programs', function (Blueprint $table) {
                if (!Schema::hasColumn('programs', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                }
            });
        } catch (\Exception $e) {}

        try { Schema::table('programs', function (Blueprint $table) { $table->index('status'); }); } catch (\Exception $e) {}
        try { Schema::table('programs', function (Blueprint $table) { $table->softDeletes(); }); } catch (\Exception $e) {}

        // 2. Add SoftDeletes & Explicit Indexes to Users
        try { Schema::table('users', function (Blueprint $table) { $table->index('role'); }); } catch (\Exception $e) {}
        try { Schema::table('users', function (Blueprint $table) { $table->softDeletes(); }); } catch (\Exception $e) {}

        // 3. Add SoftDeletes to other relational tables
        $tablesWithSoftDeletes = [
            'news',
            'galleries',
            'forums',
            'comments',
            'messages'
        ];

        foreach ($tablesWithSoftDeletes as $tableName) {
            try {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            } catch (\Exception $e) {}
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
