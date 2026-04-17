<?php

namespace App\Http\ViewComposers;

use App\Models\Ad;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class AdViewComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $ads = Cache::remember('active_ads', 3600, function () {
            return Ad::active()->get()->groupBy(function($item) {
                return strtolower(trim($item->position));
            });
        });

        $view->with('global_ads', $ads);
    }
}
