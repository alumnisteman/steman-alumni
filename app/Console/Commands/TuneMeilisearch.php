<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Meilisearch\Client;

class TuneMeilisearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tune-meilisearch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tune Meilisearch settings for optimal performance and relevance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Tuning Meilisearch settings...');

        $host = config('scout.meilisearch.host', 'http://localhost:7700');
        $key = config('scout.meilisearch.key', '');

        $client = new Client($host, $key);

        try {
            // Tune 'posts' index
            $this->info('Configuring "posts" index...');
            $client->index('posts')->updateSettings([
                'searchableAttributes' => ['caption', 'content'],
                'filterableAttributes' => ['user_id', 'created_at', 'type'],
                'sortableAttributes' => ['created_at'],
                'rankingRules' => [
                    'words',
                    'typo',
                    'proximity',
                    'attribute',
                    'sort',
                    'exactness',
                ]
            ]);

            // Tune 'users' index (assuming it's named 'users' by Scout)
            $this->info('Configuring "users" index...');
            $client->index('users')->updateSettings([
                'searchableAttributes' => ['name', 'major', 'current_job', 'company_university', 'location'],
                'filterableAttributes' => ['graduation_year', 'role', 'status'],
                'sortableAttributes' => ['graduation_year'],
                'rankingRules' => [
                    'words',
                    'typo',
                    'proximity',
                    'attribute',
                    'sort',
                    'exactness',
                ]
            ]);

            $this->info('Meilisearch tuning completed successfully!');

        } catch (\Exception $e) {
            $this->error('Failed to tune Meilisearch: ' . $e->getMessage());
        }
    }
}
