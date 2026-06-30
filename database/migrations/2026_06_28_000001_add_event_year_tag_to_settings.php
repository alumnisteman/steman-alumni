<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    public function up(): void
    {
        $newSettings = [
            [
                'key'   => 'event_year',
                'value' => '2029',
                'label' => 'Tahun Acara / Event (Dinamis)',
                'group' => 'event_chairman',
            ],
            [
                'key'   => 'event_tag',
                'value' => 'REUNI AKBAR 2029',
                'label' => 'Tag Event (Tampil di Hero & Badge)',
                'group' => 'event_chairman',
            ],
        ];

        foreach ($newSettings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }

    public function down(): void
    {
        Setting::whereIn('key', ['event_year', 'event_tag'])->delete();
    }
};
