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
        Schema::create('success_stories', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name');
            $blueprint->string('title'); // e.g., "CEO & Founder"
            $blueprint->string('major_year'); // e.g., "TKJ '12"
            $blueprint->text('quote');
            $blueprint->string('image_path')->nullable();
            $blueprint->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Link to alumni if exists
            $blueprint->boolean('is_published')->default(true);
            $blueprint->integer('order')->default(0);
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('success_stories');
    }
};
