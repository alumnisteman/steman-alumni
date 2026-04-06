<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Posts Table
        // Posts Table
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->text('content');
                $table->string('image_url')->nullable();
                $table->enum('type', ['memory', 'story', 'event'])->default('memory');
                $table->integer('likes_count')->default(0);
                $table->integer('comments_count')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Post Likes Table
        if (!Schema::hasTable('post_likes')) {
            Schema::create('post_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                
                $table->unique(['post_id', 'user_id']);
            });
        }

        // Post Comments Table
        if (!Schema::hasTable('post_comments')) {
            Schema::create('post_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->text('content');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Post Tags Table
        if (!Schema::hasTable('post_tags')) {
            Schema::create('post_tags', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Tagged User
                $table->timestamps();
                
                $table->unique(['post_id', 'user_id']);
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('post_tags');
        Schema::dropIfExists('post_comments');
        Schema::dropIfExists('post_likes');
        Schema::dropIfExists('posts');
    }
};
