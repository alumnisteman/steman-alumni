<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RssService
{
    public function fetch()
    {
        $sources = [
            'detik' => 'https://rss.detik.com/index.php/detikcom',
            'kompas' => 'https://sindikasi.kompas.com/xml/terkini',
            'sindonews' => 'https://sindonews.com/rss',
            // X (Twitter) and Threads heavily restrict access. Using RSSHub as a bridge (may be rate-limited occasionally).
            'x_twitter' => 'https://rsshub.app/twitter/trend/23424846?limit=5', // 23424846 is Indonesia WOEID
            'threads' => 'https://rsshub.app/threads/zuck' // Threads doesn't have open trend API, fetching a default profile as example or fallback
        ];

        $items = collect();

        foreach ($sources as $name => $url) {
            try {
                // Use Http client with User-Agent to prevent 403 Forbidden
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ])->timeout(5)->get($url);

                if (!$response->successful()) continue;
                
                $rss = @simplexml_load_string($response->body());
                if (!$rss || !isset($rss->channel->item)) continue;

                foreach ($rss->channel->item as $i) {
                    $sourceName = match($name) {
                        'detik' => 'Detik',
                        'kompas' => 'Kompas',
                        'sindonews' => 'SindoNews',
                        'x_twitter' => 'X (Twitter)',
                        'threads' => 'Threads',
                        default => 'RSS'
                    };

                    $items->push([
                        'type' => 'news',
                        'title' => (string) $i->title,
                        'desc' => strip_tags((string) $i->description),
                        'image' => null,
                        'url' => (string) $i->link,
                        'source' => $sourceName,
                        'published_at' => (string) $i->pubDate
                    ]);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $items->take(15);
    }
}
