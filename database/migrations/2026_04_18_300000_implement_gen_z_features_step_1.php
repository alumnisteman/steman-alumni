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
        // 1. Stories Table (24h validity)
        if (!Schema::hasTable('stories')) {
            Schema::create('stories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('image_url');
                $table->string('caption')->nullable();
                $table->integer('views_count')->default(0);
                $table->timestamp('expires_at')->index();
                $table->timestamps();
            });
        }

        // 2. Enhance Posts & Users for Gen Z Features
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'is_anonymous')) {
                $table->boolean('is_anonymous')->default(false)->after('visibility')->index();
            }
            // Add 'help' to enum if possible, or just allow it via logic
            // Note: DB level enum modification can be tricky, we'll handle it via logic or add a new column if needed
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'career_path')) {
                $table->json('career_path')->nullable()->after('bio'); // For Career Explorer Flowchart
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('career_path');
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('is_anonymous');
        });
        Schema::dropIfExists('stories');
    }
};
