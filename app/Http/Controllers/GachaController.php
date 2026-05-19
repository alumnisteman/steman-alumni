<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GachaController extends Controller
{
    /**
     * Show the Alumni Gacha landing page.
     */
    public function index()
    {
        $stats = Cache::remember('gacha_stats', 300, function () {
            return [
                'total_connects' => UserMatch::where('status', 'matched')->count(),
                'active_alumni'  => User::where('role', 'alumni')->where('status', 'active')->count(),
                'today_spins'    => UserMatch::whereDate('created_at', today())->count(),
            ];
        });

        return view('alumni.gacha.index', compact('stats'));
    }

    /**
     * Spin: return one random alumni that the current user hasn't connected with yet.
     * Called via AJAX — returns JSON.
     */
    public function spin(Request $request)
    {
        $me = auth()->user();

        // IDs already processed (liked/passed/matched)
        $doneIds = UserMatch::where('user_id', $me->id)->pluck('target_id')->toArray();
        $doneIds[] = $me->id; // exclude self

        // Optional filter params
        $major    = $request->input('major');   // jurusan
        $city     = $request->input('city');    // kota
        $interest = $request->input('interest'); // minat

        $query = User::whereNotIn('id', $doneIds)
            ->where('role', 'alumni')
            ->where('status', 'active');

        if ($major)    $query->where('major', $major);
        if ($city)     $query->where('city', 'like', "%{$city}%");
        if ($interest) $query->where('interests', 'like', "%{$interest}%");

        $alumni = $query->inRandomOrder()->first();

        if (! $alumni) {
            return response()->json([
                'empty' => true,
                'message' => 'Semua alumni sudah kamu connect! Kamu adalah alumni paling sosial di Steman 🎉',
            ]);
        }

        return response()->json([
            'empty'   => false,
            'alumni'  => [
                'id'              => $alumni->id,
                'name'            => $alumni->name,
                'avatar'          => $alumni->profile_picture_url,
                'major'           => $alumni->major ?? 'Alumni Steman',
                'graduation_year' => $alumni->graduation_year ?? '-',
                'city'            => $alumni->city ?? 'Indonesia',
                'bio'             => $alumni->bio ? \Str::limit($alumni->bio, 100) : 'Alumni Steman Ternate yang siap berkolaborasi! 🚀',
                'interests'       => $alumni->interests ?? '',
                'profile_url'     => route('alumni.show', $alumni),
                'chat_url'        => route('alumni.chat'),
            ],
        ]);
    }

    /**
     * Connect (like) or Skip (pass) an alumni.
     */
    public function connect(Request $request)
    {
        $request->validate([
            'target_id' => 'required|exists:users,id',
            'action'    => 'required|in:connect,skip',
        ]);

        $me       = auth()->user();
        $targetId = $request->target_id;
        $action   = $request->action;

        // Guard: don't double-record
        $existing = UserMatch::where('user_id', $me->id)->where('target_id', $targetId)->first();
        if ($existing) {
            return response()->json(['status' => 'already_done']);
        }

        $status = $action === 'connect' ? 'liked' : 'passed';

        UserMatch::create([
            'user_id'   => $me->id,
            'target_id' => $targetId,
            'status'    => $status,
        ]);

        $isMutual = false;
        $targetUser = null;

        if ($action === 'connect') {
            // Check for mutual connect (both liked each other)
            $reverse = UserMatch::where('user_id', $targetId)
                ->where('target_id', $me->id)
                ->where('status', 'liked')
                ->first();

            if ($reverse) {
                $isMutual = true;
                // Upgrade both to matched
                UserMatch::where('user_id', $me->id)->where('target_id', $targetId)->update(['status' => 'matched']);
                $reverse->update(['status' => 'matched']);

                // Award XP for making a new connection
                $me->increment('points', 15);
                User::find($targetId)->increment('points', 15);
            }

            $targetUser = User::find($targetId);
        }

        // Bust stats cache
        Cache::forget('gacha_stats');

        return response()->json([
            'success'   => true,
            'is_mutual' => $isMutual,
            'target'    => $isMutual ? [
                'name'       => $targetUser->name,
                'avatar'     => $targetUser->profile_picture_url,
                'chat_url'   => route('alumni.chat'),
                'profile_url'=> route('alumni.show', $targetUser),
            ] : null,
        ]);
    }

    /**
     * My connections — all alumni I've mutually matched with via Gacha.
     */
    public function myConnections()
    {
        $connections = UserMatch::where('user_id', auth()->id())
            ->where('status', 'matched')
            ->with('target:id,name,profile_picture,major,city,graduation_year')
            ->latest()
            ->paginate(12);

        return response()->json($connections);
    }
}
