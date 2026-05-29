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
        if (!Schema::hasTable('content_moderation_settings')) {
            Schema::create('content_moderation_settings', function (Blueprint $table) {
                $table->id();
                $table->string('category')->default('profanity'); // profanity, sara, local
                $table->string('word')->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index('category');
                $table->index('is_active');
            });
        }

        // Insert default blacklist words
        if (!Schema::hasTable('content_moderation_words')) {
            Schema::create('content_moderation_words', function (Blueprint $table) {
                $table->id();
                $table->string('word')->unique();
                $table->string('category')->default('profanity');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index('category');
                $table->index('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_moderation_settings');
        Schema::dropIfExists('content_moderation_words');
    }
};
