<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HealthProfile;
use Illuminate\Support\Facades\DB;

class HealthDashboardController extends Controller
{
    public function index()
    {
        // 1. Calculate Age Demographics
        $currentYear = (int) date('Y');
        
        $totalAlumni = User::where('role', 'alumni')->where('status', 'approved')->count();
        
        // Asumsi lulus umur 18. Lulusan tahun X berarti umur = currentYear - X + 18.
        // Jika umur >= 40, berarti (currentYear - X + 18) >= 40 => X <= currentYear - 22
        $maxGradYearFor40 = $currentYear - 22;
        
        $alumniOver40 = User::where('role', 'alumni')
            ->where('status', 'approved')
            ->whereNotNull('graduation_year')
            ->where('graduation_year', '<=', $maxGradYearFor40)
            ->count();
            
        $percentageOver40 = $totalAlumni > 0 ? round(($alumniOver40 / $totalAlumni) * 100, 1) : 0;

        // 2. BMI Trends (Anonymized Aggregation)
        $bmiTrends = HealthProfile::select('bmi_category', DB::raw('count(*) as count'))
            ->whereNotNull('bmi_category')
            ->groupBy('bmi_category')
            ->pluck('count', 'bmi_category')
            ->toArray();

        // 3. Activity Level Trends
        $activityTrends = HealthProfile::select('activity_level', DB::raw('count(*) as count'))
            ->whereNotNull('activity_level')
            ->groupBy('activity_level')
            ->pluck('count', 'activity_level')
            ->toArray();

        return view('admin.health.dashboard', compact(
            'totalAlumni',
            'alumniOver40',
            'percentageOver40',
            'bmiTrends',
            'activityTrends'
        ));
    }
}
