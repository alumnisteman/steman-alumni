<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Forums
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

        // 2. Posts (Nostalgia Feed)
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

            // Composite Index for feed performance
            $table->index(['user_id', 'created_at']);
        });

        // 3. Job Vacancies
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
            $table->string('type')->default('full-time'); // full-time, part-time, internship
            $table->enum('status', ['active', 'closed', 'draft'])->default('active')->index();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Chat Messages (Internal Messaging)
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengirim_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('penerima_id')->nullable()->constrained('users')->onDelete('set null');
            $table->year('angkatan_tujuan')->nullable()->index(); // Broadcast support
            $table->text('pesan');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Contact Messages (External to Admin)
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

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('job_vacancies');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('forums');
    }
};
