<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TrendingService
{
    public function getTrendingKeywords()
    {
        return Cache::remember('trending_keywords', 600, function () {
            $keywords = [];

            // 1. Fetch from NewsAPI
            $newsResponse = Http::get('https://newsapi.org/v2/top-headlines', [
                'country' => 'id',
                'pageSize' => 20,
                'apiKey' => env('NEWS_API_KEY')
            ]);

            if (!$newsResponse->successful()) {
                return ['teknologi', 'indonesia', 'pendidikan']; // Fallback
            }

            $news = $newsResponse->json();

            if (empty($news['articles'])) {
                return ['teknologi', 'indonesia', 'pendidikan']; // Fallback
            }

            foreach ($news['articles'] as $n) {
                if (empty($n['title'])) continue;
                $words = $this->extractKeywords($n['title']);
                foreach ($words as $w) {
                    $keywords[$w]['freq'] = ($keywords[$w]['freq'] ?? 0) + 1;
                    $keywords[$w]['fresh'] = time();
                    $keywords[$w]['source'] = 1;
                }
            }

            // 2. Scoring
            foreach ($keywords as $k => $v) {
                $freq = $v['freq'];
                $freshness = 1; 
                $source = $v['source'];

                $keywords[$k]['score'] = ($freq * 0.5) + ($freshness * 0.3) + ($source * 0.2);
            }

            // 3. Sort & take top
            uasort($keywords, fn($a, $b) => $b['score'] <=> $a['score']);
            
            $topKeys = array_slice(array_keys($keywords), 0, 10);
            return empty($topKeys) ? ['teknologi'] : $topKeys;
        });
    }

    private function extractKeywords($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9 ]/', '', $text);
        $words = explode(' ', $text);

        // Common Indonesian stopwords
        $stopwords = ['dan','yang','di','ke','dari','untuk','dengan','ini','itu','dalam','pada','juga','akan','oleh','tidak','ada'];

        $filtered = array_diff($words, $stopwords);
        
        // Remove short words
        return array_filter($filtered, function($word) {
            return strlen($word) > 3;
        });
    }
}
