<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-polls']);
    }

    public function index()
    {
        $polls = Poll::orderByDesc('created_at')->paginate(15);
        return view('polls.index', compact('polls'));
    }

    public function create()
    {
        return view('polls.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'options'     => 'required|array|min:2',
            'options.*'   => 'required|string|max:255',
            'is_active'   => 'sometimes|boolean',
        ]);
        $data['options'] = json_encode($data['options']);
        Poll::create($data);
        return redirect()->route('polls.index')
                         ->with('success', 'Poll created successfully.');
    }

    public function edit(Poll $poll)
    {
        return view('polls.edit', compact('poll'));
    }

    public function update(Request $request, Poll $poll)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'options'     => 'required|array|min:2',
            'options.*'   => 'required|string|max:255',
            'is_active'   => 'sometimes|boolean',
        ]);
        $data['options'] = json_encode($data['options']);
        $poll->update($data);
        return redirect()->route('polls.index')
                         ->with('success', 'Poll updated successfully.');
    }

    public function destroy(Poll $poll)
    {
        $poll->delete();
        return redirect()->route('polls.index')
                         ->with('success', 'Poll deleted.');
    }

    public function vote(Request $request, Poll $poll)
    {
        // Placeholder for vote logic
        $option = $request->input('option');
        // TODO: Store the vote
        return redirect()->route('polls.index')
                         ->with('success', 'Your vote has been recorded.');
    }
}
