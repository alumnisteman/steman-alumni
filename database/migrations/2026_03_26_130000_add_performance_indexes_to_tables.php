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
        try {
            Schema::table('news', function (Blueprint $table) {
                $table->index('category');
                $table->index('is_published');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('programs', function (Blueprint $table) {
                $table->index('status');
                $table->index('slug');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('galleries', function (Blueprint $table) {
                $table->index('type');
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['is_published']);
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['slug']);
        });

        Schema::table('galleries', function (Blueprint $table) {
            $table->dropIndex(['type']);
        });
    }
};
