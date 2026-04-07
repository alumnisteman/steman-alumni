<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mentor_expertise')->nullable()->after('mentoring');
            $table->text('mentor_bio')->nullable()->after('mentor_expertise');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mentor_expertise', 'mentor_bio']);
        });
    }
};
