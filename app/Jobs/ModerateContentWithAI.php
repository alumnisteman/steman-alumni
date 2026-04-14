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

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function handle(AIService $aiService): void
    {
        $content = $this->model->content ?? $this->model->message ?? $this->model->title;
        
        if (empty($content)) return;

        Log::info("AI Moderating " . class_basename($this->model) . " #{$this->model->id}...");

        $evaluation = $aiService->moderate($content);

        if (!$evaluation['is_safe']) {
            Log::warning("AI Flagged " . class_basename($this->model) . " #{$this->model->id} for '{$evaluation['reason']}' with score {$evaluation['score']}");
            
            // Delete if it's high confidence violation
            if ($evaluation['score'] > 0.8) {
                // Send official warning notification first
                if ($this->model->user) {
                    $this->model->user->notify(new \App\Notifications\ContentViolationNotification($this->model, $evaluation['reason']));
                }
                
                $this->model->delete();
                Log::error(class_basename($this->model) . " #{$this->model->id} deleted automatically by AI.");
            }
        }
    }
}
