<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HealthProfile;
use App\Services\HealthAIService;

class HealthController extends Controller
{
    protected HealthAIService $healthAI;

    public function __construct(HealthAIService $healthAI)
    {
        $this->healthAI = $healthAI;
    }

    public function index()
    {
        $user = auth()->user();
        $profile = $user->healthProfile ?? new HealthProfile();
        $isOver40 = $user->isOver40();

        return view('alumni.health.index', compact('user', 'profile', 'isOver40'));
    }

    public function updateLifestyle(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric|min:30|max:300',
            'height' => 'required|numeric|min:100|max:250',
            'activity_level' => 'required|in:Rendah,Sedang,Tinggi',
        ]);

        $user = auth()->user();
        
        // Calculate BMI
        $weight = (float) $request->weight;
        $heightM = (float) $request->height / 100;
        $bmi = $weight / ($heightM * $heightM);
        
        $bmiCategory = 'Normal';
        if ($bmi < 18.5) $bmiCategory = 'Underweight';
        elseif ($bmi >= 25 && $bmi < 30) $bmiCategory = 'Overweight';
        elseif ($bmi >= 30) $bmiCategory = 'Obese';

        // Get AI Recommendation
        $aiRecommendation = $this->healthAI->analyzeLifestyle([
            'weight' => $weight,
            'height' => $request->height,
            'bmi_category' => $bmiCategory,
            'activity_level' => $request->activity_level,
        ]);

        // Save Profile
        $profile = HealthProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'weight' => (string)$weight,
                'height' => (string)$request->height,
                'bmi_category' => $bmiCategory,
                'activity_level' => $request->activity_level,
                'ai_recommendation' => $aiRecommendation,
                'last_checkup_date' => now(),
            ]
        );

        return redirect()->route('alumni.health.index')->with('success', 'Profil kesehatan berhasil diperbarui.');
    }

    public function checkSymptoms(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|string|min:5|max:500',
        ]);

        $user = auth()->user();
        $symptoms = $request->symptoms;

        $aiResponse = $this->healthAI->earlyWarningCheck($symptoms);

        HealthProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'last_symptoms' => $symptoms,
                'ai_recommendation' => $aiResponse,
                'last_checkup_date' => now(),
            ]
        );

        return redirect()->route('alumni.health.index')->with('warning', 'Peringatan Dini telah dianalisis. Silakan baca rekomendasi AI di bawah.');
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $response = $this->healthAI->chat($request->message);

        return response()->json([
            'status' => 'success',
            'reply' => $response
        ]);
    }
}
