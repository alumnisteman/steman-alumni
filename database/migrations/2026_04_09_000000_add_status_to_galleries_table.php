<?php
/*
 * Created At: 2026-04-09
 * Purpose: Fix missing status column in galleries table that caused 500 error.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('galleries')) {
            if (!Schema::hasColumn('galleries', 'status')) {
                Schema::table('galleries', function (Blueprint $table) {
                    $table->enum('status', ['draft', 'published'])->default('published')->index()->after('description');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('galleries')) {
            if (Schema::hasColumn('galleries', 'status')) {
                Schema::table('galleries', function (Blueprint $table) {
                    $table->dropColumn('status');
                });
            }
        }
    }
};
