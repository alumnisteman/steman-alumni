<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FeedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedController extends Controller
{
    protected $feedService;
    protected $alumniService;

    public function __construct(FeedService $feedService, \App\Services\AlumniService $alumniService)
    {
        $this->feedService = $feedService;
        $this->alumniService = $alumniService;
    }

    /**
     * Display the alumni feed
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $page = $request->get('page', 1);
            $perPage = 10;
            
            // FOMO: Count online alumni
            $onlineCount = $this->alumniService->getOnlineAlumniCount();

            if (!$user) {
                // Handle Guest View
                $posts = \App\Models\Post::where('visibility', 'public')->latest()->paginate($perPage);
            } else {
                $posts = $this->feedService->getFeed($user, $page, $perPage);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'html' => view('alumni.feed.posts', compact('posts'))->render(),
                    'hasMore' => count($posts) >= $perPage
                ]);
            }

            return view('alumni.feed.index', compact('posts', 'onlineCount'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Feed Index Error: ' . $e->getMessage());
            
            try {
                $posts = \App\Models\Post::where('visibility', 'public')->latest()->paginate(20);
                $view = view('alumni.feed.index', compact('posts'))->render();
                return response($view);
            } catch (\Throwable $e2) {
                \Illuminate\Support\Facades\Log::error('Feed Fallback Error: ' . $e2->getMessage());
                // If even the fallback view fails, return the 500 error view directly
                return response()->view('errors.500', ['exception_message' => $e2->getMessage()], 500);
            }
        }
    }

    /**
     * Store a new post
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'image' => 'nullable|image|max:5120', // 5MB max
            'visibility' => 'nullable|in:public,friends',
            'is_anonymous' => 'nullable|boolean',
            'type' => 'nullable|string',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $imageUrl = Storage::url($path);
        }

        $post = $this->feedService->createPost(auth()->user(), [
            'content' => $request->content,
            'image_url' => $imageUrl,
            'visibility' => $request->visibility ?? 'public',
            'is_anonymous' => $request->is_anonymous ?? false,
            'type' => $request->type ?? 'memory',
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Postingan berhasil dibagikan!',
                'id' => $post->id
            ]);
        }

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
