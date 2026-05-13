<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AIService
{
    protected ?string $apiKey;
    protected ?string $openRouterKey;
    protected ?string $openRouterModel;
    protected ?string $openRouterApiBase;
    protected ?string $deepSeekKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com';
    protected string $deepSeekUrl = 'https://api.deepseek.com';
    public ?string $activeProvider = null;

    public function __construct(?string $apiKey = null)
    {
        // Prioritize: Passed key > .env config > Setting from Database
        $this->apiKey = $apiKey ?: (\config('services.gemini.api_key') ?: \setting('gemini_api_key'));
        $this->openRouterKey = \config('services.openrouter.api_key') ?: \env('OPENROUTER_API_KEY');
        $this->openRouterModel = \config('services.openrouter.model', 'google/gemini-2.0-flash-exp:free'); 
        $this->openRouterApiBase = \config('services.openrouter.api_base', 'https://openrouter.ai/api/v1');
        $this->deepSeekKey = \config('services.deepseek.api_key') ?: \env('DEEPSEEK_API_KEY');
    }

    private function tryDeepSeek(string $prompt, float $temperature): ?string
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $this->deepSeekKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post("{$this->deepSeekUrl}/chat/completions", [
                'model' => 'deepseek-chat',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => $temperature,
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }
        } catch (\Exception $e) {
            Log::error('AIService: DeepSeek Exception: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * General method to prompt Gemini with Auto-Switching capabilities
     */
    public function ask(string $prompt, float $temperature = 0.7, ?string $model = null): ?string
    {
        $this->activeProvider = null;
        $primary = \env('AI_PRIMARY_PROVIDER', 'gemini');

        // Define provider execution order based on primary choice
        $providers = $primary === 'openrouter' 
            ? ['openrouter', 'gemini', 'deepseek'] 
            : ($primary === 'deepseek' ? ['deepseek', 'gemini', 'openrouter'] : ['gemini', 'deepseek', 'openrouter']);

        foreach ($providers as $provider) {
            if ($provider === 'gemini') {
                $models = $model ? [$model] : ['gemini-1.5-flash', 'gemini-2.0-flash-exp'];
                foreach ($models as $currentModel) {
                    for ($attempt = 1; $attempt <= 2; $attempt++) {
                        $result = $this->tryRequest($prompt, $temperature, $currentModel);
                        if ($result) {
                            $this->activeProvider = 'Gemini (' . $currentModel . ')';
                            return $result;
                        }
                        if ($attempt < 2) usleep(500000); // 0.5s
                    }
                }
            } elseif ($provider === 'deepseek' && $this->deepSeekKey) {
                Log::info('AIService: Attempting DeepSeek Direct...');
                $result = $this->tryDeepSeek($prompt, $temperature);
                if ($result) {
                    $this->activeProvider = 'DeepSeek Direct';
                    return $result;
                }
            } elseif ($provider === 'openrouter' && ($this->openRouterKey ?: \env('OPENROUTER_API_KEY'))) {
                Log::info('AIService: Attempting OpenRouter...', ['model' => $this->openRouterModel]);
                $result = $this->tryOpenRouter($prompt, $temperature);
                if ($result) {
                    $this->activeProvider = 'OpenRouter (' . $this->openRouterModel . ')';
                    return $result;
                }
            }
        }

        Log::error('AIService: All AI attempts failed across all configured providers.');
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

        return $this->ask($prompt, 0.8, 'gemini-2.5-flash');
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

        // Always use v1beta as it has the widest model support
        $apiVersions = ['v1beta'];

        foreach ($apiVersions as $apiVersion) {
            try {
                $url = "{$this->baseUrl}/{$apiVersion}/models/{$model}:generateContent?key={$this->apiKey}";

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($url, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature' => $temperature,
                        'maxOutputTokens' => 1024,
                    ]
                ]);

                if ($response->successful()) {
                    $text = $response->json('candidates.0.content.parts.0.text');
                    return !empty($text) ? $text : null;
                }

                $status = $response->status();
                $body = $response->body();

                // If 404, maybe this model is not in this API version, try next version
                if ($status === 404 && $apiVersion === 'v1beta' && count($apiVersions) > 1) {
                    Log::debug("AIService: Model [$model] not found in v1beta, trying v1...");
                    continue; 
                }

                // Differentiate between quota (429) and real errors
                if ($status === 429) {
                    Log::debug("AIService: Gemini quota exceeded for [$model]. Will fallback to next model.");
                } else {
                    Log::warning("AIService: Gemini API Error ($status) for [$model] in [$apiVersion]", [
                        'url' => "{$this->baseUrl}/{$apiVersion}/models/{$model}",
                        'body' => $body
                    ]);
                }

                // If we get a 403 or 400, no point in trying other versions for this model
                if ($status === 403 || $status === 400) break;

            } catch (\Exception $e) {
                Log::error("AIService Exception for [$model] in [$apiVersion]: " . $e->getMessage());
                break;
            }
        }

        return null;
    }

    /**
     * Fallback to OpenRouter (The "Open Claw" integration)
     */
    private function tryOpenRouter(string $prompt, float $temperature): ?string
    {
        try {
            $endpoint = rtrim($this->openRouterApiBase, '/') . '/chat/completions';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openRouterKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => \config('app.url'),
                'X-Title' => 'Smart Market OS',
            ])->timeout(30)->post($endpoint, [
                'model' => $this->openRouterModel,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => $temperature,
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::warning('AIService: OpenRouter API Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('AIService: OpenRouter Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Verify if the API key is healthy
     */
    public function checkHealth(): array
    {
        if (empty($this->apiKey) && empty($this->openRouterKey) && empty($this->deepSeekKey)) {
            return ['status' => 'ERROR', 'message' => 'No AI API Keys configured.'];
        }

        $result = $this->ask("Hello, are you active? Reply with 'ACTIVE' only.", 0.1);
        
        if ($result && str_contains(strtoupper($result), 'ACTIVE')) {
            return [
                'status' => 'HEALTHY', 
                'message' => 'AI Service is connected and responding.',
                'provider' => $this->activeProvider ?? 'Unknown'
            ];
        }

        return ['status' => 'ERROR', 'message' => 'AI Service is unreachable or returned invalid response.', 'provider' => 'None'];
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
        $candidateData = \collect($candidates)->map(fn($c) => "[ID: {$c['id']}, Name: {$c['name']}, Major: {$c['major']}, Job: {$c['current_job']}]")->join("\n");
        
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
        $candidateData = \collect($candidates)->map(fn($c) => "[ID: {$c['id']}, Name: {$c['name']}, Major: {$c['major']}, Job: {$c['current_job']}]")->join("\n");
        
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
        $cacheKey = 'geocode_fail_' . md5($address);
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            Log::warning("AIService: Skipping geocode for known unsearchable address: $address");
            return null;
        }

        // Variation 1: Exact Address
        $prompt = "Convert the following Indonesian address into geographic coordinates (latitude and longitude). 
        Address: \"$address\"
        Return ONLY valid JSON format: {\"lat\": float, \"lng\": float}. 
        If address is invalid or not found, return {\"lat\": null, \"lng\": null}.";

        $result = $this->ask($prompt, 0.1);
        if ($result) {
            $json = preg_replace('/^```json\s*|\s*```$/i', '', trim($result));
            $data = json_decode($json, true);
            if (isset($data['lat']) && isset($data['lng']) && $data['lat'] !== null) {
                return $data;
            }
        }

        // Broad Search Fallback: Try Kabupaten/City
        $searchTerms = ['Kabupaten', 'Kota', 'Kecamatan'];
        foreach ($searchTerms as $term) {
            if (str_contains($address, $term)) {
                $parts = explode($term, $address);
                $broaderAddress = $term . end($parts);
                Log::warning("AIService Geocode: Specific failed, trying broader region: $broaderAddress");
                
                $prompt = "Find center coordinates for this area in Indonesia: \"$broaderAddress\". Return ONLY JSON: {\"lat\": float, \"lng\": float}.";
                $result = $this->ask($prompt, 0.1);
                if ($result) {
                    $json = preg_replace('/^```json\s*|\s*```$/i', '', trim($result));
                    $data = json_decode($json, true);
                    if (isset($data['lat']) && isset($data['lng']) && $data['lat'] !== null) {
                        return $data;
                    }
                }
            }
        }

        // Final Fallback: OpenStreetMap Nominatim with cleaned query
        Log::warning("AIService Geocode: Gemini failed, attempting Nominatim fallback for: $address");
        try {
            // Clean common terms that sometimes confuse Nominatim for villages
            $cleanAddress = str_replace(['Desa ', 'Kelurahan '], '', $address);
            $query = $cleanAddress;
            if (!str_contains(strtolower($cleanAddress), 'indonesia')) {
                $query .= ', Indonesia';
            }
            
            $response = Http::withHeaders([
                'User-Agent' => 'StemanAlumniPortal/1.0'
            ])->timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'limit' => 1
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                Log::warning("AIService Geocode Success (Nominatim): " . $data['lat'] . ", " . $data['lon']);
                return ['lat' => (float) $data['lat'], 'lng' => (float) $data['lon']];
            }
        } catch (\Exception $e) {
            Log::warning("AIService Geocode: Nominatim fallback failed: " . $e->getMessage());
        }

        // Mark as failed in cache for 24h to avoid redundant expensive API calls
        \Illuminate\Support\Facades\Cache::put($cacheKey, true, 86400);
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
    /**
     * AI Content Helper: Generate or improve text for news/jobs
     */
    public function generateContent(string $type, string $input): ?string
    {
        $prompt = "You are a creative content writer for a high-end alumni portal. 
        Help the user $type based on this input: \"$input\".
        - Tone: Professional, engaging, and modern.
        - Language: Indonesian.
        - Output ONLY the result text, no chat or meta comments.
        - If generating news, make it a compelling news article.
        - If generating a job description, make it professional and structured.";

        return $this->ask($prompt, 0.7);
    }

    /**
     * Generate a professional bio for an alumni.
     * 
     * @param string $name
     * @param string $major
     * @param string|int $gradYear
     * @param string $skills
     * @param string $experience
     * @return string|null
     */
    public function generateProfessionalBio($name, $major, $gradYear, $skills = '', $experience = '')
    {
        $prompt = "Generate a short, professional, and inspiring bio for an alumni named {$name}. 
                   Major: {$major}. 
                   Graduation Year: {$gradYear}. 
                   Skills: {$skills}. 
                   Experience: {$experience}.
                   The bio should be in Indonesian, maximum 3 sentences, and sound confident yet approachable. 
                   Return ONLY the bio text.";

        return $this->ask($prompt);
    }
}
