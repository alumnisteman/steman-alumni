<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->enum('status', ['active', 'closed', 'draft'])->default('active')->after('type');
        });

        // Migrate existing data: is_active=1 -> active, is_active=0 -> closed
        DB::statement("UPDATE polls SET status = CASE WHEN is_active = 1 THEN 'active' ELSE 'closed' END");
    }

    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
