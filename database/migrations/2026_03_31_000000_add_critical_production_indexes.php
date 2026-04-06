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
        // Add indexes individually and catch exceptions if they already exist
        try { Schema::table('users', function (Blueprint $table) { $table->index('status'); }); } catch (\Exception $e) {}
        try { Schema::table('users', function (Blueprint $table) { $table->index('role'); }); } catch (\Exception $e) {}
        try { Schema::table('users', function (Blueprint $table) { $table->unique('email'); }); } catch (\Exception $e) {}
        try { Schema::table('job_vacancies', function (Blueprint $table) { $table->index('status'); }); } catch (\Exception $e) {}
        try { Schema::table('programs', function (Blueprint $table) { $table->index('status'); }); } catch (\Exception $e) {}
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
