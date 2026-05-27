<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
        $this->middleware('can:manage-polls')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $activePolls = Poll::where('is_active', true)
            ->orderByDesc('created_at')
            ->get();

        $closedPolls = Poll::where('is_active', false)
            ->orderByDesc('created_at')
            ->get();

        return view('polls.index', compact('activePolls', 'closedPolls'));
    }

    public function create()
    {
        return view('polls.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'emoji'        => 'nullable|string|max:5',
            'question'     => 'required|string|max:255',
            'description'  => 'nullable|string',
            'type'         => 'nullable|in:single,multiple',
            'ends_at'      => 'nullable|date',
            'is_anonymous' => 'sometimes|boolean',
            'options'      => 'required|array|min:2',
            'options.*'    => 'required|string|max:255',
            'option_emojis' => 'nullable|array',
            'option_emojis.*' => 'nullable|string|max:5',
        ]);

        $optionEmojis = $request->input('option_emojis', []);
        $options = $request->input('options', []);
        $combinedOptions = [];
        foreach ($options as $i => $text) {
            if (trim($text) === '') continue;
            $combinedOptions[] = [
                'id'           => $i + 1,
                'option_emoji' => $optionEmojis[$i] ?? null,
                'option_text'  => $text,
                'votes_count'  => 0,
            ];
        }

        Poll::create([
            'emoji'        => $data['emoji'] ?? '🗳️',
            'question'     => $data['question'],
            'description'  => $data['description'] ?? null,
            'type'         => $data['type'] ?? 'single',
            'ends_at'      => $data['ends_at'] ?? null,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'is_active'    => true,
            'options'      => json_encode($combinedOptions),
            'created_by'   => auth()->id(),
        ]);

        return redirect()->route('polls.index')
                         ->with('success', 'Polling berhasil dibuat!');
    }

    public function edit(Poll $poll)
    {
        return view('polls.edit', compact('poll'));
    }

    public function update(Request $request, Poll $poll)
    {
        $data = $request->validate([
            'emoji'        => 'nullable|string|max:5',
            'question'     => 'required|string|max:255',
            'description'  => 'nullable|string',
            'type'         => 'nullable|in:single,multiple',
            'ends_at'      => 'nullable|date',
            'is_anonymous' => 'sometimes|boolean',
            'is_active'    => 'sometimes|boolean',
            'options'      => 'required|array|min:2',
            'options.*'    => 'required|string|max:255',
            'option_emojis' => 'nullable|array',
            'option_emojis.*' => 'nullable|string|max:5',
        ]);

        $optionEmojis = $request->input('option_emojis', []);
        $options = $request->input('options', []);
        $existingOptions = $poll->options ?? [];

        $combinedOptions = [];
        foreach ($options as $i => $text) {
            if (trim($text) === '') continue;
            $existingVotes = $existingOptions[$i]['votes_count'] ?? 0;
            $combinedOptions[] = [
                'id'           => $i + 1,
                'option_emoji' => $optionEmojis[$i] ?? null,
                'option_text'  => $text,
                'votes_count'  => $existingVotes,
            ];
        }

        $poll->update([
            'emoji'        => $data['emoji'] ?? '🗳️',
            'question'     => $data['question'],
            'description'  => $data['description'] ?? null,
            'type'         => $data['type'] ?? 'single',
            'ends_at'      => $data['ends_at'] ?? null,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'is_active'    => $request->boolean('is_active', true),
            'options'      => json_encode($combinedOptions),
        ]);

        return redirect()->route('polls.index')
                         ->with('success', 'Polling berhasil diperbarui!');
    }

    public function destroy(Poll $poll)
    {
        $poll->delete();
        return redirect()->route('polls.index')
                         ->with('success', 'Polling berhasil dihapus.');
    }

    public function vote(Request $request, Poll $poll)
    {
        if (!$poll->is_active) {
            return response()->json(['success' => false, 'error' => 'Polling sudah ditutup.']);
        }

        $request->validate([
            'option_ids'   => 'required|array|min:1',
            'option_ids.*' => 'integer',
        ]);

        $selectedIds = $request->input('option_ids');
        $options = $poll->options ?? [];

        $totalVotes = 0;
        foreach ($options as &$opt) {
            if (in_array($opt['id'], $selectedIds)) {
                $opt['votes_count'] = ($opt['votes_count'] ?? 0) + 1;
            }
            $totalVotes += $opt['votes_count'] ?? 0;
        }

        $poll->update(['options' => json_encode($options)]);

        $results = array_map(function ($opt) use ($totalVotes) {
            $pct = $totalVotes > 0 ? round(($opt['votes_count'] / $totalVotes) * 100) : 0;
            return [
                'id'         => $opt['id'],
                'text'       => $opt['option_text'],
                'emoji'      => $opt['option_emoji'] ?? null,
                'votes'      => $opt['votes_count'],
                'percentage' => $pct,
            ];
        }, $options);

        return response()->json([
            'success'     => true,
            'results'     => $results,
            'total_votes' => $totalVotes,
        ]);
    }
}
