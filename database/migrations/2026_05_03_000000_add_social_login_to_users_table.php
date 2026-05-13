<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'social_id')) {
                $table->string('social_id')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'social_type')) {
                $table->string('social_type')->nullable()->after('social_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['social_id', 'social_type']);
        });
    }
};
