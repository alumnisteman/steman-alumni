<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Major;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalAlumni = User::where('role', 'alumni')->count();
        
        $alumniByMajor = User::where('role', 'alumni')
            ->selectRaw('jurusan, count(*) as total')
            ->groupBy('jurusan')
            ->get();

        $alumniByYear = User::where('role', 'alumni')
            ->selectRaw('tahun_lulus, count(*) as total')
            ->groupBy('tahun_lulus')
            ->orderBy('tahun_lulus')
            ->get();

        $employmentStats = User::where('role', 'alumni')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('CASE WHEN pekerjaan_sekarang IS NOT NULL AND pekerjaan_sekarang != "" THEN "Bekerja / Studi Lanjut" ELSE "Lainnya" END as status')
            ->groupByRaw('CASE WHEN pekerjaan_sekarang IS NOT NULL AND pekerjaan_sekarang != "" THEN "Bekerja / Studi Lanjut" ELSE "Lainnya" END')
            ->get();

        return view('analytics.index', compact('totalAlumni', 'alumniByMajor', 'alumniByYear', 'employmentStats'));
    }
}
