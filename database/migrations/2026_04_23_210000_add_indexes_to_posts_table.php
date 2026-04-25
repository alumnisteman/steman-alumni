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
        Schema::table('posts', function (Blueprint $table) {
            try {
                $table->index('user_id', 'idx_posts_user_id');
            } catch (\Exception $e) {}
            
            try {
                $table->index('created_at', 'idx_posts_created_at');
            } catch (\Exception $e) {}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            try {
                $table->dropIndex('idx_posts_user_id');
            } catch (\Exception $e) {}
            
            try {
                $table->dropIndex('idx_posts_created_at');
            } catch (\Exception $e) {}
        });
    }
};
