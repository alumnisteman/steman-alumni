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
        Schema::create('story_views', function (Blueprint $row) {
            $row->id();
            $row->foreignId('story_id')->constrained()->onDelete('cascade');
            $row->foreignId('viewer_id')->constrained('users')->onDelete('cascade');
            $row->timestamp('viewed_at')->useCurrent();
            $row->timestamps();

            $row->unique(['story_id', 'viewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_views');
    }
};
