<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorController extends Controller
{
    public function index()
    {
        return view('alumni.mentor.index');
    }

    public function find(Request $request, AIService $aiService)
    {
        $user = Auth::user();
        $goal = $request->goal;

        // Fetch potential mentors (same major or random for variety)
        $candidates = User::where('id', '!=', $user->id)
            ->whereNotNull('current_job')
            ->limit(10)
            ->get(['id', 'name', 'major', 'current_job']);

        $matches = $aiService->matchMentor([
            'name' => $user->name,
            'major' => $user->major,
            'goal' => $goal
        ], $candidates->toArray());

        $mentors = [];
        foreach ($matches as $match) {
            $mentorUser = User::find($match['id']);
            if ($mentorUser) {
                $mentors[] = [
                    'user' => $mentorUser,
                    'reason' => $match['reason']
                ];
            }
        }

        return response()->json([
            'success' => true,
            'mentors' => $mentors
        ]);
    }
}
