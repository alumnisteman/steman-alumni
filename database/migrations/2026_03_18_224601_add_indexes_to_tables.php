<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('nisn');
            $table->index('tahun_lulus');
            $table->index('jurusan');
            $table->index('role');
        });

        Schema::table('job_vacancies', function (Blueprint $table) {
            $table->index('slug');
            $table->index('status');
            $table->index('type');
        });

        Schema::table('majors', function (Blueprint $table) {
            $table->index('group');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['nisn']);
            $table->dropIndex(['tahun_lulus']);
            $table->dropIndex(['jurusan']);
            $table->dropIndex(['role']);
        });

        Schema::table('job_vacancies', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['status']);
            $table->dropIndex(['type']);
        });

        Schema::table('majors', function (Blueprint $table) {
            $table->dropIndex(['group']);
            $table->dropIndex(['status']);
        });
    }
};
