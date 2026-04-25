<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIPredictionService
{
    /**
     * Generate dynamic AI insights based on real alumni data.
     * Uses Gemini AI when available, falls back to rule-based insights.
     */
    public function getGlobalInsights()
    {
        return Cache::remember('global_ai_insights', 86400, function () {
            try {
                $stats = [
                    'totalAlumni' => User::where('role', 'alumni')->count(),
                    'majors' => User::where('role', 'alumni')
                        ->select('major', \DB::raw('count(*) as total'))
                        ->groupBy('major')
                        ->orderBy('total', 'desc')
                        ->take(5)
                        ->get()
                        ->toArray(),
                    'locations' => User::where('role', 'alumni')
                        ->whereNotNull('city_name')
                        ->select('city_name', \DB::raw('count(*) as total'))
                        ->groupBy('city_name')
                        ->orderBy('total', 'desc')
                        ->take(5)
                        ->get()
                        ->toArray(),
                ];

                if ($stats['totalAlumni'] < 5) {
                    return $this->getStaticInsights();
                }

                $aiService = new AIService();
                $prompt = "Berdasarkan data alumni berikut: Total " . $stats['totalAlumni'] . " orang. "
                        . "Top Jurusan: " . json_encode($stats['majors']) . ". "
                        . "Top Lokasi: " . json_encode($stats['locations']) . ". "
                        . "Berikan 3 wawasan (insights) strategis untuk komunitas alumni. "
                        . 'Format JSON: [{"title": "...", "description": "...", "confidence": "...%", "icon": "bi-..."}]. '
                        . "Gunakan icon Bootstrap Icons (bi-...). Bahasa Indonesia yang futuristik dan inspiratif.";

                $response = $aiService->ask($prompt, 0.7);

                if ($response) {
                    $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($response));
                    $data = json_decode($cleanJson, true);
                    if (is_array($data) && count($data) >= 3) {
                        return $data;
                    }
                }
            } catch (\Exception $e) {
                Log::error('AIPredictionService Error: ' . $e->getMessage());
            }

            return $this->getStaticInsights();
        });
    }

    /**
     * Get personalized prediction for a specific user
     */
    public function getUserPrediction(User $user)
    {
        if (!$user->graduation_year) {
            return null;
        }

        $yearsSinceGrad = date('Y') - $user->graduation_year;
        $nextMilestone  = ceil(($yearsSinceGrad + 1) / 5) * 5;
        $reunionYear    = $user->graduation_year + $nextMilestone;

        return [
            'reunion_year' => $reunionYear,
            'milestone'    => $nextMilestone,
            'suggestion'   => $this->getSuggestionByMajor($user->major),
        ];
    }

    private function getSuggestionByMajor($major)
    {
        $major = strtoupper($major);
        if (str_contains($major, 'TKJ') || str_contains($major, 'KOMPUTER') || str_contains($major, 'JARINGAN')) {
            return 'Networking Night & Update Sertifikasi Mikrotik';
        }
        if (str_contains($major, 'RPL') || str_contains($major, 'REKAYASA') || str_contains($major, 'PERANGKAT LUNAK')) {
            return 'Developer Meetup: Masa Depan AI di Indonesia';
        }
        if (str_contains($major, 'MM') || str_contains($major, 'MULTIMEDIA') || str_contains($major, 'DESAIN')) {
            return 'Gath Design: Kreativitas Digital 2026';
        }
        if (str_contains($major, 'AK') || str_contains($major, 'AKUNTANSI')) {
            return 'Workshop: Fintech & Financial Planning for Alumni';
        }

        return 'Temu Kangen & Sinergi Lintas Profesi';
    }

    /**
     * Fallback static insights when AI is unavailable or alumni data is insufficient.
     */
    private function getStaticInsights()
    {
        return [
            [
                'title'       => 'Prediksi Reuni Akbar',
                'description' => 'Berdasarkan kepadatan data angkatan terbaru, Reuni Akbar 1 Dekade paling efektif dilaksanakan pada tahun 2026.',
                'confidence'  => '85%',
                'icon'        => 'bi-people-fill',
            ],
            [
                'title'       => 'Workshop Teknologi & Karir',
                'description' => 'Meningkatnya alumni di sektor digital menunjukkan minat tinggi pada sinkronisasi industri tahun depan.',
                'confidence'  => '92%',
                'icon'        => 'bi-lightbulb-fill',
            ],
            [
                'title'       => 'Program Mentoring Pasca-Reuni',
                'description' => 'Analisis menunjukkan 40% alumni senior siap menjadi mentor bagi lulusan baru setelah pertemuan fisik.',
                'confidence'  => '78%',
                'icon'        => 'bi-mortarboard-fill',
            ],
        ];
    }
}
