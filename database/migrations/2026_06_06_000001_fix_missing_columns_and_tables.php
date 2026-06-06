<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom birthday & birthday_public ke tabel users
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'birthday')) {
                    $table->date('birthday')->nullable()->after('address');
                }
                if (!Schema::hasColumn('users', 'birthday_public')) {
                    $table->boolean('birthday_public')->default(false)->after('birthday');
                }
                if (!Schema::hasColumn('users', 'interests')) {
                    $table->text('interests')->nullable()->after('bio');
                }
            });
        }

        // 2. Tambah kolom content ke success_stories
        if (Schema::hasTable('success_stories') && !Schema::hasColumn('success_stories', 'content')) {
            Schema::table('success_stories', function (Blueprint $table) {
                $table->longText('content')->nullable()->after('quote');
            });
        }

        // 3. Buat tabel birthday_greetings
        if (!Schema::hasTable('birthday_greetings')) {
            Schema::create('birthday_greetings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
                $table->string('message')->nullable();
                $table->string('emoji', 10)->nullable();
                $table->timestamps();

                $table->index(['to_user_id', 'created_at']);
                $table->index(['from_user_id', 'to_user_id']);
            });
        }

        // 4. Buat tabel museum_items
        if (!Schema::hasTable('museum_items')) {
            Schema::create('museum_items', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('category')->default('lainnya');
                $table->string('image_url')->nullable();
                $table->string('video_url')->nullable();
                $table->string('era_year')->nullable();
                $table->string('donated_by')->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->unsignedInteger('views')->default(0);
                $table->unsignedInteger('likes')->default(0);
                $table->softDeletes();
                $table->timestamps();

                $table->index(['status', 'category']);
                $table->index('uploaded_by');
            });
        }

        // 5. Buat tabel museum_item_likes
        if (!Schema::hasTable('museum_item_likes')) {
            Schema::create('museum_item_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('museum_item_id')->constrained('museum_items')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['museum_item_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('museum_item_likes');
        Schema::dropIfExists('museum_items');
        Schema::dropIfExists('birthday_greetings');

        if (Schema::hasTable('success_stories') && Schema::hasColumn('success_stories', 'content')) {
            Schema::table('success_stories', function (Blueprint $table) {
                $table->dropColumn('content');
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('users', 'interests')) $cols[] = 'interests';
                if (Schema::hasColumn('users', 'birthday_public')) $cols[] = 'birthday_public';
                if (Schema::hasColumn('users', 'birthday')) $cols[] = 'birthday';
                if (!empty($cols)) $table->dropColumn($cols);
            });
        }
    }
};
