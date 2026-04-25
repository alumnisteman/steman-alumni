<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class NewsAggregator
{
    public function get()
    {
        return Cache::remember('multi_news_v2', 600, function () {

            $trending = app(TrendingService::class)->getTrendingKeywords();

            $apiNews = app(NewsApiService::class)->fetch($trending);
            $rssNews = app(RssService::class)->fetch();

            $merged = $apiNews->merge($rssNews);

            // Dedup by title
            $unique = $merged->unique('title');

            // Scoring
            $scored = $unique->map(function ($n) {
                try {
                    $time = Carbon::parse($n['published_at']);
                    $fresh = now()->diffInHours($time) < 6 ? 5 : 1;
                } catch (\Exception $e) {
                    $fresh = 1;
                }

                $n['score'] = $fresh + rand(1, 3);
                
                // Keyword filter example (Skip irrelevant news)
                // if (str_contains(strtolower($n['title']), 'bola')) {
                //     $n['score'] -= 10;
                // }

                return $n;
            });

            return $scored->sortByDesc('score')->values()->take(15); // Return top 15
        });
    }
}
