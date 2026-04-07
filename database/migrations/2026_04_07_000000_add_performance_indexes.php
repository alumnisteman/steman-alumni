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
                $table->index('tahun_lulus');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('jurusan');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('majors', function (Blueprint $table) {
                $table->index('group');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('news', function (Blueprint $table) {
                $table->index('created_at');
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
                $table->dropIndex(['tahun_lulus']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['jurusan']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('majors', function (Blueprint $table) {
                $table->dropIndex(['group']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('news', function (Blueprint $table) {
                $table->dropIndex(['created_at']);
            });
        } catch (\Exception $e) {}
    }
};
