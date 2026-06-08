<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->tinyInteger('start_month');
            $table->tinyInteger('start_day');
            $table->tinyInteger('end_month');
            $table->tinyInteger('end_day');
            $table->string('primary_color', 20)->default('#007bff');
            $table->string('secondary_color', 20)->default('#6c757d');
            $table->string('accent_color', 20)->default('#ffc107');
            $table->string('css_class', 60);
            $table->string('banner_text')->nullable();
            $table->string('banner_subtext')->nullable();
            $table->string('banner_icon', 80)->nullable();
            $table->string('emoji', 20)->nullable();
            $table->boolean('show_countdown')->default(false);
            $table->string('countdown_label')->nullable();
            $table->tinyInteger('countdown_month')->nullable();
            $table->tinyInteger('countdown_day')->nullable();
            $table->integer('priority')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_themes');
    }
};
