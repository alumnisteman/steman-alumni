<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\LogActivity;

class ForumController extends Controller
{
    public function index()
    {
        $forums = Forum::where('status', 'active')->with('user')->latest()->paginate(15);
        return view('forums.index', compact('forums'));
    }

    public function show($id)
    {
        $forum = Forum::where('status', 'active')->with(['user', 'comments.user'])->findOrFail($id);
        return view('forums.show', compact('forum'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $forum = Forum::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // Award Points
        Auth::user()->awardPoints(10);

        LogActivity::dispatch(
            Auth::id(),
            'Create Forum Post',
            'Created discussion: ' . $forum->title,
            $request->ip(),
            $request->header('User-Agent')
        );

        return back()->with('success', 'Diskusi berhasil dibuat dan Anda mendapatkan 10 poin!');
    }

    public function storeComment(Request $request, Forum $forum)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $forum->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        $forum->increment('comments_count');

        // Award Points
        Auth::user()->awardPoints(10);

        LogActivity::dispatch(
            Auth::id(),
            'Create Forum Comment',
            'Commented on discussion: ' . $forum->title,
            $request->ip(),
            $request->header('User-Agent')
        );

        return back()->with('success', 'Komentar ditambahkan! +10 poin untuk Anda.');
    }
}
