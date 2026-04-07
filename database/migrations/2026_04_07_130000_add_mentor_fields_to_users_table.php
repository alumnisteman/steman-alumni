<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'mentoring')) {
                $table->boolean('mentoring')->default(false);
            }
            if (!Schema::hasColumn('users', 'mentor_expertise')) {
                $table->string('mentor_expertise')->nullable();
            }
            if (!Schema::hasColumn('users', 'mentor_bio')) {
                $table->text('mentor_bio')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop only if they exist to prevent errors going down
            if (Schema::hasColumn('users', 'mentor_expertise')) {
                $table->dropColumn('mentor_expertise');
            }
            if (Schema::hasColumn('users', 'mentor_bio')) {
                $table->dropColumn('mentor_bio');
            }
        });
    }
};
