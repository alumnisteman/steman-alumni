<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add privacy toggle to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'show_social')) {
                $table->boolean('show_social')->default(1)->after('profile_picture');
            }
        });

        // 2. Create social_links table (Scalable approach)
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('platform', ['instagram', 'facebook', 'tiktok', 'linkedin', 'github', 'website']);
            $table->string('url');
            $table->timestamps();
            
            // Optimization: Index for fast lookup per user
            $table->index(['user_id', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('show_social');
        });
    }
};
