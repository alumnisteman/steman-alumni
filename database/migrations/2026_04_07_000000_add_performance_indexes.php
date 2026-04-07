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
            $table->index('tahun_lulus');
            $table->index('jurusan');
        });

        Schema::table('majors', function (Blueprint $table) {
            $table->index('group');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->index('created_at');
        });
        
        Schema::table('jobs', function (Blueprint $table) {
            $table->index('status_keaktifan'); // Assuming this exists for job filtering
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['tahun_lulus']);
            $table->dropIndex(['jurusan']);
        });

        Schema::table('majors', function (Blueprint $table) {
            $table->dropIndex(['group']);
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
        
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex(['status_keaktifan']);
        });
    }
};
