<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Polymorphic Comments Table
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('commentable'); // Creates commentable_id, commentable_type (indexed by default)
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Polymorphic Likes Table
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('likeable');
            $table->timestamps();

            // Prevent duplicate likes
            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
        });

        // 3. User Tags (e.g. tagging users in posts, comments, galleries)
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagged_user_id')->constrained('users')->onDelete('cascade');
            $table->morphs('taggable');
            $table->timestamps();

            $table->unique(['tagged_user_id', 'taggable_id', 'taggable_type'], 'idx_unique_tag');
        });

        // 4. Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // e.g., 'login', 'create_post', 'update_profile'
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->index('action');
        });

        // 5. Badges / Achievements
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('awarded_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('likes');
        Schema::dropIfExists('comments');
    }
};
