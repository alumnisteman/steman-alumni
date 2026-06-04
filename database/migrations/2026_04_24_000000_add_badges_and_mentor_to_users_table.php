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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'badges')) {
                $table->json('badges')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'is_mentor')) {
                $table->boolean('is_mentor')->default(false)->after('badges');
            }
            if (!Schema::hasColumn('users', 'mentor_expertise')) {
                $table->string('mentor_expertise')->nullable()->after('is_mentor');
            }
            if (!Schema::hasColumn('users', 'mentor_bio')) {
                $table->text('mentor_bio')->nullable()->after('mentor_expertise');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'badges')) {
                $table->dropColumn('badges');
            }
            if (Schema::hasColumn('users', 'is_mentor')) {
                $table->dropColumn('is_mentor');
            }
            if (Schema::hasColumn('users', 'mentor_expertise')) {
                $table->dropColumn('mentor_expertise');
            }
            if (Schema::hasColumn('users', 'mentor_bio')) {
                $table->dropColumn('mentor_bio');
            }
        });
    }
};
