<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    /**
     * Store a new story (Valid for 24 hours)
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|max:10240',
            'caption' => 'nullable|string|max:100',
            'spotify_url' => 'nullable|url',
        ]);

        if (!$request->hasFile('image') && !$request->spotify_url) {
            return back()->with('error', 'Pilih gambar atau masukkan link Spotify.');
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('stories', 'public');
            $imageUrl = Storage::url($path);
        }

        $spotifyUrl = $request->spotify_url;
        if ($spotifyUrl) {
            // Convert to embed URL if needed
            if (str_contains($spotifyUrl, 'open.spotify.com/track/')) {
                // Ensure it's the embed format
                $spotifyUrl = str_replace('open.spotify.com/track/', 'open.spotify.com/embed/track/', $spotifyUrl);
            }
        }

        Story::create([
            'user_id' => auth()->id(),
            'type' => $spotifyUrl && !$imageUrl ? 'spotify' : 'image',
            'image_url' => $imageUrl,
            'spotify_url' => $spotifyUrl,
            'caption' => $request->caption,
            'expires_at' => now()->addHours(24),
        ]);

        return redirect()->back()->with('success', 'Story berhasil diposting! Akan hilang dalam 24 jam.');
    }

    public function storeNote(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:60',
        ]);

        $story = Story::create([
            'user_id' => auth()->id(),
            'type' => 'note',
            'content' => $request->content,
            'expires_at' => now()->addHours(24),
        ]);

        return back()->with('success', 'Catatan dibagikan!');
    }

    /**
     * Handle shared story links
     */
    public function showShared(Story $story)
    {
        if ($story->expires_at < now()) {
            return redirect('/feed')->with('error', 'Story ini sudah kadaluarsa atau dihapus.');
        }

        // We can just redirect to the feed and pass a parameter to auto-open it via JS if we want,
        // or just let them see the feed. For a seamless Gen-Z experience, let's pass a query param.
        return redirect()->route('feed.index', ['open_story' => $story->user_id]);
    }

    /**
     * View a specific story (API)
     */
    public function show(Story $story)
    {
        if ($story->expires_at < now()) {
            return response()->json(['error' => 'Story sudah kadaluarsa.'], 410);
        }

        $story->increment('views_count');

        return response()->json([
            'id' => $story->id,
            'user' => [
                'name' => $story->user->name,
                'avatar' => $story->user->profile_picture_url,
            ],
            'type' => $story->type,
            'image_url' => $story->image_url,
            'spotify_url' => $story->spotify_url,
            'caption' => $story->caption,
            'created_at_human' => $story->created_at->diffForHumans(),
            'created_at' => $story->created_at,
        ]);
    }

    /**
     * Get active stories for the feed (Grouped by User)
     */
    public function getActiveStories()
    {
        $stories = Story::active()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($story) {
                $story->created_at_human = $story->created_at->diffForHumans();
                return $story;
            })
            ->groupBy('user_id');

        return response()->json($stories);
    }
}
