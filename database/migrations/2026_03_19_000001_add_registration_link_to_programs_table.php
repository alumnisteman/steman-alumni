<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom registration_link ke tabel programs
     * jika belum ada (safe untuk fresh install maupun upgrade).
     */
    public function up(): void
    {
        if (!Schema::hasColumn('programs', 'registration_link')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->string('registration_link')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('programs', 'registration_link')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropColumn('registration_link');
            });
        }
    }
};
