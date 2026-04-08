<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ModerateContentWithAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function handle(AIService $aiService): void
    {
        Log::info("AI Moderating Post #{$this->post->id}...");

        $evaluation = $aiService->moderate($this->post->content);

        if (!$evaluation['is_safe']) {
            Log::warning("AI Flagged Post #{$this->post->id} for '{$evaluation['reason']}' with score {$evaluation['score']}");
            
            // Mark as hidden/unapproved so it doesn't appear for users
            // Or delete if it's high confidence scam
            if ($evaluation['score'] > 0.9) {
                $this->post->delete();
                Log::error("Post #{$this->post->id} deleted automatically by AI (High Confidence Scam/Spam).");
            } else {
                // Here we assume a 'status' or 'is_approved' column might be needed or just hide it
                // For now, let's just log and maybe soft delete or flag it.
                // Assuming we have a status or soft delete.
                $this->post->delete(); 
            }
        }
    }
}
