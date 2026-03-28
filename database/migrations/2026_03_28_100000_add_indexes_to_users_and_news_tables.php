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
            if (!Schema::hasIndex('users', 'users_role_index')) {
                $table->index('role');
            }
            if (!Schema::hasIndex('users', 'users_jurusan_index')) {
                $table->index('jurusan');
            }
            if (!Schema::hasIndex('users', 'users_tahun_lulus_index')) {
                $table->index('tahun_lulus');
            }
        });

        Schema::table('news', function (Blueprint $table) {
            if (!Schema::hasIndex('news', 'news_is_published_index')) {
                $table->index('is_published');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasIndex('users', 'users_role_index')) {
                $table->dropIndex(['role']);
            }
            if (Schema::hasIndex('users', 'users_jurusan_index')) {
                $table->dropIndex(['jurusan']);
            }
            if (Schema::hasIndex('users', 'users_tahun_lulus_index')) {
                $table->dropIndex(['tahun_lulus']);
            }
        });

        Schema::table('news', function (Blueprint $table) {
            if (Schema::hasIndex('news', 'news_is_published_index')) {
                $table->dropIndex(['is_published']);
            }
        });
    }
};
