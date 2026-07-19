<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('success_stories', function (Blueprint $table) {
            $table->string('category')->default('karir-profesi')->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('success_stories', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
