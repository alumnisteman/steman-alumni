<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        try {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'latitude')) {
                    $table->decimal('latitude', 10, 8)->nullable();
                }
                if (!Schema::hasColumn('users', 'longitude')) {
                    $table->decimal('longitude', 11, 8)->nullable();
                }
                if (!Schema::hasColumn('users', 'city_name')) {
                    $table->string('city_name')->nullable();
                }
                if (!Schema::hasColumn('users', 'country_name')) {
                    $table->string('country_name')->nullable()->default('Indonesia');
                }
            });
        } catch (\Exception $e) {}
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'city_name', 'country_name']);
        });
    }
};
