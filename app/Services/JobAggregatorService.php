<?php

namespace App\Services;

use App\Models\JobVacancy;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class JobAggregatorService
{
    /**
     * Parse an external job URL and try to extract metadata
     */
    public function processExternalJob(string $url)
    {
        try {
            // Attempt to fetch content (Note: Some sites block simple GET)
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->timeout(10)->get($url);

            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::warning('JobAggregatorService: Fetch failed with status ' . $response->status() . ' for URL: ' . $url);
                return ['external_link' => $url, 'error' => 'Gagal mengambil konten dari sumber (Status: ' . $response->status() . '). Situs tersebut mungkin memblokir akses otomatis.'];
            }

            $html = $response->body();
            
            if (empty($html)) {
                return ['external_link' => $url, 'error' => 'Konten halaman kosong.'];
            }

            // Extract main text to avoid sending too much HTML to AI
            $cleanText = strip_tags($html);
            $cleanText = preg_replace('/\s+/', ' ', $cleanText);
            $cleanText = substr($cleanText, 0, 5000); // Limit context

            return $this->summarizeJobWithAI($cleanText, $url);
        } catch (\Exception $e) {
            return ['external_link' => $url, 'error' => $e->getMessage()];
        }
    }

    /**
     * Using Gemini AI to summarize a job description from a URL/Text
     */
    public function summarizeJobWithAI(string $content, string $url = '')
    {
        try {
            $prompt = "Extract job details from the following text/page. 
            Return ONLY a valid JSON object with these keys: title, company, location, type, description (1 sentence summary), content (detailed bullet points), salary_range (if found).
            Language: Indonesian. 
            If it's a job listing, fill the fields. If not, return {\"error\": \"Not a job listing\"}.
            
            TEXT: " . $content;
            
            $aiService = app(AIService::class);
            $result = $aiService->ask($prompt, 0.2);
            
            if (!$result) return ['external_link' => $url, 'error' => 'AI failed'];

            $json = preg_replace('/^```json\s*|\s*```$/i', '', trim($result));
            $data = json_decode($json, true);
            
            if ($data && !isset($data['error'])) {
                $data['external_link'] = $url;
                $data['status'] = 'active';
            }

            return $data;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('JobAggregatorService AI Error: ' . $e->getMessage());
            return ['external_link' => $url, 'error' => $e->getMessage()];
        }
    }
}
