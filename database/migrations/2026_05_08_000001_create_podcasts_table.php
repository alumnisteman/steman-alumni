<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('podcasts')) {
            Schema::create('podcasts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('guest_name')->nullable();
                $table->text('description')->nullable();
                $table->string('audio_url'); // Can be local path or external link
                $table->string('thumbnail_url')->nullable();
                $table->string('duration')->nullable(); // e.g. "45:30"
                $table->enum('category', ['career', 'overseas', 'startup', 'other'])->default('career');
                $table->boolean('is_published')->default(true)->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
};
