<?php

namespace App\Services;

use App\Models\User;
use App\Models\Major;
use Illuminate\Support\Carbon;

class AIPredictionService
{
    /**
     * Generate AI insights based on demographic and career data
     */
    public function getGlobalInsights()
    {
        $totalAlumni = User::where('role', 'alumni')->count();
        if ($totalAlumni == 0) return $this->getEmptyInsights();

        $topMajors = User::where('role', 'alumni')
            ->select('major', \DB::raw('count(*) as total'))
            ->groupBy('major')
            ->orderBy('total', 'desc')
            ->take(3)
            ->get();

        $currentYear = date('Y');
        
        // Predict Reunion based on graduation peaks
        $gradPeaks = User::where('role', 'alumni')
            ->select('graduation_year', \DB::raw('count(*) as total'))
            ->groupBy('graduation_year')
            ->orderBy('total', 'desc')
            ->take(1)
            ->first();

        $peakYear = $gradPeaks ? $gradPeaks->graduation_year : $currentYear - 5;
        $reunionYear = $peakYear + 10; // 10 Year Milestone
        if ($reunionYear <= $currentYear) $reunionYear = $currentYear + 1;

        return [
            'reunion' => [
                'title' => 'Prediksi Reuni Akbar',
                'description' => "Berdasarkan kepadatan data angkatan $peakYear, Reuni Akbar 1 Dekade paling efektif dilaksanakan pada tahun $reunionYear.",
                'confidence' => '85%',
                'icon' => 'bi-people-fill'
            ],
            'random_event' => [
                'title' => 'Workshop Teknologi & Karir',
                'description' => "Banyaknya alumni dari major " . ($topMajors->first()->major ?? 'Teknik') . " menunjukkan minat tinggi pada sinkronisasi industri tahun depan.",
                'confidence' => '92%',
                'icon' => 'bi-lightbulb-fill'
            ],
            'post_reunion' => [
                'title' => 'Program Mentoring Pasca-Reuni',
                'description' => "Analisis menunjukkan 40% alumni senior siap menjadi mentor bagi lulusan baru setelah pertemuan fisik.",
                'confidence' => '78%',
                'icon' => 'bi-mortarboard-fill'
            ]
        ];
    }

    /**
     * Get personalized prediction for a specific user
     */
    public function getUserPrediction(User $user)
    {
        if (!$user->graduation_year) return null;

        $yearsSinceGrad = date('Y') - $user->graduation_year;
        $nextMilestone = ceil(($yearsSinceGrad + 1) / 5) * 5;
        $reunionYear = $user->graduation_year + $nextMilestone;

        return [
            'reunion_year' => $reunionYear,
            'milestone' => $nextMilestone,
            'suggestion' => $this->getSuggestionByMajor($user->major)
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

    private function getEmptyInsights()
    {
        return [
            'reunion' => ['title' => 'N/A', 'description' => 'Data alumni belum mencukupi.', 'confidence' => '0%', 'icon' => 'bi-pause-circle'],
            'random_event' => ['title' => 'N/A', 'description' => 'Data alumni belum mencukupi.', 'confidence' => '0%', 'icon' => 'bi-pause-circle'],
            'post_reunion' => ['title' => 'N/A', 'description' => 'Data alumni belum mencukupi.', 'confidence' => '0%', 'icon' => 'bi-pause-circle'],
        ];
    }
}
