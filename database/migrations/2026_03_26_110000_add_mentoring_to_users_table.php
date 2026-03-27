<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_mentor')->default(false)->after('bio');
            $table->text('mentor_bio')->nullable()->after('is_mentor');
            $table->string('mentor_expertise')->nullable()->after('mentor_bio');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_mentor', 'mentor_bio', 'mentor_expertise']);
        });
    }
};
