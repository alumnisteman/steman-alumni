<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Clear previously-encrypted ai_recommendation, bmi_category, and activity_level columns.
     * These fields are now stored as plain text. Old encrypted values would cause
     * DecryptException when read, so we wipe them to force fresh saves.
     */
    public function up(): void
    {
        DB::table('health_profiles')->update([
            'ai_recommendation' => null,
            'bmi_category'      => null,
            'activity_level'    => null,
        ]);
    }

    public function down(): void
    {
        // Not reversible — data was unreadable anyway
    }
};
