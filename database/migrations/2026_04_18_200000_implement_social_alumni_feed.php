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
        // 1. Enhance Posts table
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'visibility')) {
                $table->enum('visibility', ['public', 'friends'])->default('public')->after('type')->index();
            }
        });

        // 2. Create Follows table (Network Alumni)
        if (!Schema::hasTable('follows')) {
            Schema::create('follows', function (Blueprint $table) {
                $table->id();
                $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('following_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['follower_id', 'following_id']);
                $table->index('following_id');
            });
        }

        // 3. Create Feeds table (Precomputed Timeline / Fan-out on Write)
        if (!Schema::hasTable('feeds')) {
            Schema::create('feeds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Owner of this feed
                $table->foreignId('post_id')->constrained()->onDelete('cascade');
                $table->decimal('score', 16, 4)->default(0)->index(); // For ranking algorithm
                $table->timestamps();

                $table->unique(['user_id', 'post_id']);
                $table->index(['user_id', 'score']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feeds');
        Schema::dropIfExists('follows');
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('visibility');
        });
    }
};
