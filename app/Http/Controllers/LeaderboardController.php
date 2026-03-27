<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    /**
     * Display the alumni leaderboard
     */
    public function index()
    {
        // Fetch top 10 alumni by points, only approved ones
        $topAlumni = User::where('role', 'alumni')
            ->where('status', 'approved')
            ->orderBy('points', 'desc')
            ->take(10)
            ->get();

        // Separate top 3 for podium
        $podium = $topAlumni->take(3);
        $others = $topAlumni->slice(3);

        return view('leaderboard.index', compact('podium', 'others'));
    }
}
