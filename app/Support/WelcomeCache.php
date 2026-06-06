<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class WelcomeCache
{
    public static function forget(): void
    {
        Cache::forget('welcome_data');
        Cache::forget('welcome_data_static');
    }
}
