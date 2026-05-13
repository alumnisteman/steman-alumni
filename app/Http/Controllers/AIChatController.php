<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIChatController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Handle AI Assistant Chat Request
     */
    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        try {
            // Check Rate Limiting (10 requests per minute per user)
            $userId = auth()->id() ?? $request->ip();
            $key = "ai_chat_limit_" . $userId;
            
            if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 10)) {
                return response()->json([
                    'reply' => "Sangat senang membantu Anda, tapi tunggu sebentar ya! Coba tanyakan lagi dalam semenit. 🚀"
                ]);
            }
            \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

            $reply = $this->aiService->getAssistantResponse($request->message);

            return response()->json(['reply' => $reply]);

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'reply' => "Maaf, robot kami sedang sibuk nih. Silakan mampir sebentar lagi ya! 🤖"
            ], 500);
        }
    }
    /**
     * Handle AI Content Generation for Admins
     */
    public function generateContent(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:news,job',
            'input' => 'required|string|min:10',
        ]);

        if (!auth()->user() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $typeLabel = $request->type === 'news' ? 'menulis berita' : 'menulis lowongan kerja';
            $content = $this->aiService->generateContent($typeLabel, $request->input);

            return response()->json(['content' => $content]);
        } catch (\Exception $e) {
            Log::error('AI Content Generation Error: ' . $e->getMessage());
            return response()->json(['error' => 'AI sedang istirahat sejenak.'], 500);
        }
    }
}
