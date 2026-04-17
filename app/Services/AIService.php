<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AIService
{
    protected ?string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct(?string $apiKey = null)
    {
        // Prioritize: Passed key > Setting from Database > Fallback to .env config
        $this->apiKey = $apiKey ?: setting('gemini_api_key', config('services.gemini.api_key'));
    }

    /**
     * General method to prompt Gemini
     */
    public function ask(string $prompt, float $temperature = 0.7, string $model = 'gemini-1.5-flash-latest'): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('AIService: GEMINI_API_KEY is not configured.');
            return null;
        }

        try {
            // Versioning and Model Handling
            $url = "{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(12)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => $temperature,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                if (empty($text)) {
                    Log::warning('AIService: Gemini returned successful response but empty text.');
                }
                return $text;
            }

            // Enhanced Error Reporting
            $status = $response->status();
            $body = $response->body();

            if ($status === 404) {
                Log::error('AIService: Model not found (404). Check model name or API version.', ['model' => $model]);
            } elseif ($status === 503 || $status === 429) {
                Log::notice("AIService: API temporary unavailable ($status). Skipping.");
            } else {
                Log::error('AIService: Gemini API Error', [
                    'status' => $status,
                    'body' => $body
                ]);
            }

            return null;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::notice('AIService: Connection Timeout/Issue with Gemini API. Skipping.');
            return null;
        } catch (\Exception $e) {
            Log::error('AIService Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Specific method for content moderation
     */
    public function moderate(string $content): array
    {
        $prompt = "Identify if the following text contains spam, scam, hate speech, or inappropriate content for an alumni portal. Return JSON format only: {\"is_safe\": boolean, \"reason\": \"string\", \"score\": 0.0-1.0}.\n\nText: \"$content\"";
        
        $result = $this->ask($prompt, 0.1);
        
        if (!$result) {
            return ['is_safe' => true, 'reason' => 'AI unavailable', 'score' => 0];
        }

        // Strip markdown code blocks if AI returns them
        $json = preg_replace('/^```json\s*|\s*```$/i', '', trim($result));
        
        return json_decode($json, true) ?? ['is_safe' => true, 'reason' => 'Parse error', 'score' => 0];
    }

    /**
     * AI Recommendation: Match alumni based on interests/major
     */
    public function recommendAlumni(array $userProfile, array $candidates): array
    {
        $candidateData = collect($candidates)->map(fn($c) => "[ID: {$c['id']}, Name: {$c['name']}, Major: {$c['major']}, Job: {$c['current_job']}]")->join("\n");
        
        $prompt = "You are a professional networking assistant. Based on this user profile:
        Name: {$userProfile['name']}
        Major: {$userProfile['major']}
        Bio: {$userProfile['bio']}
        
        Recommend the top 3 best matching alumni from this list for professional networking:
        $candidateData
        
        Criteria: Same major, complementary skills, or interesting career paths.
        Return ONLY valid JSON: [{\"id\": integer, \"reason\": \"string\"}].
        Language: Indonesian.";

        $result = $this->ask($prompt, 0.4);

        if (!$result) return [];

        $json = preg_replace('/^```json\s*|\s*```$/i', '', trim($result));
        return json_decode($json, true) ?? [];
    }

    /**
     * AI Assistant: Conversational bot with portal knowledge
     */
    public function getAssistantResponse(string $userMessage, array $history = []): string
    {
        $schoolName = setting('school_name', 'SMKN 2 Ternate');
        $chairmanName = setting('chairman_name', 'Nama Ketua Umum');
        $siteName = setting('site_name', 'IKATAN ALUMNI SMKN 2');

        $systemPrompt = "You are 'Steman-AI', the official assistant for the $siteName portal. 
        Context:
        - Organization: Ikatan Alumni $schoolName.
        - Chairman: $chairmanName.
        - Purpose: Connecting alumni, sharing jobs, and nostalgia.
        - Features: 3D Network Map, Jobs Portal, Mentoring, and Digital Cards.
        
        Guidelines:
        1. Be friendly, professional, and helpful. 
        2. Use Indonesian language.
        3. Keep answers concise (max 3 sentences).
        4. If asked about technical issues, tell them to contact admin.
        5. Use emojis occasionally for a friendly feel. 🚀";

        $fullPrompt = "$systemPrompt\n\nUser: $userMessage\nSteman-AI:";

        return $this->ask($fullPrompt, 0.7) ?? "Maaf, saya sedang tidak bisa memproses permintaan Anda. Coba lagi nanti ya! 🙏";
    }
}
