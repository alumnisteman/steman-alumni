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
            $table->integer('desktop_offset_x')->default(50)->after('image_desktop');
            $table->integer('desktop_offset_y')->default(50)->after('desktop_offset_x');
            $table->float('desktop_zoom')->default(1.0)->after('desktop_offset_y');
            
            $table->integer('mobile_offset_x')->default(50)->after('image_mobile');
            $table->integer('mobile_offset_y')->default(50)->after('mobile_offset_x');
            $table->float('mobile_zoom')->default(1.0)->after('mobile_offset_y');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn([
                'desktop_offset_x', 'desktop_offset_y', 'desktop_zoom',
                'mobile_offset_x', 'mobile_offset_y', 'mobile_zoom'
            ]);
        });
    }
};
