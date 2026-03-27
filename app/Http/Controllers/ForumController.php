<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index()
    {
        $forums = Forum::with('user')->latest()->paginate(15);
        return view('forums.index', compact('forums'));
    }

    public function show(Forum $forum)
    {
        $forum->load(['user', 'comments.user']);
        return view('forums.show', compact('forum'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_diskusi' => 'required|string|max:255',
            'deskripsi_masalah' => 'required|string',
        ]);

        $forum = Forum::create([
            'user_id' => Auth::id(),
            'judul_diskusi' => $request->judul_diskusi,
            'deskripsi_masalah' => $request->deskripsi_masalah,
        ]);

        // Award Points
        Auth::user()->awardPoints(10);

        return back()->with('success', 'Diskusi berhasil dibuat dan Anda mendapatkan 10 poin!');
    }

    public function storeComment(Request $request, Forum $forum)
    {
        $request->validate([
            'konten' => 'required|string',
        ]);

        Comment::create([
            'forum_id' => $forum->id,
            'user_id' => Auth::id(),
            'konten' => $request->konten,
        ]);

        $forum->increment('jumlah_komentar');

        // Award Points
        Auth::user()->awardPoints(10);

        return back()->with('success', 'Komentar ditambahkan! +10 poin untuk Anda.');
    }
}
