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
        $forums = Forum::where('status', 'active')->with(['user', 'comments.user'])->latest()->paginate(15);
        return view('forums.index', compact('forums'));
    }

    public function show($id)
    {
        $forum = Forum::where('status', 'active')->with(['user', 'comments.user', 'comments' => function($query) {
            $query->latest();
        }])->findOrFail($id);
        return view('forums.show', compact('forum'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_diskusi' => 'required|string|max:255',
            'deskripsi_masalah' => 'required|string',
        ]);

        if (!\App\Services\ContentModerationService::isClean($request->judul_diskusi) || 
            !\App\Services\ContentModerationService::isClean($request->deskripsi_masalah)) {
            return back()->withInput()->withErrors(['judul_diskusi' => '⚠️ PERINGATAN ADMIN: Konten Anda mengandung kata-kata yang melanggar aturan komunitas (Makian/SARA). Mohon tetap menjaga kesantunan.']);
        }

        $forum = Forum::create([
            'user_id' => Auth::id(),
            'title' => strip_tags($request->judul_diskusi),
            'content' => strip_tags($request->deskripsi_masalah),
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

        // AI Moderation (Background)
        try { \App\Jobs\ModerateContentWithAI::dispatch($forum); } catch (\Throwable $e) { \Illuminate\Support\Facades\Log::warning('ModerateContentWithAI dispatch failed: ' . $e->getMessage()); }

        return back()->with('success', 'Diskusi berhasil dibuat dan Anda mendapatkan 10 poin!');
    }

    public function storeComment(Request $request, Forum $forum)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        if (!\App\Services\ContentModerationService::isClean($request->content)) {
            return back()->withInput()->withErrors(['content' => '⚠️ TEGURAN ADMIN: Komentar Anda tidak diperbolehkan karena mengandung kata-kata tidak pantas.']);
        }

        $comment = $forum->comments()->create([
            'user_id' => Auth::id(),
            'content' => strip_tags($request->content),
        ]);

        $forum->increment('comments_count');

        // AI Moderation
        try { \App\Jobs\ModerateContentWithAI::dispatch($comment); } catch (\Throwable $e) { \Illuminate\Support\Facades\Log::warning('ModerateContentWithAI comment dispatch failed: ' . $e->getMessage()); }

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
