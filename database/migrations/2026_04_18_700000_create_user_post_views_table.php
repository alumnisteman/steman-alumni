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
        Schema::create('user_post_views', function (Blueprint $row) {
            $row->id();
            $row->foreignId('user_id')->constrained()->onDelete('cascade');
            $row->foreignId('post_id')->constrained()->onDelete('cascade');
            $row->integer('view_time')->comment('Duration in milliseconds');
            $row->integer('scroll_depth')->nullable()->comment('Scroll percentage');
            $row->timestamps();
            
            $row->index(['user_id', 'post_id']);
            $row->index('created_at');
        });

        // Agregation table for faster scoring
        Schema::create('user_post_view_summaries', function (Blueprint $row) {
            $row->id();
            $row->foreignId('user_id')->constrained()->onDelete('cascade');
            $row->foreignId('post_id')->constrained()->onDelete('cascade');
            $row->bigInteger('total_view_time')->default(0);
            $row->integer('views_count')->default(0);
            $row->timestamps();
            
            $row->unique(['user_id', 'post_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_post_view_summaries');
        Schema::dropIfExists('user_post_views');
    }
};
