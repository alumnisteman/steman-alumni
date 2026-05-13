<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LeaderboardController extends Controller
{
    /**
     * Display the alumni leaderboard
     */
    public function index()
    {
        // Cache top 10 alumni by points for 30 minutes
        $data = Cache::remember('alumni_leaderboard', 1800, function () {
            $topAlumni = User::where('role', 'alumni')
                ->where('status', 'approved')
                ->orderBy('points', 'desc')
                ->take(10)
                ->get();

            return [
                'podium' => $topAlumni->take(3),
                'others' => $topAlumni->slice(3),
            ];
        });

        return \Illuminate\Support\Facades\View::make('leaderboard.index', [
            'podium' => $data['podium'],
            'others' => $data['others']
        ]);
    }
}
