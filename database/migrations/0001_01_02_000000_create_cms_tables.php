<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('label')->nullable();
                $table->string('group')->nullable()->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('majors')) {
            Schema::create('majors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('group')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('programs')) {
            Schema::create('programs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->longText('content')->nullable();
                $table->enum('status', ['draft', 'published'])->default('draft')->index();
                $table->string('registration_link')->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('news')) {
            Schema::create('news', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('title', 255);
                $table->string('slug')->unique();
                $table->longText('content')->nullable();
                $table->string('thumbnail')->nullable();
                $table->string('category')->nullable()->index();
                $table->enum('status', ['draft', 'published'])->default('published')->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('galleries')) {
            Schema::create('galleries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('title');
                $table->enum('type', ['photo', 'youtube', 'tiktok'])->default('photo')->index();
                $table->string('file_path')->nullable();
                $table->string('youtube_url')->nullable();
                $table->string('tiktok_url')->nullable();
                $table->text('description')->nullable();
                $table->enum('status', ['draft', 'published'])->default('published')->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('news');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('majors');
        Schema::dropIfExists('settings');
    }
};
