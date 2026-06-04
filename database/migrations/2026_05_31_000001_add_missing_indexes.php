<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingIndexes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('is_active');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->index('last_activity');
        });

        Schema::table('program_registrations', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['last_activity']);
        });

        Schema::table('program_registrations', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
}

