<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_themes', function (Blueprint $table) {
            $table->boolean('is_islamic')->default(false)->after('is_active')
                  ->comment('Tanggal mengikuti kalender Hijriah, bergeser tiap tahun');
            $table->unsignedTinyInteger('hijri_month')->nullable()->after('is_islamic')
                  ->comment('Bulan Hijriah acuan (1=Muharram ... 12=Dzulhijjah)');
            $table->unsignedTinyInteger('hijri_day')->nullable()->after('hijri_month')
                  ->comment('Tanggal Hijriah acuan (awal periode)');
            $table->unsignedTinyInteger('hijri_duration')->nullable()->after('hijri_day')
                  ->comment('Durasi tampil dalam hari (otomatis hitung end_month/end_day)');
        });
    }

    public function down(): void
    {
        Schema::table('event_themes', function (Blueprint $table) {
            $table->dropColumn(['is_islamic', 'hijri_month', 'hijri_day', 'hijri_duration']);
        });
    }
};
