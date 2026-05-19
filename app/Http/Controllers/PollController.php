<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    public function index()
    {
        $activePolls = Poll::with(['options', 'creator'])
            ->active()
            ->latest()
            ->get()
            ->map(function ($poll) {
                $poll->user_voted = auth()->check() ? $poll->hasVoted(auth()->user()) : false;
                $poll->user_vote_ids = auth()->check()
                    ? PollVote::where('poll_id', $poll->id)->where('user_id', auth()->id())->pluck('poll_option_id')
                    : collect();
                return $poll;
            });

        $closedPolls = Poll::with(['options'])
            ->where('status', 'closed')
            ->orWhere(fn ($q) => $q->where('status', 'active')->where('ends_at', '<', now()))
            ->latest()
            ->limit(5)
            ->get();

        return view('polls.index', compact('activePolls', 'closedPolls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question'   => 'required|string|max:300',
            'description'=> 'nullable|string|max:500',
            'emoji'      => 'nullable|string|max:10',
            'type'       => 'required|in:single,multiple',
            'options'    => 'required|array|min:2|max:10',
            'options.*'  => 'required|string|max:100',
            'option_emojis.*' => 'nullable|string|max:10',
            'ends_at'    => 'nullable|date|after:now',
            'is_anonymous' => 'boolean',
        ]);

        $poll = Poll::create([
            'created_by'  => auth()->id(),
            'question'    => $request->question,
            'description' => $request->description,
            'emoji'       => $request->emoji ?? '🗳️',
            'type'        => $request->type,
            'status'      => 'active',
            'is_anonymous'=> $request->boolean('is_anonymous'),
            'ends_at'     => $request->ends_at,
        ]);

        foreach ($request->options as $i => $optionText) {
            PollOption::create([
                'poll_id'      => $poll->id,
                'option_text'  => $optionText,
                'option_emoji' => $request->option_emojis[$i] ?? null,
                'sort_order'   => $i,
            ]);
        }

        return back()->with('success', 'Polling berhasil dibuat! 🗳️');
    }

    public function vote(Request $request, Poll $poll)
    {
        abort_if($poll->is_expired || $poll->status !== 'active', 403, 'Polling sudah ditutup.');

        $user = auth()->user();

        // Prevent duplicate vote
        if ($poll->hasVoted($user)) {
            return response()->json(['error' => 'Kamu sudah vote di polling ini!'], 422);
        }

        $request->validate([
            'option_ids'   => 'required|array|min:1',
            'option_ids.*' => 'exists:poll_options,id',
        ]);

        $optionIds = collect($request->option_ids);

        // For single-choice, only allow 1
        if ($poll->type === 'single' && $optionIds->count() > 1) {
            return response()->json(['error' => 'Hanya boleh memilih 1 jawaban.'], 422);
        }

        DB::transaction(function () use ($poll, $optionIds, $user) {
            foreach ($optionIds as $optionId) {
                PollVote::create([
                    'poll_id'        => $poll->id,
                    'poll_option_id' => $optionId,
                    'user_id'        => $user->id,
                ]);
                PollOption::where('id', $optionId)->increment('votes_count');
            }
        });

        // Award XP for participating
        $user->increment('points', 2);

        $poll->load('options');
        return response()->json([
            'success' => true,
            'results' => $poll->options->map(fn ($opt) => [
                'id'         => $opt->id,
                'text'       => $opt->option_text,
                'emoji'      => $opt->option_emoji,
                'votes'      => $opt->votes_count,
                'percentage' => $opt->percentage,
            ]),
            'total_votes' => $poll->total_votes,
        ]);
    }

    public function destroy(Poll $poll)
    {
        abort_if(auth()->id() !== $poll->created_by && !in_array(auth()->user()->role, ['admin', 'editor']), 403);
        $poll->delete();
        return back()->with('success', 'Polling dihapus.');
    }
}
