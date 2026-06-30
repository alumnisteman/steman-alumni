<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('geocode_attempted_at')->nullable()->after('longitude')
                ->comment('Waktu terakhir geocoding dicoba — null berarti belum pernah dicoba');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('geocode_attempted_at');
        });
    }
};
