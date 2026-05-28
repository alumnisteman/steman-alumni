<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================
        // 1. DIGITAL MUSEUM — Arsip Sejarah Sekolah
        // =====================================================
        if (!Schema::hasTable('museum_items')) {
            Schema::create('museum_items', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('category', [
                    'foto_sekolah',   // Foto sekolah tempo dulu
                    'ijazah',         // Ijazah & dokumen lama
                    'peralatan',      // Mesin bengkel & peralatan
                    'seragam',        // Seragam & atribut sekolah
                    'guru_legendaris',// Guru legendaris
                    'prestasi',       // Piala & prestasi
                    'lainnya',        // Lainnya
                ])->default('lainnya')->index();
                $table->string('image_url')->nullable();    // Foto/gambar item
                $table->string('video_url')->nullable();    // YouTube embed (hemat storage)
                $table->year('era_year')->nullable();       // Tahun era item ini
                $table->string('donated_by')->nullable();   // Disumbang oleh alumni/pihak
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
                $table->unsignedInteger('views')->default(0);
                $table->unsignedInteger('likes')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['category', 'status']);
                $table->index(['era_year', 'status']);
            });
        }

        // Museum Item Likes (prevent double like)
        if (!Schema::hasTable('museum_item_likes')) {
            Schema::create('museum_item_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('museum_item_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['museum_item_id', 'user_id']);
            });
        }

        // =====================================================
        // 2. VOTING & POLLING
        // =====================================================
        // Ensure polls table is fresh
        Schema::dropIfExists('polls');
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('options');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
                $table->id();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->string('question');
                $table->text('description')->nullable();
                $table->string('emoji')->default('🗳️');
                $table->enum('type', ['single', 'multiple'])->default('single'); // single/multiple choice
                $table->enum('status', ['active', 'closed', 'draft'])->default('active')->index();
                $table->boolean('is_anonymous')->default(false);
                $table->boolean('show_results_before_vote')->default(false);
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('poll_options')) {
            Schema::create('poll_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
                $table->string('option_text');
                $table->string('option_emoji')->nullable();
                $table->unsignedInteger('votes_count')->default(0);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['poll_id', 'sort_order']);
            });
        }

        if (!Schema::hasTable('poll_votes')) {
            Schema::create('poll_votes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
                $table->foreignId('poll_option_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                // Prevent double vote on same poll option
                $table->unique(['poll_id', 'poll_option_id', 'user_id'], 'unique_vote_per_option');
            });
        }

        // =====================================================
        // 3. ALUMNI BIRTHDAY — Ultah alumni
        // =====================================================
        // Add birthday column to users if not exists
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'birthday')) {
            Schema::table('users', function (Blueprint $table) {
                $table->date('birthday')->nullable()->after('bio');
                $table->boolean('birthday_public')->default(true)->after('birthday');
            });
        }

        // Birthday greetings sent log
        if (!Schema::hasTable('birthday_greetings')) {
            Schema::create('birthday_greetings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('to_user_id')->constrained('users')->cascadeOnDelete();
                $table->text('message')->nullable();
                $table->string('emoji')->default('🎂');
                $table->timestamps();

                $table->index(['to_user_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_greetings');
        Schema::dropIfExists('poll_votes');
        Schema::dropIfExists('poll_options');
        Schema::dropIfExists('polls');
        Schema::dropIfExists('museum_item_likes');
        Schema::dropIfExists('museum_items');

        if (Schema::hasColumn('users', 'birthday')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['birthday', 'birthday_public']);
            });
        }
    }
};
