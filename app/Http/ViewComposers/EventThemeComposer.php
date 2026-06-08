<?php

namespace App\Http\ViewComposers;

use App\Models\EventTheme;
use Illuminate\View\View;

class EventThemeComposer
{
    public function compose(View $view): void
    {
        try {
            $activeTheme = EventTheme::getActive();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('EventThemeComposer error: ' . $e->getMessage());
            $activeTheme = null;
        }

        $view->with('activeEventTheme', $activeTheme);
    }
}
