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
        Schema::table('contact_messages', function (Blueprint $blueprint) {
            $blueprint->text('ai_suggested_reply')->nullable()->after('message');
            $blueprint->boolean('is_ai_processed')->default(false)->after('ai_suggested_reply');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['ai_suggested_reply', 'is_ai_processed']);
        });
    }
};
