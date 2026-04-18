<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FeedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedController extends Controller
{
    protected $feedService;

    public function __construct(FeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    /**
     * Display the alumni feed
     */
    public function index()
    {
        $user = auth()->user();
        $posts = $this->feedService->getFeed($user);

        return view('alumni.feed.index', compact('posts'));
    }

    /**
     * Store a new post
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'image' => 'nullable|image|max:5120', // 5MB max
            'visibility' => 'required|in:public,friends',
            'is_anonymous' => 'nullable|boolean',
            'type' => 'nullable|string',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $imageUrl = Storage::url($path);
        }

        $this->feedService->createPost(auth()->user(), [
            'content' => $request->content,
            'image_url' => $imageUrl,
            'visibility' => $request->visibility,
            'is_anonymous' => $request->is_anonymous ?? false,
            'type' => $request->type ?? 'memory',
        ]);

        return redirect()->back()->with('success', 'Postingan berhasil dibagikan!');
    }

    /**
     * Toggle Follow/Unfollow
     */
    public function toggleFollow(User $user)
    {
        $result = $this->feedService->toggleFollow(auth()->user(), $user);
        
        if (!$result) {
            return response()->json(['error' => 'Gagal mengikuti.'], 400);
        }

        return response()->json($result);
    }
}
