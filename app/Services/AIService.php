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
     * General method to prompt Gemini with Auto-Switching capabilities
     */
    public function ask(string $prompt, float $temperature = 0.7, ?string $model = null): ?string
    {
        $models = $model ? [$model] : [
            'gemini-2.0-flash',          // Current default (fast + capable)
            'gemini-1.5-flash',          // Fallback: 1.5 Flash
            'gemini-1.5-flash-latest',   // Fallback: latest 1.5
        ];

        foreach ($models as $currentModel) {
            // Attempt with Retry Logic (Max 2 attempts per model)
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                $result = $this->tryRequest($prompt, $temperature, $currentModel);
                if ($result) return $result;
                
                // If failed, wait a bit before retry
                if ($attempt < 2) usleep(500000); // 0.5s
            }
        }

        return null;
    }

    /**
     * Strategic data analysis for admins
     */
    public function analyzeStats(array $stats): ?string
    {
        $prompt = "You are a senior data analyst for an alumni network portal. 
        Analyze the following statistics and provide 3-4 bullet points of strategic insights or recommendations for the admin. 
        Use a professional, encouraging, and slightly futuristic tone. Output in Indonesian.
        
        STATISTICS:
        - Total Alumni: {$stats['totalAlumni']}
        - Employment Rate: {$stats['employmentRate']}%
        - International Alumni: {$stats['internationalCount']}
        - Total Programs: {$stats['totalPrograms']}
        - Total Jobs: {$stats['totalJobs']}
        
        Keep it concise but impactful.";

        return $this->ask($prompt, 0.8, 'gemini-3.1-flash-lite-preview');
    }

    /**
     * Internal request handler with dynamic versioning
     */
    private function tryRequest(string $prompt, float $temperature, string $model): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('AIService: GEMINI_API_KEY is not configured.');
            return null;
        }

        try {
            // Use v1 for maximum compatibility with all API keys
            $apiVersion = 'v1'; 
            $url = "https://generativelanguage.googleapis.com/{$apiVersion}/models/{$model}:generateContent?key={$this->apiKey}";

            Log::warning("AIService Calling Gemini: $apiVersion/$model");





            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($url, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature' => $temperature,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                return !empty($text) ? $text : null;
            }

            $status = $response->status();
            $body = $response->body();

            // Only log errors that aren't expected "switches" (like 404 or 429) if we have more models to try
            if ($status === 404) {
                Log::warning("AIService: Model [$model] not found (404). Trying next...");
            } elseif ($status === 429) {
                Log::warning("AIService: Quota exceeded for [$model] (429). Trying next...");
            } else {
                Log::error("AIService: Gemini API Error ($status) for [$model]", ['body' => $body]);
            }

            return null;

        } catch (\Exception $e) {
            Log::error("AIService Exception for [$model]: " . $e->getMessage());
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
    /**
     * AI Career Mentor: Personalized matchmaking
     */
    public function matchMentor(array $userProfile, array $candidates): array
    {
        $candidateData = collect($candidates)->map(fn($c) => "[ID: {$c['id']}, Name: {$c['name']}, Major: {$c['major']}, Job: {$c['current_job']}]")->join("\n");
        
        $prompt = "You are an expert career counselor. Based on this user's profile:
        Name: {$userProfile['name']}
        Current Major: {$userProfile['major']}
        Career Goal: {$userProfile['goal']}
        
        Find matching mentors from this list:
        $candidateData
        
        Provide 2 potential mentors and explain why in 1 short sentence Indonesian for each.
        Return ONLY valid JSON: [{\"id\": integer, \"reason\": \"string\"}].";

        $result = $this->ask($prompt, 0.5);
        if (!$result) return [];

        $json = preg_replace('/^```json\s*|\s*```$/i', '', trim($result));
        return json_decode($json, true) ?? [];
    }

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

    /**
     * AI Geocoding: Convert address to lat/lng
     */
    public function geocode(string $address): ?array
    {
        $prompt = "Convert the following Indonesian address into geographic coordinates (latitude and longitude). 
        Address: \"$address\"
        
        Return ONLY valid JSON format: {\"lat\": float, \"lng\": float}. 
        If address is invalid or not found, return {\"lat\": null, \"lng\": null}.";

        $result = $this->ask($prompt, 0.1);
        if ($result) {
            Log::info("AIService Geocode Raw Result (Gemini): " . $result);
            $json = preg_replace('/^```json\s*|\s*```$/i', '', trim($result));
            $data = json_decode($json, true);
            if (isset($data['lat']) && isset($data['lng'])) {
                return $data;
            }
        }

        // Fallback: OpenStreetMap Nominatim
        Log::info("AIService Geocode: Gemini failed, attempting Nominatim fallback for: $address");
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'StemanAlumniPortal/1.0'
            ])->timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                Log::info("AIService Geocode Success (Nominatim): " . $data['lat'] . ", " . $data['lon']);
                return [
                    'lat' => (float) $data['lat'],
                    'lng' => (float) $data['lon']
                ];
            }
        } catch (\Exception $e) {
            Log::warning("AIService Geocode: Nominatim fallback failed: " . $e->getMessage());
        }

        return null;
    }

    /**
     * AI Profile Optimizer: Suggest missing info for better engagement
     */
    public function suggestProfileOptimizations(array $userData): ?string
    {
        $prompt = "You are a profile growth assistant. Based on this user data:
        - Major: {$userData['major']}
        - Job: {$userData['current_job']}
        - Social Links Count: {$userData['social_count']}
        - Has LinkedIn: " . ($userData['has_linkedin'] ? 'Yes' : 'No') . "
        - Bio Length: " . strlen($userData['bio'] ?? '') . " characters
        
        Provide 1 short, catchy, and motivational suggestion (Indonesian) to improve their profile.
        Example: 'Tambahkan LinkedIn Anda agar perusahaan lebih mudah melirik potensi profesional Anda!'
        Be friendly and encouraging.";

        return $this->ask($prompt, 0.7);
    }
}
