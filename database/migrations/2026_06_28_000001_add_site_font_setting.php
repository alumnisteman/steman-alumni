<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'site_font'],
            [
                'key'   => 'site_font',
                'value' => 'Inter',
                'label' => 'Font Situs',
                'group' => 'general',
            ]
        );
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'site_font')->delete();
    }
};
