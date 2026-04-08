<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\User;
use App\Models\Post;
use App\Services\AIService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AIGenerateNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:generate-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a weekly alumni highlight news article using Gemini AI';

    /**
     * Execute the console command.
     */
    public function handle(AIService $aiService)
    {
        $this->info('Gathering platform statistics for AI...');

        // Gather some context for the AI
        $alumniCount = User::where('role', 'alumni')->count();
        $recentPostsCount = Post::where('created_at', '>=', now()->subWeek())->count();
        $topAlumni = User::where('role', 'alumni')->orderBy('points', 'desc')->limit(3)->pluck('name')->toArray();
        
        $prompt = "Create a professional and engaging news article for a school alumni portal based on this week's activities:
        - Total Alumni: $alumniCount
        - New Nostalgia Posts this week: $recentPostsCount
        - Top active alumni of the week: " . implode(', ', $topAlumni) . "
        
        The article should celebrate the community, highlight the nostalgia shared, and mention the top contributors.
        Format: Return ONLY a JSON object with: {\"title\": \"string\", \"content\": \"html_string\"}.
        Use Indonesian language.";

        $this->info('Requesting AI to draft news...');
        $result = $aiService->ask($prompt, 0.8);

        if (!$result) {
            $this->error('AI failed to generate content.');
            return;
        }

        $json = preg_replace('/^```json\s*|\s*```$/i', '', trim($result));
        $data = json_decode($json, true);

        if (!$data || !isset($data['title']) || !isset($data['content'])) {
            $this->error('AI returned invalid JSON: ' . $result);
            return;
        }

        // Create the News as a DRAFT
        News::create([
            'user_id' => 1, // Assuming admin ID is 1
            'title' => $data['title'],
            'slug' => Str::slug($data['title']) . '-' . now()->format('YmdHi'),
            'content' => $data['content'],
            'status' => 'draft',
            'category' => 'AI Generated',
        ]);

        $this->info('AI News Draft created successfully: ' . $data['title']);
    }
}
