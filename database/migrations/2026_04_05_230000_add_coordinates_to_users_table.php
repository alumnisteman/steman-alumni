<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            // Coordinate for mapping (Default to Ternate Hub for initial testing if needed)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('city_name')->nullable();
            $table->string('country_name')->nullable()->default('Indonesia');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'city_name', 'country_name']);
        });
    }
};
