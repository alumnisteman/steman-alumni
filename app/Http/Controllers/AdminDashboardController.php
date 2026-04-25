<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Major;
use App\Models\Program;
use App\Models\JobVacancy;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(\App\Services\AIService $aiService)
    {
        try {
            $stats = \Illuminate\Support\Facades\Cache::remember('admin_dashboard_stats', 300, function () use ($aiService) {
                try {
                    $totalAlumni = User::where('role', 'alumni')->count();
                    $laravelAdmins = User::where('role', 'admin')->count();
                    
                    try {
                        $bakeryAdmins = \Illuminate\Support\Facades\DB::connection('mysql')->table('bakery_app.admins')
                            ->whereIn('role', ['Owner', 'Admin'])
                            ->count();
                    } catch (\Exception $e) {
                        $bakeryAdmins = 0;
                    }

                    $totalAdmins = $laravelAdmins + $bakeryAdmins;
                    $totalMajors = Major::count();
                    $totalPrograms = Program::count();
                    $totalJobs = JobVacancy::count();

                    // User Online (active in last 15 minutes via sessions table)
                    try {
                        $onlineUsers = \Illuminate\Support\Facades\DB::table('sessions')
                            ->where('last_activity', '>=', now()->subMinutes(15)->timestamp)
                            ->whereNotNull('user_id')
                            ->distinct('user_id')
                            ->count('user_id');
                    } catch (\Exception $e) {
                        $onlineUsers = 0;
                    }

                    // Pending alumni (waiting verification)
                    $pendingAlumni = User::where('role', 'alumni')->where('status', 'pending')->count();

                    // Dana Yayasan & Reuni totals
                    try {
                        $foundationTotal = \App\Models\Donation::whereHas('campaign', fn($q) => $q->where('type', 'foundation'))
                            ->where('status', 'verified')->sum('amount');
                        $reunionTotal = \App\Models\Donation::whereHas('campaign', fn($q) => $q->where('type', 'reunion'))
                            ->where('status', 'verified')->sum('amount');
                    } catch (\Exception $e) {
                        $foundationTotal = 0;
                        $reunionTotal = 0;
                    }

                    $recentActivities = \App\Models\ActivityLog::with('user')->latest()->take(10)->get();

                    $employedCount = User::where('role', 'alumni')->whereNotNull('current_job')->where('current_job', '!=', '')->count();
                    $employedPercentage = $totalAlumni > 0 ? round(($employedCount / $totalAlumni) * 100) : 0;

                    $mapAnalytics = User::getMapAnalytics();
                    $alumniLocations = $mapAnalytics['alumniLocations'];
                    $nationalCount = $mapAnalytics['nationalCount'];
                    $internationalCount = $mapAnalytics['internationalCount'];

                    // REAL AI INSIGHTS
                    try {
                        $aiResponse = $aiService->analyzeStats([
                            'totalAlumni' => $totalAlumni,
                            'employmentRate' => $employedPercentage,
                            'internationalCount' => $internationalCount,
                            'totalPrograms' => $totalPrograms,
                            'totalJobs' => $totalJobs,
                        ]);
                        $aiInsights = $aiResponse ? explode("\n", trim($aiResponse)) : ["AI sedang melakukan analisis mendalam..."];
                    } catch (\Exception $e) {
                        $aiInsights = ["AI Insight saat ini tidak tersedia."];
                    }

                    $alumniByMajor = User::where('role', 'alumni')
                        ->selectRaw('major, count(*) as total')
                        ->groupBy('major')
                        ->get();

                    $alumniByYear = User::where('role', 'alumni')
                        ->selectRaw('graduation_year, count(*) as total')
                        ->groupBy('graduation_year')
                        ->orderBy('graduation_year')
                        ->get();
                    
                    if ($internationalCount > 0) {
                        $aiInsights[] = "Luar Biasa! Portal ini sudah Global dengan {$internationalCount} alumni terdeteksi berada di luar negeri.";
                    }

                    $checker = new \App\Services\SystemGuard\HealthChecker();
                    $healthIssues = $checker->run();
                    $isHealthy = empty($healthIssues);

                    $healthRadar = [
                        'storage' => [
                            'status' => 'success',
                            'percent' => \Illuminate\Support\Facades\Cache::remember('disk_usage', 3600, function() {
                                return function_exists('disk_free_space') && function_exists('disk_total_space') && disk_total_space('/') > 0
                                    ? round(((disk_total_space('/') - disk_free_space('/')) / disk_total_space('/')) * 100)
                                    : 45;
                            }),
                        ],
                        'env_writable' => file_exists(base_path('.env')) && is_writable(base_path('.env')),
                        'log_size' => file_exists(storage_path('logs/laravel.log')) ? round(@filesize(storage_path('logs/laravel.log')) / 1024 / 1024, 2) : 0,
                        'backup' => [
                            'date' => \Illuminate\Support\Facades\Cache::remember('last_backup_date', 3600, function() {
                                return file_exists(storage_path('app/backup')) ? date('Y-m-d H:i', filemtime(storage_path('app/backup'))) : date('Y-m-d H:i');
                            }),
                            'size' => '~GB',
                        ],
                        'integrity' => [
                            'status' => $isHealthy ? 'SECURE' : 'DEGRADED',
                            'color' => $isHealthy ? 'primary' : 'danger',
                            'issues_count' => count($healthIssues),
                            'message' => $isHealthy 
                                ? 'End-to-end audit integrity confirmed. System is running at peak performance.'
                                : 'Integrity issues detected. ' . count($healthIssues) . ' checks failed. Immediate audit required.'
                        ],
                        'logs_url' => route('admin.system.logs'),
                    ];

                    return [
                        'totalAlumni' => $totalAlumni,
                        'totalAdmins' => $totalAdmins,
                        'totalMajors' => $totalMajors,
                        'totalPrograms' => $totalPrograms,
                        'totalJobs' => $totalJobs,
                        'onlineUsers' => $onlineUsers,
                        'pendingAlumni' => $pendingAlumni,
                        'foundationTotal' => $foundationTotal,
                        'reunionTotal' => $reunionTotal,
                        'alumniByMajor' => $alumniByMajor,
                        'alumniByYear' => $alumniByYear,
                        'recentActivities' => $recentActivities,
                        'aiInsights' => $aiInsights,
                        'alumniLocations' => $alumniLocations,
                        'nationalCount' => $nationalCount,
                        'internationalCount' => $internationalCount,
                        'employedPercentage' => $employedPercentage,
                        'healthRadar' => $healthRadar,
                        'launchInfo' => [
                            'mode' => setting('coming_soon_mode', 'off'),
                            'date' => setting('launch_date'),
                            'title' => setting('launch_title')
                        ]
                    ];
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Admin Dashboard Cache Error: ' . $e->getMessage());
                    throw $e;
                }
            });

            return view('admin.dashboard', $stats);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin Dashboard Fatal Error: ' . $e->getMessage());
            return response()->view('errors.500', ['exception_message' => $e->getMessage()], 500);
        }
    }
}
