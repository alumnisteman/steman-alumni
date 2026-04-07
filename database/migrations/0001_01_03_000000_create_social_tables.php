<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('forums')) {
            Schema::create('forums', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('title');
                $table->longText('content');
                $table->integer('views')->default(0);
                $table->integer('comments_count')->default(0);
                $table->enum('status', ['active', 'closed', 'archived'])->default('active')->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('content');
                $table->string('image_url')->nullable();
                $table->enum('type', ['memory', 'story', 'event'])->default('memory')->index();
                $table->integer('likes_count')->default(0);
                $table->integer('comments_count')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'created_at']);
            });
        }

        if (!Schema::hasTable('job_vacancies')) {
            Schema::create('job_vacancies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('company')->nullable();
                $table->string('location')->nullable();
                $table->text('description')->nullable();
                $table->longText('content')->nullable();
                $table->string('external_link')->nullable();
                $table->string('type')->default('full-time');
                $table->enum('status', ['active', 'closed', 'draft'])->default('active')->index();
                $table->string('image')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pengirim_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('penerima_id')->nullable()->constrained('users')->onDelete('set null');
                $table->year('angkatan_tujuan')->nullable()->index();
                $table->text('pesan');
                $table->boolean('is_read')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('contact_messages')) {
            Schema::create('contact_messages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('subject')->nullable();
                $table->text('message');
                $table->text('reply_content')->nullable();
                $table->enum('status', ['unread', 'read', 'replied'])->default('unread')->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('job_vacancies');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('forums');
    }
};
