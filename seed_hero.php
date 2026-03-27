<?php
require '/var/www/steman-alumni/vendor/autoload.php';
$app = require_once '/var/www/steman-alumni/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

Setting::updateOrCreate(['key' => 'hero_title'], ['value' => "PENGURUS PUSAT\nIKATAN ALUMNI SMKN 2", 'label' => 'Hero Title', 'group' => 'hero']);
Setting::updateOrCreate(['key' => 'hero_subtitle'], ['value' => 'MENJALIN JEJARING, MEMBANGUN KONTRIBUSI.', 'label' => 'Hero Subtitle', 'group' => 'hero']);
Setting::updateOrCreate(['key' => 'hero_background'], ['value' => '/images/hero_iluni.png', 'label' => 'Hero Background Image', 'group' => 'hero']);

echo "Hero settings seeded successfully.\n";
