<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table for Tinder-style Skill Matchmaking
        if (!Schema::hasTable('user_matches')) {
            Schema::create('user_matches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('target_id')->constrained('users')->onDelete('cascade');
                $table->enum('status', ['liked', 'passed', 'matched'])->default('liked');
                $table->timestamps();
                
                // Ensure a user can only swipe another user once
                $table->unique(['user_id', 'target_id']);
            });
        }

        // Table for Badges (Gamification)
        if (!Schema::hasTable('badges')) {
            Schema::create('badges', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->string('icon_url')->nullable(); // Path to the 3D glowing asset
                $table->string('color_theme')->default('blue'); // neon color
                $table->integer('points_required')->default(0);
                $table->timestamps();
            });
        }

        // Table for User Badges Pivot
        if (!Schema::hasTable('user_badges')) {
            Schema::create('user_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('badge_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['user_id', 'badge_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('user_matches');
    }
};
