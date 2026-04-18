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
            'image' => 'required|image|max:10240', // 10MB max for high quality
            'caption' => 'nullable|string|max:100',
        ]);

        $path = $request->file('image')->store('stories', 'public');
        $imageUrl = Storage::url($path);

        Story::create([
            'user_id' => auth()->id(),
            'image_url' => $imageUrl,
            'caption' => $request->caption,
            'expires_at' => now()->addHours(24),
        ]);

        return redirect()->back()->with('success', 'Story berhasil diposting! Akan hilang dalam 24 jam.');
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
            'image_url' => $story->image_url,
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
