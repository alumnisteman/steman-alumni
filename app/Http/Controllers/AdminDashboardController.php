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
            
            $employedCount = User::where('role', 'alumni')->whereNotNull('pekerjaan_sekarang')->where('pekerjaan_sekarang', '!=', '')->count();
            $employedPercentage = $totalAlumni > 0 ? round(($employedCount / $totalAlumni) * 100) : 0;
            
            if ($employedPercentage > 50) {
                $aiInsights[] = "Luar Biasa! Lebih dari {$employedPercentage}% alumni telah memiliki pekerjaan tetap atau usaha.";
            } else {
                $aiInsights[] = "Persentase alumni bekerja di angka {$employedPercentage}%. Perbanyak relasi loker untuk meningkatkan daya serap lulusan.";
            }

            // Stats for Charts
            $alumniByMajor = User::where('role', 'alumni')
                ->selectRaw('jurusan, count(*) as total')
                ->groupBy('jurusan')
                ->get();

            $alumniByYear = User::where('role', 'alumni')
                ->selectRaw('tahun_lulus, count(*) as total')
                ->groupBy('tahun_lulus')
                ->orderBy('tahun_lulus')
                ->get();
            
            $mapAnalytics = User::getMapAnalytics();
            $alumniLocations = $mapAnalytics['alumniLocations'];
            $nationalCount = $mapAnalytics['nationalCount'];
            $internationalCount = $mapAnalytics['internationalCount'];

            // Add AI Insights for International presence
            if ($internationalCount > 0) {
                $aiInsights[] = "Luar Biasa! Portal ini sudah Global dengan {$internationalCount} alumni terdeteksi berada di luar negeri.";
            }

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
                'internationalCount' => $internationalCount
            ];
        });

        return view('admin.dashboard', $stats);
    }

}
