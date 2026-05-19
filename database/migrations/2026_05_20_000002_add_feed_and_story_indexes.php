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
            Schema::table('posts', function (Blueprint $table) {
                if (!Schema::hasIndex('posts', 'idx_posts_visibility_latest')) {
                    $table->index(['visibility', 'created_at'], 'idx_posts_visibility_latest');
                }
                
                if (!Schema::hasIndex('posts', 'idx_posts_type_latest')) {
                    $table->index(['type', 'created_at'], 'idx_posts_type_latest');
                }
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropIndex('idx_posts_visibility_latest');
                $table->dropIndex('idx_posts_type_latest');
            });
        } catch (\Exception $e) {}
    }
};
