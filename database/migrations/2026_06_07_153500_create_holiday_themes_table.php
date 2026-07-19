<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('holiday_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // misal: kemerdekaan, hut_steman, lebaran, natal, hajat, default
            $table->date('start_date');            // tanggal mulai (inclusive)
            $table->date('end_date');              // tanggal selesai (inclusive)
            $table->string('primary_color')->nullable();   // optional CSS variable
            $table->string('secondary_color')->nullable(); // optional CSS variable
            $table->string('banner')->nullable();          // path ke banner image
            $table->integer('priority')->default(0);        // besar prioritas bila tumpang‑tindih
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holiday_themes');
    }
};
?>
