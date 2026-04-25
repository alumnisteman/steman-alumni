<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMatch;
use Illuminate\Http\Request;

class MatchmakingController extends Controller
{
    /**
     * Show the Tinder-style Swipe Interface
     */
    public function index()
    {
        // Get users that haven't been swiped yet by the current user
        $swipedIds = UserMatch::where('user_id', auth()->id())->pluck('target_id')->toArray();
        $swipedIds[] = auth()->id(); // Exclude self

        // Fetch potential connections (Alumni & Mentors)
        $candidates = User::whereNotIn('id', $swipedIds)
            ->where('role', 'alumni')
            ->whereNotNull('profile_picture')
            ->inRandomOrder()
            ->take(15)
            ->get();

        return view('portal.matchmaking', compact('candidates'));
    }

    /**
     * Handle Swipe Right (Like) or Left (Pass) via AJAX
     */
    public function swipe(Request $request)
    {
        $request->validate([
            'target_id' => 'required|exists:users,id',
            'action' => 'required|in:like,pass'
        ]);

        $userId = auth()->id();
        $targetId = $request->target_id;
        $action = $request->action;

        // Check if already swiped
        $existing = UserMatch::where('user_id', $userId)->where('target_id', $targetId)->first();
        if ($existing) {
            return response()->json(['status' => 'already_swiped']);
        }

        // Record the swipe
        UserMatch::create([
            'user_id' => $userId,
            'target_id' => $targetId,
            'status' => $action === 'like' ? 'liked' : 'passed'
        ]);

        // Check for MATCH
        $isMatch = false;
        if ($action === 'like') {
            $reverseSwipe = UserMatch::where('user_id', $targetId)
                ->where('target_id', $userId)
                ->where('status', 'liked')
                ->first();

            if ($reverseSwipe) {
                $isMatch = true;
                // Upgrade both statuses to matched
                UserMatch::where('user_id', $userId)->where('target_id', $targetId)->update(['status' => 'matched']);
                $reverseSwipe->update(['status' => 'matched']);
                
                // You could also award Gamification XP here!
                auth()->user()->increment('points', 10);
                User::find($targetId)->increment('points', 10);
            }
        }

        return response()->json([
            'success' => true,
            'match' => $isMatch
        ]);
    }
}
