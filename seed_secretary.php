<?php

namespace {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    use App\Models\Setting;

    $data = [
        ['key' => 'secretary_name', 'label' => 'Nama Sekretaris Panitia', 'group' => 'secretary'],
        ['key' => 'secretary_period', 'label' => 'Periode Sekretaris', 'group' => 'secretary'],
        ['key' => 'secretary_message', 'label' => 'Sambutan Sekretaris', 'group' => 'secretary'],
        ['key' => 'secretary_photo', 'label' => 'Foto Sekretaris', 'group' => 'secretary'],
    ];

    foreach ($data as $item) {
        $setting = Setting::firstOrCreate(['key' => $item['key']], [
            'value' => '',
            'label' => $item['label'],
            'group' => $item['group']
        ]);
        
        // If it existed but was in wrong group, update it
        if ($setting->group !== $item['group']) {
            $setting->update(['group' => $item['group'], 'label' => $item['label']]);
        }
        
        echo "Ensured: {$item['key']}\n";
    }

    echo "Seeding completed successfully.\n";
}
