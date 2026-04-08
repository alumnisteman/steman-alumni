<?php

namespace App\Jobs;

use App\Models\ContactMessage;
use App\Services\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAIAutoReply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ContactMessage $message;

    public function __construct(ContactMessage $message)
    {
        $this->message = $message;
    }

    public function handle(AIService $aiService): void
    {
        Log::info("AI Generating suggested reply for Message #{$this->message->id}...");

        $prompt = "You are the AI Assistant for the STEMAN Alumni Portal. A user sent the following message:
        Subject: {$this->message->subject}
        From: {$this->message->name}
        Content: \"{$this->message->message}\"
        
        Please draft a polite, helpful, and professional response in Indonesian. 
        Focus on acknowledging their concern and stating that the admin team will review it soon.
        Return ONLY the text of the reply.";

        $suggestion = $aiService->ask($prompt, 0.7);

        if ($suggestion) {
            $this->message->update([
                'ai_suggested_reply' => $suggestion,
                'is_ai_processed' => true
            ]);
            Log::info("AI Suggestion updated for Message #{$this->message->id}");
        }
    }
}
