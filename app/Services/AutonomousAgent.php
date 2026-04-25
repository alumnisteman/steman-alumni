<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class AutonomousAgent
{
    protected $geminiKey;

    public function __construct()
    {
        // Consistent with AIService: check .env first, then database setting
        $this->geminiKey = env('GEMINI_API_KEY');
        if (empty($this->geminiKey)) {
            try {
                $this->geminiKey = \App\Models\Setting::where('key', 'gemini_api_key')->value('value');
            } catch (\Throwable $e) {
                // DB may not be ready (e.g., during queue boot), silently skip
            }
        }
    }

    /**
     * Analyze the exception and attempt to self-heal
     */
    public function diagnoseAndHeal($errorLog, $filePath, $lineNumber)
    {
        if (!$this->geminiKey || empty($filePath)) {
            Log::warning('AutonomousAgent: Missing API key or file path.');
            return false;
        }

        Log::info("AutonomousAgent: Commencing analysis for error in {$filePath} at line {$lineNumber}");

        // Gather context
        $fileContext = '';
        if (File::exists($filePath)) {
            $lines = file($filePath);
            $start = max(0, $lineNumber - 50);
            $end = min(count($lines), $lineNumber + 50);
            $fileContext = implode("", array_slice($lines, $start, $end - $start));
        }

        $prompt = $this->buildPrompt($errorLog, $filePath, $lineNumber, $fileContext);
        
        // Ask AI for patch
        $response = $this->askGemini($prompt);
        
        if (!$response) {
            return false;
        }

        return $this->applyHealInstruction($response, $filePath);
    }

    private function askGemini($prompt)
    {
        try {
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $this->geminiKey;
            
            $response = \Illuminate\Support\Facades\Http::timeout(30)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                ]
            ]);

            if (!$response->successful()) {
                Log::error('AutonomousAgent: Gemini API error: ' . $response->status());
                return null;
            }

            $text = $response->json('candidates.0.content.parts.0.text') ?? '';
            return $this->parseAIResponse($text);

        } catch (\Exception $e) {
            Log::error('AutonomousAgent: Gemini API exception: ' . $e->getMessage());
            return null;
        }
    }

    private function buildPrompt($errorLog, $filePath, $lineNumber, $fileContext)
    {
        return "You are an autonomous Laravel 11 self-healing agent. A Fatal Error occurred in the production environment.
        
ERROR MESSAGE:
{$errorLog}

FILE PATH:
{$filePath}

CONTEXT AROUND LINE {$lineNumber}:
```php
{$fileContext}
```

Your task is to analyze this error and provide a fix.
Respond ONLY with a JSON object in this exact format, with no markdown formatting around the JSON:
{
    \"type\": \"file_edit\" or \"command\",
    \"search\": \"exact string to find in the file (if file_edit)\",
    \"replace\": \"exact string to replace it with (if file_edit)\",
    \"command\": \"artisan command to run (if type is command, e.g., 'optimize:clear')\",
    \"reasoning\": \"Short explanation of the fix\"
}
Do not wrap the JSON in markdown blocks like ```json.";
    }

    private function parseAIResponse($text)
    {
        // Strip markdown if AI misbehaves
        $text = str_replace(['```json', '```'], '', $text);
        return json_decode(trim($text), true);
    }

    private function applyHealInstruction($instruction, $filePath)
    {
        if (!isset($instruction['type'])) return false;

        Log::info("AutonomousAgent: AI suggested fix: " . $instruction['reasoning']);

        if ($instruction['type'] === 'command' && isset($instruction['command'])) {
            try {
                // Only allow specific safe commands to prevent destruction
                $allowedCommands = ['optimize:clear', 'config:clear', 'cache:clear', 'view:clear', 'route:clear'];
                if (in_array($instruction['command'], $allowedCommands)) {
                    Log::info("AutonomousAgent: Executing artisan " . $instruction['command']);
                    Artisan::call($instruction['command']);
                    return true;
                }
                Log::warning("AutonomousAgent: AI suggested unauthorized command: " . $instruction['command']);
                return false;
            } catch (\Exception $e) {
                Log::error("AutonomousAgent: Command execution failed: " . $e->getMessage());
                return false;
            }
        }

        if ($instruction['type'] === 'file_edit' && isset($instruction['search'], $instruction['replace'])) {
            if (!File::exists($filePath)) return false;

            $content = File::get($filePath);
            
            // Backup the file
            $backupPath = storage_path('app/ai_backups/' . basename($filePath) . '_' . time());
            if (!File::isDirectory(storage_path('app/ai_backups'))) {
                File::makeDirectory(storage_path('app/ai_backups'), 0755, true);
            }
            File::put($backupPath, $content);

            // Apply patch
            $newContent = str_replace($instruction['search'], $instruction['replace'], $content);
            
            if ($newContent === $content) {
                Log::warning("AutonomousAgent: AI patch search string did not match file content.");
                return false;
            }

            File::put($filePath, $newContent);

            // Validate Syntax
            $output = [];
            $returnVar = 0;
            exec("php -l " . escapeshellarg($filePath), $output, $returnVar);
            
            if ($returnVar !== 0) {
                Log::error("AutonomousAgent: Syntax error introduced by AI. Rolling back!");
                File::put($filePath, $content); // Rollback
                return false;
            }

            // SUCCESS! Clear cache to apply changes immediately
            try {
                Artisan::call('optimize:clear');
                Log::info("AutonomousAgent: System optimized after patch.");
            } catch (\Exception $e) {
                Log::warning("AutonomousAgent: Patch applied but failed to clear cache: " . $e->getMessage());
            }

            Log::info("AutonomousAgent: Successfully patched file {$filePath}. Syntax OK.");
            return true;
        }

        return false;
    }
}
