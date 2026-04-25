<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsApiService
{
    public function fetch($keywords = [])
    {
        $q = empty($keywords) ? 'indonesia' : implode(' OR ', $keywords);

        $res = Http::get('https://newsapi.org/v2/everything', [
            'q' => $q,
            'language' => 'id',
            'pageSize' => 10,
            'apiKey' => env('NEWS_API_KEY')
        ]);

        if (!$res->successful()) {
            return collect([]);
        }

        return collect($res->json()['articles'] ?? [])
            ->map(fn($n) => [
                'type' => 'news',
                'title' => $n['title'] ?? 'No Title',
                'desc' => $n['description'] ?? '',
                'image' => $n['urlToImage'] ?? null,
                'url' => $n['url'] ?? '#',
                'source' => $n['source']['name'] ?? 'NewsAPI',
                'published_at' => $n['publishedAt'] ?? now()->toIso8601String()
            ]);
    }
}
