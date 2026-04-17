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
        Schema::table('ads', function (Blueprint $table) {
            $table->renameColumn('image', 'image_desktop');
            $table->renameColumn('mobile_image', 'image_mobile');
            $table->renameColumn('clicks', 'click');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->renameColumn('image_desktop', 'image');
            $table->renameColumn('image_mobile', 'mobile_image');
            $table->renameColumn('click', 'clicks');
        });
    }
};
