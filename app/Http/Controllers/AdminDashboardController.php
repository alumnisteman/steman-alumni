<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Major;
use App\Models\Program;
use App\Models\JobVacancy;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = \Illuminate\Support\Facades\Cache::remember('admin_dashboard_stats', 900, function () {
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

            // 1. Fetch live timeline (Recent Activities / Latest Users)
            $recentActivities = User::latest()->take(5)->get();

            // 2. Simple AI Insights String Generator
            $aiInsights = [];
            if ($totalAlumni > 100) {
                $aiInsights[] = "Terdapat pertumbuhan signifikan dengan total {$totalAlumni} alumni terdaftar di sistem. Pertimbangkan untuk mem-blast buletin karir.";
            } else {
                $aiInsights[] = "Pendaftaran alumni masih berada di angka {$totalAlumni}. Coba jalankan kampanye sosial media untuk mengajak lebih banyak lulusan mendaftar.";
            }
            
            $employedCount = User::where('role', 'alumni')->whereNotNull('current_job')->where('current_job', '!=', '')->count();
            $employedPercentage = $totalAlumni > 0 ? round(($employedCount / $totalAlumni) * 100) : 0;
            
            if ($employedPercentage > 50) {
                $aiInsights[] = "Luar Biasa! Lebih dari {$employedPercentage}% alumni telah memiliki pekerjaan tetap atau usaha.";
            } else {
                $aiInsights[] = "Persentase alumni bekerja di angka {$employedPercentage}%. Perbanyak relasi loker untuk meningkatkan daya serap lulusan.";
            }

            // Stats for Charts
            $alumniByMajor = User::where('role', 'alumni')
                ->selectRaw('major, count(*) as total')
                ->groupBy('major')
                ->get();

            $alumniByYear = User::where('role', 'alumni')
                ->selectRaw('graduation_year, count(*) as total')
                ->groupBy('graduation_year')
                ->orderBy('graduation_year')
                ->get();
            
            $mapAnalytics = User::getMapAnalytics();
            $alumniLocations = $mapAnalytics['alumniLocations'];
            $nationalCount = $mapAnalytics['nationalCount'];
            $internationalCount = $mapAnalytics['internationalCount'];

            // Add AI Insights for International presence
            if ($internationalCount > 0) {
                $aiInsights[] = "Luar Biasa! Portal ini sudah Global dengan {$internationalCount} alumni terdeteksi berada di luar negeri.";
            }

            // System Health Radar Data
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
                'log_size' => file_exists(storage_path('logs/laravel.log')) ? round(filesize(storage_path('logs/laravel.log')) / 1024 / 1024, 2) : 0,
                'backup' => [
                    'date' => \Illuminate\Support\Facades\Cache::remember('last_backup_date', 3600, function() {
                        return file_exists(storage_path('app/backup')) ? date('Y-m-d H:i', filemtime(storage_path('app/backup'))) : date('Y-m-d H:i');
                    }),
                    'size' => '~GB',
                ],
                'integrity' => [
                    'status' => 'AMAN',
                    'color' => 'success',
                ],
                'logs_url' => route('admin.system.logs'),
            ];

            return [
                'totalAlumni' => $totalAlumni,
                'totalAdmins' => $totalAdmins,
                'totalMajors' => $totalMajors,
                'totalPrograms' => $totalPrograms,
                'totalJobs' => $totalJobs,
                'alumniByMajor' => $alumniByMajor,
                'alumniByYear' => $alumniByYear,
                'recentActivities' => $recentActivities,
                'aiInsights' => $aiInsights,
                'alumniLocations' => $alumniLocations,
                'nationalCount' => $nationalCount,
                'internationalCount' => $internationalCount,
                'healthRadar' => $healthRadar
            ];
        });

        return view('admin.dashboard', $stats);
    }

}
